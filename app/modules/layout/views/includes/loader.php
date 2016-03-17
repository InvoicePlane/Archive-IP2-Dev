<div id="loader">
    <div id="loader-indicator">
        <?php echo lang('loading'); ?>
        <div class="loader-cube">
            <div class="sk-cube1 sk-cube"></div>
            <div class="sk-cube2 sk-cube"></div>
            <div class="sk-cube4 sk-cube"></div>
            <div class="sk-cube3 sk-cube"></div>
        </div>
    </div>
    <div id="loader-error" class="loader-content">
        <div class="loader-message">
            <?php echo lang('loading_error'); ?><br/>
            <a href="https://wiki.invoiceplane.com/en/1.0/general/faq"
               class="btn btn-success" target="_blank">
                <i class="fa fa-support fa-margin-right"></i> <?php echo lang('loading_error_help'); ?>
            </a>
        </div>
    </div>
</div>