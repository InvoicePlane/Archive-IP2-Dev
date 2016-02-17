<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Setup
 * @package Modules\Setup\Controllers
 * @property CI_DB_query_builder $db
 * @property CI_Encrypt $encrypt
 * @property CI_Input $input
 * @property CI_Session $session
 * @property Layout $layout
 * @property Mdl_Settings $mdl_settings
 * @property Mdl_Setup $mdl_setup
 * @property Mdl_Users $mdl_users
 */
class Setup extends Setup_Controller
{
    public $errors = 0;

    /**
     * Setup constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('mdl_setup');
    }

    /**
     * Index page, redirects to setup/language
     */
    public function index()
    {
        redirect('setup/language');
    }

    /**
     * Setup step to set the language
     */
    public function language()
    {
        if ($this->input->post('btn_continue')) {
            $this->session->set_userdata('ip_lang', $this->input->post('ip_lang'));
            $this->session->set_userdata('install_step', 'prerequisites');
            redirect('setup/prerequisites');
        }

        $this->session->unset_userdata('install_step');
        $this->session->unset_userdata('is_upgrade');

        $this->load->helper('directory');

        $raw_languages = directory_map(APPPATH . '/language', 1);
        sort($raw_languages);

        $languages = [];
        foreach ($raw_languages as $language) {
            if ($language != 'index.html') {
                $language = str_replace('/', '', $language);
                $languages[] = [
                    'value' => $language,
                    'label' => ucfirst($language),
                ];
            }
        }

        $this->layout->set('languages', $languages);

        $this->layout->buffer('content', 'setup/language');
        $this->layout->render('base');
    }

    /**
     * Setup step to check the pre requisites
     */
    public function prerequisites()
    {
        if ($this->session->userdata('install_step') != 'prerequisites') {
            redirect('setup/language');
        }

        if ($this->input->post('btn_continue')) {
            $this->session->set_userdata('install_step', 'configure_database');
            redirect('setup/configure_database');
        }

        $this->layout->set(
            array(
                'basics' => $this->check_basics(),
                'writables' => $this->check_writables(),
                'errors' => $this->errors
            )
        );

        $this->layout->buffer('content', 'setup/prerequisites');
        $this->layout->render('base');
    }

    /**
     * Setup step to configure the database
     */
    public function configure_database()
    {
        if ($this->session->userdata('install_step') != 'configure_database') {
            $this->restart_setup();
        }

        if ($this->input->post('btn_continue')) {

            $this->load->database();

            // This might be an upgrade - check if it is
            if (!$this->db->table_exists('versions')) {
                // This appears to be an install
                $this->session->set_userdata('is_upgrade', false);
                $this->session->set_userdata('install_step', 'install_tables');
                redirect('setup/install_tables');
            } else {
                // This appears to be an upgrade
                $this->session->set_userdata('is_upgrade', true);
                $this->session->set_userdata('install_step', 'upgrade_tables');
                redirect('setup/upgrade_tables');
            }
        }

        $db_check = $this->check_database();

        if ($db_check['success'] === 1) {
            $this->write_database_config();
        }

        $this->layout->set('database', $db_check);
        $this->layout->set('errors', $this->errors);
        $this->layout->buffer('content', 'setup/configure_database');
        $this->layout->render('base');
    }

    /**
     * Setup step to install the basic database tables
     */
    public function install_tables()
    {
        if ($this->session->userdata('install_step') != 'install_tables') {
            $this->restart_setup();
        }

        if ($this->input->post('btn_continue')) {
            $this->session->set_userdata('install_step', 'create_user');
            redirect('setup/create_user');
        }

        $this->load->database();

        $this->layout->set(
            array(
                'success' => $this->mdl_setup->install_tables(),
                'errors' => $this->mdl_setup->errors
            )
        );

        $this->layout->buffer('content', 'setup/install_tables');
        $this->layout->render('base');
    }

    /**
     * Setup step to upgrade all database tables
     */
    public function upgrade_tables()
    {
        if ($this->session->userdata('install_step') != 'upgrade_tables') {
            $this->restart_setup();
        }

        if ($this->input->post('btn_continue')) {
            if (!$this->session->userdata('is_upgrade')) {
                $this->session->set_userdata('install_step', 'create_user');
                redirect('setup/create_user');
            } else {
                $this->session->set_userdata('install_step', 'complete');
                redirect('setup/complete');
            }
        }

        $this->load->database();

        $this->layout->set(
            array(
                'success' => $this->mdl_setup->upgrade_tables(),
                'errors' => $this->mdl_setup->errors
            )
        );

        $this->layout->buffer('content', 'setup/upgrade_tables');
        $this->layout->render('base');
    }

    /**
     * Setup step to create the initial user
     */
    public function create_user()
    {
        if ($this->session->userdata('install_step') != 'create_user') {
            $this->restart_setup();
        }

        $this->load->database();

        $this->load->model('users/mdl_users');

        $this->load->helper('country');

        if ($this->mdl_users->run_validation()) {
            
            $db_array = $this->mdl_users->db_array();

            $this->mdl_users->save(null, $db_array);

            $this->session->set_userdata('install_step', 'configure_application');
            redirect('setup/configure_application');
            
        }

        $this->layout->set(
            array(
                'countries' => get_country_list(lang('cldr')),
                'password_suggestion' => generate_password_suggestion(),
            )
        );
        $this->layout->buffer('content', 'setup/create_user');
        $this->layout->render('base');
    }

    /**
     * Setup step to create configure the application
     */
    public function configure_application()
    {
        if ($this->session->userdata('install_step') != 'configure_application') {
            $this->restart_setup();
        }

        // Skip this step
        if ($this->input->post('btn_skip')) {
            $this->session->set_userdata('install_step', 'complete');
            redirect('setup/complete');
        }

        // Save the settings values to the database
        if ($this->input->post('btn_continue')) {

            $settings = $this->input->post('settings');

            $this->load->database();

            $this->load->library('encrypt');
            $this->load->library('form_validation');

            $this->load->model('mdl_settings');

            foreach ($settings as $key => $value) {
                if ($key == 'smtp_password' && !empty($value)) {
                    $this->mdl_settings->save($key, $this->encrypt->encode($value));
                } else {
                    $this->mdl_settings->save($key, $value);
                }
            }
            
            $this->session->set_userdata('install_step', 'complete');
            redirect('setup/complete');
        }

        $this->load->helper('country');
        $this->load->helper('date');
        
        $this->layout->set(
            array(
                'countries' => get_country_list(lang('cldr')),
                'current_date' => new DateTime(),
                'date_formats' => date_formats(),
                'first_days_of_weeks' => array("0" => lang("sunday"), "1" => lang("monday")),
            )
        );
        $this->layout->buffer('content', 'setup/configure_application');
        $this->layout->render('base');
    }

    /**
     * Setup step to confirm the setup ran successfully
     */
    public function complete()
    {
        if ($this->session->userdata('install_step') != 'complete') {
            $this->restart_setup();
        }

        $this->layout->buffer('content', 'setup/complete');
        $this->layout->render('base');
    }

    /**
     * Returns directories or files that need to be writable
     * @return array
     */
    private function check_writables()
    {
        $checks = array();

        $writables = array(
            DATAFOLDER,
            DATAFOLDER_ARCHIVES,
            DATAFOLDER_CFILES,
            DATAFOLDER_IMAGES,
            DATAFOLDER_IMPORT,
            DATAFOLDER_TEMP,
            APPPATH . 'config/',
            APPPATH . 'logs',
        );

        foreach ($writables as $writable) {
            if (!is_writable($writable)) {
                $checks[] = array(
                    'message' => str_replace(IP_PATH, '', $writable) . ' ' . lang('is_not_writable'),
                    'success' => 0
                );

                $this->errors += 1;
            } else {
                $checks[] = array(
                    'message' => str_replace(IP_PATH, '', $writable) . ' ' . lang('is_writable'),
                    'success' => 1
                );
            }
        }

        return $checks;
    }

    /**
     * Checks if the database connection can be established
     *
     * @return array
     */
    private function check_database()
    {
        if ($this->input->post('db_hostname')
            && $this->input->post('db_username')
            && $this->input->post('db_hostname')
            && $this->input->post('db_hostname')
        ) {

            $hostname = addcslashes($this->input->post('db_hostname'), '\'\\');
            $username = addcslashes($this->input->post('db_username'), '\'\\');
            $password = addcslashes($this->input->post('db_password'), '\'\\');
            $database = addcslashes($this->input->post('db_database'), '\'\\');

            $dsn = 'mysql:host=' . $hostname . ';dbname=' . $database;

            // Test the database connection and return errors if they occur
            try {
                
                new PDO($dsn, $username, $password);
                
            } catch (PDOException $e) {
                $error_message = $e->getMessage();

                $this->errors += 1;
                
                // Prepare the error message
                if (strpos($error_message, '[1045]')) {
                    $error_message = lang('cannot_connect_database_server');
                } elseif (strpos($error_message, '[1044]')) {
                    $error_message = lang('cannot_select_specified_database');
                } else {
                    $error_message = lang('setup_database_unknown_connection_error') . '<br>' . $error_message;
                }
                
                return array(
                    'message' => $error_message,
                    'success' => 0
                );
            }

            // No errors? Connection seems to be okay
            return array(
                'message' => lang('database_properly_configured'),
                'success' => 1
            );
        }

        $this->errors += 1;

        return array(
            'message' => lang('cannot_connect_database_server'),
            'success' => 0
        );
    }

    /**
     * Runs basic checks like the PHP version, timezone,...
     * @return array
     */
    private function check_basics()
    {
        $checks = array();

        $php_required = '5.4';
        $php_installed = PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION;

        if ($php_installed < $php_required) {
            $this->errors += 1;

            $checks[] = array(
                'message' => sprintf(lang('php_version_fail'), $php_installed, $php_required),
                'success' => 0
            );
        } else {
            $checks[] = array(
                'message' => lang('php_version_success'),
                'success' => 1
            );
        }

        if (!ini_get('date.timezone')) {
            $checks[] = array(
                'message' => sprintf(lang('php_timezone_fail'), date_default_timezone_get()),
                'warning' => 1
            );
        } else {
            $checks[] = array(
                'message' => lang('php_timezone_success'),
                'success' => 1
            );
        }

        return $checks;
    }

    /**
     * Writes the database configuration to the configuration file
     */
    private function write_database_config()
    {
        $this->load->helper('file');

        $db_file = file_get_contents(APPPATH . 'config/database_empty.php');

        $hostname = addcslashes($this->input->post('db_hostname'), '\'\\');
        $username = addcslashes($this->input->post('db_username'), '\'\\');
        $password = addcslashes($this->input->post('db_password'), '\'\\');
        $database = addcslashes($this->input->post('db_database'), '\'\\');

        $dsn = 'mysql:host=' . $hostname . ';dbname=' . $database;

        $db_file = str_replace(
            '\'dsn\' => \'\'',
            '\'dsn\' => \'' . $dsn . '\'',
            $db_file);
        $db_file = str_replace(
            '\'hostname\' => \'localhost\'',
            '\'hostname\' => \'' . $hostname . '\'',
            $db_file);
        $db_file = str_replace(
            '\'username\' => \'\'',
            '\'username\' => \'' . $username . '\'',
            $db_file);
        $db_file = str_replace(
            '\'password\' => \'\'',
            '\'password\' => \'' . $password . '\'',
            $db_file);
        $db_file = str_replace(
            '\'database\' => \'\'',
            '\'database\' => \'' . $database . '\'',
            $db_file);

        write_file(APPPATH . 'config/database.php', $db_file);
    }

    /**
     * Delete the database configuration file and redirect ot the prerequisites
     */
    private function restart_setup()
    {
        if (file_exists(APPPATH . 'config/database.php')) {
            unlink(APPPATH . 'config/database.php');
        }

        set_alert('danger', lang('setup_restarted'));

        $this->session->set_userdata('install_step', 'prerequisites');
        
        redirect('setup/prerequisites');
    }
}
