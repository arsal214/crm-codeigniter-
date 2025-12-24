<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
               <div class="tw-mb-2 sm:tw-mb-4">
                    <a href="#" onclick="new_kpi_function(); return false;" class="btn btn-primary">
                        <i class="fa-regular fa-plus tw-mr-1"></i>
                        <?php echo 'New Employee KPI'; ?>
                    </a>
                </div>

                <div class="panel_s">
                    <div class="panel-body panel-table-full">
                        <?php if (count($employeekpis) > 0) { ?>
                            <table class="table dt-table" data-order-col="3" data-order-type="asc">
                            <thead>
                                <th><?php echo _l('id'); ?></th>
                                <th><?php echo 'KPI'; ?></th>
                                <th><?php echo 'Category'; ?></th>
                                <th><?php echo 'Employee Name'; ?></th>
                                <th><?php echo 'Currency'; ?></th>
                                <th><?php echo 'No of Actions'; ?></th>
                                <th><?php echo 'Day'; ?></th>
                                <th><?php echo 'Week'; ?></th>
                                <th><?php echo 'Month'; ?></th>
                                <th><?php echo 'Year'; ?></th>
                                <th><?php echo _l('options'); ?></th>
                            </thead>
                            <tbody>
                                <?php foreach ($employeekpis as $employeekpi) { ?>
                                    <tr>
                                        <td><?php echo $employeekpi->employee_kpi_id; ?></td>
                                        <td>
                                            <a href="#" 
                                               onclick="edit_kpi_function(this, <?php echo $employeekpi->employee_kpi_id; ?>); return false"
                                               data-currencyid="<?php echo $employeekpi->currencyid; ?>"
                                               data-category_id="<?php echo $employeekpi->category_id; ?>"
                                               data-name="<?php echo $employeekpi->kpi_name; ?>"
                                               data-name="<?php echo $employeekpi->category_name; ?>"
                                               data-day="<?php echo $employeekpi->day; ?>"
                                               data-week="<?php echo $employeekpi->week; ?>"
                                               data-month="<?php echo $employeekpi->month; ?>"
                                               data-year="<?php echo $employeekpi->year; ?>"
                                               data-count="<?php echo $employeekpi->no_of_actions; ?>"
                                               data-kpi_function_id="<?php echo $employeekpi->kpi_function_id; ?>"
                                               data-staff_id="<?php echo $employeekpi->staffid; ?>">
                                               <?php echo $employeekpi->kpi_name; ?>
                                            </a>
                                        </td>
                                        <td><?php echo $employeekpi->category_name; ?></td>
                                        <td><?php echo $employeekpi->employee_name; ?></td>
                                        <td><?php echo $employeekpi->symbol; ?></td>
                                        <td><?php echo $employeekpi->no_of_actions; ?></td>
                                        <td><?php echo $employeekpi->day; ?></td>
                                        <td><?php echo $employeekpi->week; ?></td>
                                        <td><?php echo $employeekpi->month; ?></td>
                                        <td><?php echo $employeekpi->year; ?></td>
                                        <td>
                                            <div class="tw-flex tw-items-center tw-space-x-3">
                                               <a href="#"
                                                   onclick="edit_kpi_function(this, <?php echo $employeekpi->employee_kpi_id; ?>); return false"
                                                   class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700"
                                                   data-currencyid="<?php echo $employeekpi->currencyid; ?>"
                                                   data-category_id="<?php echo $employeekpi->category_id; ?>"
                                                   data-name="<?php echo $employeekpi->kpi_name; ?>"
                                                   data-day="<?php echo $employeekpi->day; ?>"
                                                   data-week="<?php echo $employeekpi->week; ?>"
                                                   data-month="<?php echo $employeekpi->month; ?>"
                                                   data-year="<?php echo $employeekpi->year; ?>"
                                                   data-count="<?php echo $employeekpi->no_of_actions; ?>"
                                                   data-kpi_function_id="<?php echo $employeekpi->kpi_function_id; ?>"
                                                   data-staff_id="<?php echo $employeekpi->staffid; ?>"> 
                                                    <i class="fa-regular fa-pen-to-square fa-lg"></i>
                                                </a>

                                                <a href="<?php echo admin_url('sales_marketing/delete_employee_kpi/' . $employeekpi->employee_kpi_id); ?>"
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
                            <p class="no-margin"><?php echo _l('employee_kpi_not_found'); ?></p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="kpi" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('sales_marketing/employee_kpis')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title">Edit Employee KPI</span>
                    <span class="add-title">New Employee KPI</span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php echo render_select('kpi_function_id', $kpi_functions, ['kpi_function_id', 'kpi_name'], 'KPI Function'); ?>
                        <?php echo render_select('staffid', $staff, ['staffid', 'fullname'], 'Employee Name'); ?>
                        <div class="form-group">
                            <?php echo render_select('category_id', $categories, ['category_id', 'name'], 'Select Category', $selected_category_id, ['category_id' => 'category_select']); ?>
                        </div>
                        <div class="form-group" id="currency_container">
                            <?php echo render_select('currencyid', $currencies, ['id', 'symbol'], 'Select Currency', $selected_currency_id, ['id' => 'currency_select']); ?>
                        </div>

                        <div class="form-group">
                            <label for="no_of_actions">No of Actions</label>
                            <input type="number" id="no_of_actions" name="no_of_actions" class="form-control" oninput="calculateValues()" />
                        </div>
                        <div class="form-group">
                            <label for="day">Day</label>
                            <input type="text" id="day" name="day" class="form-control" readonly />
                        </div>
                        <div class="form-group">
                            <label for="week">Week</label>
                            <input type="text" id="week" name="week" class="form-control" readonly />
                        </div>
                        <div class="form-group">
                            <label for="month">Month</label>
                            <input type="text" id="month" name="month" class="form-control" readonly />
                        </div>
                        <div class="form-group">
                            <label for="year">Year</label>
                            <input type="text" id="year" name="year" class="form-control" readonly />
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
            $('#currency_container').hide(); // Default hide

            $('#kpi select[name="kpi_function_id"]').on('change', function() {
                var kpi_function_id = $(this).val(); // Get selected KPI function ID
            
                if (kpi_function_id) {
                    $.ajax({
                        url: "<?php echo admin_url('sales_marketing/get_kpi_count'); ?>",
                        type: 'POST',
                        data: { kpi_function_id: kpi_function_id },
                        dataType: 'json',
                        success: function(response) {
                            if (response.kpi_count === 'Amount') {
                                $('#currency_container').show();
                            } else {
                                $('#currency_container').hide();
                            }
                        },
                        error: function() {
                            console.error("Error fetching KPI count");
                        }
                    });
                } else {
                    $('#currency_container').hide();
                }
            });


        
            $('#kpi').on('hidden.bs.modal', function () {
                $('#additional').html(''); // Clear additional hidden inputs
                $('#kpi input[name="kpi_name"]').val('');
                $('#kpi input[name="no_of_actions"]').val('').attr('type', 'text').removeAttr('min max'); // Reset type
                $('#kpi select[name="kpi_function_id"]').val('').trigger('change');
                $('#kpi select[name="staffid"]').val('').trigger('change');
                $('#kpi input[name="day"]').val('');
                $('#kpi input[name="week"]').val('');
                $('#kpi input[name="month"]').val('');
                $('#kpi input[name="year"]').val('');
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


$(document).ready(function () {
    $('select[name="kpi_function_id"]').change(function () {
        var kpiFunctionId = $(this).val();
        if (kpiFunctionId) {
            $.ajax({
                url: "<?php echo admin_url('sales_marketing/get_kpi_count'); ?>",
                type: "POST",
                data: { kpi_function_id: kpiFunctionId },
                dataType: "json",
                success: function (response) {
                    if (response.status) {
                        adjustNoOfActionsField(response.kpi_count);
                    }
                }
            });
        }
    });
});
function adjustNoOfActionsField(kpi_count, selectedValue = null) {
    var fieldContainer = $('#no_of_actions').parent(); // Get parent container
    var inputField = $('#no_of_actions');

    if (kpi_count === "Time") {
        // Replace input with select if needed
        if (!inputField.is('select')) {
            inputField.replaceWith('<select id="no_of_actions" name="no_of_actions" class="form-control"></select>');
        }

        var selectField = $('#no_of_actions');
        selectField.empty(); // Clear existing options

        for (var i = 0; i <= 24; i++) {
            selectField.append(`<option value="${i}">${i}</option>`);
        }

        if (selectedValue !== null) {
            selectField.val(selectedValue); // Set previous value
        }
        selectField.change(function () {
            calculateValues(); // Recalculate values when select option changes
        });
    } else {
        // Replace select with input if needed
        if (!inputField.is('input')) {
            $('#no_of_actions').replaceWith('<input type="text" id="no_of_actions" name="no_of_actions" class="form-control" />');
        }

        inputField = $('#no_of_actions'); // Reassign after replacing

        if (kpi_count === "Number") {
            inputField.attr('type', 'number');
            inputField.removeAttr('max');
        } else {
            inputField.attr('type', 'text');
            inputField.removeAttr('min max');
        }

        if (selectedValue !== null) {
            inputField.val(selectedValue);
        }
         // Attach oninput handler for number and text input
        inputField.on('input', function () {
            calculateValues(); // Recalculate values when input changes
        });
    }
}


function edit_kpi_function(invoker, id) {
    let currencyid = $(invoker).data('currencyid');  // Assuming 'currencyid' is available on the invoker
    let category_id = $(invoker).data('category_id'); 
    let name = $(invoker).data('name');
    let count = $(invoker).data('count');
    let kpiFunctionId = $(invoker).data('kpi_function_id');
    let staffId = $(invoker).data('staff_id');
    let day = $(invoker).data('day'); // Assuming 'action_unit' is available in the data
    let week = $(invoker).data('week'); // Assuming 'action_unit' is available in the data
    let month = $(invoker).data('month'); // Assuming 'action_unit' is available in the data
    let year = $(invoker).data('year'); // Assuming 'action_unit' is available in the data

    $('#kpi input[name="kpi_name"]').val(name);
    $('#kpi select[name="currencyid"]').val(currencyid).trigger('change');  // Select the correct currency
    $('#kpi select[name="category_id"]').val(category_id).trigger('change');  // Select the correct currency
    $('#kpi input[name="day"]').val(day);
    $('#kpi input[name="week"]').val(week);
    $('#kpi input[name="month"]').val(month);
    $('#kpi input[name="year"]').val(year);
    $('#kpi select[name="kpi_function_id"]').val(kpiFunctionId).trigger('change');
    $('#kpi select[name="staffid"]').val(staffId).trigger('change');
    $('#additional').html(hidden_input('id', id));

    $.post("<?php echo admin_url('sales_marketing/get_kpi_count'); ?>", 
    { kpi_function_id: kpiFunctionId }, function(response) {
        if (response.status) {
            adjustNoOfActionsField(response.kpi_count, count);
        }
    }, "json");

    $('#kpi').modal('show');
    $('.add-title').addClass('hide');
    $('.edit-title').removeClass('hide');
}
function calculateValues() {
    var no_of_actions = document.getElementById('no_of_actions').value;

    // Calculate Day, Week, Month, Year based on the number of actions
    document.getElementById('day').value = no_of_actions;
    document.getElementById('week').value = no_of_actions * 5;
    document.getElementById('month').value = no_of_actions * 22;
    document.getElementById('year').value = no_of_actions * 260;
}
</script>

</body>
</html>
