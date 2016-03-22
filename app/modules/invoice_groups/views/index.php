<div id="headerbar">

    <div class="topmenu">
        <h1><?php echo lang('invoice_groups'); ?></h1>

        <div class="pull-right">
            <a class="btn btn-sm btn-primary" href="<?php echo site_url('invoice_groups/form'); ?>">
                <i class="fa fa-plus fa-margin-right"></i> <?php echo lang('new'); ?>
            </a>
        </div>

        <div class="pull-right">
            <?php if (has_pages('mdl_invoice_groups')) : ?>
                <div class="submenu-item">
                    <?php echo pager(site_url('invoice_groups/index'), 'mdl_invoice_groups'); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<div id="content" class="table-inside">

    <?php $this->layout->load_view('layout/includes/alerts'); ?>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-sm">

            <thead>
            <tr>
                <th><?php echo lang('name'); ?></th>
                <th><?php echo lang('next_id'); ?></th>
                <th><?php echo lang('left_pad'); ?></th>
                <th><?php echo lang('options'); ?></th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($invoice_groups as $invoice_group) { ?>
                <tr>
                    <td><?php echo $invoice_group->name; ?></td>
                    <td><?php echo $invoice_group->next_id; ?></td>
                    <td><?php echo $invoice_group->left_pad; ?></td>
                    <td>
                        <div class="options dropdown">
                            <a class="btn btn-secondary btn-sm  dropdown-toggle" data-toggle="dropdown" href="#">
                                <i class="fa fa-cog"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                <a href="<?php echo site_url('invoice_groups/form/' . $invoice_group->id); ?>" class="dropdown-item">
                                    <i class="fa fa-pencil fa-margin-right fa-margin-right"></i> <?php echo lang('edit'); ?>
                                </a>
                                <a href="<?php echo site_url('invoice_groups/delete/' . $invoice_group->id); ?>" class="dropdown-item"
                                   onclick="return confirm('<?php echo lang('delete_record_warning'); ?>');">
                                    <i class="fa fa-trash-o fa-margin fa-margin-right"></i> <?php echo lang('delete'); ?>
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php } ?>
            </tbody>

        </table>
    </div>
</div>