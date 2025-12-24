<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
if(!isset($sam_id)){ $sam_id = $this->uri->segment(4); }  

$this->load->model('contracts_model');
$condition = array('sam_id' => $sam_id);
if(!is_admin() && staff_cant('view', 'contracts')){  
    $condition['contracts.addedfrom'] = get_staff_user_id();    
}
$contract_rec = $this->contracts_model->get('', $condition, true);
// echo "<pre>"; print_r($contract_rec); exit;
$this->load->model('currencies_model');
$base_currency = $this->currencies_model->get_base_currency();
//echo "<pre>"; print_r($contract_rec); exit;
?>
<div class="table-responsive">
      <?php if (1) { ?>
            <a href="<?php echo admin_url(SAM_MODULE.'/contracts/contract/0/'.$sam_id); ?>"
                class="btn btn-primary pull-left display-block new-contract-btn">
                <i class="fa-regular fa-plus tw-mr-1"></i>
                <?php echo _l('sam_new_contract'); ?>
            </a>
            <?php } ?>
    <table class="table table-sam-contracts dataTable no-footer">
        <thead>
            <tr role="row">
                <th><?=_l('sam_subject')?></th>
                <th><?=_l('sam_customer')?></th>
                <th><?=_l('sam_contract_type')?></th>
                <th><?=_l('sam_contract_value')?></th>
                <th><?=_l('start_date')?></th>
                <th><?=_l('end_date')?></th>
                <th><?=_l('sam_signature')?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            if($contract_rec){
                foreach($contract_rec as $k => $v){
                    $signed = "Not Signed";
                    if($v['signed']==1 || $v['marked_as_signed'] == 1){
                        $signed = '<span class="text-success">Signed</span>';
                    }
                    $trashed = '';
                    if($v['trash']==1){
                        $trashed = '<span class="label label-danger pull-right">Trash</span>';
                    }
                    $tr_class = '';
                    if($v['dateend'] < date('Y-m-d')){
                        $tr_class = 'class="danger has-row-options odd"';
                    }
            ?>
                    <tr <?=$tr_class?>>                        
                        <td>
                            <?php
                            if (staff_can('view', 'contracts') || staff_can('view_own', 'contracts')) {
                            ?>
                            <a href="<?=admin_url(SAM_MODULE.'/contracts/contract/'.$v['id'].'/'.$v['sam_id'])?>" target="_blank"><?=$v['subject']?></a>
                            <?php
                            }
                            else{
                                echo $v['subject'];
                            }
                            echo $trashed;
                            ?>
                            <div class="row-options"> 
                                <a href="<?=site_url('contract/'.$v['id'].'/'.$v['hash'])?>" target="_blank"><?=_l('sam_view')?></a>
                                <?php
                                if (staff_can('edit', 'contracts')) {
                                ?>
                                    | 
                                    <a href="<?=admin_url(SAM_MODULE.'/contracts/contract/'.$v['id'].'/'.$v['sam_id'])?>" target="_blank"><?=_l('sam_edit')?></a> 
                                <?php                                    
                                }
                                if (staff_can('delete', 'contracts')) {
                                ?>
                                    | 
                                    <a href="<?=admin_url(SAM_MODULE.'/contracts/delete/'.$v['id'].'/'.$v['sam_id'])?>" class="text-danger _delete"><?=_l('sam_delete')?></a>
                                <?php
                                }
                                ?>
                            </div>
                        </td>
                        <td><a href="<?=admin_url('clients/client/'.$v['client'])?>" target="_blank"><?=$v['company']?></a></td>
                        <td><?=$v['type_name']?></td>
                        <td>
                        <?php
                            if($base_currency && isset($base_currency->symbol)){
                                echo e(app_format_money($v['contract_value'], $base_currency));
                                //echo e($base_currency->symbol).$v['contract_value'];    
                            }
                            else{
                                echo $v['contract_value'];
                            }
                            
                        ?>
                        </td>
                        <td><?=$v['datestart']?></td>
                        <td><?=$v['dateend']?></td>
                        <td><?=$signed?></td> 
                    </tr>
            <?php
                }
            }
            ?>
        </tbody>
    </table>
</div>

<!--<div class="modal fade" id="type" tabindex="2" role="dialog" style="display: none;">
    <div class="modal-dialog">
        <form action="<?=admin_url(SAM_MODULE.'/contracts/type')?>" id="contract-type-form" method="post" accept-charset="utf-8" novalidate="novalidate">
            <input type="hidden" class="" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">                                                                         
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    <h4 class="modal-title">
                        <span class="edit-title hide">Edit Contract Type</span>
                        <span class="add-title">New Contract Type</span>
                    </h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div id="additional"></div>
                            <div class="form-group" app-field-wrapper="name">
                            <label for="name" class="control-label"> <small class="req text-danger">* </small>
                            Name
                            </label>
                            <input type="text" id="name" name="name" class="form-control" value="">
                        </div>                    
                    </div>
                </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </div>
        </form>    
    </div>
</div>-->
