<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="tw-mb-2 sm:tw-mb-4">
                    <a href="#" onclick="new_kpi_function(); return false;" class="btn btn-primary">
                        <i class="fa-regular fa-plus tw-mr-1"></i>
                        <?php echo 'New KPI Function'; ?>
                    </a>
                </div>
                <div class="panel_s">
                    <div class="panel-body panel-table-full">
                        <?php if (count($kpifunctions) > 0) { ?>
                            <table class="table dt-table" data-order-col="3" data-order-type="asc">
                                <thead>
                                <th><?php echo _l('id'); ?></th>
                                <th><?php echo 'KPI'; ?></th>
                                <th><?php echo 'KPI Counting';?></th>
                                <th><?php echo _l('options'); ?></th>
                                </thead>
                                <tbody>
                                <?php foreach ($kpifunctions as $kpifunction) { ?>
                                    <tr>
                                        <td><?php echo $kpifunction->kpi_function_id; ?></td>
                                        <td><a href="#"
                                               onclick="edit_kpi_function(this,<?php echo $kpifunction->kpi_function_id; ?>); return false"
                                               data-count="<?php echo $kpifunction->kpi_count; ?>"
                                               data-name="<?php echo $kpifunction->kpi_name; ?>"
                                               >
                                                <?php echo $kpifunction->kpi_name; ?>
                                               
                                               </a><br/>
                                        </td>
                                        <td><?php echo $kpifunction->kpi_count; ?></td>
                                        <td>
                                            <div class="tw-flex tw-items-center tw-space-x-3">
                                                <a href="#"
                                                   onclick="edit_kpi_function(this,<?php echo $kpifunction->kpi_function_id; ?>); return false"
                                                   data-count="<?php echo $kpifunction->kpi_count; ?>"
                                                   data-name="<?php echo $kpifunction->kpi_name; ?>"
                                                   class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700">
                                                    <i class="fa-regular fa-pen-to-square fa-lg"></i>
                                                </a>
                                                <!--<a href="<?php echo admin_url('sales_marketing/delete_kpi_function/' . $kpifunction->kpi_function_id); ?>"-->
                                                <!--   class="tw-mt-px tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700 _delete">-->
                                                <!--    <i class="fa-regular fa-trash-can fa-lg"></i>-->
                                                <!--</a>-->
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        <?php } else { ?>
                            <p class="no-margin"><?php echo _l('stages_not_found'); ?></p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="kpi" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('sales_marketing/kpi_functions')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo 'Edit KPI Function'; //echo _l('edit_stage'); ?></span>
                    <span class="add-title"><?php echo 'New KPI Function'; //echo _l('new_stage'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php echo render_input('kpi_name', 'KPI Name'); ?>
                       <?php echo render_select('kpi_count', 
                        [
                            ['id' => 'Number', 'name' => 'Number'],
                            ['id' => 'Amount', 'name' => 'Amount'],
                            ['id' => 'Time', 'name' => 'Time']
                        ], 
                        ['id', 'name'], 
                        'KPI Counting'
                    ); ?>


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
        }, manage_deals_kpi);

        // Trigger change on page load to check if "Amount" is already selected
        $('#kpi select[name="kpi_count"]').trigger('change');       
       $('#kpi').on('hidden.bs.modal', function () {
            $('#additional').html('');
            $('#kpi input[name="kpi_name"]').val('');
            $('#kpi select[name="kpi_count"]').val('').trigger('change');
            $('.add-title').removeClass('hide');
            $('.edit-title').addClass('hide');
        });

    });

    function manage_deals_kpi(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function (response) {
            window.location.reload();
        });
        return false;
    }

    function new_kpi_function() {
        $('#kpi').modal('show');
        $('.edit-title').addClass('hide');
    }

function edit_kpi_function(invoker, id) {
    let name = $(invoker).data('name');
    let count = $(invoker).data('count');

    // Set the value of kpi_name input
    $('#kpi input[name="kpi_name"]').val(name);
    
    // Set the selected value of kpi_count dropdown
    $('#kpi select[name="kpi_count"]').val(count).trigger('change'); // Ensure the dropdown updates

    // Append the hidden input with the id
    $('#additional').html(hidden_input('id', id));
    
    // Show the modal
    $('#kpi').modal('show');
    
    // Hide the "Add" title and show the "Edit" title
    $('.add-title').addClass('hide');
    $('.edit-title').removeClass('hide');
}


</script>
</body>

</html>