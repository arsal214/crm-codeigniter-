<?php

defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row">

    <?php echo form_open(admin_url('appointment_manager/appointments'), array('id' => 'appmgr_appointment_form')) ?>

    <div class="col-md-12">

        <?php echo form_hidden('location'); ?>

        <?php echo form_hidden('isEdit', 0); ?>

        <div class="select-placeholder mbot15">
            <select id="appointee" name="appointee" data-live-search="true" data-width="100%" class="ajax-search" data-empty-title="<?php echo _l('appmgr_appointee_label'); ?>" data-none-selected-text="<?php echo _l('appmgr_appointee_label'); ?>">
            </select>
        </div>

        <?php echo render_date_input('appointment_date', 'appmgr_appointm_entdate'); ?>

        <div class="row cstm-group">
            <div class="col-md-6 itm-wrap">
                <?php echo render_datetime_input('appointment_start_time', 'appmgr_appoint_start_time'); ?>
            </div>
            <div class="col-md-6 itm-wrap">
                <?php echo render_datetime_input('appointment_end_time', 'appmgr_appoint_end_time'); ?>
            </div>

        </div>

        <div class="row cstm-group">

            <div class="col-md-6 itm-wrap">
                <?php echo render_select('client', $clients, ['userid', 'company'], 'appmgr_client_label'); ?>
            </div>
            <div class="col-md-6 itm-wrap">
                <?php if (staff_can('approve', 'appointment_manager')) { ?>
                    <?php echo render_select('status', $statuses, ['id', 'name'], 'appmgr_status'); ?>
                <?php } ?>
            </div>
        </div>
        <div class="row cstm-group">
            <div class="col-md-6 itm-wrap">
                <?php echo render_select('treatment', $treatments, ['id', 'tittle'], 'appmgr_treatment_label'); ?>
            </div>
            <div class="col-md-6 itm-wrap">
                <?php echo render_select('service_cat[]', $categories, ['id', 'name'], 'appmgr_ser_cat','', ['multiple' => true]); ?>
            </div>
        </div>

        <?php echo render_textarea('description', 'appmgr_appointm_description', '', ['rows' => 5]); ?>


        <div class="form-group">
            <div class="row">
                <div class="col-md-12">
                    <label for="reminder_before"><?php echo _l('appmgr_appointment_reminder'); ?></label>
                </div>

            </div>

            <div class="row cstm-group">
                <div class="col-md-6 itm-wrap">
                    <div class="input-group">
                        <input type="number" class="form-control" name="reminder_before" value="" id="reminder_before">
                        <span class="input-group-addon"><i class="fa-regular fa-circle-question" data-toggle="tooltip" data-title="<?php echo _l('reminder_notification_placeholder'); ?>"></i></span>
                    </div>
                </div>
                <div class="col-md-6 itm-wrap apt-cl">
                    <select name="reminder_before_type" id="reminder_before_type" class="selectpicker" data-width="100%">
                        <option value="minutes"><?php echo _l('minutes'); ?></option>
                        <option value="hours"><?php echo _l('hours'); ?></option>
                        <option value="days"><?php echo _l('days'); ?></option>
                        <option value="weeks"><?php echo _l('weeks'); ?></option>
                    </select>
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-primary float-right"><?php echo _l('appmgr_btn_book'); ?></button>
        <button type="button" onclick="closeAppointmentModal(); return false;" class="btn btn-light float-right"><?php echo _l('close'); ?></button>
    </div>

    <?php echo form_close(); ?>

</div>