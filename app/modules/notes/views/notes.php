<div class="notes">
    <h4><?php echo lang('notes'); ?></h4>

    <div class="notes-content">
        <?php $this->layout->load_view('notes/partial_notes', $notes); ?>
    </div>

    <div class="card card-block">
        <form>
            <div class="input-group">
                <textarea id="note-content" class="form-control" rows="2"></textarea>
                <div class="input-group-addon with-button" id="basic-addon2">
                    <button id="save-note" class="btn btn-primary btn-sm show-loader match-parent-height"
                            data-type="<?php echo $type; ?>" data-id="<?php echo $type_id; ?>">
                        <i class="fa fa-plus fa-margin-right"></i><?php echo lang('add_note'); ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>