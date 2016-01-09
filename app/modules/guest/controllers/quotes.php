<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Quotes
 * @package Modules\Guest\Controllers
 */
class Quotes extends Guest_Controller
{
    /**
     * Quotes constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('quotes/mdl_quotes');
    }

    /**
     * Index page, redirects to 'guest/quotes/status/open'
     */
    public function index()
    {
        // Display open quotes by default
        redirect('guest/quotes/status/open');
    }

    /**
     * Returns all quotes based on the given status and page no.
     * Example: 'guest/quotes/status/open' returns all open quotes
     * @param string $status
     * @param int $page
     */
    public function status($status = 'open', $page = 0)
    {
        redirect_to_set();

        // Determine which group of quotes to load
        switch ($status) {
            case 'approved':
                $this->mdl_quotes->is_approved()->where_in('ip_quotes.client_id', $this->user_clients);
                break;
            case 'rejected':
                $this->mdl_quotes->is_rejected()->where_in('ip_quotes.client_id', $this->user_clients);
                $this->layout->set('show_invoice_column', true);
                break;
            default:
                $this->mdl_quotes->is_open()->where_in('ip_quotes.client_id', $this->user_clients);
                break;
        }

        $this->mdl_quotes->paginate(site_url('guest/quotes/status/' . $status), $page);
        $quotes = $this->mdl_quotes->result();

        $this->layout->set('quotes', $quotes);
        $this->layout->set('status', $status);
        $this->layout->buffer('content', 'guest/quotes_index');
        $this->layout->render('layout_guest');
    }

    /**
     * Returns the details page for a quote for the given ID
     * @param $quote_id
     */
    public function view($quote_id)
    {
        redirect_to_set();

        $this->load->model('quotes/mdl_quote_items');
        $this->load->model('quotes/mdl_quote_tax_rates');

        $quote = $this->mdl_quotes->guest_visible()->where('ip_quotes.quote_id',
            $quote_id)->where_in('ip_quotes.client_id', $this->user_clients)->get()->row();

        if (!$quote) {
            show_404();
        }

        $this->mdl_quotes->mark_viewed($quote->quote_id);

        $this->layout->set(
            array(
                'quote' => $quote,
                'items' => $this->mdl_quote_items->where('quote_id', $quote_id)->get()->result(),
                'quote_tax_rates' => $this->mdl_quote_tax_rates->where('quote_id', $quote_id)->get()->result(),
                'quote_id' => $quote_id
            )
        );

        $this->layout->buffer('content', 'guest/quotes_view');
        $this->layout->render('layout_guest');
    }

    /**
     * Returns the generated PDF of the quote based on the given ID
     * @param $quote_id
     * @param bool $stream
     * @param null $quote_template
     */
    public function generate_pdf($quote_id, $stream = true, $quote_template = null)
    {
        $this->load->helper('pdf');

        $this->mdl_quotes->mark_viewed($quote_id);

        $quote = $this->mdl_quotes->guest_visible()->where('ip_quotes.quote_id',
            $quote_id)->where_in('ip_quotes.client_id', $this->user_clients)->get()->row();

        if (!$quote) {
            show_404();
        } else {
            generate_quote_pdf($quote_id, $stream, $quote_template);
        }
    }

    /**
     * Marks a quote as approved
     * @param $quote_id
     */
    public function approve($quote_id)
    {
        $this->load->model('quotes/mdl_quotes');
        $this->load->helper('mailer');

        $this->mdl_quotes->approve_quote_by_id($quote_id);
        email_quote_status($quote_id, "approved");

        redirect_to('guest/quotes');
    }

    /**
     * Marks a quote as rejected
     * @param $quote_id
     */
    public function reject($quote_id)
    {
        $this->load->model('quotes/mdl_quotes');
        $this->load->helper('mailer');

        $this->mdl_quotes->reject_quote_by_id($quote_id);
        email_quote_status($quote_id, "rejected");

        redirect_to('guest/quotes');
    }
}
