<div class="panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal">
        <span aria-hidden="true">×</span><span class="sr-only">Close</span>
        </button>
        <h3 class="panel-title">Timesheet</h3>
    </div>
    <div class="panel-body" style="padding-top:0">
        <?php 
        if (total_rows(db_prefix() . '_sam_taskstimers', ['end_time' => null, 'staff_id !=' => get_staff_user_id(), 'sam_id' => $sam_id]) > 0) {
    /*                            $startedTimers = $this->Sam_tasks_model->get_timers($sam_id, ['staff_id !=' => get_staff_user_id(), 'end_time' => null]);

            $usersWorking = '';

            foreach ($startedTimers as $t) {
                $usersWorking .= '<b>' . e(get_staff_full_name($t['staff_id'])) . '</b>, ';
            }

            $usersWorking = rtrim($usersWorking, ', '); */
        ?>
            <div class="alert alert-info">
                <?php 
                //echo _l((count($startedTimers) == 1 ? 'task_users_working_on_tasks_single' : 'task_users_working_on_tasks_multiple'), $usersWorking);
                ?>
            </div>
        <?php
        } 
        ?>
        <div id="task_single_timesheets">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th class="tw-text-sm"><?php echo _l('timesheet_user'); ?></th>
                            <th class="tw-text-sm"><?php echo _l('timesheet_start_time'); ?></th>
                            <th class="tw-text-sm"><?php echo _l('timesheet_end_time'); ?></th>
                            <th class="tw-text-sm"><?php echo _l('timesheet_time_spend'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $timers_found = false;
                        foreach ($timers as $timesheet) { ?>
                            <?php
                                $timers_found = true; ?>
                                <tr>
                                    <td class="tw-text-sm">
                                        <?php if ($timesheet['note']) {
                                            echo '<i class="fa fa-comment" data-html="true" data-placement="right" data-toggle="tooltip" data-title="' . e($timesheet['note']) . '"></i>';
                                        } ?>
                                        <a href="<?php echo admin_url('staff/profile/' . $timesheet['staff_id']); ?>"
                                        target="_blank">
                                            <?php echo get_staff_full_name($timesheet['staff_id']); ?>
                                        </a>
                                    </td>
                                    <td class="tw-text-sm"><?php echo e(_dt($timesheet['start_time'], true)); ?></td>
                                    <td class="tw-text-sm">
                                        <?php
                                        if ($timesheet['end_time'] !== null) {
                                            echo e(_dt($timesheet['end_time'], true));
                                        } else {
                                            
                                        } ?>
                                    </td>
                                    <td class="tw-text-sm">
                                        <div class="tw-flex">
                                            <div class="tw-grow">
                                                <?php
                                                $time_spent = $timesheet['time_spent'];
                                                if ($time_spent == null || $timesheet['end_time'] == null) {                                                  
                                                  //if (0) {
                                                      echo _l('time_h') . ': ' . e(seconds_to_time_format(time() - $timesheet['start_time'])) . '<br />';
                                                      echo _l('time_decimal') . ': ' . e(sec2qty(time() - $timesheet['start_time'])) . '<br />';
                                                  } else {
                                                      echo _l('time_h') . ': ' . e(seconds_to_time_format($time_spent)) . '<br />';
                                                      echo _l('time_decimal') . ': ' . e(sec2qty($time_spent)) . '<br />';
                                                  } ?>
                                            </div>
                                            <?php
                                            if (1) { 
                                            ?>
                                                <div class="tw-flex tw-items-center tw-shrink-0 tw-self-start tw-space-x-1.5 tw-ml-2">
                                                <?php
                                                    $timesheet_id = $timesheet['id'];
                                                    if($timesheet['staff_id'] == get_staff_user_id()) { 
                                                    ?> 
                                                        <a href="#" onclick="remove_timesheet_item(<?=$timesheet_id?>)" class="text-danger">
                                                            <i class="fa fa-remove"></i>
                                                        </a>
                                                    <?php
                                                    }
                                                    if ($timesheet['staff_id'] == get_staff_user_id()) {
                                                        echo '<a href="#" class="sam-edit-timesheet text-info" data-toggle="tooltip" data-title="' . _l('edit') . '" data-timesheet-id="' . $timesheet['id'] . '">
                                                            <i class="fa fa-edit"></i>
                                                        </a>';
                                                    }
                                                    ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="timesheet-edit task-modal-edit-timesheet-<?php echo $timesheet['id'] ?> hide"
                                        colspan="5">
                                        <form class="task-modal-edit-timesheet-form">
                                            <input type="hidden" name="timer_id" value="<?php echo $timesheet['id'] ?>">
                                            <div class="timesheet-start-end-time">
                                                <div class="col-md-6">
                                                    <?php echo render_datetime_input('start_time', 'task_log_time_start', _dt($timesheet['start_time'], true)); ?>
                                                </div>
                                                <div class="col-md-6">
                                                    <?php echo render_datetime_input('end_time', 'task_log_time_end', _dt($timesheet['end_time'], true)); ?>
                                                </div>
                                            </div>
                                            <div class="col-md-12">
                                                <div class="form-group">
                                                    <label class="control-label">
                                                        <?php echo _l('task_single_log_user'); ?>
                                                    </label>
                                                    <br />
                                                    <select name="single_timesheet_staff_id" class="selectpicker" data-width="100%">
                                                        <option value="<?=get_staff_user_id()?>" selected="selected"><?=get_staff_full_name(get_staff_user_id())?></option>
                                                    </select>
                                                </div>
                                                <?php //echo render_textarea('note', 'note', $timesheet['note'], ['id' => 'note' . $timesheet['id']]); ?>
                                            </div>
                                            <div class="col-md-12 text-right">
                                                <button type="button"
                                                    class="btn btn-default edit-timesheet-cancel"><?php echo _l('cancel'); ?></button>
                                                <button class="btn btn-success edit-timesheet-submit">
                                                    <?php echo _l('submit'); ?></button>
                                            </div>
                                        </form>
                                    </td>
                                </tr>
                            <?php
                            
                        } ?>
                        <?php if ($timers_found == false) { ?>
                        <tr>
                            <td colspan="5" class="text-center bold"><?php echo _l('no_timers_found'); ?></td>
                        </tr>
                        <?php } ?>
                        <tr class="odd">
                            <td colspan="5" class="add-timesheet">
                                <form class="task-modal-add-timesheet-form"
                                    <div class="col-md-12">
                                        <p class="font-medium bold mtop5"><?php echo _l('add_timesheet'); ?></p>
                                        <hr class="mtop10 mbot10" />
                                    </div>
                                    <div class="timesheet-start-end-time">
                                        <div class="col-md-6">
                                            <?php echo render_datetime_input('timesheet_start_time', 'task_log_time_start'); ?>
                                        </div>
                                        <div class="col-md-6">
                                            <?php echo render_datetime_input('timesheet_end_time', 'task_log_time_end'); ?>
                                        </div>
                                    </div>
                                    <div class="timesheet-duration hide" style="margin-bottom:20px">
                                        <div class="col-md-12" style="margin-bottom:10px">
                                            <i class="fa-regular fa-circle-question pointer pull-left mtop2" data-toggle="popover"
                                                data-html="true" data-content="
                                                :15 - 15 <?php echo _l('minutes'); ?><br />
                                                2 - 2 <?php echo _l('hours'); ?><br />
                                                5:5 - 5 <?php echo _l('hours'); ?> & 5 <?php echo _l('minutes'); ?><br />
                                                2:50 - 2 <?php echo _l('hours'); ?> & 50 <?php echo _l('minutes'); ?><br />
                                                "></i> 
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group" style="display:flex">
                                                <input type="text" id="timesheet_duration1" class="form-control" name="timesheet_duration1" placeholder="HH" value="">
                                                <?php //echo render_input('timesheet_duration', 'project_timesheet_time_spend', '', 'text', ['placeholder' => 'HH:MM']); ?>
                                                <span> &nbsp </span> 
                                                <input type="text" id="timesheet_duration2" class="form-control" name="timesheet_duration2" placeholder="MM" value="">
                                            </div>
                                        </div>             
                            
                                    </div>
                                    <div class="col-md-12 mbot15">
                                        <a href="#" class="timesheet-toggle-enter-type">
                                            <span class="timesheet-duration-toggler-text switch-to">
                                                <?php echo _l('timesheet_duration_instead'); ?>
                                            </span>
                                            <span class="timesheet-date-toggler-text hide ">
                                                <?php echo _l('timesheet_date_instead'); ?>
                                            </span>
                                        </a>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label class="control-label">
                                                <?php echo _l('task_single_log_user'); ?>
                                            </label>
                                            <br />
                                            <select name="single_timesheet_staff_id" class="selectpicker" data-width="100%">
                                                <option value="<?=get_staff_user_id()?>" selected="selected"><?=get_staff_full_name(get_staff_user_id())?></option>
                                            </select>
                                        </div>
                                        <?php //echo render_textarea('task_single_timesheet_note', 'note'); ?>
                                    </div>
                                    <div class="col-md-12 text-right">
                                        <?php
                                        $disable_button = '';
                                        //if ($this->sam_tasks_model->is_timer_started_for_task2(['sam_id' => $sam_id, 'stage_id' => $stage_id, 'staff_id' => get_staff_user_id()])) {
                                        if (1) {
                                            $disable_button = 'disabled ';
                                            //echo '<div class="text-right mbot15 text-danger">' . _l('add_task_timer_started_warning') . '</div>';
                                        } ?> 
                                                <button class="btn btn-success add-timesheet-submit">
                                                    <?php echo _l('submit'); ?>
                                                </button>

                                    </div>
                                <?=form_close()?>
                            </td>
                        </tr>
                </tbody>
            </table>
        </div>
        <hr />
        </div>
        <div class="clearfix"></div>
    </div>
</div>
<script>
init_selectpicker();
init_datepicker();
init_lightbox();

$('.edit-timesheet-cancel').click(function() {
    $('.timesheet-edit').addClass('hide');
    $('.add-timesheet').removeClass('hide');
});

$('.sam-edit-timesheet').click(function() {
    var edit_timesheet_id = $(this).data('timesheet-id');
    $('.timesheet-edit, .add-timesheet').addClass('hide');
    $('.task-modal-edit-timesheet-' + edit_timesheet_id).removeClass('hide');
});

$('.task-modal-add-timesheet-form').submit(event => {
    event.preventDefault();
    $('.add-timesheet-submit').prop('disabled', true);

    var form = new FormData(event.target);
    var data = {};
                                          
    data.timesheet_start_time = form.get('timesheet_start_time');
    data.timesheet_end_time = form.get('timesheet_end_time');
    data.timesheet_duration = form.get('timesheet_duration1')+':'+form.get('timesheet_duration2');
    data.single_timesheet_staff_id = form.get('single_timesheet_staff_id');          
    //data.note = form.get('note');

    $.post(admin_url + '<?=SAM_MODULE?>/tasks/add_timesheet/<?=$sam_id.'/'.$pipeline_id.'/'.$stage_id?>', data).done(function(response) {
        response = JSON.parse(response);
        if (response.success === true || response.success == 'true') {
            //init_task_modal(data.timesheet_task_id);
            alert_float('success', response.message);
        } else {
            alert_float('warning', response.message);
        }
        $('.add-timesheet-submit').prop('disabled', false), 
        //window.location.reload();
        get_sam_timesheet('<?=admin_url(SAM_MODULE.'/tasks/get_task_data/'.$sam_id.'/'.$pipeline_id.'/'.$stage_id)?>')
    });
});

$('.task-modal-edit-timesheet-form').submit(event => {
    event.preventDefault();
    $('.edit-timesheet-submit').prop('disabled', true);

    var form = new FormData(event.target);
    var data = {};

    data.timer_id = form.get('timer_id');
    data.start_time = form.get('start_time');
    data.end_time = form.get('end_time');
    data.timesheet_staff_id = form.get('staff_id');
    data.note = form.get('note');

    $.post(admin_url + 'sales_marketing/tasks/update_timesheet/<?=$sam_id.'/'.$pipeline_id.'/'.$stage_id?>', data).done(function(response) {
        response = JSON.parse(response);
        if (response.success === true || response.success == 'true') {
            //init_task_modal(data.timesheet_task_id);
            alert_float('success', response.message);
        } else {
            alert_float('warning', response.message);
        }
        $('.edit-timesheet-submit').prop('disabled', false); 
        //window.location.reload();
        get_sam_timesheet('<?=admin_url(SAM_MODULE.'/tasks/get_task_data/'.$sam_id.'/'.$pipeline_id.'/'.$stage_id)?>')
    });
});


function get_sam_timesheet(url) {
    requestGet(url).done((function(e) {
        $("#myModal").html("<div class='modal-dialog'><div class='modal-content'>"+e+"</div></div>");     
    }
    ))
}

function remove_timesheet_item(timesheet_id) {
    confirm_delete() 
    &&     
    $.post(admin_url + '<?=SAM_MODULE?>/tasks/delete_timesheet/'+timesheet_id, {}).done(function(response) {
        response = JSON.parse(response);
        if (response.success === true || response.success == 'true') {
            //init_task_modal(data.timesheet_task_id);
            alert_float('success', response.message);
        } else {
            alert_float('warning', response.message);
        }                                                   
        //window.location.reload();
        get_sam_timesheet('<?=admin_url(SAM_MODULE.'/tasks/get_task_data/'.$sam_id.'/'.$pipeline_id.'/'.$stage_id)?>')
    });
}

</script>
