<?php 
defined('BASEPATH') or exit('No direct script access allowed'); 
if(isset($invoiceid)){
    $invoice = $this->invoices_model->get($invoiceid); //echo "<pre>"; print_r($invoice); exit;
    $invoice = $invoice[0];
}
?>

<div class="col-md-12"> 
    <div class="row"> 
        <div class="col-md-12 small-table-right-col">
            <div id="invoice" class=""></div>
        </div>
    </div>
</div>