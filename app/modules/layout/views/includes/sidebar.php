<div class="sidebar-inner">

    <div class="sidebar-top">
        <a href="<?php echo site_url('users/form/' . $this->session->user['id']); ?>">
            <?php
            echo '<b>' . $this->session->user['name'] . '</b>';
            if ($this->session->user['company']) {
                echo " (" . $this->session->user['company'] . ")";
            }
            ?>
        </a>
        <a href="#" class="sidebar-toggle pull-right"><i class="fa fa-close hidden-lg-up"></i></a>
    </div>

    <ul class="menu">
        <li>
            <?php echo anchor('dashboard', lang('dashboard'), 'class="visible-sm-inline-block"') ?>
        </li>
        <li>
            <a href="#" data-toggle="collapse" data-target="#submenu-clients" class="has-submenu collapsed">
                <?php echo lang('clients'); ?>
                <span class="pull-right"><span class="menu-icon fa fa-fw"></span></span>
            </a>
            <ul id="submenu-clients" class="submenu collapse">
                <li><?php echo anchor('clients/form', lang('add_client')); ?></li>
                <li><?php echo anchor('clients/index', lang('view_clients')); ?></li>
            </ul>
        </li>
        <li>
            <a href="#" data-toggle="collapse" data-target="#submenu-quotes" class="has-submenu collapsed">
                <?php echo lang('quotes'); ?>
                <span class="pull-right"><span class="menu-icon fa fa-fw"></span></span>
            </a>
            <ul id="submenu-quotes" class="submenu collapse">
                <li><a href="#" class="create-quote"><?php echo lang('create_quote'); ?></a></li>
                <li><?php echo anchor('quotes/index', lang('view_quotes')); ?></li>
            </ul>
        </li>
        <li>
            <a href="#" data-toggle="collapse" data-target="#submenu-invoices" class="has-submenu collapsed">
                <?php echo lang('invoices'); ?>
                <span class="pull-right"><span class="menu-icon fa fa-fw"></span></span>
            </a>
            <ul id="submenu-invoices" class="submenu collapse">
                <li><a href="#" class="create-invoice"><?php echo lang('create_invoice'); ?></a></li>
                <li><?php echo anchor('invoices/index', lang('view_invoices')); ?></li>
                <li><?php echo anchor('invoices/recurring/index', lang('view_recurring_invoices')); ?></li>
            </ul>
        </li>
        <li>
            <a href="#" data-toggle="collapse" data-target="#submenu-products" class="has-submenu collapsed">
                <?php echo lang('products'); ?>
                <span class="pull-right"><span class="menu-icon fa fa-fw"></span></span>
            </a>
            <ul id="submenu-products" class="submenu collapse">
                <li><?php echo anchor('products/form', lang('create_product')); ?></li>
                <li><?php echo anchor('products/index', lang('view_products')); ?></li>
                <li><?php echo anchor('families/index', lang('product_families')); ?></li>
            </ul>
        </li>
        <li>
            <a href="#" data-toggle="collapse" data-target="#submenu-payments" class="has-submenu collapsed">
                <?php echo lang('payments'); ?>
                <span class="pull-right"><span class="menu-icon fa fa-fw"></span></span>
            </a>
            <ul id="submenu-payments" class="submenu collapse">
                <li><?php echo anchor('payments/form', lang('enter_payment')); ?></li>
                <li><?php echo anchor('payments/index', lang('view_payments')); ?></li>
            </ul>
        </li>
        <li>
            <a href="#" data-toggle="collapse" data-target="#submenu-tasks" class="has-submenu collapsed">
                <?php echo lang('tasks'); ?>
                <span class="pull-right"><span class="menu-icon fa fa-fw"></span></span>
            </a>
            <ul id="submenu-tasks" class="submenu collapse">
                <li><?php echo anchor('tasks/form', lang('create_task')); ?></li>
                <li><?php echo anchor('tasks/index', lang('show_tasks')); ?></li>
                <li><?php echo anchor('projects/index', lang('projects')); ?></li>
            </ul>
        </li>
        <li>
            <a href="#" data-toggle="collapse" data-target="#submenu-reports" class="has-submenu collapsed">
                <?php echo lang('reports'); ?>
                <span class="pull-right"><span class="menu-icon fa fa-fw"></span></span>
            </a>
            <ul id="submenu-reports" class="submenu collapse">
                <li><?php echo anchor('reports/invoice_aging', lang('invoice_aging')); ?></li>
                <li><?php echo anchor('reports/payment_history', lang('payment_history')); ?></li>
                <li><?php echo anchor('reports/sales_by_client', lang('sales_by_client')); ?></li>
                <li><?php echo anchor('reports/sales_by_year', lang('sales_by_date')); ?></li>
            </ul>
        </li>

        <li class="divider"></li>

        <li>
            <a href="http://docs.invoiceplane.com/" target="_blank">
                <?php echo lang('documentation'); ?>
            </a>
        </li>
        <li>
            <a href="#" data-toggle="collapse" data-target="#submenu-settings" class="has-submenu collapsed">
                <?php echo lang('settings'); ?>
                <span class="pull-right"><span class="menu-icon fa fa-fw"></span></span>
            </a>
            <ul id="submenu-settings" class="submenu collapse">
                <li><?php echo anchor('custom_fields/index', lang('custom_fields')); ?></li>
                <li><?php echo anchor('email_templates/index', lang('email_templates')); ?></li>
                <li><?php echo anchor('invoice_groups/index', lang('invoice_groups')); ?></li>
                <li><?php echo anchor('invoices/archive', lang('invoice_archive')); ?></li>
                <li><?php echo anchor('payment_methods/index', lang('payment_methods')); ?></li>
                <li><?php echo anchor('tax_rates/index', lang('tax_rates')); ?></li>
                <li><?php echo anchor('users/index', lang('user_accounts')); ?></li>
                <li class="divider"></li>
                <li><?php echo anchor('settings', lang('system_settings')); ?></li>
                <li><?php echo anchor('import', lang('import_data')); ?></li>
            </ul>
        </li>
        <li>
            <a href="<?php echo site_url('sessions/logout'); ?>">
                <?php echo lang('logout'); ?>
            </a>
        </li>
    </ul>

    <?php if (isset($filter_display) and $filter_display == TRUE) { ?>
        <?php $this->layout->load_view('filter/jquery_filter'); ?>
        <form role="search" onsubmit="return false;">
            <div class="form-group">
                <input id="filter" type="text" class="search-query form-control input-sm"
                       placeholder="<?php echo $filter_placeholder; ?>">
            </div>
        </form>
    <?php } ?>
</div>