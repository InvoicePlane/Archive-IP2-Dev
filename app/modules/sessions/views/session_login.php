<div id="login" class="col-sm-10 col-sm-offset-1 col-md-6 col-md-offset-3">

    <div class="login-logo">
        <?php if ($login_logo) { ?>
            <img src="<?php echo DATAFOLDER_IMAGES . $login_logo; ?>">
        <?php } ?>
    </div>

    <div class="card card-block">

        <?php if (!$login_logo) { ?>
            <h2><?php echo lang('login'); ?></h2>
        <?php } ?>

        <?php $this->layout->load_view('layout/includes/alerts'); ?>

        <form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>">

            <div class="form-group">
                <label for="email"><?php echo lang('email'); ?></label>
                <input type="email" name="email" id="email" class="form-control"
                       placeholder="<?php echo lang('email'); ?>"
                    <?php echo(empty($_POST['email']) ?: 'value="' . $_POST['email'] . '"') ?>>
            </div>

            <div class="form-group">
                <label for="password" class="control-label"><?php echo lang('password'); ?></label>
                <input type="password" name="password" id="password" class="form-control"
                       placeholder="<?php echo lang('password'); ?>"
                    <?php echo(empty($_POST['password']) ?: 'value="' . $_POST['password'] . '"') ?>>
            </div>

            <button type="submit" name="btn_login" value="1" class="btn btn-primary">
                <i class="fa fa-unlock fa-margin-right"></i><?php echo lang('login'); ?>
            </button>

            <a href="<?php echo site_url('sessions/passwordreset'); ?>" class="btn text-muted pull-right">
                <?php echo lang('forgot_your_password'); ?>
            </a>

        </form>

    </div>
    
</div>