<div id="setup" class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">

    <h1 class="m-y-2">
        <img src="<?php echo THEME_URL;?>img/logo_200x100.png" alt="InvoicePlane">
    </h1>

    <div class="card">

        <div class="card-header">
            <?php echo lang('setup_database_details'); ?>
        </div>

        <form class="card-block" method="post" action="<?php echo site_url($this->uri->uri_string()); ?>">

            <p><?php echo lang('setup_database_message'); ?></p>

            <?php if (!$database['success']) { ?>

                <?php if ($database['message'] and $_POST) { ?>
                    <div class="alert alert-danger">
                        <?php echo $database['message']; ?>
                    </div>
                <?php } ?>

                <div class="form-group">
                    <label for="db_hostname">
                        <?php echo lang('hostname'); ?>
                    </label>
                    <input type="text" name="db_hostname" id="db_hostname" class="form-control"
                           value="<?php echo $this->input->post('db_hostname'); ?>">
                    <small class="text-muted"><?php echo lang('setup_db_hostname_info'); ?></small>
                </div>

                <div class="form-group">
                    <label>
                        <?php echo lang('username'); ?>
                    </label>
                    <input type="text" name="db_username" id="db_username" class="form-control"
                           value="<?php echo $this->input->post('db_username'); ?>">
                    <small class="text-muted"><?php echo lang('setup_db_username_info'); ?></small>
                </div>

                <div class="form-group">
                    <label>
                        <?php echo lang('password'); ?>
                    </label>
                    <input type="password" name="db_password" id="db_password" class="form-control"
                           value="<?php echo $this->input->post('db_password'); ?>">
                    <small class="text-muted"><?php echo lang('setup_db_password_info'); ?></small>
                </div>

                <div class="form-group">
                    <label>
                        <?php echo lang('database'); ?>
                    </label>
                    <input type="text" name="db_database" id="db_database" class="form-control"
                           value="<?php echo $this->input->post('db_database'); ?>">
                    <small class="text-muted"><?php echo lang('setup_db_database_info'); ?></small>
                </div>
            <?php } ?>

            <?php if ($errors) { ?>
                <input type="submit" class="btn btn-danger" name="btn_try_again"
                       value="<?php echo lang('check_db_connection'); ?>">
            <?php } else { ?>
                <p><i class="fa fa-check text-success fa-margin"></i>
                    <?php echo lang('setup_database_configured_message'); ?>
                </p>
                <input type="submit" class="btn btn-success" name="btn_continue"
                       value="<?php echo lang('continue'); ?>">
            <?php } ?>

        </form>

    </div>
</div>
