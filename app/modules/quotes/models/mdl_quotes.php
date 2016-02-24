<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Quotes
 * @package Modules\Quotes\Models
 * @property CI_DB_query_builder $db
 * @property CI_Loader $load
 * @property Mdl_Clients $mdl_clients
 * @property Mdl_Invoice_Groups $mdl_invoice_groups
 * @property Mdl_Quote_Custom $mdl_quote_custom
 * @property Mdl_Quote_Items $mdl_quote_items
 * @property Mdl_Quote_Tax_Rates $mdl_quote_tax_rates
 * 
 * @TODO Rename quote notes to quote terms
 */
class Mdl_Quotes extends Response_Model
{
    public $table = 'quotes';
    public $primary_key = 'quotes.quote_id';
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
                'href' => 'quotes/status/draft'
            ),
            '2' => array(
                'label' => lang('sent'),
                'class' => 'sent',
                'href' => 'quotes/status/sent'
            ),
            '3' => array(
                'label' => lang('viewed'),
                'class' => 'viewed',
                'href' => 'quotes/status/viewed'
            ),
            '4' => array(
                'label' => lang('approved'),
                'class' => 'approved',
                'href' => 'quotes/status/approved'
            ),
            '5' => array(
                'label' => lang('rejected'),
                'class' => 'rejected',
                'href' => 'quotes/status/rejected'
            ),
            '6' => array(
                'label' => lang('canceled'),
                'class' => 'canceled',
                'href' => 'quotes/status/canceled'
            )
        );
    }

    /**
     * The default select directive used in every query
     */
    public function default_select()
    {
        $this->db->select("
            SQL_CALC_FOUND_ROWS custom_quote.*,
            clients.*,
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
			quote_amounts.quote_amount_id,
			IFNULL(quote_amounts.item_subtotal, '0.00') AS item_subtotal,
			IFNULL(quote_amounts.item_tax_total, '0.00') AS item_tax_total,
			IFNULL(quote_amounts.tax_total, '0.00') AS tax_total,
			IFNULL(quote_amounts.total, '0.00') AS total,
            invoices.invoice_number,
			quotes.*", false);
    }

    /**
     * The default order by directive used in every query
     */
    public function default_order_by()
    {
        $this->db->order_by('quotes.id DESC');
    }

    /**
     * The default join directive used in every query
     */
    public function default_join()
    {
        $this->db->join('clients', 'clients.id = quotes.client_id');
        $this->db->join('users', 'users.id = quotes.user_id');
        $this->db->join('quote_amounts', 'quote_amounts.id = quotes.quote_id', 'left');
        $this->db->join('invoices', 'invoices.id = quotes.invoice_id', 'left');
        $this->db->join('custom_client', 'client_custom.client_id = clients.client_id', 'left');
        $this->db->join('custom_user', 'user_custom.user_id = users.user_id', 'left');
        $this->db->join('custom_quote', 'quote_custom.quote_id = quotes.quote_id', 'left');
    }

    /**
     * Returns the validation rules for quotes
     * @return array
     */
    public function validation_rules()
    {
        return array(
            'user_id' => array(
                'field' => 'user_id',
                'label' => lang('user'),
                'rule' => 'required',
            ),
            'client_name' => array(
                'field' => 'client_name',
                'label' => lang('client'),
                'rules' => 'required',
            ),
            'date_created' => array(
                'field' => 'date_created',
                'label' => lang('quote_date'),
                'rules' => 'required',
            ),
            'invoice_group_id' => array(
                'field' => 'invoice_group_id',
                'label' => lang('quote_group'),
                'rules' => 'required',
            ),
            'password' => array(
                'field' => 'password',
                'label' => lang('quote_password'),
            ),
        );
    }

    /**
     * Returns the validation rules for quotes which already exist
     * @return array
     */
    public function validation_rules_save_quote()
    {
        return array(
            'quote_number' => array(
                'field' => 'quote_number',
                'label' => lang('quote') . ' #',
                'rules' => 'required|is_unique[quotes.quote_number' . (($this->id) ? '.id.' . $this->id : '') . ']',
            ),
            'date_created' => array(
                'field' => 'date_created',
                'label' => lang('date'),
                'rules' => 'required',
            ),
            'date_expires' => array(
                'field' => 'date_expires',
                'label' => lang('due_date'),
                'rules' => 'required',
            ),
            'password' => array(
                'field' => 'password',
                'label' => lang('quote_password'),
            )
        );
    }

    /**
     * Creates a quote
     * @param array $db_array
     * @return int
     */
    public function create($db_array = null)
    {
        $quote_id = parent::save(null, $db_array);

        // Create an quote amount record
        $db_array = array(
            'quote_id' => $quote_id
        );

        $this->db->insert('quote_amounts', $db_array);

        // Create the default invoice tax record if applicable
        if ($this->mdl_settings->setting('default_invoice_tax_rate')) {
            $db_array = array(
                'quote_id' => $quote_id,
                'tax_rate_id' => $this->mdl_settings->setting('default_invoice_tax_rate'),
                'include_item_tax' => $this->mdl_settings->setting('default_include_item_tax'),
                'tax_rate_amount' => 0
            );

            $this->db->insert('quote_tax_rates', $db_array);
        }

        return $quote_id;
    }

    /**
     * Returns a random string for the URL key
     * @TODO duplicate as of Mdl_Invoices::get_url_key()
     * @return string
     */
    public function get_url_key()
    {
        $this->load->helper('string');
        return random_string('alnum', 15);
    }

    /**
     * Copies quote items, tax rates, etc from source to target
     * @param int $source_id
     * @param int $target_id
     */
    public function copy_quote($source_id, $target_id)
    {
        $this->load->model('quotes/mdl_quote_items');

        $quote_items = $this->mdl_quote_items->where('quote_id', $source_id)->get()->result();

        foreach ($quote_items as $quote_item) {
            $db_array = array(
                'quote_id' => $target_id,
                'tax_rate_id' => $quote_item->tax_rate_id,
                'name' => $quote_item->name,
                'description' => $quote_item->description,
                'quantity' => $quote_item->quantity,
                'price' => $quote_item->price,
                'item_order' => $quote_item->item_order
            );

            $this->mdl_quote_items->save($target_id, null, $db_array);
        }

        $quote_tax_rates = $this->mdl_quote_tax_rates->where('quote_id', $source_id)->get()->result();

        foreach ($quote_tax_rates as $quote_tax_rate) {
            $db_array = array(
                'quote_id' => $target_id,
                'tax_rate_id' => $quote_tax_rate->tax_rate_id,
                'include_item_tax' => $quote_tax_rate->include_item_tax,
                'amount' => $quote_tax_rate->amount
            );

            $this->mdl_quote_tax_rates->save($target_id, null, $db_array);
        }

        $this->load->model('custom_fields/mdl_quote_custom');
        $db_array = $this->mdl_quote_custom->where('quote_id', $source_id)->get()->row_array();
        if (count($db_array) > 2) {
            unset($db_array['quote_custom_id']);
            $db_array['quote_id'] = $target_id;
            $this->mdl_quote_custom->save_custom($target_id, $db_array);
        }
    }

    /**
     * Returns the prepared database array
     * @return array
     */
    public function db_array()
    {
        $db_array = parent::db_array();

        // Get the client id for the submitted quote
        $this->load->model('clients/mdl_clients');
        $db_array['client_id'] = $this->mdl_clients->client_lookup($db_array['client_name']);
        unset($db_array['client_name']);

        $db_array['date_created'] = date_to_mysql($db_array['quote_date_created']);
        $db_array['date_expires'] = $this->get_date_due($db_array['quote_date_created']);
        $db_array['quote_number'] = $this->get_quote_number($db_array['invoice_group_id']);
        $db_array['notes'] = $this->mdl_settings->setting('default_quote_notes');

        if (!isset($db_array['status_id'])) {
            $db_array['status_id'] = 1;
        }

        // Generate the unique url key
        $db_array['quote_url_key'] = $this->get_url_key();

        return $db_array;
    }

    /**
     * Returns the generated invoice number based on the invoice group ID
     * @see Mdl_Invoice_Groups::generate_invoice_number()
     * @param $invoice_group_id
     * @return mixed
     */
    public function get_quote_number($invoice_group_id)
    {
        $this->load->model('invoice_groups/mdl_invoice_groups');
        return $this->mdl_invoice_groups->generate_invoice_number($invoice_group_id);
    }

    /**
     * Returns the calculated dua date based on the date created
     * @param $quote_date_created
     * @return string
     */
    public function get_date_due($quote_date_created)
    {
        $quote_date_expires = new DateTime($quote_date_created);
        $quote_date_expires->add(new DateInterval('P' . $this->mdl_settings->setting('quotes_expire_after') . 'D'));
        return $quote_date_expires->format('Y-m-d');
    }

    /**
     * Deletes a quote from the database and all orphaned entries
     * @param $quote_id
     */
    public function delete($quote_id)
    {
        parent::delete($quote_id);

        $this->load->helper('orphan');
        delete_orphans();
    }

    /**
     * Query to get the quotes that are drafts
     * @return $this
     */
    public function is_draft()
    {
        $this->filter_where('status_id', 1);
        return $this;
    }

    /**
     * Query to get the quotes that were sent
     * @return $this
     */
    public function is_sent()
    {
        $this->filter_where('status_id', 2);
        return $this;
    }

    /**
     * Query to get the quotes that were viewed
     * @return $this
     */
    public function is_viewed()
    {
        $this->filter_where('status_id', 3);
        return $this;
    }

    /**
     * Query to get the quotes that were approved
     * @return $this
     */
    public function is_approved()
    {
        $this->filter_where('status_id', 4);
        return $this;
    }

    /**
     * Query to get the quotes that were rejected
     * @return $this
     */
    public function is_rejected()
    {
        $this->filter_where('status_id', 5);
        return $this;
    }

    /**
     * Query to get the quotes that were cancelled
     * @return $this
     */
    public function is_canceled()
    {
        $this->filter_where('status_id', 6);
        return $this;
    }

    /**
     * Query to get the quotes that are open
     * @return $this
     */
    public function is_open()
    {
        $this->filter_where_in('status_id', array(2, 3));
        return $this;
    }

    /**
     * Query to get the quotes that are visible ot the guest
     * @return $this
     */
    public function guest_visible()
    {
        $this->filter_where_in('status_id', array(2, 3, 4, 5));
        return $this;
    }

    /**
     * Query to get the quotes by client
     * @param $client_id
     * @return $this
     */
    public function by_client($client_id)
    {
        $this->filter_where('quotes.client_id', $client_id);
        return $this;
    }

    /**
     * Approves a quote by its URL key
     * @param $url_key
     */
    public function approve_quote_by_key($url_key)
    {
        $this->db->where_in('status_id', array(2, 3));
        $this->db->where('url_key', $url_key);
        $this->db->set('status_id', 4);
        $this->db->update('quotes');
    }

    /**
     * Rejects a quote by its URL key
     * @param $url_key
     */
    public function reject_quote_by_key($url_key)
    {
        $this->db->where_in('status_id', array(2, 3));
        $this->db->where('url_key', $url_key);
        $this->db->set('status_id', 5);
        $this->db->update('quotes');
    }

    /**
     * Approves a quote by its ID
     * @param $quote_id
     */
    public function approve_quote_by_id($quote_id)
    {
        $this->db->where_in('status_id', array(2, 3));
        $this->db->where('id', $quote_id);
        $this->db->set('status_id', 4);
        $this->db->update('quotes');
    }

    /**
     * Rejects a quote by its ID
     * @param $quote_id
     */
    public function reject_quote_by_id($quote_id)
    {
        $this->db->where_in('status_id', array(2, 3));
        $this->db->where('id', $quote_id);
        $this->db->set('status_id', 5);
        $this->db->update('quotes');
    }

    /**
     * Marks a quote as viewed
     * @param $quote_id
     */
    public function mark_viewed($quote_id)
    {
        $this->db->select('status_id');
        $this->db->where('id', $quote_id);

        $quote = $this->db->get('quotes');

        if ($quote->num_rows()) {
            if ($quote->row()->status_id == 2) {
                $this->db->where('id', $quote_id);
                $this->db->set('status_id', 3);
                $this->db->update('quotes');
            }
        }
    }

    /**
     * Marks a quote as sent
     * @param $quote_id
     */
    public function mark_sent($quote_id)
    {
        $this->db->select('status_id');
        $this->db->where('id', $quote_id);

        $quote = $this->db->get('quotes');

        if ($quote->num_rows()) {
            if ($quote->row()->status_id == 1) {
                $this->db->where('id', $quote_id);
                $this->db->set('status_id', 2);
                $this->db->update('quotes');
            }
        }
    }
}
