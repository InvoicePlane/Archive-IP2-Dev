<title>
    <?php
    if ($this->mdl_settings->setting('custom_title') != '') {
        echo $this->mdl_settings->setting('custom_title');
    } else {
        echo 'InvoicePlane';
    } ?>
</title>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
<meta http-equiv="x-ua-compatible" content="ie=edge">
<meta name="robots" content="NOINDEX,NOFOLLOW">

<link rel="icon" type="image/png" href="<?php echo THEME_URL; ?>img/favicon.png">

<link rel="stylesheet" href="<?php echo THEME_URL; ?>css/app.min.css">
<link rel="stylesheet" href="<?php echo THEME_URL; ?>css/custom.css">

<?php if ($this->mdl_settings->setting('monospace_amounts') == 1) { ?>
    <link rel="stylesheet" href="<?php echo THEME_URL; ?>css/monospace.min.css">
<?php } ?>

<script src="<?php echo base_url(); ?>themes/core/js/dependencies.min.js"></script>
<script src="<?php echo base_url(); ?>themes/core/js/dependencies.min.js"></script>
<script src="<?php echo base_url(); ?>themes/core/js/app.min.js"></script>

<script>
    $(document).ready(function () {
        $('.create-invoice').click(function () {
            $('#modal-placeholder').load("<?php echo site_url('invoices/invoices_ajax/modal_create_invoice'); ?>");
        });

        $('.client-create-invoice').click(function () {
            $('#modal-placeholder').load("<?php echo site_url('invoices/invoices_ajax/modal_create_invoice'); ?>", {
                client_name: $(this).data('client-name')
            });
        });

        $('.create-quote').click(function () {
            $('#modal-placeholder').load("<?php echo site_url('quotes/ajax/modal_create_quote'); ?>");
        });

        $('#btn_quote_to_invoice').click(function () {
            $('#modal-placeholder').load("<?php echo site_url('quotes/ajax/modal_quote_to_invoice'); ?>/" + $(this).data('quote-id'));
        });

        $('#btn_copy_invoice').click(function () {
            $('#modal-placeholder').load("<?php echo site_url('invoices/ajax/modal_copy_invoice'); ?>", {
                invoice_id: $(this).data('invoice-id')
            });
        });

        $('#btn_create_credit').click(function () {
            $('#modal-placeholder').load("<?php echo site_url('invoices/ajax/modal_create_credit'); ?>", {
                invoice_id: $(this).data('invoice-id')
            });
        });

        $('#btn_copy_quote').click(function () {
            $('#modal-placeholder').load("<?php echo site_url('quotes/ajax/modal_copy_quote'); ?>", {
                quote_id: $(this).data('quote-id')
            });
        });

        $('.client-create-quote').click(function () {
            $('#modal-placeholder').load("<?php echo site_url('quotes/ajax/modal_create_quote'); ?>", {
                client_name: $(this).data('client-name')
            });
        });

        $(document).on('click', '.invoice-add-payment', function () {
            $('#modal-placeholder').load("<?php echo site_url('payments/ajax/modal_add_payment'); ?>", {
                invoice_id: $(this).data('invoice-id'),
                invoice_balance: $(this).data('invoice-balance'),
                invoice_payment_method: $(this).data('invoice-payment-method')
            });
        });
    });
</script>