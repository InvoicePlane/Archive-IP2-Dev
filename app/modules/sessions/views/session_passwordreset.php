<div class="centerbox-wrapper">
    <div id="login" class="centerbox">

        <div class="login-logo"></div>

        <div class="card card-block">

            <h2><?php echo lang('password_reset'); ?></h2>

            <?php $this->layout->load_view('layout/includes/alerts'); ?>

            <p><?php echo lang('password_reset_info'); ?></p>

            <form method="post" action="<?php echo site_url($this->uri->uri_string()); ?>">

                <div class="form-group">
                    <label for="email" class="control-label"><?php echo lang('email'); ?></label>
                    <input type="text" name="email" id="email" class="form-control"
                           placeholder="<?php echo lang('email'); ?>">
                </div>

                <button type="submit" name="btn_reset" value="1" class="btn btn-warning">
                    <?php echo lang('reset_password'); ?>
                </button>

            </form>

        </div>

    </div>

</div>