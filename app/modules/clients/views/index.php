<div id="headerbar">

    <div class="topmenu">
        <h1><?php echo lang('clients'); ?></h1>

        <div class="pull-right">
            <a class="btn btn-sm btn-primary" href="<?php echo site_url('clients/form'); ?>">
                <i class="fa fa-plus fa-margin-right"></i> <?php echo lang('new'); ?>
            </a>
        </div>
    </div>

    <div class="submenu">
        <?php if (has_pages('mdl_clients')) : ?>
            <div class="submenu-item">
                <?php echo pager(site_url('clients/status/' . $this->uri->segment(3)), 'mdl_clients'); ?>
            </div>
        <?php endif; ?>

        <div class="submenu-item">
            <ul class="nav nav-pills index-options">
                <li class="nav-item">
                    <a href="<?php echo site_url('clients/status/active'); ?>"
                       class="nav-link <?php echo($this->uri->segment(3) === 'active' || !$this->uri->segment(3) ? 'active' : '') ?>">
                        <?php echo lang('active'); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo site_url('clients/status/inactive'); ?>"
                       class="nav-link <?php echo($this->uri->segment(3) === 'inactive' || !$this->uri->segment(3) ? 'active' : '') ?>">
                        <?php echo lang('inactive'); ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?php echo site_url('clients/status/all'); ?>"
                       class="nav-link <?php echo($this->uri->segment(3) === 'all' || !$this->uri->segment(3) ? 'active' : '') ?>">
                        <?php echo lang('all'); ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>

</div>

<div id="content" class="filter-results table-inside">

    <?php $this->layout->load_view('layout/includes/alerts'); ?>

    <?php $this->layout->load_view('clients/partial_client_table'); ?>

</div>