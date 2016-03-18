<div id="headerbar">

    <div class="topmenu">
        <h1><?php echo lang('custom_fields'); ?></h1>

        <div class="pull-right">
            <a class="btn btn-sm btn-primary" href="<?php echo site_url('custom_fields/form'); ?>">
                <i class="fa fa-plus fa-margin-right"></i> <?php echo lang('new'); ?>
            </a>
        </div>

        <div class="pull-right">
            <?php if (has_pages('mdl_custom_fields')) : ?>
                <div class="submenu-item">
                    <?php echo pager(site_url('custom_fields/index'), 'mdl_custom_fields'); ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<div id="content" class="filter-results table-inside">

    <?php echo $this->layout->load_view('layout/includes/alerts'); ?>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-sm">

            <thead>
            <tr>
                <th><?php echo lang('table'); ?></th>
                <th><?php echo lang('label'); ?></th>
                <th><?php echo lang('column'); ?></th>
                <th><?php echo lang('type'); ?></th>
                <th><?php echo lang('options'); ?></th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($custom_fields as $custom_field) : ?>
                <tr>
                    <td><?php echo $custom_field->table; ?></td>
                    <td><?php echo $custom_field->label; ?></td>
                    <td><?php echo $custom_field->column; ?></td>
                    <td><?php echo lang($custom_field->type); ?></td>
                    <td>
                        <div class="options btn-group">
                            <a class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown" href="#">
                                <i class="fa fa-cog"></i>
                            </a>
                            <div class="dropdown-menu dropdown-menu-sm dropdown-menu-right">
                                <a href="<?php echo site_url('custom_fields/form/' . $custom_field->id); ?>" class="dropdown-item">
                                    <i class="fa fa-edit fa-margin-right"></i> <?php echo lang('edit'); ?>
                                </a>

                                <a href="<?php echo site_url('custom_fields/delete/' . $custom_field->id); ?>"
                                   onclick="return confirm('<?php echo lang('delete_record_warning'); ?>');"  class="dropdown-item">
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

</div>
