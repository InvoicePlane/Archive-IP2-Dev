<script>
    $(document).ready(function () {
        $('#create-invoice').modal('show').on('shown.bs.modal', function () {
            $("#client_id").select2({
                language: '<?php //echo lang('cldr'); ?>de',
                ajax: {
                    method: 'post',
                    url: '<?php echo site_url('clients/clients_ajax/name_query'); ?>',
                    dataType: 'json',
                    delay: 300,
                    data: function (params) {
                        return {
                            client_name: params.term
                        };
                    },
                    processResults: function (data) {
                        return {
                            results: $.map(data, function (item) {
                                return {
                                    text: item.name,
                                    id: item.id
                                }
                            })
                        };
                    },
                    cache: true
                }
            });
        });


        // Creates the invoice
        $('#invoice_create_confirm').click(function () {
            // Posts the data to validate and create the invoice
            $.post("<?php echo site_url('invoices/invoices_ajax/create'); ?>", {
                    client_id: $('#client_id').val(),
                    date_created: $('#date_created').val(),
                    invoice_group_id: $('#invoice_group_id').val(),
                    pdf_password: $('#pdf_password').val()
                },
                function (data) {
                    console.log(data);
                    var response = JSON.parse(data);
                    if (response.success == '1') {
                        // The validation was successful and invoice was created
                        window.location = "<?php echo site_url('invoices/view'); ?>/" + response.invoice_id;
                    }
                    else {
                        // The validation was not successful
                        $('.form-group').removeClass('has-danger');
                        for (var key in response.validation_errors) {
                            $('#' + key).parent().addClass('has-danger');
                        }
                    }
                });
        });
    });
</script>

<div id="create-invoice" class="modal fade" role="dialog" aria-labelledby="modal_create_invoice" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal">
                    <i class="fa fa-close"></i>
                </button>
                <h4 class="modal-title"><?php echo lang('create_invoice'); ?></h4>

            </div>
            <div class="modal-body">

                <input class="hidden" id="payment_method_id"
                       value="<?php echo $this->mdl_settings->setting('default_payment_method'); ?>">

                <div class="form-group">
                    <label for="client_id"><?php echo lang('client'); ?></label>
                    <select name="client_id" id="client_id" class="form-control" width="100%">
                        <?php if (isset($client)) {
                            echo '<option value="' . $client['id'] . '">' . html_escape($client['name']) . '</option>';
                        } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="date_created"><?php echo lang('date'); ?></label>
                    <input type="date" name="date_created" id="date_created"
                           class="form-control datepicker">
                </div>

                <div class="form-group">
                    <label for="invoice_group_id"><?php echo lang('invoice_group'); ?></label>
                    <select name="invoice_group_id" id="invoice_group_id" class="form-control">
                        <?php foreach ($invoice_groups as $group) { ?>
                            <option value="<?php echo $group->id; ?>"
                                    <?php
                                    $invoice_group = $this->mdl_settings->setting('invoices.default_invoice_group');
                                    if ($invoice_group == $group->id) { ?>selected="selected"<?php } ?>>
                                <?php echo $group->name; ?>
                            </option>
                        <?php } ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="pdf_password"><?php echo lang('pdf_password'); ?></label>
                    <input type="text" name="pdf_password" id="pdf_password" class="form-control"
                           value="<?php
                           $pdf_password = $this->mdl_settings->setting('pre_password');
                           if ($pdf_password) {
                               echo $pdf_password;
                           } ?>" autocomplete="off">
                </div>

            </div>
            <div class="modal-footer">

                <div class="btn-group">
                    <button class="btn btn-danger" type="button" data-dismiss="modal">
                        <i class="fa fa-times fa-margin-right"></i> <?php echo lang('cancel'); ?>
                    </button>
                    <button class="btn btn-success ajax-loader" id="invoice_create_confirm" type="button">
                        <i class="fa fa-check fa-margin-right"></i> <?php echo lang('submit'); ?>
                    </button>
                </div>

            </div>

        </form>
    </div>
</div>
