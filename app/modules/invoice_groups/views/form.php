<form method="post" class="form-horizontal">

    <div id="headerbar">
        <h1><?php echo lang('add_invoice_group'); ?></h1>
        <?php $this->layout->load_view('layout/includes/header_buttons'); ?>
    </div>

    <div id="content">

        <div class="row">
            <div class="col-md-6">

                <?php $this->layout->load_view('layout/includes/alerts'); ?>

                <div class="form-group">
                    <label for="name"><?php echo lang('name'); ?></label>
                    <input type="text" name="name" id="name" class="form-control"
                           value="<?php echo $this->mdl_invoice_groups->form_value('name'); ?>">
                </div>

                <div class="form-group">
                    <label for="next_id"><?php echo lang('next_id'); ?></label>
                    <input type="number" min="0" name="next_id" id="next_id" class="form-control"
                           value="<?php echo $this->mdl_invoice_groups->form_value('next_id'); ?>">
                </div>

                <div class="form-group">
                    <label for="left_pad"><?php echo lang('left_pad'); ?></label>
                    <input type="number" min="0" name="left_pad" id="left_pad" class="form-control"
                           value="<?php echo $this->mdl_invoice_groups->form_value('left_pad'); ?>">
                    <small class="text-muted"><?php echo lang('left_pad_help'); ?></small>
                </div>

                <div class="form-group">
                    <label for="identifier_format"><?php echo lang('identifier_format'); ?></label>
                    <input type="text" class="form-control taggable"
                           name="identifier_format" id="identifier_format"
                           value="<?php echo $this->mdl_invoice_groups->form_value('identifier_format'); ?>"
                           placeholder="INV-{{{id}}}">
                    <small class="text-muted"><?php echo lang('identifier_format_help'); ?></small>
                </div>

                <div class="form-group">
                    <h4><?php echo lang('identifier_format_template_tags'); ?></h4>

                    <p><?php echo lang('identifier_format_template_tags_instructions'); ?></p>


                    <div class="table-respnsive">
                        <table class="table table-sm table-bordered">
                            <tr>
                                <th><?php echo lang('tag'); ?></th>
                                <th><?php echo lang('example'); ?></th>
                            </tr>
                            <tr>
                                <td>
                                    <a href="#" data-target="#identifier_format" data-tag="{{{id}}}">
                                        <?php echo lang('id_quote_invoice'); ?>
                                    </a>
                                </td>
                                <td>
                                    <?php
                                    $next_id = ($this->mdl_invoice_groups->form_value('next_id') ? $this->mdl_invoice_groups->form_value('next_id') : 1);
                                    echo $this->mdl_invoice_groups->format_id($next_id, $this->mdl_invoice_groups->form_value('left_pad')); ?>
                                </td>
                            </tr>
                            <?php
                            $tags = $this->mdl_invoice_groups->template_tags();
                            foreach ($tags as $tag => $tag_details) :
                                ?>
                                <tr>
                                    <td>
                                        <a href="#" data-target="#identifier_format"
                                           data-tag="{{{<?php echo $tag; ?>}}}">
                                            <?php echo lang($tag_details['lang']); ?>
                                        </a>
                                    </td>
                                    <td>
                                        <?php echo $tag_details['formatting']; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                </div>

            </div>
        </div>

    </div>
</form>
