<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
               <div class="tw-mb-2 sm:tw-mb-4">
                    <a href="#" onclick="new_employee_overall(); return false;" class="btn btn-primary">
                        <i class="fa-regular fa-plus tw-mr-1"></i>
                        <?php echo 'OverallPerformance'; ?>
                    </a>
                </div>

                <div class="panel_s">
                    <div class="panel-body panel-table-full">
                        <?php if (count($employeeoverallperformances) > 0) { ?>
                            <table class="table" id="customers_table" data-order-col="3" data-order-type="asc">
                            <thead>
                                <th><?php echo _l('id'); ?></th>
                                <th><?php echo 'Employee Name'; ?></th>
                                <th><?php echo 'Kpi Name'; ?></th>
                                <th><?php echo 'Actual Weight'; ?></th>
                                <th><?php echo _l('options'); ?></th>
                            </thead>
                            <tbody>
                                <?php foreach ($employeeoverallperformances as $employeecountry) { ?>
                                    <tr>
                                        <td><?php echo $employeecountry->overallperformance_id; ?></td>
                                        <td><?php echo $employeecountry->employee_name; ?></td>
                                        <td><?php echo $employeecountry->kpi_name; ?></td>
                                        <td><?php echo $employeecountry->kpi_weight; ?></td>
                                        <td>
                                            <div class="tw-flex tw-items-center tw-space-x-3">
                                               <a href="#"
                                                   onclick="edit_employee_performance(this, <?php echo $employeecountry->overallperformance_id; ?>); return false"
                                                   class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700"
                                                   data-kpi_function_id="<?php echo $employeecountry->kpi_function_id; ?>"
                                                   data-kpi_weight="<?php echo $employeecountry->kpi_weight; ?>"
                                                   data-staff_id="<?php echo $employeecountry->staffid; ?>">
                                                    <i class="fa-regular fa-pen-to-square fa-lg"></i>
                                                </a>

                                                <a href="<?php echo admin_url('sales_marketing/delete_overallperformance/' . $employeecountry->overallperformance_id); ?>"
                                                   class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700 _delete">
                                                    <i class="fa-regular fa-trash-can fa-lg"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                        <?php } else { ?>
                            <p class="no-margin"><?php echo _l('No OverAll performance'); ?></p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="employeecountry" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('sales_marketing/add_overall')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title">Edit Overallperformance</span>
                    <span class="add-title">New Overallperformance</span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php echo render_select('staffid', $staff, ['staffid', 'fullname'], 'Employee Name'); ?>
                        <?php echo render_select('kpi_function_id', $kpi_function, ['kpi_function_id', 'kpi_name'], 'Kpi Name'); ?>
                        <!-- Add input field for kpi_function_id -->
                        <div class="form-group">
                            <label for="kpi_weight">Actual Weight</label>
                            <input type="text" class="form-control" name="kpi_weight" id="kpi_weight" required>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<!-- /.modal -->
<?php init_tail(); ?>
<script>
    'use strict';
      $(function () {
            appValidateForm($('form'), {
                name: 'required'
            }, manage_deals_kpi);
        
            $('#employeecountry').on('hidden.bs.modal', function () {
                $('#additional').html(''); // Clear additional hidden inputs
                $('#employeecountry input[name="kpi_weight"]').val('');
                $('#employeecountry select[name="kpi_function_id"]').val('').trigger('change');
                $('#employeecountry select[name="staffid"]').val('').trigger('change');
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

    function new_employee_overall () {
        $('#employeecountry').modal('show');
        $('.edit-title').addClass('hide');
    }


function edit_employee_performance(invoker, id) {
    let overallId = $(invoker).data('kpi_function_id');
    let staffId = $(invoker).data('staff_id');
    let kpi_weight = $(invoker).data('kpi_weight'); // Corrected to use 'employee_id' instead of 'employee_desktime_id'
    $('#employeecountry select[name="kpi_function_id"]').val(overallId).trigger('change');
    $('#employeecountry select[name="staffid"]').val(staffId).trigger('change');
    $('#employeecountry input[name="kpi_weight"]').val(kpi_weight);
    
    $('#additional').html(hidden_input('id', id));
    
    // Show the modal and switch to Edit mode
    $('#employeecountry').modal('show');
    $('.add-title').addClass('hide');
    $('.edit-title').removeClass('hide');
}


</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>

<script>
  $(document).ready(function() {
    $('#customers_table').DataTable({
        "order": [[ 0, 'asc' ]], // S.No کے مطابق ترتیب دینا
        "pageLength": 25 // فی صفحہ 25 ریکارڈز دکھائیں
    });
});

</script>
</body>
</html>
