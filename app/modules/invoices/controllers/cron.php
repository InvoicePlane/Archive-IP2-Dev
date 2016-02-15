<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * Class Cron
 * @package Modules\Invoices\Controllers
 * @property Mdl_Email_Templates $mdl_email_templates
 * @property Mdl_Invoices $mdl_invoices
 * @property Mdl_Invoices_Recurring $mdl_invoices_recurring
 * @property Mdl_Uploads $mdl_uploads
 */
class Cron extends Base_Controller
{
    /**
     * Processes the open recurring invoices
     * @param null string $cron_key
     */
    public function recur($cron_key = null)
    {
        if ($cron_key == $this->mdl_settings->setting('cron_key')) {
            $this->load->model('invoices/mdl_invoices_recurring');
            $this->load->model('invoices/mdl_invoices');
            $this->load->helper('mailer');

            // Gather a list of recurring invoices to generate
            $invoices_recurring = $this->mdl_invoices_recurring->active()->get()->result();

            foreach ($invoices_recurring as $invoice_recurring) {
                // This is the original invoice id
                $source_id = $invoice_recurring->invoice_id;

                // This is the original invoice
                // $invoice = $this->db->where('ip_invoices.invoice_id', $source_id)->get('ip_invoices')->row();
                $invoice = $this->mdl_invoices->get_by_id($source_id);

                // Create the new invoice
                $db_array = array(
                    'user_id' => $invoice->user_id,
                    'client_id' => $invoice->client_id,
                    'invoice_group_id' => $invoice->invoice_group_id,
                    'invoice_number' => $this->mdl_invoices->get_invoice_number($invoice->invoice_group_id),
                    'date_due' => $this->mdl_invoices->get_date_due($invoice_recurring->next_date, $invoice_recurring->invoices_due_after),
                    'terms' => $invoice->terms,
                    'url_key' => $this->mdl_invoices->get_url_key(),
                    'date_created' => $invoice_recurring->next_date,
                );

                // This is the new invoice id
                $target_id = $this->mdl_invoices->create($db_array, false);

                // Copy the original invoice to the new invoice
                $this->mdl_invoices->copy_invoice($source_id, $target_id);

                // Update the next recur date for the recurring invoice
                $this->mdl_invoices_recurring->set_next_recur_date($invoice_recurring->id);

                // Email the new invoice if applicable
                if ($this->mdl_settings->setting('automatic_email_on_recur') and mailer_configured()) {
                    $new_invoice = $this->mdl_invoices->get_by_id($target_id);

                    // Set the email body, use default email template if available
                    $this->load->model('email_templates/mdl_email_templates');

                    $email_template_id = $invoice_recurring->email_template_id;
                    if (!$email_template_id) {
                        return;
                    }

                    $email_template = $this->mdl_email_templates->where('id', $email_template_id)->get();
                    if ($email_template->num_rows() == 0) {
                        return;
                    }

                    $tpl = $email_template->row();

                    // Prepare the attachments
                    $this->load->model('upload/mdl_uploads');
                    $attachment_files = $this->mdl_uploads->get_invoice_uploads($target_id);

                    // Prepare the body
                    // @TODO IP-390 - Use template files for email template body
                    $body = $tpl->body_template_file;
                    if (strlen($body) != strlen(strip_tags($body))) {
                        $body = htmlspecialchars_decode($body);
                    } else {
                        $body = htmlspecialchars_decode(nl2br($body));
                    }

                    $from = !empty($tpl->from_email) ?
                        array(
                            $tpl->from_email,
                            $tpl->from_name
                        ) :
                        array($invoice->user_email, "");

                    $subject = !empty($tpl->subject) ?
                        $tpl->subject :
                        lang('invoice') . ' #' . $new_invoice->invoice_number;

                    $pdf_template = $tpl->pdf_template;
                    $to = $tpl->to_email;
                    $cc = $tpl->cc;
                    $bcc = $tpl->bcc;
                    $send_pdf = $tpl->send_pdf;
                    $send_attachments = $tpl->send_attachments;
                    
                    if (email_invoice($target_id, $pdf_template, $from, $to, $subject, $body, $cc, $bcc, $attachment_files, $send_pdf, $send_attachments)) {
                        $this->mdl_invoices->mark_sent($target_id);
                    } else {
                        log_message("warning", "Invoice " . $target_id . "could not be sent. Please review your Email settings.");
                    }
                }
            }
        }
    }
}
