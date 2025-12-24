<?php
$p_subject="";
$p_dealvalue="";
$p_id="";
if($proposal_data){
    $p_id = $proposal_data['id'];
    $p_subject = $proposal_data['subject'];
    $p_dealvalue = $proposal_data['total'];
}
?>
<div class="modal-header">
    <button type="button" class="close" onclick="close_modal_manually('#myModal')" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
    <h4 class="modal-title" id="myModalLabel">
        <span class="edit-title">Create New Contract</span>
    </h4>
</div>
<div class="panel_s">
    <div class="panel-body">
        <form action="<?=admin_url(SAM_MODULE.'/contracts/contract/0/'.$sam_id)?>" id="sam-contract-form" method="post" accept-charset="utf-8" novalidate="novalidate">                     
            <input type="hidden" class="" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
            <div class="form-group">
                <div class="checkbox checkbox-primary no-mtop checkbox-inline">
                    <input type="checkbox" id="trash" name="trash">
                    <label for="trash">
                        <i class="fa-regular fa-circle-question" data-toggle="tooltip" data-placement="right" title="If you add contract to trash, won't be shown on client side, won't be included in chart and other stats and also by default won't be shown when you will list all contracts."></i>
                        <?=_l('sam_trash')?>
                    </label>
                </div>
                <div class="checkbox checkbox-primary checkbox-inline">
                    <input type="checkbox" name="not_visible_to_client" id="not_visible_to_client">
                    <label for="not_visible_to_client">
                        <?=_l('sam_hidefrom_customer')?>                                
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label for="pro_id" class="control-label">
                    <span class="text-danger">*</span>
                    Proposal
                </label>
                <div class="dropdown" style="width: 100%;">
                    <select id="pro_id" name="pro_id" class="selectpicker" data-width="100%" class="" readonly>
                        <?php
                        if(isset($p_id)){                                           
                            echo '<option value="'.$p_id.'"  selected="selected">'.e(format_proposal_number($p_id)).'</option>';
                        }
                        ?>
                    </select>
                </div>
            </div>
            <div class="form-group f_client_id">
                <label for="clientid" class="control-label">
                    <span class="text-danger">*</span>
                    <?=_l('sam_customer')?>
                </label>
                <div class="dropdown" style="width: 100%;">
                    <select id="clientid" name="client" class="selectpicker" data-width="100%" class="" readonly>
                        <?php
                        if(isset($customer_id)){
                            $customer_info = get_client($customer_id);
                            if(isset($customer_info->company)){
                                echo '<option value="'.$customer_info->userid.'"  selected="selected">'.$customer_info->company.'</option>';
                            }
                        }
                        ?>
                    </select>
                </div>
            </div>

            <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" title="Subject is also visible to customer"></i>
            <div class="form-group" app-field-wrapper="subject">
                <label for="subject" class="control-label"> 
                    <small class="req text-danger">* </small>
                    <?=_l('sam_subject')?>
                </label>
                <input type="text" id="subject" name="subject" class="form-control" value="<?=$p_subject?>" readonly>
            </div>                        
            <div class="form-group">
                <label for="contract_value"><?=_l('sam_contract_value')?></label>
                <div class="input-group" data-toggle="tooltip" title="" data-original-title="Base currency will be used.">
                    <input type="number" class="form-control" name="contract_value" value="<?=$p_dealvalue?>" readonly>
                    <div class="input-group-addon">
                        <?php echo e($base_currency->symbol); ?>                                
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label for="contract_type" class="control-label">
                    <span class="contract_type_label"><?=_l('sam_contract_type')?></span>
                </label>
                <div class="dropdown" style="width: 100%;">
                    <select name="contract_type" id="contract_type" class="selectpicker _select_input_group" data-width="100%"
                        data-live-search="true"
                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <option value=""></option> 
                        <?php
                        if($types){
                            foreach($types as $val){
                                echo "<option value='".$val['id']."'>".$val['name']."</option>";
                            }    
                        }
                        ?>
                    </select> 
                </div>
            </div>
            
<!--            <div class="form-group form-group-select-input-contract_type input-group-select" id=""> 
                <label for="contract_type" class="control-label">
                    <span class="contract_type_label">Contract type</span>
                </label>
                
                <div class="input-group input-group-select select-contract_type">  
                    <div class="dropdown bootstrap-select input-group-btn _select_input_group bs3" app-field-wrapper="contract_type" style="width: 100%;">
                        <select name="contract_type" id="contract_type" class="selectpicker _select_input_group" data-width="100%"
                            data-live-search="true"
                            data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                            <option value=""></option> 
                            <?php
                /*            if($types){
                                foreach($types as $val){
                                    echo "<option value='".$val['id']."'>".$val['name']."</option>";
                                }    
                            } */
                            ?>
                        </select>     
                    </div>
                    <div class="input-group-btn">
                        <a href="#" class="btn btn-default" onclick="new_type();return false;">
                            <i class="fa fa-plus"></i>
                        </a>
                    </div>                                       
                </div>    
            </div> -->
                        
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group" app-field-wrapper="datestart">
                        <label for="datestart" class="control-label"> 
                            <small class="req text-danger">* </small>
                            <?=_l('start_date')?>
                        </label>
                        <div class="input-group date">
                            <input type="text" id="datestart" name="datestart" class="form-control datepicker" value="" autocomplete="off">
                            <div class="input-group-addon">
                                <i class="fa-regular fa-calendar calendar-icon"></i>
                            </div>
                        </div>
                    </div>                            
                </div>
                <div class="col-md-6">
                    <div class="form-group" app-field-wrapper="dateend">
                        <label for="dateend" class="control-label">
                            <?=_l('end_date')?>
                        </label>
                        <div class="input-group date">
                            <input type="text" id="dateend" name="dateend" class="form-control datepicker" value="" autocomplete="off" fdprocessedid="qlsyp">
                            <div class="input-group-addon">
                                <i class="fa-regular fa-calendar calendar-icon"></i>
                            </div>
                        </div>
                    </div>                            
                </div>
            </div>
            <div class="form-group" app-field-wrapper="description">
                <label for="description" class="control-label">
                    <?=_l('sam_description')?>
                </label>
                <textarea id="description" name="description" class="form-control" rows="10"></textarea>
            </div>                                                
            <div class="text-right tw-space-x-1">
                <button type="submit" class="btn btn-primary" data-form="#sam-contract-form"><?=_l('sam_save')?></button>
            </div>
        </form>                    
    </div>
</div>

<script>

init_datepicker();

$('body').find('select.selectpicker').not('.ajax-search').selectpicker({
    showSubtext: true,
}); 

$(function() {
    //init_ajax_project_search_by_customer_id();

    appValidateForm($('#sam-contract-form'), {
        client: 'required',
        datestart: 'required',
        subject: 'required'
    },contractFormHandler2);

    /*appValidateForm($('#renew-contract-form'), {
        new_start_date: 'required'
    });*/
       
});

function contractFormHandler2(form) {                                     

    var formURL = $(form).attr("action");
    var formData = new FormData($(form)[0]);

    $.ajax({
        type: 'POST',
        data: formData,
        mimeType: "multipart/form-data",
        contentType: false,
        cache: false,
        processData: false,
        url: formURL
    }).done(function(response) {
        response = JSON.parse(response);
        if (response.success) {
            alert_float('success', response.message);
            $("#myModal").modal('hide'); 
            $(window).off('beforeunload');
            window.location.reload(); 
        }

    }).fail(function(error) {
        $("#myModal").modal('hide');
        alert_float('danger', JSON.parse(error.responseText));
    });
    
    return false;
} 

function new_type(){
    $('#type').modal('show');
    $('.edit-title').addClass('hide');
}  

</script>
