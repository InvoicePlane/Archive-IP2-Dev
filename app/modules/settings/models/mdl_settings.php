<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Settings
 * @package Modules\Settings\Models
 * @property CI_DB_query_builder $db
 */
class Mdl_Settings extends CI_Model
{
    public $settings = array();
    public $table = 'settings';
    public $primary_key = 'settings.id';

    /**
     * Get a settings value by its key
     * @param $key
     * @return null
     */
    public function get($key)
    {
        $this->db->select('value');
        $this->db->where('key', $key);
        $query = $this->db->get('settings');

        if ($query->row()) {
            return $query->row()->value;
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
            'key' => $key,
            'value' => $value
        );

        if ($this->get($key) !== null) {
            $this->db->where('key', $key);
            $this->db->update('settings', $db_array);
        } else {
            $this->db->insert('settings', $db_array);
        }
    }

    /**
     * Deletes a settings value by key
     * @param $key
     */
    public function delete($key)
    {
        $this->db->where('key', $key);
        $this->db->delete('settings');
    }

    /**
     * Loads the settings for the current session
     */
    public function load_settings()
    {
        $settings = $this->db->get('settings')->result();

        foreach ($settings as $data) {
            $this->settings[$data->key] = $data->value;
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
