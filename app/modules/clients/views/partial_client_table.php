<div class="table-responsive">
    <table class="table table-bordered table-striped table-sm">
        <thead>
        <tr>
            <th></th>
            <th><?php echo lang('name'); ?></th>
            <th><?php echo lang('email_address'); ?></th>
            <th><?php echo lang('phone_number'); ?></th>
            <th class="text-right"><?php echo lang('balance'); ?></th>
            <th class="text-right"><?php echo lang('options'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($clients as $client) : ?>
            <tr>
                <td class="text-center">
                    <?php if ($client->is_active) { ?>
                        <i class="fa fa-check text-success" data-toggle="tooltip" data-placement="right"
                           title="<?php echo lang('active') ?>"></i>
                    <? } else { ?>
                        <i class="fa fa-ban text-danger" data-toggle="tooltip" data-placement="right"
                           title="<?php echo lang('inactive') ?>"></i>
                    <?php } ?>
                </td>
                <td>
                    <?php echo anchor('clients/view/' . $client->id, $client->name); ?>
                </td>
                <td>
                    <?php echo $client->email; ?>
                </td>
                <td>
                    <?php echo(($client->phone ? $client->phone : ($client->mobile ? $client->mobile : ''))); ?>
                </td>
                <td class="amount">
                    <?php echo format_currency($client->client_invoice_balance); ?>
                </td>
                <td>
                    <div class="options dropdown">
                        <a class="btn btn-secondary btn-sm  dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="fa fa-cog"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                            <a href="<?php echo site_url('clients/view/' . $client->id); ?>" class="dropdown-item">
                                <i class="fa fa-eye fa-margin-right"></i> <?php echo lang('view'); ?>
                            </a>
                            <a href="<?php echo site_url('clients/form/' . $client->id); ?>" class="dropdown-item">
                                <i class="fa fa-pencil fa-margin-right"></i> <?php echo lang('edit'); ?>
                            </a>
                            <a href="#" class="client-create-quote dropdown-item"
                               data-client-name="<?php echo $client->name; ?>">
                                <i class="fa fa-file fa-margin-right"></i> <?php echo lang('create_quote'); ?>
                            </a>
                            <a href="#" class="client-create-invoice dropdown-item"
                               data-client-name="<?php echo $client->name; ?>">
                                <i class="fa fa-file-text fa-margin-right"></i> <?php echo lang('create_invoice'); ?>
                            </a>
                            <a href="<?php echo site_url('clients/delete/' . $client->id); ?>"
                               onclick="return confirm('<?php echo lang('delete_warning'); ?>');"
                               class="dropdown-item">
                                <i class="fa fa-trash-o fa-margin-right"></i> <?php echo lang('delete'); ?>
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>