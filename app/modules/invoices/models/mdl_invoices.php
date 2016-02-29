<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Invoices
 * @package Modules\Invoices\Models
 *
 * @property CI_DB_query_builder $db
 * @property CI_Loader $load
 * @property Mdl_Clients $mdl_clients
 * @property Mdl_Invoice_Custom $mdl_invoice_custom
 * @property Mdl_Invoice_Groups $mdl_invoice_groups
 * @property Mdl_Invoice_Tax_Rates $mdl_invoice_tax_rates
 * @property Mdl_Items $mdl_items
 */
class Mdl_Invoices extends Response_Model
{
    public $table = 'invoices';
    public $primary_key = 'invoices.id';
    public $date_created_field = 'date_created';
    public $date_modified_field = 'date_modified';

    /**
     * Returns an array that holds all available status codes with
     * their label, class and href
     * @return array
     */
    public function statuses()
    {
        return array(
            '1' => array(
                'label' => lang('draft'),
                'class' => 'draft',
                'href' => 'invoices/status/draft'
            ),
            '2' => array(
                'label' => lang('sent'),
                'class' => 'sent',
                'href' => 'invoices/status/sent'
            ),
            '3' => array(
                'label' => lang('viewed'),
                'class' => 'viewed',
                'href' => 'invoices/status/viewed'
            ),
            '4' => array(
                'label' => lang('paid'),
                'class' => 'paid',
                'href' => 'invoices/status/paid'
            )
        );
    }

    /**
     * The default select directive used in every query
     */
    public function default_select()
    {
        $this->db->select("
            SQL_CALC_FOUND_ROWS custom_invoice.*,
            custom_client.*,
            custom_user.*,
            users.name,
			users.company,
			users.address_1,
			users.address_2,
			users.city,
			users.state,
			users.zip,
			users.country,
			users.phone,
			users.fax,
			users.mobile,
			users.email,
			users.web,
			users.vat_id,
			users.tax_code,
			clients.*,
			invoice_amounts.id,
			IFNULL(invoice_amounts.item_subtotal, '0.00') AS item_subtotal,
			IFNULL(invoice_amounts.tax_total, '0.00') AS tax_total,
			IFNULL(invoice_amounts.tax_total, '0.00') AS tax_total,
			IFNULL(invoice_amounts.total, '0.00') AS total,
			IFNULL(invoice_amounts.paid, '0.00') AS paid,
			IFNULL(invoice_amounts.balance, '0.00') AS balance,
			invoice_amounts.sign AS sign,
            (CASE WHEN invoices.status_id NOT IN (1,4) AND DATEDIFF(NOW(), date_due) > 0 THEN 1 ELSE 0 END) is_overdue,
			DATEDIFF(NOW(), date_due) AS days_overdue,
            (CASE (SELECT COUNT(*) FROM invoices_recurring WHERE invoices_recurring.invoice_id = invoices.id and invoices_recurring.next_date <> '0000-00-00') WHEN 0 THEN 0 ELSE 1 END) AS invoice_is_recurring,
			invoices.*", false);
    }

    /**
     * The default order directive used in every query
     */
    public function default_order_by()
    {
        $this->db->order_by('invoices.id DESC');
    }

    /**
     * The default join directive used in every query
     */
    public function default_join()
    {
        $this->db->join('clients', 'clients.id = invoices.client_id');
        $this->db->join('users', 'users.id = invoices.user_id');
        $this->db->join('invoice_amounts', 'invoice_amounts.invoice_id = invoices.id', 'left');
        $this->db->join('custom_client', 'custom_client.client_id = clients.id', 'left');
        $this->db->join('custom_user', 'custom_user.user_id = users.id', 'left');
        $this->db->join('custom_invoice', 'custom_invoice.invoice_id = invoices.id', 'left');
    }

    /**
     * Returns the validation rules for invoices
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'client_name' => array(
                'field' => 'client_name',
                'label' => lang('client'),
                'rules' => 'required'
            ),
            'date_created' => array(
                'field' => 'date_created',
                'label' => lang('invoice_date'),
                'rules' => 'required'
            ),
            'invoice_time_created' => array(
                'rules' => 'required'
            ),
            'invoice_group_id' => array(
                'field' => 'invoice_group_id',
                'label' => lang('invoice_group'),
                'rules' => 'required'
            ),
            'invoice_password' => array(
                'field' => 'invoice_password',
                'label' => lang('invoice_password')
            ),
            'user_id' => array(
                'field' => 'user_id',
                'label' => lang('user'),
                'rule' => 'required'
            ),
            'payment_method' => array(
                'field' => 'payment_method',
                'label' => lang('payment_method')
            ),
        );
    }

    /**
     * Returns the validation rules for invoices which already exist
     * @return array
     */
    public function validation_rules_save_invoice()
    {
        return array(
            'invoice_number' => array(
                'field' => 'invoice_number',
                'label' => lang('invoice') . ' #',
                'rules' => 'required|is_unique[invoices.invoice_number' . (($this->id) ? '.invoice_id.' . $this->id : '') . ']'
            ),
            'date_created' => array(
                'field' => 'date_created',
                'label' => lang('date'),
                'rules' => 'required'
            ),
            'date_due' => array(
                'field' => 'date_due',
                'label' => lang('due_date'),
                'rules' => 'required'
            ),
            'invoice_time_created' => array(
                'rules' => 'required'
            ),
            'invoice_password' => array(
                'field' => 'invoice_password',
                'label' => lang('invoice_password')
            )
        );
    }

    /**
     * Creates an invoice
     * @param null $db_array
     * @param bool $include_invoice_tax_rates
     * @return int
     */
    public function create($db_array = null, $include_invoice_tax_rates = true)
    {
        $invoice_id = parent::save(null, $db_array);

        // Create an invoice amount record
        $db_array = array(
            'invoice_id' => $invoice_id
        );

        $this->db->insert('invoice_amounts', $db_array);

        if ($include_invoice_tax_rates) {
            // Create the default invoice tax record if applicable
            if ($this->mdl_settings->setting('default_invoice_tax_rate')) {
                $db_array = array(
                    'invoice_id' => $invoice_id,
                    'tax_rate_id' => $this->mdl_settings->setting('default_invoice_tax_rate'),
                    'include_tax' => $this->mdl_settings->setting('default_include_tax'),
                    'amount' => 0
                );

                $this->db->insert('invoice_tax_rates', $db_array);
            }
        }

        return $invoice_id;
    }

    /**
     * Returns a random string for the URL key
     * @return string
     */
    public function get_url_key()
    {
        $this->load->helper('string');
        return random_string('alnum', 15);
    }

    /**
     * Copies invoice items, tax rates, etc from source to target
     * @param int $source_id
     * @param int $target_id
     */
    public function copy_invoice($source_id, $target_id)
    {
        $this->load->model('invoices/mdl_items');

        $invoice_items = $this->mdl_items->where('invoice_id', $source_id)->get()->result();

        foreach ($invoice_items as $invoice_item) {
            $db_array = array(
                'invoice_id' => $target_id,
                'tax_rate_id' => $invoice_item->tax_rate_id,
                'name' => $invoice_item->name,
                'description' => $invoice_item->description,
                'quantity' => $invoice_item->quantity,
                'price' => $invoice_item->price,
                'order' => $invoice_item->order
            );

            $this->mdl_items->save($target_id, null, $db_array);
        }

        $invoice_tax_rates = $this->mdl_invoice_tax_rates->where('invoice_id', $source_id)->get()->result();

        foreach ($invoice_tax_rates as $invoice_tax_rate) {
            $db_array = array(
                'invoice_id' => $target_id,
                'tax_rate_id' => $invoice_tax_rate->tax_rate_id,
                'include_tax' => $invoice_tax_rate->include_tax,
                'amount' => $invoice_tax_rate->amount
            );

            $this->mdl_invoice_tax_rates->save($target_id, null, $db_array);
        }

        $this->load->model('custom_fields/mdl_invoice_custom');
        $db_array = $this->mdl_invoice_custom->where('invoice_id', $source_id)->get()->row_array();
        if (count($db_array) > 2) {
            unset($db_array['invoice_custom_id']);
            $db_array['invoice_id'] = $target_id;
            $this->mdl_invoice_custom->save_custom($target_id, $db_array);
        }
    }

    /**
     * Copies invoice items, tax rates, etc from source to target
     * @param int $source_id
     * @param int $target_id
     */
    public function copy_credit_invoice($source_id, $target_id)
    {
        $this->load->model('invoices/mdl_items');

        $invoice_items = $this->mdl_items->where('invoice_id', $source_id)->get()->result();

        foreach ($invoice_items as $invoice_item) {
            $db_array = array(
                'invoice_id' => $target_id,
                'tax_rate_id' => $invoice_item->tax_rate_id,
                'name' => $invoice_item->name,
                'description' => $invoice_item->description,
                'quantity' => -$invoice_item->quantity,
                'price' => $invoice_item->price,
                'order' => $invoice_item->order
            );

            $this->mdl_items->save($target_id, null, $db_array);
        }

        $invoice_tax_rates = $this->mdl_invoice_tax_rates->where('invoice_id', $source_id)->get()->result();

        foreach ($invoice_tax_rates as $invoice_tax_rate) {
            $db_array = array(
                'invoice_id' => $target_id,
                'tax_rate_id' => $invoice_tax_rate->tax_rate_id,
                'include_tax' => $invoice_tax_rate->include_tax,
                'amount' => -$invoice_tax_rate->amount
            );

            $this->mdl_invoice_tax_rates->save($target_id, null, $db_array);
        }

        $this->load->model('custom_fields/mdl_invoice_custom');
        $db_array = $this->mdl_invoice_custom->where('invoice_id', $source_id)->get()->row_array();
        if (count($db_array) > 2) {
            unset($db_array['invoice_custom_id']);
            $db_array['invoice_id'] = $target_id;
            $this->mdl_invoice_custom->save_custom($target_id, $db_array);
        }
    }

    /**
     * Returns the prepared database array
     * @return array
     */
    public function db_array()
    {
        $db_array = parent::db_array();

        // Get the client id for the submitted invoice
        $this->load->model('clients/mdl_clients');
        $db_array['client_id'] = $this->mdl_clients->client_lookup($db_array['client_name']);
        unset($db_array['client_name']);

        $db_array['date_created'] = date_to_mysql($db_array['date_created']);
        $db_array['date_due'] = $this->get_date_due($db_array['date_created']);
        $db_array['invoice_number'] = $this->get_invoice_number($db_array['invoice_group_id']);
        $db_array['terms'] = $this->mdl_settings->setting('default_terms');

        if (!isset($db_array['status_id'])) {
            $db_array['status_id'] = 1;
        }

        // Generate the unique url key
        $db_array['url_key'] = $this->get_url_key();

        return $db_array;
    }

    /**
     * Returns the generated invoice number based on the invoice group ID
     * @see Mdl_Invoice_Groups::generate_invoice_number()
     * @param $invoice_group_id
     * @return mixed
     */
    public function get_invoice_number($invoice_group_id)
    {
        $this->load->model('invoice_groups/mdl_invoice_groups');
        return $this->mdl_invoice_groups->generate_invoice_number($invoice_group_id);
    }

    /**
     * Returns the calculated dua date based on the date created
     * @param $date_created
     * @param null $invoices_due_after
     * @return string
     */
    public function get_date_due($date_created, $invoices_due_after = null)
    {
        if ($invoices_due_after == null) {
            $invoices_due_after = $this->mdl_settings->setting('invoices_due_after');
        }
        $date_due = new DateTime($date_created);
        $date_due->add(new DateInterval('P' . $invoices_due_after . 'D'));
        return $date_due->format('Y-m-d H:i:s');
    }

    /**
     * Deletes an invoice from the database and all orphaned entries
     * @param $invoice_id
     */
    public function delete($invoice_id)
    {
        parent::delete($invoice_id);

        $this->load->helper('orphan');
        delete_orphans();
    }

    /**
     * Query to get the invoices which are open
     * @return $this
     */
    public function is_open()
    {
        $this->filter_where_in('status_id', array(2, 3));
        return $this;
    }

    /**
     * Query to get the invoices which are visible to the guest
     * @return $this
     */
    public function guest_visible()
    {
        $this->filter_where_in('status_id', array(2, 3, 4));
        return $this;
    }

    /**
     * Query to get the invoices which are drafts
     * @return $this
     */
    public function is_draft()
    {
        $this->filter_where('status_id', 1);
        return $this;
    }

    /**
     * Query to get the invoices which are marked as sent
     * @return $this
     */
    public function is_sent()
    {
        $this->filter_where('status_id', 2);
        return $this;
    }

    /**
     * Query to get the invoices which are marked as viewed
     * @return $this
     */
    public function is_viewed()
    {
        $this->filter_where('status_id', 3);
        return $this;
    }

    /**
     * Query to get the invoices which are marked as paid
     * @return $this
     */
    public function is_paid()
    {
        $this->filter_where('status_id', 4);
        return $this;
    }

    /**
     * Query to get the invoices which are overdue
     * @return $this
     */
    public function is_overdue()
    {
        $this->filter_having('is_overdue', 1);
        return $this;
    }

    /**
     * Query to get the invoices by client
     * @param $client_id
     * @return $this
     */
    public function by_client($client_id)
    {
        $this->filter_where('invoices.client_id', $client_id);
        return $this;
    }

    /**
     * Marks an invoice as viewed based in the given Id
     * @param $invoice_id
     */
    public function mark_viewed($invoice_id)
    {
        $this->db->select('status_id');
        $this->db->where('invoice_id', $invoice_id);

        $invoice = $this->db->get('invoices');

        if ($invoice->num_rows()) {
            if ($invoice->row()->status_id == 2) {
                $this->db->where('id', $invoice_id);
                $this->db->set('status_id', 3);
                $this->db->update('invoices');
            }

            // Set the invoice to read-only if feature is not disabled and setting is view
            if ($this->config->item('disable_read_only') == false && $this->mdl_settings->setting('read_only_toggle') == 'viewed') {
                $this->db->where('id', $invoice_id);
                $this->db->set('is_read_only', 1);
                $this->db->update('invoices');
            }
        }
    }

    /**
     * Marks an invoice as sent based in the given Id
     * @param $invoice_id
     */
    public function mark_sent($invoice_id)
    {
        $this->db->select('status_id');
        $this->db->where('invoice_id', $invoice_id);

        $invoice = $this->db->get('invoices');

        if ($invoice->num_rows()) {
            if ($invoice->row()->status_id == 1) {
                $this->db->where('id', $invoice_id);
                $this->db->set('status_id', 2);
                $this->db->update('invoices');
            }

            // Set the invoice to read-only if feature is not disabled and setting is sent
            if ($this->config->item('disable_read_only') == false && $this->mdl_settings->setting('read_only_toggle') == 'sent') {
                $this->db->where('id', $invoice_id);
                $this->db->set('is_read_only', 1);
                $this->db->update('invoices');
            }
        }
    }
}
