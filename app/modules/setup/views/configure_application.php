<div id="setup" class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">

    <h1 class="m-y-2">
        <img src="<?php echo THEME_URL; ?>img/logo_200x100.png" alt="InvoicePlane">
    </h1>

    <div class="card">

        <div class="card-header">
            <?php echo lang('setup_configure_application'); ?>
        </div>

        <form class="card-block" method="post" action="<?php echo site_url($this->uri->uri_string()); ?>">

            <p><?php echo lang('setup_configure_application_message'); ?></p>

            <div class="form-group">
                <button type="submit" class="btn btn-secondary" name="btn_skip" value="1">
                    <i class="fa fa-angle-right fa-margin-right"></i><?php echo lang('setup_skip_step'); ?>
                </button>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-6">

                    <div class="form-group">
                        <label for="settings[first_day_of_week]" class="control-label">
                            <?php echo lang('first_day_of_week'); ?>
                        </label>
                        <select name="settings[first_day_of_week]" class="form-control">
                            <?php foreach ($first_days_of_weeks as $day_id => $day_name) { ?>
                                <option value="<?php echo $day_id; ?>">
                                    <?php echo $day_name; ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                </div>
                <div class="col-md-6">

                    <div class="form-group">
                        <label for="settings[date_format]" class="control-label">
                            <?php echo lang('date_format'); ?>
                        </label>
                        <select name="settings[date_format]" class="form-control">
                            <?php
                            foreach ($date_formats as $date_format) { ?>
                                <option value="<?php echo $date_format['setting']; ?>">
                                    <?php echo $current_date->format($date_format['setting']); ?>
                                </option>
                            <?php } ?>
                        </select>
                    </div>

                </div>
            </div>

            <br>

            <div class="row">
                <div class="col-md-6">

                    <div class="form-group">
                        <label class="control-label">
                            <?php echo lang('currency_symbol'); ?>
                        </label>
                        <input type="text" name="settings[currency_symbol]" class="form-control"
                               value="&euro;">
                    </div>

                    <div class="form-group">
                        <label for="settings[thousands_separator]" class="control-label">
                            <?php echo lang('thousands_separator'); ?>
                        </label>
                        <input type="text" name="settings[thousands_separator]" class="form-control"
                               value=",">
                    </div>

                </div>
                <div class="col-md-6">

                    <div class="form-group">
                        <label for="settings[currency_symbol_placement]" class="control-label">
                            <?php echo lang('currency_symbol_placement'); ?>
                        </label>
                        <select name="settings[currency_symbol_placement]" class="form-control">
                            <option value="after"><?php echo lang('after_amount'); ?></option>
                            <option value="before"><?php echo lang('before_amount'); ?></option>
                            <option value="afterspace"><?php echo lang('after_amount_space'); ?></option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="settings[decimal_point]" class="control-label">
                            <?php echo lang('decimal_point'); ?>
                        </label>
                        <input type="text" name="settings[decimal_point]" class="form-control"
                               value=".">
                    </div>

                </div>
            </div>

            <br>

            <div class="row">
                <div class="col-md-6">

                    <div class="form-group">
                        <label for="settings[invoices_due_after]" class="control-label">
                            <?php echo lang('invoices_due_after'); ?>
                        </label>
                        <input type="text" name="settings[invoices_due_after]" class="form-control"
                               value="30">
                    </div>

                </div>
                <div class="col-md-6">

                    <div class="form-group">
                        <label for="settings[quotes_expire_after]" class="control-label">
                            <?php echo lang('quotes_expire_after'); ?>
                        </label>
                        <input type="text" name="settings[quotes_expire_after]" class="form-control"
                               value="15">
                    </div>

                </div>
            </div>

            <br>

            <div class="row">

                <script>
                    $(function () {
                        toggle_smtp_settings();
                        
                        $('#email_send_method').change(function () {
                            toggle_smtp_settings();
                        });

                        function toggle_smtp_settings() {
                            if ($('#email_send_method').val() == 'smtp') {
                                $('#div-smtp-settings').show();
                            } else {
                                $('#div-smtp-settings').hide();
                            }
                        }
                    });
                </script>

                <div class="col-md-6">

                    <div class="form-group">
                        <label for="settings[email_send_method]" class="control-label">
                            <?php echo lang('email_send_method'); ?>
                        </label>
                        <select name="settings[email_send_method]" id="email_send_method"
                                class="form-control">
                            <option value=""></option>
                            <option value="phpmail">
                                <?php echo lang('email_send_method_phpmail'); ?>
                            </option>
                            <option value="sendmail">
                                <?php echo lang('email_send_method_sendmail'); ?>
                            </option>
                            <option value="smtp">
                                <?php echo lang('email_send_method_smtp'); ?>
                            </option>
                        </select>
                    </div>

                </div>
                <div id="div-smtp-settings" class="col-md-6">

                    <div class="form-group">
                        <label for="settings[smtp_server_address]" class="control-label">
                            <?php echo lang('smtp_server_address'); ?>
                        </label>
                        <input type="text" name="settings[smtp_server_address]" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="settings[smtp_authentication]">
                            <?php echo lang('smtp_requires_authentication'); ?>
                        </label>
                        <select name="settings[smtp_authentication]" class="form-control">
                            <option value="0">
                                <?php echo lang('no'); ?>
                            </option>
                            <option value="1">
                                <?php echo lang('yes'); ?>
                            </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="settings[smtp_username]" class="control-label">
                            <?php echo lang('smtp_username'); ?>
                        </label>
                        <input type="text" name="settings[smtp_username]" class="form-control">
                    </div>

                    <div class="form-group">
                        <label for="smtp_password" class="control-label">
                            <?php echo lang('smtp_password'); ?>
                        </label>
                        <input type="password" id="smtp_password" class="form-control" name="settings[smtp_password]">
                    </div>

                    <div class="form-group">
                        <div>
                            <label for="settings[smtp_port]" class="control-label">
                                <?php echo lang('smtp_port'); ?>
                            </label>
                            <input type="text" name="settings[smtp_port]" class="form-control">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="settings[smtp_security]" class="control-label">
                            <?php echo lang('smtp_security'); ?>
                        </label>
                        <select name="settings[smtp_security]" class="form-control">
                            <option value=""><?php echo lang('none'); ?></option>
                            <option value="ssl"><?php echo lang('smtp_ssl'); ?></option>
                            <option value="tls"><?php echo lang('smtp_tls'); ?></option>
                        </select>
                    </div>

                </div>
            </div>

            <button type="submit" class="btn btn-success" name="btn_continue" value="1">
                <i class="fa fa-angle-right fa-margin-right"></i><?php echo lang('continue'); ?>
            </button>

        </form>

    </div>
</div>
