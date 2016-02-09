<div id="setup" class="col-sm-10 col-sm-offset-1 col-md-8 col-md-offset-2">

    <h1 class="m-y-2">
        <img src="<?php echo THEME_URL;?>img/logo_200x100.png" alt="InvoicePlane">
    </h1>
    
    <div class="card">
        
        <div class="card-header">
            <?php echo lang('setup_choose_language'); ?>
        </div>

        <form class="card-block" method="post" action="<?php echo site_url($this->uri->uri_string()); ?>">

            <div class="form-group">
                <label for="ip_lang"><?php echo lang('setup_choose_language_message'); ?></label>
                <select name="ip_lang" class="form-control">
                    <?php foreach ($languages as $language) { ?>
                        <option value="<?php echo $language['value']; ?>">
                            <?php echo $language['label']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <button class="btn btn-success loader" type="submit" name="btn_continue" value="1">
                <i class="fa fa-angle-right fa-margin-right"></i><?php echo lang('continue'); ?>
            </button>

        </form>

    </div>
</div>

