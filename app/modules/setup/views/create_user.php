<script type="text/javascript">
    $(document).ready(function () {
        $("#user_country").select2({
            placeholder: "<?php echo lang('country'); ?>",
            allowClear: true
        });
    });
</script>

<div id="setup" class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">

    <h1 class="m-y-2">
        <img src="<?php echo THEME_URL;?>img/logo_200x100.png" alt="InvoicePlane">
    </h1>

    <div class="card">

        <div class="card-header">
            <?php echo lang('setup_create_user'); ?>
        </div>

        <form class="card-block" method="post" action="<?php echo site_url($this->uri->uri_string()); ?>">

            <?php echo $this->layout->load_view('layout/includes/alerts'); ?>

            <p><?php echo lang('setup_create_user_message'); ?></p>

            <input type="hidden" name="user_role_id" value="1">

            <div class="form-group">
                <label for="email">
                    <?php echo lang('email_address'); ?>
                </label>
                <input type="email" name="email" id="user_email" class="form-control"
                       value="<?php echo $this->mdl_users->form_value('user_email'); ?>">
                <small class="text-muted"><?php echo lang('setup_user_email_info'); ?></small>
            </div>

            <div class="form-group">
                <label for="company">
                    <?php echo lang('company_name'); ?>
                </label>
                <input type="text" name="company" id="user_company" class="form-control"
                       value="<?php echo $this->mdl_users->form_value('user_company'); ?>">
                <small class="text-muted"><?php echo lang('setup_user_company_info'); ?></small>
            </div>

            <div class="form-group">
                <label for="name">
                    <?php echo lang('name'); ?>
                </label>
                <input type="text" name="name" id="user_name" class="form-control"
                       value="<?php echo $this->mdl_users->form_value('user_name'); ?>">
            </div>

            <div class="form-group">
                <label for="password">
                    <?php echo lang('password'); ?>
                </label>
                <input type="password" name="password" id="user_password" class="form-control">
                <small class="text-muted">
                    <?php echo lang('setup_user_password_info') . ' ' . $password_suggestion; ?>
                </small>
            </div>

            <div class="form-group">
                <label for="passwordv">
                    <?php echo lang('verify_password'); ?>
                </label>
                <input type="password" name="passwordv" id="user_passwordv" class="form-control">
                <small class="text-muted"><?php echo lang('setup_user_password_verify_info'); ?></small>
            </div>

            <legend><?php echo lang('address_information'); ?></legend>
            <p><?php echo lang('setup_user_address_info'); ?></p>

            <div class="form-group">
                <label for="address_1">
                    <?php echo lang('street_address'); ?>
                </label>
                <input type="text" name="address_1" id="user_address_1" class="form-control"
                       value="<?php echo $this->mdl_users->form_value('user_address_1'); ?>"
                       placeholder="<?php echo lang('optional'); ?>">
            </div>

            <div class="form-group">
                <label for="address_2">
                    <?php echo lang('street_address_2'); ?>
                </label>
                <input type="text" name="address_2" id="user_address_2" class="form-control"
                       value="<?php echo $this->mdl_users->form_value('user_address_2'); ?>"
                       placeholder="<?php echo lang('optional'); ?>">
            </div>

            <div class="form-group">
                <label for="city">
                    <?php echo lang('city'); ?>
                </label>
                <input type="text" name="city" id="user_city" class="form-control"
                       value="<?php echo $this->mdl_users->form_value('user_city'); ?>"
                       placeholder="<?php echo lang('optional'); ?>">
            </div>

            <div class="form-group">
                <label for="state">
                    <?php echo lang('state'); ?>
                </label>
                <input type="text" name="state" id="user_state" class="form-control"
                       value="<?php echo $this->mdl_users->form_value('user_state'); ?>"
                       placeholder="<?php echo lang('optional'); ?>">
            </div>

            <div class="form-group">
                <label for="zip">
                    <?php echo lang('zip_code'); ?>
                </label>
                <input type="text" name="zip" id="user_zip" class="form-control"
                       value="<?php echo $this->mdl_users->form_value('user_zip'); ?>"
                       placeholder="<?php echo lang('optional'); ?>">
            </div>

            <div class="form-group">
                <label for="country">
                    <?php echo lang('country'); ?>
                </label>
                <select name="country" id="user_country" class="form-control">
                    <option></option>
                    <?php foreach ($countries as $cldr => $country) { ?>
                        <option value="<?php echo $cldr; ?>"
                                <?php if ($this->mdl_users->form_value('user_country') == $cldr) { ?>selected="selected"<?php } ?>><?php echo $country ?></option>
                    <?php } ?>
                </select>
            </div>

            <br>

            <div class="form-group">
                <label for="phone">
                    <?php echo lang('phone'); ?>
                </label>
                <input type="text" name="phone" id="user_phone" class="form-control"
                       value="<?php echo $this->mdl_users->form_value('user_phone'); ?>"
                       placeholder="<?php echo lang('optional'); ?>">
            </div>

            <div class="form-group">
                <label for="fax">
                    <?php echo lang('fax'); ?>
                </label>
                <input type="text" name="fax" id="user_fax" class="form-control"
                       value="<?php echo $this->mdl_users->form_value('user_fax'); ?>"
                       placeholder="<?php echo lang('optional'); ?>">
            </div>

            <div class="form-group">
                <label for="mobile">
                    <?php echo lang('mobile'); ?>
                </label>
                <input type="text" name="mobile" id="user_mobile" class="form-control"
                       value="<?php echo $this->mdl_users->form_value('user_mobile'); ?>"
                       placeholder="<?php echo lang('optional'); ?>">
            </div>

            <div class="form-group">
                <label for="web">
                    <?php echo lang('web'); ?>
                </label>
                <input type="text" name="web" id="user_web" class="form-control"
                       value="<?php echo $this->mdl_users->form_value('user_web'); ?>"
                       placeholder="<?php echo lang('optional'); ?>">
            </div>

            <input type="submit" class="btn btn-success" name="btn_continue"
                   value="<?php echo lang('continue'); ?>">

        </form>

    </div>
</div>
