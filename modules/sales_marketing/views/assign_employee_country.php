<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
               <div class="tw-mb-2 sm:tw-mb-4">
                    <a href="#" onclick="new_employee_country(); return false;" class="btn btn-primary">
                        <i class="fa-regular fa-plus tw-mr-1"></i>
                        <?php echo 'Assign Country To Employee'; ?>
                    </a>
                </div>

                <div class="panel_s">
                    <div class="panel-body panel-table-full">
                        <?php if (count($employeecountries) > 0) { ?>
                            <table class="table dt-table" data-order-col="3" data-order-type="asc">
                            <thead>
                                <th><?php echo _l('id'); ?></th>
                                <th><?php echo 'Employee Name'; ?></th>
                                <th><?php echo 'Country'; ?></th>
                                <th><?php echo _l('options'); ?></th>
                            </thead>
                            <tbody>
                                <?php foreach ($employeecountries as $employeecountry) { ?>
                                    <tr>
                                        <td><?php echo $employeecountry->employee_country_id; ?></td>
                                        <td><?php echo $employeecountry->employee_name; ?></td>
                                        <td><?php echo str_replace(',', ', ', $employeecountry->country_names); ?></td>
                                        <td>
                                            <div class="tw-flex tw-items-center tw-space-x-3">
                                               <a href="#"
                                                   onclick="edit_employee_country(this, <?php echo $employeecountry->employee_country_id; ?>); return false"
                                                   class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700"
                                                   data-name="<?php echo $employeecountry->country_names; ?>"
                                                   data-country_id="<?php echo $employeecountry->country_ids; ?>"
                                                   data-staff_id="<?php echo $employeecountry->staffid; ?>">
                                                    <i class="fa-regular fa-pen-to-square fa-lg"></i>
                                                </a>

                                                <a href="<?php echo admin_url('sales_marketing/delete_employee_country/' . $employeecountry->employee_country_id); ?>"
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
                            <p class="no-margin"><?php echo _l('No Country Assign To Employee'); ?></p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="employeecountry" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('sales_marketing/employee_countries')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title">Edit Employee Country</span>
                    <span class="add-title">New Employee Country</span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php echo render_select('staffid', $staff, ['staffid', 'fullname'], 'Employee Name'); ?>
                        <?php echo render_select('multiple_country_id[]', $countries, ['country_id', 'country_name'], 'Country Name', '', ['multiple' => true]); ?>
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
                $('#employeecountry input[name="country_name"]').val('');
                $('#employeecountry select[name="country_id"]').val('').trigger('change');
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

    function new_employee_country() {
        $('#employeecountry').modal('show');
        $('.edit-title').addClass('hide');
    }


function edit_employee_country(invoker, id) {
    let name = $(invoker).data('name');
    let country_ids = $(invoker).data('country_id').toString().split(','); // Ensure it's an array
    let staffId = $(invoker).data('staff_id');

    $('#employeecountry input[name="country_name"]').val(name);
    $('#employeecountry select[name="multiple_country_id[]"]').val(country_ids).trigger('change'); // Select multiple
    $('#employeecountry select[name="staffid"]').val(staffId).trigger('change');
    $('#additional').html(hidden_input('id', id));
    $('#employeecountry').modal('show');
    $('.add-title').addClass('hide');
    $('.edit-title').removeClass('hide');
}

</script>

</body>
</html>
