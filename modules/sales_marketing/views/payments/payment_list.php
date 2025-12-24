<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
if(!isset($sam_id)){ $sam_id = $this->uri->segment(4); }

//$this->load->model('sam_model');
$condition = array('sam_id' => $sam_id);
if(!is_admin()){
    $condition['invoices.addedfrom'] = get_staff_user_id();    
}
$payment_rec = $this->sam_model->getPaymentRecords($sam_id,'', []);
// ?>
<div class="table-responsive">
    <table class="table table-sam-payments dataTable no-footer">
        <thead>
            <tr role="row">
                <th><?=_l('sam_payment#')?></th>
                <th><?=_l('sam_invoice#')?></th>
                <th><?=_l('sam_payment_mode')?></th>
                <th><?=_l('sam_transactionid')?></th>
                <th><?=_l('sam_customer')?></th>
                <th><?=_l('amount')?></th>
                <th><?=_l('date')?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            if($payment_rec){
                foreach($payment_rec as $k => $v){
                    
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
                            <a href="<?=admin_url(SAM_MODULE.'/payments/payment/'.$v['id'])?>" target="_blank"><?=$v['id']?></a>
                            <div class="row-options">             
                                <a href="<?=admin_url(SAM_MODULE.'/payments/payment/'.$v['id'])?>" target="_blank">View</a>
                                <?php
                                if (staff_can('delete', 'payments')) {
                                ?>    
                                | <a href="<?=admin_url(SAM_MODULE.'/payments/delete/'.$v['id'].'/'.$sam_id)?>" class="text-danger _delete">Delete</a>
                                <?php
                                }
                                ?>    
                                
                            </div>
                        </td> 
                        <td>
                            <a href="<?=admin_url(SAM_MODULE.'/invoices/#'.$v['invoiceid'])?>" target="_blank"><?=format_invoice_number($v['invoiceid'])?></a>
                        </td>                      
                        <td><?=$v['paymentmode']?></td>
                        <td><?=$v['transactionid']?></td>
                        <td><a href="<?=admin_url('clients/client/'.$v['clientid'])?>" target="_blank"><?=$v['company']?></a></td>
                        <td><?=$currency_symbol.$v['amount']?></td>
                        <td><?=$v['date']?></td>   
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

