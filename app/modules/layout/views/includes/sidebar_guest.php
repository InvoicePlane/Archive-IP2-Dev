<div class="sidebar-inner">

    <div class="sidebar-top">
        <a href="<?php echo site_url('users/form/' . $this->session->user['id']); ?>">
            <?php
            echo $this->session->user['name'];
            if ($this->session->user['company']) {
                echo " (" . $this->session->user['company'] . ")";
            }
            ?>
        </a>
        <a href="#" class="sidebar-toggle pull-right"><i class="fa fa-close hidden-lg-up"></i></a>
    </div>

    <ul class="menu">

        <li><?php echo anchor('guest', lang('dashboard')); ?></li>
        <li><?php echo anchor('guest/quotes/index', lang('quotes')); ?></li>
        <li><?php echo anchor('guest/invoices/index', lang('invoices')); ?></li>
        <li><?php echo anchor('guest/payments/index', lang('payments')); ?></li>

        <li class="divider"></li>

        <li><?php echo anchor('sessions/logout', lang('logout')); ?></li>

    </ul>
</div>
