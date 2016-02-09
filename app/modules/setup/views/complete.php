<div id="setup" class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">

    <h1 class="m-y-2">
        <img src="<?php echo THEME_URL;?>img/logo_200x100.png" alt="InvoicePlane">
    </h1>

    <div class="card">

        <div class="card-header">
            <?php echo lang('setup_complete'); ?>
        </div>

        <div class="card-block">
            
            <p><?php echo lang('setup_complete_message'); ?></p>

            <p class="alert alert-info">
                <?php echo lang('setup_complete_support_note'); ?>
            </p>

            <a href="<?php echo site_url('sessions/login'); ?>" class="btn btn-success">
                <i class="fa fa-check fa-margin-right"></i> <?php echo lang('login'); ?>
            </a>
            
        </div>

    </div>
</div>
