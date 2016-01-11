<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Settings
 * @package Modules\Settings\Models
 */
class Mdl_Settings extends CI_Model
{
    public $settings = array();

    /**
     * Get a settings value by its key
     * @param $key
     * @return null
     */
    public function get($key)
    {
        $this->db->select('setting_value');
        $this->db->where('setting_key', $key);
        $query = $this->db->get('ip_settings');

        if ($query->row()) {
            return $query->row()->setting_value;
        } else {
            return null;
        }
    }

    /**
     * Save a settings value by key
     * @param $key
     * @param $value
     */
    public function save($key, $value)
    {
        $db_array = array(
            'setting_key' => $key,
            'setting_value' => $value
        );

        if ($this->get($key) !== null) {
            $this->db->where('setting_key', $key);
            $this->db->update('ip_settings', $db_array);
        } else {
            $this->db->insert('ip_settings', $db_array);
        }
    }

    /**
     * Deletes a settings value by key
     * @param $key
     */
    public function delete($key)
    {
        $this->db->where('setting_key', $key);
        $this->db->delete('ip_settings');
    }

    /**
     * Loads the settings for the current session
     */
    public function load_settings()
    {
        $ip_settings = $this->db->get('ip_settings')->result();

        foreach ($ip_settings as $data) {
            $this->settings[$data->setting_key] = $data->setting_value;
        }
    }

    /**
     * Returns a settings key if it's available
     * @param $key
     * @return string
     */
    public function setting($key)
    {
        return (isset($this->settings[$key])) ? $this->settings[$key] : '';
    }

    /**
     * Overrides a settings value fo the current session
     * @param $key
     * @param $value
     */
    public function set_setting($key, $value)
    {
        $this->settings[$key] = $value;
    }
}
