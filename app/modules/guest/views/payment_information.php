<div id="headerbar">
    <h1><?php echo lang('online_payment_for'); ?> #<?php echo $invoice->invoice_number; ?></h1>

    <div class="pull-right">
        <a href="<?php echo site_url('guest/invoices/generate_pdf/'); ?>"
           class="btn btn-sm btn-default">
            <i class="fa fa-print"></i> <?php echo lang('download_pdf'); ?>
        </a>
    </div>

</div>

<div id="content">

    <?php echo $this->layout->load_view('layout/alerts'); ?>

    <?php if ($disable_form === false) { ?>

        <h2><?php echo lang('total') . ': ' . format_currency($invoice->invoice_total); ?></h2>
        <h2><?php echo lang('balance') . ': ' . format_currency($invoice->invoice_balance); ?></h2>
        <br/>

        <form action="<?php echo site_url('guest/payment_handler/make_payment/'); ?>"
              method="post" id="payment-information-form">

            <div class="row">
                <div class="col-xs-12 col-sm-8 col-md-6">

                    <div class="form-group">
                        <input type="hidden" name="invoice_url_key" value="<?php echo $invoice->invoice_url_key; ?>">

                        <label for="gateway"><?php echo lang('online_payment_method'); ?></label>
                        <select name="gateway" id="gateway-select" class="form-control">
                            <?php
                            // Display all available gateways
                            foreach ($gateways as $gateway) { ?>
                                <option
                                    value="<?php echo $gateway; ?>">
                                    <?php echo ucwords(str_replace('_', ' ', $gateway)); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                    <div class="panel panel-default">

                        <div class="panel-heading">
                            <h3 class="panel-title"><?php echo lang('creditcard_details'); ?></h3>
                        </div>

                        <div class="panel-body">
                            <div class="alert alert-info">
                                <?php echo lang('online_payment_creditcard_hint'); ?>
                            </div>

                            <div class="form-group">
                                <label class="control-label">
                                    <?php echo lang('creditcard_number'); ?>
                                </label>
                                <input type="text" name="creditcard_number" class="input-sm form-control">
                            </div>

                            <div class="row">
                                <div class="col-xs-4">
                                    <div class="form-group">
                                        <label class="control-label">
                                            <?php echo lang('creditcard_expiry_month'); ?>
                                        </label>
                                        <input type="number" name="creditcard_expiry_month"
                                               class="input-sm form-control"
                                               min="1" max="12">
                                    </div>
                                </div>
                                <div class="col-xs-4">
                                    <div class="form-group">
                                        <label class="control-label">
                                            <?php echo lang('creditcard_expiry_year'); ?>
                                        </label>
                                        <input type="number" name="creditcard_expiry_year" class="input-sm form-control"
                                               min="<?php echo date('Y'); ?>" max="<?php echo date('Y') + 20; ?>">
                                    </div>
                                </div>
                                <div class="col-xs-4">
                                    <div class="form-group">
                                        <label class="control-label">
                                            <?php echo lang('creditcard_cvv'); ?>
                                        </label>
                                        <input type="number" name="creditcard_cvv" class="input-sm form-control">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>
            </div>

            <div class="form-group">
                <button class="btn btn-success btn-lg ajax-loader" type="submit">
                    <i class="fa fa-credit-card fa-margin">&nbsp;</i><?php echo lang('pay_now'); ?>
                </button>
            </div>

        </form>

    <?php } ?>

</div>