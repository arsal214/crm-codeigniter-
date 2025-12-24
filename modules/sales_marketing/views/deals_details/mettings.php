<?php
$sub_active = 1;
$task_timer_id = $this->uri->segment(6);
if ($task_timer_id) {
    $sub_active = 2;
    $mettings_details = get_sam_row('tbl_sam_mettings', array('mettings_id' => $task_timer_id));
}
$edited = has_permission('sam', '', 'edit');
if (!empty($mettings_details)) {
    $id = $mettings_details->mettings_id;
} else {
    $id = null;
}
?>
<div class="nav-tabs-custom ">
    <!-- Tabs within a box -->
    <ul class="nav nav-tabs" style="margin-top: -20px; margin-bottom: 0px">
        <li class="<?= $sub_active == 1 ? 'active' : ''; ?>"><a href="#all_metting"
                                                                data-toggle="tab"><?= _l('sam_all_meetings') ?></a>
        </li>
        <?php if (1) { ?>
            <li class="<?= $sub_active == 2 ? 'active' : ''; ?>"><a href="#new_metting"
                                                                    data-toggle="tab"><?= _l('sam_new_meeting') ?></a>
            </li>
        <?php } ?>
    </ul>
    <div class="tab-content bg-white">
        <!-- ************** general *************-->
        <div class="tab-pane <?= $sub_active == 1 ? 'active' : ''; ?>" id="all_metting">

            <div class="table-responsive">
                <table class="table table-striped " cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th><?= _l('sam_subject') ?></th>
                        <th><?= _l('end_date') ?></th>
                        <th><?= _l('responsible') ?></th>
                        <th class="col-options no-sort"><?= _l('action') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $all_deals_details = get_sam_result('tbl_sam_mettings', array('module' => 'sam', 'module_field_id' => $deals_details->id));
                    if (!empty($all_deals_details)) :
                        foreach ($all_deals_details as $v_mettings) :
                            ?>
                            <tr id="table-meeting-<?= $v_mettings->mettings_id ?>">
                                <td>
                                    <a data-toggle="modal" data-target="#myModal"
                                       href="<?= base_url('admin/'.SAM_MODULE.'/meeting_details/' . $v_mettings->mettings_id) ?>"><?= $v_mettings->meeting_subject ?></a>
                                </td>
                                <td><?php echo _dt($v_mettings->end_date) ?>
                                </td>
                                <td><?php echo get_staff_full_name(get_staff_user_id()) ?></td>
                                <td>
                                    <a href="<?= base_url('admin/'.SAM_MODULE.'/meeting_details/' . $v_mettings->mettings_id) ?>"
                                       class="btn btn-xs btn-info" data-placement="top" data-toggle="modal"
                                       data-target="#myModal">
                                        <i class="fa fa-list"></i>
                                         <a href="<?= base_url('admin/' . SAM_MODULE . '/details/' . $deals_details->id . '/mettings/' . $v_mettings->mettings_id) ?>" 
                                           class="btn btn-xs btn-warning">
                                            <i class="fa fa-edit"></i> Edit
                                        </a>
                                        <!-- Delete Button -->
                                        <a href="<?= base_url('admin/' . SAM_MODULE . '/delete_deals_call/' . $deals_details->id . '/' . $v_mettings->mettings_id) ?>" 
                                           class="btn btn-xs btn-danger">
                                            <i class="fa fa-remove"></i>
                                        </a>
                                </td>
                            </tr>
                        <?php
                        endforeach;
                    endif;
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="tab-pane <?= $sub_active == 2 ? 'active' : ''; ?>" id="new_metting">

            <?php echo form_open(base_url('admin/'.SAM_MODULE.'/saved_metting/' . $id), array('id' => 'deals_calls_form', 'enctype' => 'multipart/form-data', 'data-parsley-validate' => '', 'role' => 'form')); ?>

            <?php
            $deals_id = $this->uri->segment(4);  
            ?>
            <input type="hidden" name="deals_id" value="<?php echo $deals_id; ?>" class="form-control">

            <div class="row">
                <div class="form-group mtop20">
                    <div class="col-md-6 mtop15">
                        <label for="attendees" class="control-label"><?= _l('sam_meeting_format') ?></label>
                        <select name="format" id="format" data-width="100%" class="selectpicker" required="">
                            <option value=""></option>
                            <?php
                            $meeting_format= $mettings_details->format;
                            
                            if($meeting_format==1){
                                echo '<option value="'.$meeting_format.'" selected="selected">'._l('sam_meeting_online').'</option>';    
                                echo '<option value="2">'._l('sam_meeting_inperson').'</option>';    
                            } 
                            else if($meeting_format==2){
                                echo '<option value="1">'._l('sam_meeting_online').'</option>';    
                                echo '<option value="'.$meeting_format.'" selected="selected">'._l('sam_meeting_inperson').'</option>';    
                            } 
                            else{
                                echo '<option value="1">'._l('sam_meeting_online').'</option>';    
                                echo '<option value="2">'._l('sam_meeting_inperson').'</option>';     
                            }    
                            
                            ?>                                                            
                        </select>
                    </div>
                    <div class="col-md-6 mtop15">
                        <label for="meeting_subject" class="control-label"><?= _l('sam_meeting_subject') ?></label>
                        <input type="text" required="" name="meeting_subject" class="form-control" value="<?php
                        if (!empty($mettings_details->meeting_subject)) {
                            echo $mettings_details->meeting_subject;
                        }
                        ?>">
                    </div>
                    <div class="col-md-6 mtop15">
                        <label for="start_date" class="control-label"><?= _l('start_date_time') ?></label>
                        <div class="input-group">
                            <input type="text" required="" name="start_date" onchange="CalculateMeetingDuration()" class="form-control datetimepicker"
                                   value="<?php

                                   if (!empty($mettings_details->start_date)) {
                                       echo date('Y-m-d H:i:s', strtotime($mettings_details->start_date));
                                   } else {
                                       echo date('Y-m-d H:i:s');
                                   }
                                   ?>" data-date-format="yyyy-mm-dd hh:ii:ss">
                            <div class="input-group-addon">
                                <a href="#"><i class="fa fa-calendar"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mtop15">
                        <label for="end_date" class="control-label"><?= _l('end_date_time') ?></label>
                        <div class="input-group">
                            <input type="text" required="" name="end_date" onchange="CalculateMeetingDuration()" class="form-control datetimepicker"
                                   value="<?php
                                   if (!empty($mettings_details->end_date)) {
                                       echo date('Y-m-d H:i:s', strtotime($mettings_details->end_date));
                                   } else {
                                       echo date('Y-m-d H:i:s');
                                   }
                                   ?>" data-date-format="yyyy-mm-dd hh:ii:ss">

                            <div class="input-group-addon">
                                <a href="#"><i class="fa fa-calendar"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mtop15">
                        <label for="meeting_duration" class="control-label"><?= _l('sam_meeting_duration') ?></label>
                        <input type="text" id="meeting_duration" name="meeting_duration" class="form-control" value="" readonly>
                    </div>
                    <div class="col-md-6 mtop15">
                        <label for="attendees" class="control-label"><?= _l('attend_person') ?></label>
                        <select multiple="multiple" name="attendees[]" data-width="100%" class=" selectpicker"
                                required="">
                            <option value=""><?= _l('select') . _l('attendess') ?></option>
                            <?php
                            $all_user_attendees = $this->db->get('tblstaff')->result();
                            if (!empty($all_user_attendees)) {
                                foreach ($all_user_attendees as $v_user_attendees) {
                                    
                                    if($v_user_attendees->staffid == get_staff_user_id()){
                                        echo "<option value='".$v_user_attendees->staffid."' selected>".get_staff_full_name($v_user_attendees->staffid)."</option>";    
                                    }
                                    else{
                                         echo "<option value='".$v_user_attendees->staffid."'>".get_staff_full_name($v_user_attendees->staffid)."</option>";     
                                    }   
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6 mtop15">
                        <label for="user_id" class="control-label"><?= _l('responsible') ?></label>
                        <select name="user_id" class="form-control select_box selectpicker" style="width: 100%">
                            <?php
                            //$responsible_user_info = $this->db->where(array('role !=' => '2'))->get('tblstaff')->result();
                            $responsible_user_info = $this->db->where(array())->get('tblstaff')->result();
                            if (!empty($responsible_user_info)) {
                                foreach ($responsible_user_info as $v_responsible_user) {                                        
                                    if($v_responsible_user->staffid == get_staff_user_id()){
                                        echo "<option value='".$v_responsible_user->staffid."' selected>".get_staff_full_name($v_responsible_user->staffid)."</option>";    
                                    }
                                    else{
                                         echo "<option value='".$v_responsible_user->staffid."'>".get_staff_full_name($v_responsible_user->staffid)."</option>";     
                                    }
                                }
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-6 mtop15">
                        <label for="location" class="control-label"><?= _l('location') ?></label>
                        <input type="text" required="" name="location" class="form-control" value="<?php
                        if (!empty($mettings_details->location)) {
                            echo $mettings_details->location;
                        }
                        ?>">
                    </div>
                    <div class="col-md-12 mtop15">
                        <!-- End discount Fields -->
                        <div class="form-group terms">
                            <label class="control-label"><?= _l('sam_description') ?><span
                                        class="text-danger"> *</span> </label>
                            <div class="">
                            <textarea name="description" class="form-control tinymce" rows="5"><?php
                                if (!empty($mettings_details->description)) {
                                    echo $mettings_details->description;
                                }
                                ?></textarea>

                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <button type="submit" class="btn btn-sm btn-primary pull-right"><?= _l('updates') ?></button>
            <?php echo form_close(); ?>

        </div>
    </div>
</div>
<script>
$(document).ready(function () {  
    CalculateMeetingDuration();    
});
function CalculateMeetingDuration(){
    start_date = $('input[name="start_date"]').val();
    end_date = $('input[name="end_date"]').val();

    data = {};
    data.start_date = start_date;
    data.end_date = end_date;
    $.post(admin_url + '<?=SAM_MODULE?>/CalculateTime',data).done(function(response) {  
        //response = JSON.parse(response);  
        $('input[name="meeting_duration"]').val(response);                                                                                               
    });
        
    /*var diff=(Date.parse(end_date)-Date.parse(start_date))/1000/60;
    var hours = String(100+Math.floor(diff/60)).substr(1);
    var mins = String(100+diff%60).substr(1);
    var sec = String(100+diff%3600).substr(1);
    var duration = hours+':'+mins+':'+sec;  */

}
</script>