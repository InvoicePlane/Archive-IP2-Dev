<div id="login" class="col-sm-10 col-sm-offset-1 col-md-6 col-md-offset-3">

    <div class="login-logo"></div>

    <div class="card card-block">

        <h2><?php echo lang('set_new_password'); ?></h2>

        <?php $this->layout->load_view('layout/includes/alerts'); ?>

        <form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>">

            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

            <div class="form-group">
                <label for="new_password" class="control-label"><?php echo lang('new_password'); ?></label>
                <input type="password" name="new_password" id="new_password" class="form-control"
                       placeholder="<?php echo lang('new_password'); ?>">
            </div>

            <button type="submit" name="btn_new_password" value="1" class="btn btn-warning">
                <?php echo lang('set_new_password'); ?>
            </button>

        </form>

    </div>

</div>