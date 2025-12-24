<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
if(!isset($sam_id)){ $sam_id = $this->uri->segment(4); }

//$this->load->model('sam_model');
$condition = array('sam_id' => $sam_id);
if(!is_admin()){
    $condition['invoices.addedfrom'] = get_staff_user_id();    
}
$payment_rec = $this->sam_model->getPaymentRecords($sam_id,'', []);
//echo "<pre>"; print_r($invoice_rec); exit;
?>
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
                            <a href="<?=admin_url('payments/payment/'.$v['id'])?>" target="_blank"><?=$v['id']?></a>
                            <div class="row-options">             
                                <a href="<?=admin_url('payments/payment/'.$v['id'])?>" target="_blank">View</a>
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
                            <a href="<?=admin_url('invoices/list_invoices/'.$v['invoiceid'])?>" target="_blank"><?=format_invoice_number($v['invoiceid'])?></a>
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

