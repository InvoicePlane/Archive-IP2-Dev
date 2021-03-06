<div id="headerbar">
    <h1><?php echo $client->name; ?></h1>

    <div class="pull-right btn-group">
        <a href="#" class="btn btn-sm btn-secondary client-create-quote"
           data-client-name="<?php echo $client->name; ?>">
            <i class="fa fa-file fa-margin-right"></i> <?php echo lang('create_quote'); ?>
        </a>

        <a href="#" class="btn btn-sm btn-secondary client-create-invoice"
           data-client-name="<?php echo $client->name; ?>"><i
                class="fa fa-file-text fa-margin-right"></i> <?php echo lang('create_invoice'); ?></a>

        <a href="<?php echo site_url('clients/form/' . $client->id); ?>"
           class="btn btn-sm btn-secondary">
            <i class="fa fa-pencil fa-margin-right"></i> <?php echo lang('edit'); ?>
        </a>

        <a class="btn btn-sm btn-danger"
           href="<?php echo site_url('clients/delete/' . $client->id); ?>"
           onclick="return confirm('<?php echo lang('delete_warning'); ?>');">
            <i class="fa fa-trash-o fa-margin-right"></i> <?php echo lang('delete'); ?>
        </a>
    </div>

</div>

<ul class="nav nav-tabs" role="tablist">
    <li class="nav-item">
        <a class="nav-link active" data-toggle="tab" href="#client-details"><?php echo lang('details'); ?></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#client-quotes"><?php echo lang('quotes'); ?></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#client-invoices"><?php echo lang('invoices'); ?></a>
    </li>
    <li class="nav-item">
        <a class="nav-link" data-toggle="tab" href="#client-payments"><?php echo lang('payments'); ?></a>
    </li>
</ul>

<div class="tab-content">
    <div class="tab-pane active" id="client-details" role="tabpanel">

        <?php $this->layout->load_view('layout/includes/alerts'); ?>

        <div class="row">
            <div class="col-xs-12 col-md-6 col-lg-8">
                <h3><?php echo $client->name; ?></h3>

                <p>
                    <?php echo ($client->address_1) ? $client->address_1 . '<br>' : ''; ?>
                    <?php echo ($client->address_2) ? $client->address_2 . '<br>' : ''; ?>
                    <?php echo ($client->city) ? $client->city : ''; ?>
                    <?php echo ($client->state) ? $client->state : ''; ?>
                    <?php echo ($client->zip) ? $client->zip : ''; ?>
                    <?php echo ($client->country) ? '<br>' . get_country_name(lang('cldr'), $client->country) : ''; ?>
                </p>
            </div>
            <div class="col-xs-12 col-md-6 col-lg-4">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <tr>
                            <td>
                                <?php echo lang('total_billed'); ?>
                            </td>
                            <td class="amount">
                                <?php echo format_currency($client->client_invoice_total); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php echo lang('total_paid'); ?>
                            </td>
                            <td class="amount">
                                <?php echo format_currency($client->client_invoice_paid); ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php echo lang('total_balance'); ?>
                            </td>
                            <td class="amount">
                                <?php echo format_currency($client->client_invoice_balance); ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-xs-12 col-md-6">
                <h4><?php echo lang('contact_information'); ?></h4>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <?php if ($client->email) : ?>
                            <tr>
                                <td><?php echo lang('email'); ?></td>
                                <td><?php echo auto_link($client->email, 'email'); ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if ($client->phone) : ?>
                            <tr>
                                <td><?php echo lang('phone'); ?></td>
                                <td><?php echo $client->phone; ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if ($client->mobile) : ?>
                            <tr>
                                <td><?php echo lang('mobile'); ?></td>
                                <td><?php echo $client->mobile; ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if ($client->fax) : ?>
                            <tr>
                                <td><?php echo lang('fax'); ?></td>
                                <td><?php echo $client->fax; ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if ($client->web) : ?>
                            <tr>
                                <td><?php echo lang('web'); ?></td>
                                <td><?php echo auto_link($client->web, 'url', true); ?></td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>

            <div class="col-xs-12 col-md-6">
                <h4><?php echo lang('tax_information'); ?></h4>

                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <?php if ($client->vat_id) : ?>
                            <tr>
                                <td><?php echo lang('vat_id'); ?></td>
                                <td><?php echo $client->vat_id; ?></td>
                            </tr>
                        <?php endif; ?>
                        <?php if ($client->tax_code) : ?>
                            <tr>
                                <td><?php echo lang('tax_code'); ?></td>
                                <td><?php echo $client->tax_code; ?></td>
                            </tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>

        <?php if ($custom_fields) : ?>
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <h4><?php echo lang('custom_fields'); ?></h4>

                    <div class="table-responsive">
                        <table class="table table-sm table-bordered">
                            <?php foreach ($custom_fields as $custom_field) :
                                if (!empty($client->{$custom_field->column})) : ?>
                                    <tr>
                                        <th class="custom-field-label"><?php echo $custom_field->label ?></th>
                                        <td>
                                            <?php
                                            echo $this->mdl_custom_fields->format_field_value(
                                                $custom_field,
                                                $client->{$custom_field->column}
                                            );
                                            ?>
                                        </td>
                                    </tr>
                                <?php endif;
                            endforeach; ?>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <hr>

        <?php
        $this->layout->load_view('notes/notes', [
            'notes' => $notes,
            'type' => 'client',
            'type_id' => $client->id,
        ]);
        ?>

    </div>
    <div class="tab-pane table-inside" id="client-quotes" role="tabpanel">
        <?php echo $quote_table; ?>
    </div>
    <div class="tab-pane table-inside" id="client-invoices" role="tabpanel">
        <?php echo $invoice_table; ?>
    </div>
    <div class="tab-pane table-inside" id="client-payments" role="tabpanel">
        <?php echo $payment_table; ?>
    </div>
</div>
