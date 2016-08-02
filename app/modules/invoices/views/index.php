<div id="headerbar">

    <div class="topmenu">
        <h1><?php echo lang('invoices'); ?></h1>

        <div class="pull-right">
            <a class="create-invoice btn btn-sm btn-primary" href="#">
                <i class="fa fa-plus fa-margin-right"></i> <?php echo lang('new'); ?>
            </a>
        </div>
    </div>

    <div class="submenu">
        <?php if (has_pages('mdl_invoices')) : ?>
            <div class="submenu-item">
                <?php echo pager(site_url('invoices/status/' . $this->uri->segment(3)), 'mdl_invoices'); ?>
            </div>
        <?php endif; ?>

        <div class="submenu-item">
            <ul class="nav nav-pills index-options">

                <?php foreach ($statuses as $status) : ?>
                    <li class="nav-item">
                        <a href="<?php echo site_url($status['href']); ?>"
                           class="nav-link <?php echo($this->uri->segment(3) === $status['class'] ? 'active' : '') ?>">
                            <?php echo $status['status_name']; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

</div>

<div id="content" class="filter-results table-inside">

    <?php $this->layout->load_view('layout/includes/alerts'); ?>

    <?php $this->layout->load_view('invoices/partial_invoice_table', array('invoices' => $invoices)); ?>

</div>