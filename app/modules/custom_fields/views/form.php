<form method="post" class="form-horizontal">

    <div id="headerbar">
        <h1 class="pull-left"><?php echo lang('add_custom_field'); ?></h1>
        <?php $this->layout->load_view('layout/includes/header_buttons'); ?>
    </div>

    <div id="content">

        <?php $this->layout->load_view('layout/includes/alerts'); ?>

        <div class="row">
            <div class="col-md-8 col-lg-6">
                <div class="form-group">
                    <label for="table"><?php echo lang('table'); ?></label>
                    <select name="table" id="table"
                            class="form-control">
                        <?php foreach ($custom_field_tables as $table => $label) { ?>
                            <option value="<?php echo $table; ?>"
                                    <?php if ($this->mdl_custom_fields->form_value('table') == $table) { ?>selected="selected"<?php } ?>>
                                <?php echo lang($label); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="type"><?php echo lang('type'); ?></label>

                    <select name="type" id="type"
                            class="form-control">
                        <?php foreach ($custom_field_types as $type => $label) { ?>
                            <option value="<?php echo $type; ?>"
                                    <?php if ($this->mdl_custom_fields->form_value('type') == $type) { ?>selected="selected"<?php } ?>>
                                <?php echo lang($label); ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="label"><?php echo lang('label'); ?></label>

                    <input type="text" name="label" id="label" class="form-control"
                           value="<?php echo $this->mdl_custom_fields->form_value('label'); ?>">
                </div>
            </div>
        </div>

    </div>

</form>