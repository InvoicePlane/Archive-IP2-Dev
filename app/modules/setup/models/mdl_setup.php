<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Setup
 * @package Modules\Setup\Models
 * @property CI_DB_query_builder $db
 * @property CI_Loader $load
 * @property CI_Session $session
 */
class Mdl_Setup extends CI_Model
{
    public $errors = array();

    /**
     * Installs the basic tables
     * 
     * @return bool
     */
    public function install_tables()
    {
        $file_contents = file_get_contents(APPPATH . 'modules/setup/sql/000_2.0.0.sql');

        $this->execute_contents($file_contents);

        $this->save_version('000_2.0.0.sql');

        if ($this->errors) {
            return false;
        }

        $this->install_default_data();

        $this->install_default_settings();

        return true;
    }

    /**
     * Runs all upgrades on the database
     * 
     * @return bool
     */
    public function upgrade_tables()
    {
        $this->load->helper('directory');
        
        // Collect the available SQL files
        $sql_files = directory_map(APPPATH . 'modules/setup/sql', 1);

        // Sort them so they're in natural order
        sort($sql_files);

        // Unset the installer
        unset($sql_files[0]);

        // Loop through the files and take appropriate action
        foreach ($sql_files as $sql_file) {
            if (substr($sql_file, -4) == '.sql') {
                
                $this->db->where('version_file', $sql_file);
                $update_applied = $this->db->get('versions');

                if (!$update_applied->num_rows()) {
                    $file_contents = file_get_contents(APPPATH . 'modules/setup/sql/' . $sql_file);

                    $this->execute_contents($file_contents);

                    $this->save_version($sql_file);

                    // Check for any required upgrade methods
                    $upgrade_method = 'upgrade_' . str_replace('.', '_', substr($sql_file, 0, -4));

                    if (method_exists($this, $upgrade_method)) {
                        $this->$upgrade_method();
                    }
                }
            }
        }

        if ($this->errors) {
            return false;
        }

        $this->install_default_settings();

        return true;
    }

    /**
     * Executes the SQL files
     * 
     * @param $contents
     */
    private function execute_contents($contents)
    {
        $commands = explode(';', $contents);

        foreach ($commands as $command) {
            if (trim($command)) {
                if (!$this->db->query(trim($command) . ';')) {
                    $this->errors[] = $this->db->_error_message();
                }
            }
        }
    }

    /**
     * Inserts the default data that is needed to run properly
     */
    public function install_default_data()
    {
        // Save the standard invoice group
        $this->db->insert('invoice_groups', array(
                'name' => 'Invoice Default',
                'identifier_format' => 'I-{{{ID}}}',
                'next_id' => 1,
                'left_pad' => 3,
            )
        );

        // Save the standard quote invoice group
        $this->db->insert('invoice_groups', array(
            'name' => 'Quote Default',
            'identifier_format' => 'Q-{{{ID}}}',
            'next_id' => 1,
            'left_pad' => 3,
        ));

        // @TODO IP-366 - User roles (set permissions)
        // Save the system admin user role
        $this->db->insert('user_roles', array(
            'name' => 'system_admin',
            'permissions' => json_encode(array('all')),
            'is_client' => false,
        ));

        // Save the manager user role
        $this->db->insert('user_roles', array(
            'name' => 'manager',
            'permissions' => json_encode(array('all')),
        ));

        // Save the system admin user role
        $this->db->insert('user_roles', array(
            'name' => 'user',
            'permissions' => json_encode(array('all')),
        ));

        // Save the client user role
        $this->db->insert('user_roles', array(
            'name' => 'client',
            'permissions' => json_encode(array('all')),
        ));
    }

    /**
     * Inserts the default settings that is needed to run properly
     */
    private function install_default_settings()
    {
        $this->load->helper('string');

        $default_settings = array(
            'default_language' => $this->session->userdata('ip_lang'),
            'date_format' => 'm/d/Y',
            'currency_symbol' => '$',
            'currency_symbol_placement' => 'before',
            'thousands_separator' => ',',
            'decimal_point' => '.',
            'tax_rate_decimal_places' => 2,
            'item_price_decimal_places' => 2,
            'item_amount_decimal_places' => 2,
            'invoices_due_after' => 30,
            'quotes_expire_after' => 15,
            'default_invoice_group' => 1,
            'default_quote_group' => 2,
            'api_key' => hash('md5', microtime()),
        );

        foreach ($default_settings as $setting_key => $setting_value) {
            $this->db->where('key', $setting_key);

            if (!$this->db->get('settings')->num_rows()) {
                $db_array = array(
                    'key' => $setting_key,
                    'value' => $setting_value
                );

                $this->db->insert('settings', $db_array);
            }
        }
    }

    /**
     * Saves the version for a SQL file
     * @param $sql_file
     */
    private function save_version($sql_file)
    {
        $version_db_array = array(
            'file' => $sql_file,
            'sql_errors' => count($this->errors),
            'date_applied' => date('Y-m-d H:i:s'),
        );

        $this->db->insert('versions', $version_db_array);
    }

    /*
     * Place upgrade functions here
     * e.g. if table rows have to be converted
     * public function upgrade_010_1_0_1() { ... }
     */
}
