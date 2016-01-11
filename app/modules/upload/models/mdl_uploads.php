<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Uploads
 * @package Modules\Upload\Models
 */
class Mdl_Uploads extends Response_Model
{
    public $table = 'ip_uploads';
    public $primary_key = 'ip_uploads.upload_id';
    public $date_modified_field = 'uploaded_date';

    /**
     * The default order directive used in every query
     */
    public function default_order_by()
    {
        $this->db->order_by('ip_uploads.upload_id ASC');
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
        $query = $this->db->query("Select file_name_new,file_name_original from ip_uploads where url_key = '" . $quote->quote_url_key . "'");
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
        $query = $this->db->query("Select file_name_new,file_name_original from ip_uploads where url_key = '" . $invoice->invoice_url_key . "'");

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
        $this->db->delete('ip_uploads');
    }

    /**
     * Query to get all uploads for a client by its ID
     * @param $client_id
     * @return $this
     */
    public function by_client($client_id)
    {
        $this->filter_where('ip_uploads.client_id', $client_id);
        return $this;
    }
}
