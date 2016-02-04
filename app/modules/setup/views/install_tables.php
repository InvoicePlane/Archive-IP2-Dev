<div id="setup" class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">

    <h1 class="m-y-2">
        <img src="<?php echo THEME_URL;?>img/logo_200x100.png" alt="InvoicePlane">
    </h1>

    <div class="card">

        <div class="card-header">
            <?php echo lang('setup_install_tables'); ?>
        </div>

        <form class="card-block" method="post" action="<?php echo site_url($this->uri->uri_string()); ?>">

            <?php if ($errors) { ?>
                <p><?php echo lang('setup_tables_errors'); ?></p>

                <?php foreach ($errors as $error) { ?>
                    <div class="alert alert-danger">
                        <?php echo $error; ?>
                    </div>
                <?php } ?>

            <?php } else { ?>
                <p>
                    <i class="fa fa-check text-success fa-margin-right"></i>
                    <?php echo lang('setup_tables_success'); ?>
                </p>
            <?php } ?>

            <?php if ($errors) { ?>
                <input type="submit" class="btn btn-danger" name="btn_try_again"
                       value="<?php echo lang('try_again'); ?>">
            <?php } else { ?>
                <input type="submit" class="btn btn-success" name="btn_continue"
                       value="<?php echo lang('continue'); ?>">
            <?php } ?>

        </form>

    </div>
</div>
