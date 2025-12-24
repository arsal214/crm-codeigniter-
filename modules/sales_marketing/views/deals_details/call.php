<?php
$sub_active = 1;
$calls_id = $this->uri->segment(6);
if ($calls_id) {
    $sub_active = 2;
    $call_info = get_sam_row('tbl_sam_calls', array('calls_id' => $calls_id));
}
$edited = has_permission('sam', '', 'edit');
if (!empty($call_info)) {
    $id = $call_info->calls_id;
} else {
    $id = null;
}
?>


<div class="nav-tabs-custom ">
    <!-- Tabs within a box -->
    <ul class="nav nav-tabs" style="margin-top: -20px; margin-bottom: 0px">
        <li class="<?= $sub_active == 1 ? 'active' : ''; ?>"><a href="#manage"
                                                                data-toggle="tab"><?= _l('all_call') ?></a>
        </li>
        <?php if (1) { ?>
            <li class="<?= $sub_active == 2 ? 'active' : ''; ?>"><a href="#create"
                                                                    data-toggle="tab"><?= _l('new_call') ?></a>
            </li>
        <?php } ?>
    </ul>
    <div class="tab-content bg-white">
        <!-- ************** general *************-->
        <div class="tab-pane <?= $sub_active == 1 ? 'active' : ''; ?>" id="manage">

            <div class="table-responsive">
                <table class="table table-striped " cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th><?= _l('date') ?></th>
                        <th><?= _l('call_summary') ?></th>
                        <th><?= _l('contact') ?></th>
                        <th><?= _l('responsible') ?></th>
                        <th class="col-options no-sort"><?= _l('action') ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    $all_calls_info = get_sam_result('tbl_sam_calls', array('module_field_id' => $deals_details->id));
                    if (!empty($all_calls_info)) :
                        foreach ($all_calls_info as $v_calls) :
                            $user = $this->sam_model->check_deals_by(array('staffid' => $v_calls->user_id), 'tblstaff');
                            ?>
                            <tr id="leads_call_<?= $deals_details->id ?>">
                                <td><?= _d($v_calls->date) ?>
                                </td>
                                <td><?= $v_calls->call_summary ?></td>
                                <td>
                                    <?php
                                    if (!empty($deals_details->customers['name'])) {
                                        echo $deals_details->customers['name'];
                                    }
                                    ?></td>
                                <td><?= $user->firstname . ' ' . $user->lastname ?></td>
                                <td>
                                    <a href="<?= base_url('admin/'.SAM_MODULE.'/call_details/' . $v_calls->calls_id) ?>"
                                       class="btn btn-xs btn-info" data-placement="top" data-toggle="modal"
                                       data-target="#myModal">
                                        <i class="fa fa-list "></i></a>
                                    <!-- Inline Link for Testing -->
                    <a href="<?= base_url('admin/' . SAM_MODULE . '/details/' . $deals_details->id . '/call/' . $v_calls->calls_id) ?>" 
                       class="btn btn-xs btn-warning">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                    <!-- Delete Button -->
                    <a href="<?= base_url('admin/' . SAM_MODULE . '/delete_deals_call/' . $deals_details->id . '/' . $v_calls->calls_id) ?>" 
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
        <div class="tab-pane <?= $sub_active == 2 ? 'active' : ''; ?>" id="create">
            <?php echo form_open(base_url('admin/'.SAM_MODULE.'/saved_call/' . $deals_details->id . '/' . $id), array('id' => 'deals_calls_form', 'enctype' => 'multipart/form-data', 'data-parsley-validate' => '', 'role' => 'form')); ?>

            <div class="row">
                <div class="form-group mtop20">
                    <div class="col-md-6 mtop15">
                        <label class="control-label"><?= _l('date') ?><span class="text-danger">
                        *</span></label>
                        <div class="">
                            <div class="input-group">
                                <input type="text" required="" name="date" class="form-control datepicker" value="<?php
                                if (!empty($call_info->date)) {
                                    echo $call_info->date;
                                } else {
                                    echo date('Y-m-d');
                                }
                                ?>" data-date-format="<?= config_item('date_picker_format'); ?>">
                                <div class="input-group-addon">
                                    <a href="#"><i class="fa fa-calendar"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 mtop15">
                        <label class="control-label"><?= _l('call_type') ?></label>
                        <div class="">
                            <select name="call_type" class="form-control select_box" style="width: 100%">
                                <option value="outbound" <?php if (!empty($call_info->call_type) && $call_info->call_type == 'outbound') {
                                    echo 'selected';
                                } ?>><?= _l('outbound') ?></option>
                                <option value="inbound" <?php if (!empty($call_info->call_type) && $call_info->call_type == 'inbound') {
                                    echo 'selected';
                                } ?>><?= _l('inbound') ?></option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 mtop15">
                        <label class="control-label"><?= _l('outcome') ?><span class="text-danger">*</span></label>
                        <div class="">
                            <?php
                            $all_outcomes = [
                                'left_voice_message' => _l('left_voice_message'),
                                'moved_conversion_forward' => _l('moved_conversion_forward'),
                                'no_answer' => _l('no_answer'),
                                'not_interested' => _l('not_interested'),
                                'busy' => _l('busy'),
                                'wrong_number' => _l('wrong_number'),
                                'switched_off' => _l('switched_off'),
                                'call_back' => _l('call_back'),
                                'other' => _l('other'),
                            ]
                            ?>
                            <select name="outcome" class="form-control selectpicker" style="width: 100%">
                                <?php foreach ($all_outcomes as $key => $value) { ?>
                                    <option value="<?= $key ?>" <?php if (!empty($call_info->outcome) && $call_info->outcome == $key) {
                                        echo 'selected';
                                    } ?>><?= $value ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 mtop15">
                        <label class="control-label"><?= _l('call_duration') ?></label>
                        <div class="form-group" style="display:flex">
                            <?php
                            $call_hour = ''; $call_min = ''; $call_sec = '';
                            if (!empty($call_info->duration)) {
                                $call_duration = explode(':',$call_info->duration);
                                if(isset($call_duration[0])){
                                    $call_hour = $call_duration[0];
                                }
                                if(isset($call_duration[1])){
                                    $call_min = $call_duration[1];
                                }    
                                if(isset($call_duration[2])){
                                    $call_sec = $call_duration[2];
                                }
                            }
                            ?>
                            <input type="text" id="duration1" class="form-control" name="duration1" placeholder="HH" value="<?=$call_hour?>">
                            <span> &nbsp </span> 
                            <input type="text" id="duration2" class="form-control" name="duration2" placeholder="MM" value="<?=$call_min?>">
                            <span> &nbsp </span> 
                            <input type="text" id="duration3" class="form-control" name="duration3" placeholder="SS" value="<?=$call_sec?>">
                        </div>
                        <!--<div class="">
                            <input type="text" name="duration" class="form-control" id="duration" placeholder="00:35:20"
                                   value="<?php if (!empty($call_info->duration)) {
                                       //echo $call_info->duration;
                                   } ?>">
                        </div>-->

                    </div>

                    <div class="col-md-6 mtop15">
                        <label class="control-label"><?= _l('contact') ?></label>
                        <div class="">
                            <select name="client_id" class="form-control selectpicker" style="width: 100%">
                                <?php
                                if (!empty($deals_details->customers)) {
                                    ?>
                                    <option value="<?= $deals_details->customers['id'] ?>" <?php
                                    if (!empty($call_info) && $call_info->client_id == $deals_details->customers['id']) {
                                        echo 'selected';
                                    }
                                    ?>>
                                        <?= $deals_details->customers['name'] ?>
                                    </option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>

                    </div>
                    <div class="col-md-6 mtop15">
                        <label class="control-label"><?= _l('responsible') ?><span class="text-danger"> *</span></label>
                        <div class="">
                            <select name="user_id" class="form-control selectpicker" style="width: 100%" required="">
                                <option value=""><?= _l('responsible') ?></option>
                                <?php
                                if (!empty($staff)) {
                                    foreach ($staff as $key => $v_user) {
                                        if($v_user['staffid'] == get_staff_user_id()){
                                            echo "<option value='".$v_user['staffid']."' selected>".get_staff_full_name($v_user['staffid'])."</option>";
                                        }
                                        else{
                                            echo "<option value='".$v_user['staffid']."'>".get_staff_full_name($v_user['staffid'])."</option>";
                                        }
                                    }
                                }
                                ?>
                            </select>
                        </div>

                    </div>

                    <div class="col-md-12 mtop15">
                        <!-- End discount Fields -->
                        <div class="form-group terms">
                            <label class="control-label"><?= _l('call_summary') ?><span
                                        class="text-danger"> *</span> </label>
                            <div class="">
                            <textarea name="call_summary" class="form-control tinymce" rows="5"><?php
                                if (!empty($call_info->call_summary)) {
                                    echo $call_info->call_summary;
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