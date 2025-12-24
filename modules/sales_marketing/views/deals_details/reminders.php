<?php
if(!isset($sam_id)) { 
    $sam_id = $this->uri->segment(4); 

    if (is_admin()) {    
        $this->load->model('dashboard_model');        
        $all_staff = $this->dashboard_model->get_all_staff();
    }
    else{
        $all_staff = '';    
    }

    //get all reminders of deal
    $this->load->model('reminder_model');
    $cond = array('rel_id' => $sam_id, 'rel_type' => 'sam');
    $reminder_res = $this->reminder_model->get_reminders('',$cond);
    //echo "<pre>"; print_r($reminder_res); exit;
}
?>
<div class="table-responsive">
    <div class="tw-mb-2 sm:tw-mb-6">
        <div class="_buttons">
            <a href="#" data-toggle="modal" class="btn btn-primary" data-target=".add-reminder-modal-sam"><i class="fa-regular fa-bell"></i>
                <?=_l('sam_set_deal_reminder')?>
            </a> 
        </div>  
    </div>
    <table class="table table-reminders-sam dataTable no-footer" id="table-reminders-sam" data-default-order="" role="grid" aria-describedby="">
        <thead>
            <tr role="row">
                <th>
                    Title
                </th>
                <th>
                    <?=_l('sam_description')?>
                </th>
                <th>
                    <?=_l('sam_date')?>
                </th>
                <th>Reminder Type</th>
                <th>Close Reminder</th>
                <th>
                    <?=_l('sam_remind')?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            if($reminder_res){
                foreach($reminder_res as $k => $val){
            ?>
                    <tr>
                        <td class="sorting_1">
                            <?=$val['title']?>
                            <div class="row-options">
                                <a href="#" class="edit-reminder" onclick="edit_reminder_form(<?=$val['id']?>,<?=$val['rel_id']?>)">
                                    <?=_l('sam_edit')?>
                                </a> | 
                                <a href="#" onclick="delete_reminder_record(<?=$val['id']?>,<?=$val['rel_id']?>)" class="text-danger delete-reminder1">
                                    <?=_l('sam_delete')?> 
                                </a>
                            </div>
                        </td>
                        <td>
                            <?=$val['description']?>
                        </td>
                        <td class="sorting_1"><?=$val['date']?></td>
                      
                        <td style="color:green;">
                            <?php 
                            if ($val['ismeeting'] == 0) {
                                echo 'Reminder';
                            } elseif ($val['ismeeting'] == 1) {
                                echo 'Meeting';
                            } elseif ($val['ismeeting'] == 2) {
                                echo 'Calls';
                            } elseif ($val['ismeeting'] == 3) {
                                echo 'Task';
                            }
                            ?>
                        </td>
                           <td>
                            <input type="checkbox" onchange="updateIsNotified(this, <?=$val['id']?>)" <?=($val['isnotified'] == 1 ? 'checked' : '')?> />
                        </td>
                        <td>
                            <a href="<?=admin_url('staff/profile/'.$val['staff'])?>">
                                <img src="<?=site_url()?>/assets/images/user-placeholder.jpg" class="staff-profile-image-small"> 
                                <?=get_staff_full_name($val['staff'])?>
                            </a>
                        </td>
                       
                    </tr>
            <?php
                }
            }
            ?>
        </tbody>
    </table>
</div>


<div class="modal fade modal-reminder add-reminder-modal-sam" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: none; padding-left: 17px;">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="#" id="form-reminder-lead" method="post" accept-charset="utf-8" novalidate="novalidate">
                <div class="modal-header">
                    <button type="button" class="close close-reminder-modal" data-rel-id="1" data-rel-type="sam" aria-label="Close" fdprocessedid="7o2bk6" value="">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">
                        <i class="fa-regular fa-circle-question" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="This option allows you to never forget anything about your customers."></i>
                        <?=_l('sam_set_deal_reminder')?>
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">                            
                            <input type="hidden" id="rel_id" name="rel_id" value="<?=$sam_id?>">

                            <input type="hidden" id="rel_type" name="rel_type" value="sam">

                            <div class="form-group" app-field-wrapper="reminder_title">
                                <label for="description" class="control-label"> 
                                    <small class="req text-danger"> </small>
                                    Title
                                </label>
                                <input type="text" id="reminder_title" name="reminder_title" class="form-control">
                            </div> 
                            
                            <div class="form-group" app-field-wrapper="date">
                                <label for="date" class="control-label"> 
                                    <small class="req text-danger">* </small>
                                    Date to be notified
                                </label>
                                <div class="input-group date">
                                    <input type="text" id="date" name="date" class="form-control datetimepicker" data-date-min-date="" data-step="" value="" autocomplete="off" fdprocessedid="e75rw">
                                    <div class="input-group-addon">
                                        <i class="fa-regular fa-calendar calendar-icon"></i>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group" app-field-wrapper="staff">
                                <label for="staff" class="control-label"> 
                                    <small class="req text-danger">* </small>
                                    Set reminder to
                                </label>
                                <div class="select-placeholder">
                                    <select id="staff" name="staff" class="selectpicker" data-width="100%">
                                        <?php
                                        if(isset($all_staff) && $all_staff!=""){
                                            foreach($all_staff as $s){
                                                if($s['staffid']==get_staff_user_id()){
                                                    echo "<option value='".$s['staffid']."' selected='selected'>".$s['fullname']."</option>";
                                                }    
                                                else{
                                                    echo "<option value='".$s['staffid']."'>".get_staff_full_name($s['staffid'])."</option>";
                                                }
                                            }
                                        }
                                        else{
                                            echo "<option value='".get_staff_user_id()."' selected='selected'>".get_staff_full_name(get_staff_user_id())."</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group" app-field-wrapper="description">
                                <label for="description" class="control-label"> 
                                    <small class="req text-danger">* </small>
                                    Description
                                </label>
                                <textarea id="description" name="description" class="form-control" rows="4"></textarea>
                            </div>  
                            <!-- <div class="form-group">
                                <div class="checkbox checkbox-primary">
                                    <input type="checkbox" name="notify_by_email" id="notify_by_email" value="">
                                    <label for="notify_by_email">Send also an email for this reminder</label>
                                </div>
                            </div> -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default close-reminder-modal" data-rel-id="1" data-rel-type="sam" fdprocessedid="vrjze" value="">Close</button>
                    <button type="button" id="add_sam_reminder_btn" class="btn btn-primary" fdprocessedid="tsbpm" value="">Save</button>
                </div>
            </form>        
        </div>
    </div>
</div> 

<div id="sam_reminder_modal_edit"></div>



<script>

init_selectpicker();
function init_selectpicker() {
    appSelectPicker()
}
//console.log($('ul.nav navbar-pills navbar-pills-flat nav-tabs nav-stacked').tabs() );

//$("ul.nav navbar-pills navbar-pills-flat nav-tabs nav-stacked").html('testing');    

$("body").on("click", ".close-reminder-modal", (function() {
        $(".add-reminder-modal-sam").modal("hide")
    }
))

$("body").on("click", ".close-edit-reminder-modal", (function() {
        //$(".edit-reminder-modal-sam-" + $(this).data("rel-id")).modal("hide")
        $(".edit-reminder-modal-sam-1").modal("hide")
    }
))

function get_sam_reminders(url) {
    requestGet(url).done((function(e) {
        //console.log(e);
        $("#table-reminders-sam tbody").html(e);     
        var active_tab = $("ul.nav navbar-pills navbar-pills-flat nav-tabs nav-stacked li a.active").html('testing');
    }
    ))
}

$('#add_sam_reminder_btn').on('click', function(e) { 
    e.preventDefault();                              
    data = {};                
    data.title = $('input[name=reminder_title]').val();
    data.date = $('#date').val();
    data.staff = $('#staff').val();    
    data.description = $('#description').val();
    data.rel_id = $('#rel_id').val();
    data.rel_type = $('#rel_type').val();
    $.post(admin_url + '<?=SAM_MODULE?>/reminders/add_reminder/<?=$sam_id?>/sam', data).done(function(response) {  
        response = JSON.parse(response);  
        //console.log(response);                                 
        alert_float(response[0],response[1]);  
        //$(".add-reminder-modal-sam").modal("hide");  
        //get_sam_reminders('<?=admin_url(SAM_MODULE."/reminders/get_reminders/".$sam_id)?>'); 
        $(window).off('beforeunload');
        window.location.reload();                                                                                                      
    });
});  


function edit_reminder_form(id,sam_id){
    //e.preventDefault();                              
    $.get(admin_url + '<?=SAM_MODULE?>/reminders/edit_reminder/'+id+'/'+sam_id).done(function(response) {  
        //response = JSON.parse(response);  
        //console.log(response);  
        $("#sam_reminder_modal_edit").html(response); 
        $(".edit-reminder-modal-sam-1").modal("show");
        //$(window).off('beforeunload');
        //window.location.reload();                                                                                                        
    });
}

function delete_reminder_record(id,sam_id){
    //e.preventDefault(); 
    confirm_delete()
    &&                             
    $.post(admin_url + '<?=SAM_MODULE?>/reminders/delete_reminder/'+id+'/'+sam_id,{}).done(function(response) {  
        response = JSON.parse(response); 
        alert_float(response[0],response[1]);  
        //get_sam_reminders('<?=admin_url(SAM_MODULE."/reminders/get_reminders/".$sam_id)?>');
        //$(window).off('beforeunload');
        window.location.reload();  
                                                                                                              
    });

}

</script>
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
function updateIsNotified(checkbox, reminderId) {
    const isChecked = checkbox.checked ? 1 : 0;

    $.ajax({
        url: "<?=admin_url('sales_marketing/update_isnotified')?>",
        type: "POST",
        data: {
            reminder_id: reminderId,
            isnotified: isChecked
        },
        success: function(response) {
            // SweetAlert success message
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Reminder status updated successfully!',
                confirmButtonText: 'Okay'
            });
        },
        error: function() {
            // SweetAlert error message
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'There was an error updating the status!',
                confirmButtonText: 'Try Again'
            });
        }
    });
}

</script>


<?php //init_tail(); ?>