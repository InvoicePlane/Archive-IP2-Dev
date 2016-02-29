<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Uploads
 * @package Modules\Upload\Models
 *
 * @property CI_DB_query_builder $db
 * @property CI_Loader $load
 * @property Mdl_Invoices $mdl_invoices
 * @property Mdl_Quotes $mdl_quotes
 *
 * @TODO Merge get_*_uploads functions into one function with new argument to address all available upload possibilities
 */
class Mdl_Uploads extends Response_Model
{
    public $table = 'uploads';
    public $primary_key = 'uploads.id';
    public $date_created_field = 'date_uploaded';
    public $date_modified_field = 'date_modified';

    /**
     * The default order directive used in every query
     */
    public function default_order_by()
    {
        $this->db->order_by('uploads.id ASC');
    }

    /**
     * Creates an upload entry
     * @param null $db_array
     * @return int
     */
    public function create($db_array = null)
    {
        $upload_id = parent::save(null, $db_array);

        return $upload_id;
    }

    /**
     * Gets all uploads for a quote ID
     * @param $id
     * @return array
     */
    public function get_quote_uploads($id)
    {
        $this->load->model('quotes/mdl_quotes');

        $quote = $this->mdl_quotes->get_by_id($id);

        $query = $this->db->query("
            SELECT file_name_new, file_name_original
            FROM uploads
            WHERE url_key = '" . $quote->quote_url_key . "'"
        );

        $names = array();

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                array_push($names, array(
                    'path' => getcwd() . '/uploads/customer_files/' . $row->file_name_new,
                    'filename' => $row->file_name_original
                ));
            }
        }

        return $names;
    }

    /**
     * Gets all uploads for an invoice ID
     * @param $id
     * @return array
     */
    public function get_invoice_uploads($id)
    {
        $this->load->model('invoices/mdl_invoices');

        $invoice = $this->mdl_invoices->get_by_id($id);

        $query = $this->db->query("
            SELECT file_name_new, file_name_original
            FROM uploads
            WHERE url_key = '" . $invoice->invoice_url_key . "'"
        );

        $names = array();

        if ($query->num_rows() > 0) {
            foreach ($query->result() as $row) {
                array_push($names, array(
                    'path' => getcwd() . '/uploads/customer_files/' . $row->file_name_new,
                    'filename' => $row->file_name_original
                ));
            }
        }

        return $names;
    }

    /**
     * Deletes an upload from the database
     * @param $url_key
     * @param $filename
     */
    public function delete($url_key, $filename)
    {
        $this->db->where('url_key', $url_key);
        $this->db->where('file_name_original', $filename);
        $this->db->delete('uploads');
    }

    /**
     * Query to get all uploads for a client by its ID
     * @param $client_id
     * @return $this
     */
    public function by_client($client_id)
    {
        $this->filter_where('uploads.client_id', $client_id);
        return $this;
    }
}
