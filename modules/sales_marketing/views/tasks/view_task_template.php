<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal-body">
    <input id="taskid" type="hidden" value="<?php echo $task->id?>">
    <div class="row">
        <div class="col-md-12 task-single-col-left">
            <?php 
            if (total_rows(db_prefix() . '_sam_taskstimers', ['end_time' => null, 'staff_id !=' => get_staff_user_id(), 'task_id' => $task->id]) > 0) {
                $startedTimers = $this->Sam_tasks_model->get_timers($task->id, ['staff_id !=' => get_staff_user_id(), 'end_time' => null]);

                $usersWorking = '';

                foreach ($startedTimers as $t) {
                    $usersWorking .= '<b>' . e(get_staff_full_name($t['staff_id'])) . '</b>, ';
                }

                $usersWorking = rtrim($usersWorking, ', '); 
            ?>
                <div class="alert alert-info">
                    <?php 
                    echo _l((count($startedTimers) == 1 ? 'task_users_working_on_tasks_single' : 'task_users_working_on_tasks_multiple'), $usersWorking);
                    ?>
                </div>
            <?php
            } 
            ?>
            <div class="clearfix"></div>
            <p class="no-margin pull-left mright5">
                <a href="#" class="btn btn-default mright5" data-toggle="tooltip"
                    data-title="<?php echo _l('task_timesheets'); ?>"
                    onclick="slideToggle('#task_single_timesheets'); return false;">
                    <i class="fa fa-th-list"></i>
                </a>
            </p>
            <?php 
            if ($task->billed == 0) {
                $is_assigned = $task->current_user_is_assigned;
                if (!$this->tasks_model->is_timer_started($task->id)) { ?>
                    <p class="no-margin pull-left" <?php if (!$is_assigned) { ?> data-toggle="tooltip"
                    data-title="<?php echo _l('task_start_timer_only_assignee'); ?>" <?php } ?>>
                        <a href="#" class="mbot10 btn<?php if (!$is_assigned || $task->status == Tasks_model::STATUS_COMPLETE) {
                        echo ' disabled btn-default';
                        } else {
                        echo ' btn-success';
                        } ?>" onclick="timer_action(this, <?php echo e($task->id); ?>); return false;">
                            <i class="fa-regular fa-clock"></i> <?php echo _l('task_start_timer'); ?>
                        </a>
                    </p>
                <?php } else { ?>
                    <p class="no-margin pull-left">
                        <a href="#" data-toggle="popover" data-placement="<?php echo is_mobile() ? 'bottom' : 'right'; ?>"
                        data-html="true" data-trigger="manual" data-title="<?php echo _l('note'); ?>"
                        data-content='<?php echo render_textarea('timesheet_note'); ?><button type="button" onclick="timer_action(this, <?php echo e($task->id); ?>, <?php echo $this->tasks_model->get_last_timer($task->id)->id; ?>);" class="btn btn-primary btn-sm"><?php echo _l('save'); ?></button>'
                        class="btn mbot10 btn-danger<?php if (!$is_assigned) {
                        echo ' disabled';
                        } ?>" onclick="return false;">
                            <i class="fa-regular fa-clock"></i> <?php echo _l('task_stop_timer'); ?>
                        </a>
                    </p>
                <?php } ?>
            <?php
            } ?>
            <div class="clearfix"></div>
            <hr class="hr-10" />
            <div id="task_single_timesheets" class="<?php if (!$this->session->flashdata('task_single_timesheets_open')){ echo 'hide';}?>">
                <div class="table-responsive">
                    <table class="table table-bordered">
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
                            foreach ($task->timesheets as $timesheet) { ?>
                                <?php if (staff_can('edit',  'tasks') || staff_can('create',  'tasks') || staff_can('delete',  'tasks') || $timesheet['staff_id'] == get_staff_user_id()) {
                                    $timers_found = true; ?>
                                    <tr>
                                        <td class="tw-text-sm">
                                            <?php if ($timesheet['note']) {
                                                echo '<i class="fa fa-comment" data-html="true" data-placement="right" data-toggle="tooltip" data-title="' . e($timesheet['note']) . '"></i>';
                                            } ?>
                                            <a href="<?php echo admin_url('staff/profile/' . $timesheet['staff_id']); ?>"
                                            target="_blank">
                                                <?php echo e($timesheet['full_name']); ?>
                                            </a>
                                        </td>
                                        <td class="tw-text-sm"><?php echo e(_dt($timesheet['start_time'], true)); ?></td>
                                        <td class="tw-text-sm">
                                            <?php
                                            if ($timesheet['end_time'] !== null) {
                                                echo e(_dt($timesheet['end_time'], true));
                                            } else {
                                                // Allow admins to stop forgotten timers by staff member
                                                if (!$task->billed && is_admin()) { ?>
                                                    <a href="#" data-toggle="popover" data-placement="bottom" data-html="true"
                                                    data-trigger="manual" data-title="<?php echo _l('note'); ?>"
                                                    data-content='<?php echo render_textarea('timesheet_note'); ?><button type="button" onclick="timer_action(this, <?php echo e($task->id); ?>, <?php echo e($timesheet['id']); ?>, 1);" class="btn btn-primary btn-sm"><?php echo _l('save'); ?></button>'
                                                    class="text-danger" onclick="return false;">
                                                        <i class="fa-regular fa-clock"></i>
                                                        <?php echo _l('task_stop_timer'); ?>
                                                    </a>
                                                <?php
                                                }
                                            } ?>
                                        </td>
                                        <td class="tw-text-sm">
                                            <div class="tw-flex">
                                                <div class="tw-grow">
                                                    <?php
                                                    if ($timesheet['time_spent'] == null) {
                                                    echo _l('time_h') . ': ' . e(seconds_to_time_format(time() - $timesheet['start_time'])) . '<br />';
                                                    echo _l('time_decimal') . ': ' . e(sec2qty(time() - $timesheet['start_time'])) . '<br />';
                                                    } else {
                                                    echo _l('time_h') . ': ' . e(seconds_to_time_format($timesheet['time_spent'])) . '<br />';
                                                    echo _l('time_decimal') . ': ' . e(sec2qty($timesheet['time_spent'])) . '<br />';
                                                    } ?>
                                                </div>
                                                <?php
                                                if (!$task->billed) { ?>
                                                <div
                                                class="tw-flex tw-items-center tw-shrink-0 tw-self-start tw-space-x-1.5 tw-ml-2">
                                                <?php
                                                if (staff_can('delete_timesheet', 'tasks') || (staff_can('delete_own_timesheet', 'tasks') && $timesheet['staff_id'] == get_staff_user_id())) {
                                                echo '<a href="' . admin_url('sales_marketing/tasks/delete_timesheet/' . $timesheet['id']) . '" class="task-single-delete-timesheet text-danger" data-task-id="' . $task->id . '"><i class="fa fa-remove"></i></a>';
                                                }
                                                if (staff_can('edit_timesheet', 'tasks') || (staff_can('edit_own_timesheet', 'tasks') && $timesheet['staff_id'] == get_staff_user_id())) {
                                                echo '<a href="#" class="task-single-edit-timesheet text-info" data-toggle="tooltip" data-title="' . _l('edit') . '" data-timesheet-id="' . $timesheet['id'] . '">
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
                                        <td class="timesheet-edit task-modal-edit-timesheet-<?php echo $timesheet['id'] ?> hide" colspan="5">
                                        </td>
                                    </tr>
                                <?php
                                } 
                            } ?>
                            <?php if ($timers_found == false) { ?>
                            <tr>
                                <td colspan="5" class="text-center bold"><?php echo _l('no_timers_found'); ?></td>
                            </tr>
                            <?php } ?>
                            <?php 
                            if ($task->billed == 0 && ($is_assigned || (count($task->assignees) > 0 && is_admin())) && $task->status != Tasks_model::STATUS_COMPLETE) {
                            ?>
                            <tr class="odd">
                                <td colspan="5" class="add-timesheet">
                                    <form class="task-timer-submit" action="">
                                    <?php
                                    $csrf = array(
                                            'name' => $this->security->get_csrf_token_name(),
                                            'hash' => $this->security->get_csrf_hash()
                                    );
                                    ?>
                                    <input type="hidden" name="<?=$csrf['name'];?>" value="<?=$csrf['hash'];?>" />
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
                                        <div class="timesheet-duration hide">
                                            <div class="col-md-12">
                                                <i class="fa-regular fa-circle-question pointer pull-left mtop2" data-toggle="popover"
                                                    data-html="true" data-content="
                                                    :15 - 15 <?php echo _l('minutes'); ?><br />
                                                    2 - 2 <?php echo _l('hours'); ?><br />
                                                    5:5 - 5 <?php echo _l('hours'); ?> & 5 <?php echo _l('minutes'); ?><br />
                                                    2:50 - 2 <?php echo _l('hours'); ?> & 50 <?php echo _l('minutes'); ?><br />
                                                    "></i> 
                                                <?php echo render_input('timesheet_duration', 'project_timesheet_time_spend', '', 'text', ['placeholder' => 'HH:MM']); ?>
                                            </div>
                                        </div>
                                        <div class="col-md-12 mbot15 mntop15">
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
                                                <?php foreach ($task->assignees as $assignee) {
                                                    if ((staff_cant('create', 'tasks') && staff_cant('edit', 'tasks') && $assignee['assigneeid'] != get_staff_user_id()) || ($task->rel_type == 'project' && staff_cant('edit', 'projects') && $assignee['assigneeid'] != get_staff_user_id())) {
                                                        continue;
                                                    }
                                                    $selected = '';
                                                    if ($assignee['assigneeid'] == get_staff_user_id()) {
                                                        $selected = ' selected';
                                                    } ?>
                                                        <option<?php echo e($selected); ?> value="<?php echo e($assignee['assigneeid']); ?>">
                                                            <?php echo e($assignee['full_name']); ?>
                                                        </option>
                                                        <?php
                                                    } ?>
                                                </select>
                                            </div>
                                            <?php //echo render_textarea('task_single_timesheet_note', 'note'); ?>
                                        </div>
                                        <div class="col-md-12 text-right">
                                            <?php
                                                 $disable_button = '';
                                            if ($this->tasks_model->is_timer_started_for_task($task->id, ['staff_id' => get_staff_user_id()])) {
                                                $disable_button = 'disabled ';
                                                echo '<div class="text-right mbot15 text-danger">' . _l('add_task_timer_started_warning') . '</div>';
                                            } ?>
                                            <button <?php echo e($disable_button); ?>data-task-id="<?php echo e($task->id); ?>"
                                                class="btn btn-success">
                                                <i class="fa fa-plus"></i>
                                                <?php echo _l('submit'); ?>
                                            </button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                        <?php
                        } ?>
                    </tbody>
                </table>
            </div>
                <hr />
            </div>
            <div class="clearfix"></div>
        </div>
    </div>
</div>
<script>
init_selectpicker();
init_datepicker();
init_lightbox();

//function SubmitTaskTimer(event){
    
    $('form').submit(function (e) {
        e.preventDefault();
        const data = {
            starttime: $('#timesheet_start_time').val(),
            endtime: $('#timesheet_end_time').val(),
            endtime: $('#single_timesheet_staff_id').val(),
        };

        $.ajax({
            type: 'POST',
            url: admin_url + 'sales_marketing/tasks/update_timesheet/',
            data: JSON.stringify(data),
            contentType: 'application/json',
        })
        .done((data) => {
            console.log({ data });
        })
        .fail((err) => {
            console.error(err);
        })
        .always(() => {
            console.log('always called');
        }); 
    });

    
    
    
    //event.preventDefault();
    //$('.edit-timesheet-submit').prop('disabled', true);

/*    var form = new FormData(event.target);
    var data = {};
     

    data.timer_id = form.get('timer_id');
    data.start_time = form.get('start_time');  alert(data.start_time)
    data.end_time = form.get('end_time');
    data.timesheet_staff_id = form.get('staff_id');
    data.timesheet_task_id = form.get('task_id');
    data.note = form.get('note');  console.log(form); */

/*    $.post(admin_url + 'sales_marketing/tasks/update_timesheet', data).done(function(response) {
        response = JSON.parse(response);
        if (response.success === true || response.success == 'true') {
            init_task_modal(data.timesheet_task_id);
            alert_float('success', response.message);
        } else {
            alert_float('warning', response.message);
        }
        $('.edit-timesheet-submit').prop('disabled', false);
    }); */
//}


</script>