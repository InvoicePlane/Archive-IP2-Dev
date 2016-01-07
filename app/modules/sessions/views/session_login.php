<div class="login-logo col-sm-10 col-sm-offset-1 col-md-6 col-md-offset-3">
    <?php if ($login_logo) { ?>
        <img src="<?php echo base_url(); ?>data/images/<?php echo $login_logo; ?>">
    <?php } ?>
</div>

<div class="card card-block col-sm-10 col-sm-offset-1 col-md-6 col-md-offset-3">

    <?php $this->layout->load_view('layout/includes/alerts'); ?>

    <?php if (!$login_logo) { ?>
        <h2><?php echo lang('login'); ?></h2>
    <?php } ?>

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

        <button type="submit" name="btn_login" value="submit" class="btn btn-primary">
            <i class="fa fa-unlock fa-margin-right"></i><?php echo lang('login'); ?>
        </button>

    </form>

    <div class="text-right small">
        <a href="<?php echo base_url(); ?>sessions/passwordreset" class="text-muted">
            <?php echo lang('forgot_your_password'); ?>
        </a>
    </div>

</div>