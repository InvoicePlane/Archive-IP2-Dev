<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Mdl_Invoice_Amounts
 * @package Modules\Invoices\Models
 *
 * @property CI_Config $config
 * @property CI_DB_query_builder $db
 * @property CI_Loader $load
 * @property Mdl_Invoices $mdl_invoices
 * @property Mdl_Invoice_Tax_Rates $mdl_invoice_tax_rates
 */
class Mdl_Invoice_Amounts extends CI_Model
{
    /**
     * Calculates the basic amounts for the invoice for the given ID
     *  
     * **invoice_amounts**  
     * 
     * * invoice_amount_id  
     * * invoice_id  
     * * subtotal = SUM(subtotal)  
     * * tax_total = SUM(tax_total)  
     * * invoice_tax_total  
     * * total = subtotal + tax_total + invoice_tax_total  
     * * paid  
     * * balance = total - paid  
     *   
     * **IP_INVOICE_ITEM_AMOUNTS**  
     * 
     * * item_amount_id  
     * * item_id  
     * * item_tax_rate_id  
     * * subtotal = item_quantity * item_price  
     * * tax_total = subtotal * tax_rate_percent  
     * * item_total = subtotal + tax_total  
     *  
     * @param $invoice_id
     */
    public function calculate($invoice_id)
    {
        // Get the basic totals
        $query = $this->db->query("
        SELECT  SUM(subtotal) AS subtotal,
		        SUM(tax_total) AS tax_total,
		        SUM(subtotal) + SUM(tax_total) AS total,
		        SUM(discount) AS discount
		FROM invoice_item_amounts
		WHERE item_id IN (
		    SELECT item_id FROM invoice_items WHERE invoice_id = " . $this->db->escape($invoice_id) . "
		    )
        ");

        $invoice_amounts = $query->row();

        $subtotal = $invoice_amounts->subtotal - $invoice_amounts->discount;
        $invoice_subtotal = $subtotal + $invoice_amounts->tax_total;
        $total = $this->calculate_discount($invoice_id, $invoice_subtotal);

        // Get the amount already paid
        $query = $this->db->query("SELECT SUM(amount) AS paid FROM payments WHERE invoice_id = " . $this->db->escape($invoice_id));

        $paid = $query->row()->paid;

        // Create the database array and insert or update
        $db_array = array(
            'invoice_id' => $invoice_id,
            'sign' => $subtotal,
            'subtotal' => $subtotal,
            'tax_total' => $invoice_amounts->tax_total,
            'total' => $total,
            'paid' => ($paid) ? $paid : 0,
            'balance' => $total - $paid
        );

        $this->db->where('invoice_id', $invoice_id);
        
        if ($this->db->get('invoice_amounts')->num_rows()) {
            // The record already exists; update it
            $this->db->where('invoice_id', $invoice_id);
            $this->db->update('invoice_amounts', $db_array);
        } else {
            // The record does not yet exist; insert it
            $this->db->insert('invoice_amounts', $db_array);
        }

        // Calculate the invoice taxes
        $this->calculate_invoice_taxes($invoice_id);

        // Set to paid if applicable
        if ($db_array['balance'] == 0) {

            // Get the payment method id first
            $this->db->where('invoice_id', $invoice_id);
            $payment = $this->db->get('payments')->row();
            $payment_method_id = (isset($payment->payment_method_id) ? $payment->payment_method_id : 0);

            $this->db->where('invoice_id', $invoice_id);
            $this->db->set('status_id', 4);
            $this->db->set('payment_method', $payment_method_id);
            $this->db->update('invoices');
        }
        if ($this->config->item('disable_read_only') == false && $db_array['balance'] == 0 && $db_array['total'] != 0) {
            $this->db->where('invoice_id', $invoice_id);
            $this->db->set('is_read_only', 1);
            $this->db->update('invoices');
        }
    }

    /**
     * Calculates the taxes for the invoice for the given ID
     * @param $invoice_id
     */
    public function calculate_invoice_taxes($invoice_id)
    {
        // First check to see if there are any invoice taxes applied
        $this->load->model('invoices/mdl_invoice_tax_rates');
        $invoice_tax_rates = $this->mdl_invoice_tax_rates->where('invoice_id', $invoice_id)->get()->result();

        if ($invoice_tax_rates) {
            // There are invoice taxes applied
            // Get the current invoice amount record
            $invoice_amount = $this->db->where('invoice_id', $invoice_id)->get('invoice_amounts')->row();

            // Loop through the invoice taxes and update the amount for each of the applied invoice taxes
            foreach ($invoice_tax_rates as $invoice_tax_rate) {
                if ($invoice_tax_rate->include_item_tax) {
                    // The invoice tax rate should include the applied item tax
                    $invoice_tax_rate_amount = ($invoice_amount->subtotal + $invoice_amount->tax_total) * ($invoice_tax_rate->amount / 100);
                } else {
                    // The invoice tax rate should not include the applied item tax
                    $invoice_tax_rate_amount = $invoice_amount->subtotal * ($invoice_tax_rate->amount / 100);
                }

                // Update the invoice tax rate record
                $db_array = array(
                    'amount' => $invoice_tax_rate_amount
                );
                $this->db->where('id', $invoice_tax_rate->id);
                $this->db->update('invoice_tax_rates', $db_array);
            }

            // Update the invoice amount record with the total invoice tax amount
            $this->db->query("UPDATE invoice_amounts SET tax_total = (SELECT SUM(amount) FROM invoice_tax_rates WHERE invoice_id = " . $this->db->escape($invoice_id) . ") WHERE invoice_id = " . $this->db->escape($invoice_id));

            // Get the updated invoice amount record
            $invoice_amount = $this->db->where('invoice_id', $invoice_id)->get('invoice_amounts')->row();

            // Recalculate the invoice total and balance
            $total = $invoice_amount->subtotal + $invoice_amount->tax_total + $invoice_amount->tax_total;
            $total = $this->calculate_discount($invoice_id, $total);
            $balance = $total - $invoice_amount->paid;

            // Update the invoice amount record
            $db_array = array(
                'total' => $total,
                'balance' => $balance
            );

            $this->db->where('invoice_id', $invoice_id);
            $this->db->update('invoice_amounts', $db_array);

            // Set to paid if applicable
            if ($balance <= 0) {
                $this->db->where('id', $invoice_id);
                $this->db->set('status_id', 4);
                $this->db->update('invoices');
            }
            if ($this->config->item('disable_read_only') == false && $balance == 0 && $total != 0) {
                $this->db->where('id', $invoice_id);
                $this->db->set('is_read_only', 1);
                $this->db->update('invoices');
            }
        } else {
            // No invoice taxes applied

            $db_array = array(
                'invoice_tax_total' => '0.00'
            );

            $this->db->where('invoice_id', $invoice_id);
            $this->db->update('invoice_amounts', $db_array);
        }
    }

    /**
     * Calculates the discounts for the invoice for the given ID
     * @param $invoice_id
     * @param $total
     * @return float
     */
    public function calculate_discount($invoice_id, $total)
    {
        $this->db->where('invoice_id', $invoice_id);
        $invoice_data = $this->db->get('invoices')->row();

        $total = (float)number_format($total, 2, '.', '');
        $discount_amount = (float)number_format($invoice_data->discount_amount, 2, '.', '');
        $discount_percent = (float)number_format($invoice_data->discount_percent, 2, '.', '');

        $total = $total - $discount_amount;
        $total = $total - round(($total / 100 * $discount_percent), 2);

        return $total;
    }

    /**
     * Returns the total amounts for the dashboard overview based on the given period
     * @param null $period
     * @return mixed
     */
    public function get_total_invoiced($period = null)
    {
        switch ($period) {
            case 'month':
                return $this->db->query("
					SELECT SUM(total) AS total_invoiced 
					FROM invoice_amounts
					WHERE invoice_id IN 
					(SELECT invoice_id FROM invoices
					WHERE MONTH(date_created) = MONTH(NOW()) 
					AND YEAR(date_created) = YEAR(NOW()))")->row()->total_invoiced;
            case 'last_month':
                return $this->db->query("
					SELECT SUM(total) AS total_invoiced 
					FROM invoice_amounts
					WHERE invoice_id IN 
					(SELECT id FROM invoices
					WHERE MONTH(date_created) = MONTH(NOW() - INTERVAL 1 MONTH)
					AND YEAR(date_created) = YEAR(NOW() - INTERVAL 1 MONTH))")->row()->total_invoiced;
            case 'year':
                return $this->db->query("
					SELECT SUM(total) AS total_invoiced 
					FROM invoice_amounts
					WHERE invoice_id IN 
					(SELECT id FROM invoices WHERE YEAR(date_created) = YEAR(NOW()))")->row()->total_invoiced;
            case 'last_year':
                return $this->db->query("
					SELECT SUM(total) AS total_invoiced 
					FROM invoice_amounts
					WHERE invoice_id IN 
					(SELECT id FROM invoices WHERE YEAR(date_created) = YEAR(NOW() - INTERVAL 1 YEAR))")->row()->total_invoiced;
            default:
                return $this->db->query("SELECT SUM(total) AS total_invoiced FROM invoice_amounts")->row()->total_invoiced;
        }
    }

    /**
     * Returns the total paid amounts for the dashboard overview based on the given period
     * @param string $period
     * @return mixed
     */
    public function get_total_paid($period = null)
    {
        switch ($period) {
            case 'month':
                return $this->db->query("
					SELECT SUM(paid) AS total_paid 
					FROM invoice_amounts
					WHERE invoice_id IN 
					(SELECT id FROM invoices
					WHERE MONTH(date_created) = MONTH(NOW())
					AND YEAR(date_created) = YEAR(NOW()))")->row()->total_paid;
            case 'last_month':
                return $this->db->query("SELECT SUM(paid) AS total_paid 
					FROM invoice_amounts
					WHERE invoice_id IN 
					(SELECT id FROM invoices
					WHERE MONTH(date_created) = MONTH(NOW() - INTERVAL 1 MONTH)
					AND YEAR(date_created) = YEAR(NOW() - INTERVAL 1 MONTH))")->row()->total_paid;
            case 'year':
                return $this->db->query("SELECT SUM(paid) AS total_paid 
					FROM invoice_amounts
					WHERE invoice_id IN 
					(SELECT id FROM invoices WHERE YEAR(date_created) = YEAR(NOW()))")->row()->total_paid;
            case 'last_year':
                return $this->db->query("SELECT SUM(paid) AS total_paid 
					FROM invoice_amounts
					WHERE invoice_id IN 
					(SELECT id FROM invoices WHERE YEAR(date_created) = YEAR(NOW() - INTERVAL 1 YEAR))")->row()->total_paid;
            default:
                return $this->db->query("SELECT SUM(paid) AS total_paid FROM invoice_amounts")->row()->total_paid;
        }
    }

    /**
     * Returns the total balance for the dashboard overview based on the given period
     * @param null $period
     * @return mixed
     */
    public function get_total_balance($period = null)
    {
        switch ($period) {
            case 'month':
                return $this->db->query("SELECT SUM(balance) AS total_balance 
					FROM invoice_amounts
					WHERE invoice_id IN 
					(SELECT id FROM invoices
					WHERE MONTH(date_created) = MONTH(NOW())
					AND YEAR(date_created) = YEAR(NOW()))")->row()->total_balance;
            case 'last_month':
                return $this->db->query("SELECT SUM(balance) AS total_balance 
					FROM invoice_amounts
					WHERE invoice_id IN 
					(SELECT id FROM invoices
					WHERE MONTH(date_created) = MONTH(NOW() - INTERVAL 1 MONTH)
					AND YEAR(date_created) = YEAR(NOW() - INTERVAL 1 MONTH))")->row()->total_balance;
            case 'year':
                return $this->db->query("SELECT SUM(balance) AS total_balance 
					FROM invoice_amounts
					WHERE invoice_id IN 
					(SELECT id FROM invoices WHERE YEAR(date_created) = YEAR(NOW()))")->row()->total_balance;
            case 'last_year':
                return $this->db->query("SELECT SUM(balance) AS total_balance 
					FROM invoice_amounts
					WHERE invoice_id IN 
					(SELECT id FROM invoices WHERE YEAR(date_created) = (YEAR(NOW() - INTERVAL 1 YEAR)))")->row()->total_balance;
            default:
                return $this->db->query("SELECT SUM(balance) AS total_balance FROM invoice_amounts")->row()->total_balance;
        }
    }

    /**
     * Returns the amounts for each status for the dashboard overview based on the given period
     * @param string $period
     * @return array
     */
    public function get_status_totals($period = '')
    {
        switch ($period) {
            default:
            case 'this-month':
                $invoices = $this->db->query("
					SELECT invoices.status_id, (CASE invoices.status_id WHEN 4 THEN SUM(invoice_amounts.paid) ELSE SUM(invoice_amounts.balance) END) AS sum_total, COUNT(*) AS num_total
					FROM invoice_amounts
					JOIN invoices ON invoices.id = invoice_amounts.invoice_id
                        AND MONTH(invoices.date_created) = MONTH(NOW())
                        AND YEAR(invoices.date_created) = YEAR(NOW())
					GROUP BY invoices.status_id")->result_array();
                break;
            case 'last-month':
                $invoices = $this->db->query("
					SELECT status_id, (CASE invoices.status_id WHEN 4 THEN SUM(paid) ELSE SUM(balance) END) AS sum_total, COUNT(*) AS num_total
					FROM invoice_amounts
					JOIN invoices ON invoices.id = invoice_amounts.invoice_id
                        AND MONTH(invoices.date_created) = MONTH(NOW() - INTERVAL 1 MONTH)
                        AND YEAR(invoices.date_created) = YEAR(NOW())
					GROUP BY invoices.status_id")->result_array();
                break;
            case 'this-quarter':
                $invoices = $this->db->query("
					SELECT status_id, (CASE invoices.status_id WHEN 4 THEN SUM(invoice_amounts.paid) ELSE SUM(invoice_amounts.balance) END) AS sum_total, COUNT(*) AS num_total
					FROM invoice_amounts
					JOIN invoices ON invoices.id = invoice_amounts.invoice_id
                        AND QUARTER(invoices.date_created) = QUARTER(NOW())
                        AND YEAR(invoices.date_created) = YEAR(NOW())
					GROUP BY invoices.status_id")->result_array();
                break;
            case 'last-quarter':
                $invoices = $this->db->query("
					SELECT status_id, (CASE invoices.status_id WHEN 4 THEN SUM(paid) ELSE SUM(balance) END) AS sum_total, COUNT(*) AS num_total
					FROM invoice_amounts
					JOIN invoices ON invoices.id = invoice_amounts.invoice_id
                        AND QUARTER(invoices.date_created) = QUARTER(NOW() - INTERVAL 1 QUARTER)
                        AND YEAR(invoices.date_created) = YEAR(NOW())
					GROUP BY invoices.status_id")->result_array();
                break;
            case 'this-year':
                $invoices = $this->db->query("
					SELECT status_id, (CASE invoices.status_id WHEN 4 THEN SUM(invoice_amounts.paid) ELSE SUM(invoice_amounts.balance) END) AS sum_total, COUNT(*) AS num_total
					FROM invoice_amounts
					JOIN invoices ON invoices.id = invoice_amounts.invoice_id
                        AND YEAR(invoices.date_created) = YEAR(NOW())
					GROUP BY invoices.status_id")->result_array();
                break;
            case 'last-year':
                $invoices = $this->db->query("
					SELECT status_id, (CASE invoices.status_id WHEN 4 THEN SUM(paid) ELSE SUM(balance) END) AS sum_total, COUNT(*) AS num_total
					FROM invoice_amounts
					JOIN invoices ON invoices.id = invoice_amounts.invoice_id
                        AND YEAR(invoices.date_created) = YEAR(NOW() - INTERVAL 1 YEAR)
					GROUP BY invoices.status_id")->result_array();
                break;
        }

        $output = [];

        foreach ($this->mdl_invoices->statuses() as $key => $status) {
            $output[$key] = array(
                'class' => $status['class'],
                'href' => $status['href'],
                'sum_total' => 0,
                'num_total' => 0
            );
        }

        foreach ($invoices as $invoice) {
            $output[$invoice['status_id']] = array_merge($output[$invoice['status_id']], $invoice);
        }

        return $output;
    }
}
