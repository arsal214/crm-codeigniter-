<?php
if(isset($reminder_data[0]))
{
    $reminder_data = $reminder_data[0];
?>

    <div class="modal fade modal-reminder edit-reminder-modal-sam-1" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="display: block; padding-left: 17px;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="#" id="form-edit-reminder-lead" method="post" accept-charset="utf-8" novalidate="novalidate">
                    <div class="modal-header">
                        <button type="button" class="close close-edit-reminder-modal" data-rel-id="<?=$reminder_data['rel_id']?>" data-rel-type="sam" aria-label="Close" fdprocessedid="7o2bk6" value="">
                            <span aria-hidden="true">×</span>
                        </button>
                        <h4 class="modal-title" id="myModalLabel">
                            <i class="fa-regular fa-circle-question" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="This option allows you to never forget anything about your customers."></i>
                            <?=_l('sam_edit_deal_reminder')?>
                        </h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-12">                            
                                <input type="hidden" id="rel_id" name="rel_id" value="<?=$reminder_data['rel_id']?>">

                                <input type="hidden" id="rel_type1" name="rel_type1" value="<?=$reminder_data['rel_type']?>">

                                <input type="hidden" id="reminder_id" name="reminder_id" value="<?=$reminder_data['id']?>">

                                <div class="form-group" app-field-wrapper="description">
                                    <label for="description1" class="control-label"> 
                                        <small class="req text-danger"> </small>
                                        Title
                                    </label>
                                    <input type="text" id="reminder_title1" name="reminder_title1" class="form-control" value="<?=$reminder_data['title']?>">
                                </div> 
                                
                                <div class="form-group" app-field-wrapper="date">
                                    <label for="date1" class="control-label"> 
                                        <small class="req text-danger">* </small>
                                        Date to be notified
                                    </label>
                                    <div class="input-group date">
                                        <input type="text" id="date1" name="date1" class="form-control datetimepicker" data-date-min-date="" data-step="30" value="<?=$reminder_data['date']?>" autocomplete="off" fdprocessedid="e75rw">
                                        <div class="input-group-addon">
                                            <i class="fa-regular fa-calendar calendar-icon"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group" app-field-wrapper="staff1">
                                    <label for="staff1" class="control-label"> 
                                        <small class="req text-danger">* </small>
                                        Set reminder to
                                    </label>
                                    <div class="select-placeholder">
                                        <select id="staff1" name="staff1" class="selectpicker" data-width="100%">
                                            <?php
                                            if(isset($all_staff) && $all_staff!=""){
                                                foreach($all_staff as $s){
                                                    if($s['staffid']==$reminder_data['staff']){
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
                                    <label for="description1" class="control-label"> 
                                        <small class="req text-danger">* </small>
                                        Description
                                    </label>
                                    <textarea id="description1" name="description1" class="form-control" rows="4"><?=$reminder_data['description']?></textarea>
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
                        <button type="button" class="btn btn-default close-edit-reminder-modal" data-rel-id="<?=$reminder_data['rel_id']?>" data-rel-type="sam" fdprocessedid="vrjze" value="">Close</button>
                        <button type="submit" id="edit_sam_reminder_btn" class="btn btn-primary" fdprocessedid="tsbpm" value="">Save</button>
                    </div>
                </form>        
            </div>
        </div>
    </div> 

<script>
init_selectpicker();
function init_selectpicker() {
    appSelectPicker()
}

$('#edit_sam_reminder_btn').on('click', function(e) { 
    e.preventDefault();                              
    data = {};     
    data.title = $('input[name=reminder_title1]').val();           
    reminder_id = $('#reminder_id').val();
    data.date = $('#date1').val();
    data.staff = $('#staff1').val();    
    data.description = $('#description1').val();
    //data.rel_id = $('#rel_id1').val();
    //data.rel_type = $('#rel_type1').val();
    $.post(admin_url + '<?=SAM_MODULE?>/reminders/edit_reminder/'+reminder_id+'/'+<?=$reminder_data['rel_id']?>, data).done(function(response) {  
        response = JSON.parse(response);  
        //console.log(response);                                 
        alert_float(response[0],response[1]);  
        //$(".edit-reminder-modal-sam-1").modal("hide");  
        //get_sam_reminders('<?=admin_url(SAM_MODULE."/reminders/get_reminders/".$reminder_data['rel_id'])?>');                                                                                                       
        $(window).off('beforeunload');
        window.location.reload(); 
    });
}); 
</script>    
<?php
}
?>

<?php //init_tail(); ?>

