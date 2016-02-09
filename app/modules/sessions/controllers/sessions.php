<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Sessions
 * @package Modules\Sessions\Controllers
 */
class Sessions extends Base_Controller
{
    /**
     * Sessions constructor.
     */
    public function __construct()
    {
        Base_Controller::__construct();
    }

    /**
     * Authenticate function that checks if the given user credentials are correct
     *
     * @param $email_address
     * @param $password
     * @return bool
     */
    public function authenticate($email_address, $password)
    {
        $this->load->model('mdl_sessions');

        if ($this->mdl_sessions->auth($email_address, $password)) {
            return true;
        }

        return false;
    }

    /**
     * Index page, redirects to sessions/login
     */
    public function index()
    {
        redirect('sessions/login');
    }

    /**
     * Returns the login form
     */
    public function login()
    {
        $view_data = array(
            'login_logo' => $this->mdl_settings->setting('login_logo')
        );

        // Check if the the form was submitted
        if ($this->input->post('btn_login')) {

            // Check if the user exists
            $this->db->where('email', $this->input->post('email'));
            $query = $this->db->get('users');
            $user = $query->row();

            if (empty($user)) {
                $this->session->set_flashdata('alert_error', lang('loginalert_user_not_found'));
            } else {

                // Check if the user is marked as active
                if ($user->is_active == 0) {
                    $this->session->set_flashdata('alert_error', lang('loginalert_user_inactive'));
                } else {
                    // Try to authenticate the user
                    $email = $this->input->post('email');
                    $password = $this->input->post('password');

                    if ($this->authenticate($email, $password)) {
                        redirect('sessions/login');
                    } else {
                        $this->session->set_flashdata('alert_error', lang('loginalert_credentials_incorrect'));
                    }
                }
            }
        }

        $this->layout->set($view_data);
        $this->layout->buffer('content', 'sessions/session_login');
        $this->layout->render('base');
    }

    /**
     * Loggs out the user and redirects to the login page
     */
    public function logout()
    {
        $this->session->sess_destroy();

        redirect('sessions/login');
    }

    /**
     * Handles the password reset form
     * @param null $token
     * @return mixed
     */
    public function passwordreset($token = null)
    {
        // Check if a token was provided
        if ($token) {
            
            $this->db->where('passwordreset_token', $token);
            $user = $this->db->get('users');
            $user = $user->row();

            if (empty($user)) {
                // Redirect back to the login screen with an alert
                $this->session->set_flashdata('alert_success', lang('wrong_passwordreset_token'));
                redirect('sessions/login');
            }

            $formdata = array(
                'id' => $user->id
            );

            $this->layout->set($formdata);
            $this->layout->buffer('content', 'sessions/session_new_password');
            $this->layout->render('base');

        }

        // Check if the form for a new password was used
        if ($this->input->post('btn_new_password')) {
            
            $new_password = $this->input->post('new_password');
            $user_id = $this->input->post('id');

            if (empty($user_id) || empty($new_password)) {
                $this->session->set_flashdata('alert_error', lang('loginalert_no_password'));
                redirect($_SERVER['HTTP_REFERER']);
            }

            // Call the save_change_password() function from users model
            $this->load->model('users/mdl_users');
            $this->mdl_users->save_change_password(
                $user_id, $new_password
            );

            // Update the user and set him active again
            $db_array = array(
                'passwordreset_token' => '',
            );

            $this->db->where('id', $user_id);
            $this->db->update('users', $db_array);

            // Redirect back to the login form
            redirect('sessions/login');

        }

        // Check if the password reset form was used
        if ($this->input->post('btn_reset')) {
            $email = $this->input->post('email');
            
            if (empty($email)) {
                $this->session->set_flashdata('alert_error', lang('loginalert_user_not_found'));
                redirect($_SERVER['HTTP_REFERER']);
            }

            // Test if a user with this email exists
            if ($this->db->where('email', $email)) {
                // Create a passwordreset token
                $email = $this->input->post('email');
                $token = md5(microtime() . $email);

                // Save the token to the database and set the user to inactive
                $db_array = array(
                    'passwordreset_token' => $token,
                );

                $this->db->where('email', $email);
                $this->db->update('users', $db_array);

                // Send the email with reset link
                $this->load->library('email');

                // Preprare some variables for the email
                $email_resetlink = site_url('sessions/passwordreset/' . $token);
                
                $email_message = $this->load->view('emails/passwordreset', array(
                    'resetlink' => $email_resetlink
                ), true);
                
                $email_from = 'system@' . preg_replace("/^[\w]{2,6}:\/\/([\w\d\.\-]+).*$/", "$1", site_url());

                // Set email configuration
                $config['mailtype'] = 'html';
                $this->email->initialize($config);

                // Set the email params
                $this->email->from($email_from);
                $this->email->to($email);
                $this->email->subject(lang('password_reset'));
                $this->email->message($email_message);

                // Send the reset email
                $this->email->send();

                // Redirect back to the login screen with an alert
                $this->session->set_flashdata('alert_success', lang('email_successfully_sent'));
                redirect('sessions/login');
            }
        }

        $this->layout->buffer('content', 'sessions/session_passwordreset');
        $this->layout->render('base');
    }
}
