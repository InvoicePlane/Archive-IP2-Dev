<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Import
 * @package Modules\Import\Models
 *
 * @property CI_DB_query_builder $db
 * @property Mdl_Invoices $mdl_invoices
 * @property Mdl_Items $mdl_items
 * @property Mdl_Payments $mdl_payments
 */
class Mdl_Import extends Response_Model
{
    public $table = 'imports';
    public $primary_key = 'imports.id';

    public $expected_headers = array(
        'clients.csv' => array(
            'name',
            'address_1',
            'address_2',
            'city',
            'state',
            'zip',
            'country',
            'phone',
            'fax',
            'mobile',
            'email',
            'web',
            'vat_id',
            'tax_code',
            'is_active'
        ),
        'invoices.csv' => array(
            'user_email',
            'client_name',
            'invoice_number',
            'invoice_date_due',
            'discount_amount',
            'discount_percent',
            'terms',
            'date_created',
        ),
        'invoice_items.csv' => array(
            'invoice_number',
            'tax_rate',
            'name',
            'description',
            'quantity',
            'price',
            'discount_amount',
            'item_date_added',
        ),
        'payments.csv' => array(
            'invoice_number',
            'payment_method',
            'amount',
            'note',
            'payment_date',
        )
    );

    public $primary_keys = array(
        'clients' => 'id',
        'invoices' => 'id',
        'invoice_items' => 'id',
        'payments' => 'id'
    );

    /**
     * Mdl_Import constructor.
     */
    public function __construct()
    {
        // Provides better line ending detection
        ini_set("auto_detect_line_endings", true);
    }

    /**
     * The default select directive used in every query
     */
    public function default_select()
    {
        $this->db->select("
            SQL_CALC_FOUND_ROWS imports.*,
            (
                SELECT COUNT(*)
                FROM import_details
                WHERE table_name = 'clients'
                    AND import_details.id = imports.id
            ) AS num_clients,
            (
                SELECT COUNT(*)
                FROM import_details
                WHERE table_name = 'invoices'
                    AND import_details.id = imports.id
            ) AS num_invoices,
            (
                SELECT COUNT(*)
                FROM import_details
                WHERE table_name = 'invoice_items'
                    AND import_details.id = imports.id
            ) AS num_invoice_items,
            (
                SELECT COUNT(*)
                FROM import_details
                WHERE table_name = 'payments'
                    AND import_details.id = imports.id
            ) AS num_payments",
            false);
    }

    /**
     * The default order by directive used in every query
     */
    public function default_order_by()
    {
        $this->db->order_by('imports.import_date DESC');
    }

    /**
     * Saves an import to the database
     * @return int
     */
    public function start_import()
    {
        $db_array = array(
            'import_date' => date('Y-m-d H:i:s')
        );

        $this->db->insert('imports', $db_array);

        return $this->db->insert_id();
    }

    /**
     * Imports data for a specified file and table
     * @param $file
     * @param $table
     * @return array|bool
     */
    public function import_data($file, $table)
    {
        // Open the file
        $handle = fopen('./uploads/import/' . $file, 'r');

        $row = 1;
        $fileheaders = array();

        // Get the expected file headers
        $headers = $this->expected_headers[$file];

        // Init an array to store the inserted ids
        $ids = array();

        while (($data = fgetcsv($handle, 1000, ",")) <> false) {
            // Check to make sure the file headers match the expected headers
            if ($row == 1) {
                foreach ($headers as $header) {
                    if (!in_array($header, $data)) {
                        return false;
                    }
                }
                $fileheaders = $data;
            } elseif ($row > 1) {
                // Init the array
                $db_array = array();
                // Loop through each of the values in the row
                foreach ($headers as $key => $header) {
                    $db_array[$header] = ($data[array_keys($fileheaders,
                            $header)[0]] <> 'NULL') ? $data[array_keys($fileheaders, $header)[0]] : '';
                }

                // Insert the record
                $this->db->insert($table, $db_array);

                // Record the inserted id
                $ids[] = $this->db->insert_id();
            }

            $row++;
        }

        // Return the array of recorded ids
        return $ids;
    }

    /**
     * Function to import invoices
     * @uses file uploads/import/invoices.csv
     * @return array|bool
     */
    public function import_invoices()
    {
        // Open the file
        $handle = fopen('./uploads/import/invoices.csv', 'r');

        $row = 1;

        // Get the list of expected headers
        $headers = $this->expected_headers['invoices.csv'];

        // Init an array to store the inserted ids
        $ids = array();

        while (($data = fgetcsv($handle, 1000, ",")) <> false) {
            // Init $record_error as false
            $record_error = false;

            // Check to make sure the file headers match expected headers
            if ($row == 1 and $data <> $headers) {
                return false;
            } elseif ($row > 1) {
                // Init the array
                $db_array = array();

                // Loop through each of the values in the row
                foreach ($headers as $key => $header) {
                    if ($header == 'user_email') {
                        // Attempt to replace email address with user id
                        $this->db->where('email', $data[$key]);
                        $user = $this->db->get('users');

                        if ($user->num_rows()) {
                            $header = 'user_id';
                            $data[$key] = $user->row()->user_id;
                        } else {
                            // Email address not found
                            $record_error = true;
                        }
                    } elseif ($header == 'client_name') {
                        // Replace client name with client id
                        $header = 'client_id';
                        $this->db->where('name', $data[$key]);
                        $client = $this->db->get('clients');

                        if ($client->num_rows()) {
                            // Existing client found
                            $data[$key] = $client->row()->client_id;
                        } else {
                            // Existing client not found - create new client
                            $client_db_array = array(
                                'name' => $data[$key],
                            );

                            $this->db->insert('clients', $client_db_array);
                            $data[$key] = $this->db->insert_id();
                        }
                    }
                    // Each invoice needs a url key
                    $db_array['url_key'] = $this->mdl_invoices->get_url_key();

                    // Assign the final value to the array
                    $db_array[$header] = ($data[$key] <> 'NULL') ? $data[$key] : '';
                }

                // Check for any record errors
                if (!$record_error) {
                    // No record errors exist - go ahead and create the invoice
                    $db_array['invoice_group_id'] = 0;
                    $ids[] = $this->mdl_invoices->create($db_array);
                }
            }

            $row++;
        }

        // Return the array of recorded ids
        return $ids;
    }

    /**
     * Function to import invoice items
     * @uses file uploads/import/invoice_items.csv
     * @return array|bool
     */
    public function import_invoice_items()
    {
        // Open the file
        $handle = fopen('./uploads/import/invoice_items.csv', 'r');

        $row = 1;

        // Get the list of expected headers
        $headers = $this->expected_headers['invoice_items.csv'];

        // Init an array to store the inserted ids
        $ids = array();

        while (($data = fgetcsv($handle, 1000, ",")) <> false) {
            // Init record_error as false
            $record_error = false;

            // Check to make sure the file headers match expected headers
            if ($row == 1 and $data <> $headers) {
                return false;
            } elseif ($row > 1) {
                // Init the array
                $db_array = array();

                foreach ($headers as $key => $header) {
                    if ($header == 'invoice_number') {
                        // Replace invoice_number with invoice_id
                        $this->db->where('invoice_number', $data[$key]);
                        $invoices = $this->db->get('invoices');

                        if ($invoices->num_rows()) {
                            $header = 'invoice_id';
                            $data[$key] = $invoices->row()->id;
                        } else {
                            $record_error = true;
                        }
                    } elseif ($header == 'tax_rate') {
                        // Replace item_tax_rate with item_tax_rate_id
                        $header = 'tax_rate_id';
                        if ($data[$key] > 0) {
                            $this->db->where('tax_rate_percent', $data[$key]);
                            $tax_rate = $this->db->get('tax_rates');

                            if ($tax_rate->num_rows()) {
                                $data[$key] = $tax_rate->row()->tax_rate_id;
                            } else {
                                $this->db->insert('tax_rates', array(
                                        'name' => $data[$key],
                                        'percent' => $data[$key],
                                    )
                                );
                                $data[$key] = $this->db->insert_id();
                            }
                        } else {
                            $data[$key] = 0;
                        }
                    }

                    // Assign the final value to the array
                    $db_array[$header] = ($data[$key] <> 'NULL') ? $data[$key] : '';
                }

                if (!$record_error) {
                    // No errors, go ahead and create the record
                    $ids[] = $this->mdl_items->save($db_array['invoice_id'], null, $db_array);
                }
            }

            $row++;
        }

        return $ids;
    }

    /**
     * Function to import payments
     * @uses file uploads/import/payments.csv
     * @return array|bool
     */
    public function import_payments()
    {
        $handle = fopen('./uploads/import/payments.csv', 'r');

        $row = 1;

        $headers = $this->expected_headers['payments.csv'];

        $ids = array();

        while (($data = fgetcsv($handle, 1000, ",")) <> false) {
            $record_error = false;

            if ($row == 1 and $data <> $headers) {
                return false;
            } elseif ($row > 1) {
                $db_array = array();

                foreach ($headers as $key => $header) {
                    if ($header == 'invoice_number') {
                        $this->db->where('invoice_number', $data[$key]);
                        $invoices = $this->db->get('invoices');

                        if ($invoices->num_rows()) {
                            $header = 'invoice_id';
                            $data[$key] = $invoices->row()->id;
                        } else {
                            $record_error = true;
                        }
                    } elseif ($header == 'payment_method') {
                        $header = 'payment_method_id';

                        if ($data[$key]) {
                            $this->db->where('payment_method_name', $data[$key]);
                            $payment_method = $this->db->get('payment_methods');

                            if ($payment_method->num_rows()) {
                                $data[$key] = $payment_method->row()->id;
                            } else {
                                $this->db->insert('payment_methods', array('name' => $data[$key]));
                                $data[$key] = $this->db->insert_id();
                            }
                        } else {
                            // No payment method provided
                            $data[$key] = 0;
                        }
                    }

                    $db_array[$header] = ($data[$key] <> 'NULL') ? $data[$key] : '';
                }

                if (!$record_error) {
                    $ids[] = $this->mdl_payments->save(null, $db_array);
                }
            }

            $row++;
        }

        return $ids;
    }

    /**
     * Saves information about the import process to the database
     * @param $import_id
     * @param $table_name
     * @param $import_lang_key
     * @param $ids
     */
    public function record_import_details($import_id, $table_name, $import_lang_key, $ids)
    {
        foreach ($ids as $id) {
            $db_array = array(
                'import_id' => $import_id,
                'lang_key' => $import_lang_key,
                'table_name' => $table_name,
                'record_id' => $id
            );

            $this->db->insert('import_details', $db_array);
        }
    }

    /**
     * Deletes an import process from the database based on the given ID
     * @param $import_id
     */
    public function delete($import_id)
    {
        // Gather the import details
        $import_details = $this->db->where('id', $import_id)->get('import_details')->result();

        // Loop through details and delete each of the imported records
        foreach ($import_details as $import_detail) {
            $this->db->query("DELETE FROM " . $import_detail->table_name . " WHERE " . $this->primary_keys[$import_detail->table_name] . ' = ' . $import_detail->record_id);
        }

        // Delete the master import record
        parent::delete($import_id);

        // Delete the detail records
        $this->db->where('id', $import_id);
        $this->db->delete('import_details');

        // Delete any orphaned records
        $this->load->helper('orphan');
        delete_orphans();
    }
}
