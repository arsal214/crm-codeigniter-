<?php
defined('BASEPATH') or exit('No direct script access allowed');
$data['sam_rec'] = $sam_rec;
//echo $this->uri->uri_string(); exit;
?>
<div class="modal-header" style="padding:0.7em;background-color:#2b384c;">
    <button type="button" class="close" data-dismiss="modal" data-rel-id="1" data-rel-type="sam" aria-label="Close" style="color:#ffffff">
        <span aria-hidden="true">×</span>
    </button>
    <h4 class="modal-title" id="myModalLabel" style="color:#ffffff">
        <?=_l('create_new_invoice')?>                   
    </h4>
</div>
<div class="panel_s">
    <div class="panel-body" style="padding:0.3em">
        <div class="row">
            <?php
            echo form_open($this->uri->uri_string(), ['id' => 'invoice-form', 'class' => '_transaction_form invoice-form']);
            ?>
            <div class="col-md-12">
                <?php $this->load->view(SAM_MODULE.'/invoices/invoice_template',$data); ?>
            </div>
            <?php echo form_close(); ?>
            <?php $this->load->view(SAM_MODULE.'/invoices/invoice_items'); ?>
        </div>                  
    </div>
</div>

<script>
 
$(function() { 
    init_datepicker(); 
    $('body').find('select.selectpicker').not('.ajax-search').selectpicker({
        showSubtext: true,
        refresh: true
    });
    
    $(".modal").on("hidden.bs.modal", function(e) { 
        $(".modal-xl2 .modal-content").html('');
    });
    
    //init_selectpicker();
    validate_invoice_form();
    // Init accountacy currency symbol
    init_currency();
    // Project ajax search
    init_ajax_project_search_by_customer_id();
    // Maybe items ajax search
    init_ajax_search('items', '#item_select.ajax-search', undefined, admin_url + 'items/search');
});

</script>
