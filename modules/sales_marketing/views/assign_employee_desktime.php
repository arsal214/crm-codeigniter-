<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
               <div class="tw-mb-2 sm:tw-mb-4">
                    <a href="#" onclick="new_employee_desktime(); return false;" class="btn btn-primary">
                        <i class="fa-regular fa-plus tw-mr-1"></i>
                        <?php echo 'Desktime'; ?>
                    </a>
                </div>

                <div class="panel_s">
                    <div class="panel-body panel-table-full">
                        <?php if (count($employeedesktimes) > 0) { ?>
                            <table class="table dt-table" data-order-col="3" data-order-type="asc">
                            <thead>
                                <th><?php echo _l('id'); ?></th>
                                <th><?php echo 'Employee Name'; ?></th>
                                <th><?php echo 'Employee Desktime ID'; ?></th>
                                <th><?php echo _l('options'); ?></th>
                            </thead>
                            <tbody>
                                <?php foreach ($employeedesktimes as $employeecountry) { ?>
                                    <tr>
                                        <td><?php echo $employeecountry->desktime_id; ?></td>
                                        <td><?php echo $employeecountry->employee_name; ?></td>
                                        <td><?php echo $employeecountry->employee_desktime_id; ?></td>
                                        <td>
                                            <div class="tw-flex tw-items-center tw-space-x-3">
                                               <a href="#"
                                                   onclick="edit_employee_country(this, <?php echo $employeecountry->desktime_id; ?>); return false"
                                                   class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700"
                                                   data-employee_id="<?php echo $employeecountry->employee_desktime_id; ?>"
                                                   data-staff_id="<?php echo $employeecountry->staffid; ?>">
                                                    <i class="fa-regular fa-pen-to-square fa-lg"></i>
                                                </a>

                                                <a href="<?php echo admin_url('sales_marketing/delete_desktime/' . $employeecountry->desktime_id); ?>"
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
                            <p class="no-margin"><?php echo _l('No Desktime'); ?></p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="employeecountry" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('sales_marketing/add_desktime')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title">Edit Desktime</span>
                    <span class="add-title">New Desktime</span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php echo render_select('staffid', $staff, ['staffid', 'fullname'], 'Employee Name'); ?>
                        
                        <!-- Add input field for employee_desktime_id -->
                        <div class="form-group">
                            <label for="employee_desktime_id">Employee Desktime ID</label>
                            <input type="number" class="form-control" name="employee_desktime_id" id="employee_desktime_id" required>
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
                $('#employeecountry input[name="employee_desktime_id"]').val('');
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

    function new_employee_desktime () {
        $('#employeecountry').modal('show');
        $('.edit-title').addClass('hide');
    }


function edit_employee_country(invoker, id) {
    // Retrieve the necessary data attributes for the edit operation
    let desktimeId = $(invoker).data('employee_id'); // Corrected to use 'employee_id' instead of 'employee_desktime_id'
    let staffId = $(invoker).data('staff_id');
    
    // Set the employee desktime ID and staff ID in the modal fields
    $('#employeecountry input[name="employee_desktime_id"]').val(desktimeId);
    $('#employeecountry select[name="staffid"]').val(staffId).trigger('change');
    
    // Optionally, add hidden input for the record ID
    $('#additional').html(hidden_input('id', id));
    
    // Show the modal and switch to Edit mode
    $('#employeecountry').modal('show');
    $('.add-title').addClass('hide');
    $('.edit-title').removeClass('hide');
}


</script>

</body>
</html>
