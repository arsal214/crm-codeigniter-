<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
if(!isset($sam_id)){ $sam_id = $this->uri->segment(4); }

$this->load->model('invoices_model');
$condition = array('sam_id' => $sam_id);
if(!is_admin() && staff_cant('view', 'invoices')){ 
    $condition['invoices.addedfrom'] = get_staff_user_id();    
}
$invoice_rec = $this->invoices_model->get('', $condition);
//echo "<pre>"; print_r($invoice_rec); exit;
?>
<div class="table-responsive">
    <div>
        <?php
        if(staff_can('create', 'invoices')){
        ?>
            <a href="<?=admin_url(SAM_MODULE.'/invoices/invoice/0/'.$sam_id)?>" data-toggle="modal" data-target="#myModal_xl2" class="btn btn-primary pull-left display-block tw-mb-2 sm:tw-mb-4">
                <i class="fa-regular fa-plus tw-mr-1"></i>
                <?=_l('sam_new_invoice')?>                    
            </a>
        <?php
        }
        ?>
    </div>
    <table class="table table-sam-invoices dataTable no-footer">
        <thead>
            <tr role="row">
                <th><?=_l('sam_invoice#')?></th>
                <th><?=_l('amount')?></th>
                <th><?=_l('sam_total_tax')?></th>
                <th><?=_l('sam_date')?></th>
                <th><?=_l('sam_customer')?></th>
                <th><?=_l('due_date')?></th>
                <th><?=_l('status')?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            if($invoice_rec){
                foreach($invoice_rec as $k => $v){
                    $status = "<span class='label label-danger  s-status invoice-status-1'>"._l('sam_unpaid')."</span>";
                    if($v['status']==2){
                        $status = "<span class='label label-success  s-status invoice-status-2'>"._l('sam_paid')."</span>";
                    }
                    elseif($v['status']==3){
                        $status = "<span class='label label-warning  s-status invoice-status-3'>"._l('sam_partially_paid')."</span>";
                    }
                    elseif($v['status']==4){
                        $status = "<span class='label label-warning  s-status invoice-status-3'>"._l('sam_overdue')."</span>";
                    }
                    elseif($v['status']==5){
                        $status = "<span class='label label-default  s-status invoice-status-5'>"._l('sam_cancelled')."</span>";
                    }
                    elseif($v['status']==6){
                        $status = "<span class='label label-default  s-status invoice-status-6'>"._l('sam_draft')."</span>";
                    }
                    $customer_name = "";
                    $customer_info = get_client($v['clientid']);
                    if(isset($customer_info->company)){
                        $customer_name = $customer_info->company;
                    }
                    $currency_symbol = "";
                    $currency = get_currency($v['currency']);
                    if(isset($currency->symbol)){
                        $currency_symbol = $currency->symbol;
                    }
            ?>
                    <tr>  
                        <td>
                            <a href="<?=admin_url('invoices#/'.$v['id'])?>" target="_blank"><?=format_invoice_number($v['id'])?></a>
                            <div class="row-options">             
                                <a href="<?=site_url('invoice/'.$v['id'].'/'.$v['hash'])?>" target="_blank"><?=_l('sam_view')?></a> 
                                <?php
                                if (staff_can('edit', 'invoices')) {
                                ?>
                                    |<a href="<?=admin_url('invoices/invoice/'.$v['id'])?>" target="_blank"><?=_l('sam_edit')?></a>                    
                                <?php
                                }
                                ?> 
            
                            </div>
                        
                        </td>                      
                        <td><?=$currency_symbol.$v['total']?></td>
                        <td><?=$currency_symbol.$v['total_tax']?></td>
                        <td><?=$v['date']?></td>
                        <td><a href="<?=admin_url('clients/client/'.$v['clientid'])?>" target="_blank"><?=$customer_name?></a></td>
                        <td><?=$v['duedate']?></td>
                        <td><?=$status?></td>   
                    </tr>
            <?php
                }
            }   
            ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="myModal_xl2" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-xl2">
        <div class="modal-content">
            <div class="modal-header" style="padding:0.7em;">
                <button type="button" class="close" data-dismiss="modal" data-rel-id="1" data-rel-type="sam" aria-label="Close" value="">
                    <span aria-hidden="true">×</span>
                </button>
            </div>    
        </div>
    </div>
</div>

<script>
$(".modal-xl2").attr('style','width:80%');

/*$('#myModal_xl2').on('show.bs.modal', function () {
    
    setTimeout(function(){
        var title = $('.panel-title').text();
        if(title==""){
            title = "Invoice Info";
        }
        var content ='<div class="modal-header" style="padding:0.7em"><button type="button" class="close" data-dismiss="modal" data-rel-id="1" data-rel-type="sam" aria-label="Close" value="" fdprocessedid="21y2vf">'+
                        '<span aria-hidden="true">×</span>'+
                    '</button>'+
                    '<h4 class="modal-title" id="myModalLabel">'+title+'</h4></div>';
        //$('.panel_s .modal-content .panel-heading').attr('style','padding:0.7em');
        //$('.panel_s .modal-content .panel-heading').html(content);  
        
        //$(".modal-xl2 .modal-content").prepend(content);  
    }     
    , 2000);
});
  
function record_payment(e) { 
    void 0 !== e && "" !== e && $("#invoice").load(admin_url + "invoices/record_invoice_payment_ajax/" + e)
} */

</script>
