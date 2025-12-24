<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="tw-mb-2 sm:tw-mb-4">
                    <a href="#" onclick="new_status(); return false;" class="btn btn-primary">
                        <i class="fa-regular fa-plus tw-mr-1"></i>
                        <?php echo 'New Status'; //echo _l('new_status'); ?>
                    </a>
                </div>
                <div class="panel_s">
                    <div class="panel-body panel-table-full">
                        <?php if (count($statuses) > 0) { ?>
                            <table class="table dt-table" data-order-col="3" data-order-type="asc">
                                <thead>
                                    <th><?php echo _l('id'); ?></th>
                                    <th><?php echo 'Status'; ?></th>
                                    <th><?php echo 'Color'; ?></th> <!-- Add Color column -->
                                    <th><?php echo _l('options'); ?></th>
                                </thead>
                                <tbody>
                                    <?php foreach ($statuses as $status) { ?>
                                        <tr>
                                            <td><?php echo $status->status_id; ?></td>
                                            <td>
                                                <a href="#"
                                                   onclick="edit_status(this, <?php echo $status->status_id; ?>); return false"
                                                   data-name="<?php echo $status->status_name; ?>"
                                                   data-color="<?php echo $status->color; ?>"><?php echo $status->status_name; ?></a><br/>
                                            </td>
                                            <!-- Display the color in the color column -->
                                            <td>
                                                <?php if ($status->color) { ?>
                                                    <div style="width: 20px; height: 20px; background-color: <?php echo $status->color; ?>;"></div>
                                                <?php } else { ?>
                                                    <span>No color</span>
                                                <?php } ?>
                                            </td>
                                            <td>
                                                <div class="tw-flex tw-items-center tw-space-x-3">
                                                    <a href="#"
                                                       onclick="edit_status(this, <?php echo $status->status_id; ?>); return false"
                                                       data-name="<?php echo $status->status_name; ?>"
                                                       data-color="<?php echo $status->color; ?>"
                                                       class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700">
                                                        <i class="fa-regular fa-pen-to-square fa-lg"></i>
                                                    </a>
                                                    <a href="<?php echo admin_url('sales_marketing/delete_status/' . $status->status_id); ?>"
                                                       class="tw-mt-px tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700 _delete">
                                                        <i class="fa-regular fa-trash-can fa-lg"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        <?php } else { ?>
                            <p class="no-margin"><?php echo _l('status_not_found'); ?></p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="status" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('sales_marketing/statuses')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo 'Edit Status';?></span>
                    <span class="add-title"><?php echo 'New Status'; //echo _l('new_status'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
               <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php echo render_input('name', 'Status Name'); ?>
                        <!-- Add color input field -->
                        <?php echo render_input('color', 'Color', '', 'color'); ?>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
            </div>
        </div>
        <!-- /.modal-content -->
        <?php echo form_close(); ?>
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<?php init_tail(); ?>
<script>
    'use strict';
    $(function () {
        appValidateForm($('form'), {
            name: 'required'
        }, manage_deals_status);
        $('#status').on('hidden.bs.modal', function (event) {
            $('#additional').html('');
            $('#status input[name="name"]').val('');
            $('#status input[name="color"]').val(color); // Set the color input value
            $('.add-title').removeClass('hide');
            $('.edit-title').removeClass('hide');
        });
    });

    function manage_deals_status(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function (response) {
            window.location.reload();
        });
        return false;
    }

    function new_status() {
        $('#status').modal('show');
        $('.edit-title').addClass('hide');
    }

  function edit_status(invoker, id) {
    let name = $(invoker).data('name');
    let color = $(invoker).data('color'); // Get the color value
    $('#additional').append(hidden_input('id', id));
    $('#status input[name="name"]').val(name);
    $('#status input[name="color"]').val(color); // Set the color input value
    $('#status').modal('show');
    $('.add-title').addClass('hide');
}

</script>
</body>

</html>