<script>
    $(function () {
        $('#name').focus();
        $("#country").select2({
            placeholder: "",
            allowClear: true
        });
    });
</script>

<form method="post">

    <div id="headerbar">
        <h1><?php echo lang('add_client'); ?></h1>
        <?php $this->layout->load_view('layout/includes/header_buttons'); ?>
    </div>

    <div id="content">

        <?php echo $this->layout->load_view('layout/includes/alerts'); ?>

        <input class="hidden" name="is_update" type="hidden"
               value="<?php echo($this->mdl_clients->form_value('is_update') ? 1 : 0) ?>">

        <div class="row">

            <div class="col-md-6">

                <h4><?php echo lang('personal_information'); ?></h4>

                <div class="form-group">
                    <div class="input-group">
                        <span class="input-group-addon" id="basic-addon1">
                            <?php echo lang('active_client'); ?>&nbsp;
                            <input id="is_active" name="is_active" type="checkbox" value="1"
                                <?php if ($this->mdl_clients->form_value('active')) : ?>
                                    checked="checked"
                                <?php endif; ?>>
                        </span>
                        <input id="name" name="name" type="text" class="form-control"
                               placeholder="<?php echo lang('name'); ?>"
                               value="<?php echo $this->mdl_clients->form_value('name', true); ?>">
                    </div>
                </div>
            </div>

        </div>

        <div class="row">

            <div class="col-md-6">

                <h4><?php echo lang('address'); ?></h4>

                <div class="form-group">
                    <label for="address_1"><?php echo lang('street_address'); ?></label>
                    <input type="text" name="address_1" id="address_1" class="form-control"
                           value="<?php echo $this->mdl_clients->form_value('address_1', true); ?>">
                </div>

                <div class="form-group">
                    <label for="address_2"><?php echo lang('street_address_2'); ?></label>
                    <input type="text" name="address_2" id="address_2" class="form-control"
                           value="<?php echo $this->mdl_clients->form_value('address_2', true); ?>">
                </div>

                <div class="form-group">
                    <label for="city"><?php echo lang('city'); ?></label>
                    <input type="text" name="city" id="city" class="form-control"
                           value="<?php echo $this->mdl_clients->form_value('city', true); ?>">
                </div>

                <div class="form-group">
                    <label for="state"><?php echo lang('state'); ?></label>
                    <input type="text" name="state" id="state" class="form-control"
                           value="<?php echo $this->mdl_clients->form_value('state', true); ?>">
                </div>

                <div class="form-group">
                    <label for="zip"><?php echo lang('zip_code'); ?></label>
                    <input type="text" name="zip" id="zip" class="form-control"
                           value="<?php echo $this->mdl_clients->form_value('zip', true); ?>">
                </div>

                <div class="form-group">
                    <label for="country"><?php echo lang('country'); ?></label>
                    <select name="country" id="country" class="form-control">
                        <option></option>
                        <?php foreach ($countries as $cldr => $country) : ?>
                            <option value="<?php echo $cldr; ?>"
                                <?php if ($selected_country == $cldr) : ?>
                                    selected="selected"
                                <?php endif; ?>>
                                <?php echo $country ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

            </div>

            <div class="col-md-6">

                <h4><?php echo lang('contact_information'); ?></h4>

                <div class="form-group">
                    <label for="phone"><?php echo lang('phone_number'); ?></label>
                    <input type="text" name="phone" id="phone" class="form-control"
                           value="<?php echo $this->mdl_clients->form_value('phone', true); ?>">
                </div>

                <div class="form-group">
                    <label for="fax"><?php echo lang('fax_number'); ?></label>
                    <input type="text" name="fax" id="fax" class="form-control"
                           value="<?php echo $this->mdl_clients->form_value('fax', true); ?>">
                </div>

                <div class="form-group">
                    <label for="mobile"><?php echo lang('mobile_number'); ?></label>
                    <input type="text" name="mobile" id="mobile" class="form-control"
                           value="<?php echo $this->mdl_clients->form_value('mobile', true); ?>">
                </div>

                <div class="form-group">
                    <label for="email"><?php echo lang('email_address'); ?></label>
                    <input type="text" name="email" id="email" class="form-control"
                           value="<?php echo $this->mdl_clients->form_value('email', true); ?>">
                </div>

                <div class="form-group">
                    <label for="web"><?php echo lang('web_address'); ?></label>
                    <input type="text" name="web" id="web" class="form-control"
                           value="<?php echo $this->mdl_clients->form_value('web', true); ?>">
                </div>

            </div>

        </div>

        <div class="row">

            <div class="col-md-6">

                <legend><?php echo lang('tax_information'); ?></legend>

                <div class="form-group">
                    <label for="vat_id"><?php echo lang('vat_id'); ?></label>
                    <input type="text" name="vat_id" id="vat_id" class="form-control"
                           value="<?php echo $this->mdl_clients->form_value('vat_id', true); ?>">
                </div>

                <div class="form-group">
                    <label for="tax_code"><?php echo lang('tax_code'); ?></label>
                    <input type="text" name="tax_code" id="tax_code" class="form-control"
                           value="<?php echo $this->mdl_clients->form_value('tax_code', true); ?>">
                </div>

            </div>

        </div>

        <?php if ($custom_fields) : ?>

            <h4><?php echo lang('custom_fields'); ?></h4>

            <?php foreach ($custom_fields as $custom_field) : ?>

                <div class="form-group">
                    <label for="custom[<?php echo $custom_field->custom_field_column; ?>]">
                        <?php echo $custom_field->custom_field_label; ?>
                    </label>

                    <?php switch ($custom_field->custom_field_type) {
                        case 'input':
                            $column = $custom_field->custom_field_column;
                            $value = $this->mdl_clients->form_value('custom[' . $column . ']', true);
                            ?>
                            <input type="text" class="form-control"
                                   name="custom[<?php echo $custom_field->custom_field_column; ?>]"
                                   id="<?php echo $custom_field->custom_field_column; ?>"
                                   value="<?php echo $value; ?>">
                            <?php break;
                        case 'textarea':
                            $column = $custom_field->custom_field_column;
                            $value = $this->mdl_clients->form_value('custom[' . $column . ']', true);
                            ?>
                            <textarea name="custom[<?php echo $custom_field->custom_field_column; ?>]"
                                      id="<?php echo $custom_field->custom_field_column; ?>"
                                      class="form-control"><?php echo $value; ?></textarea>
                            <?php break;
                    } ?>
                </div>

            <?php endforeach; ?>

        <?php endif; ?>
    </div>
</form>
