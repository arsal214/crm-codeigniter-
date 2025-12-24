<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php
            echo form_open($this->uri->uri_string(), ['id' => 'invoice-form', 'class' => '_transaction_form invoice-form']);
            $data['invoice'] = "";
            if (isset($invoice)) {
                echo form_hidden('isedit');
                $data['invoice'] = $invoice;
            }
            ?>
            <div class="col-md-12">
                <h4
                    class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700 tw-flex tw-items-center tw-space-x-2">
                    <span>
                        <?php echo e(isset($invoice) ? format_invoice_number($invoice) : _l('create_new_invoice')); ?>
                    </span>
                    <?php echo isset($invoice) ? format_invoice_status($invoice->status) : ''; ?>
                </h4>      
                <?php if(isset($invoice)) {?>
                    <div class="row-options" style="font-size:1.25em">
                        <?php echo "Proposal #: " ?>
                        <a href="<?php echo admin_url(SAM_MODULE.'/proposals/#' . $invoice->pro_id); ?>" target="_blank">
                            <?php echo e(format_proposal_number($invoice->pro_id)); ?>
                        </a>
                         | <?php echo "Contract #: " ?>
                        <a href="<?php echo admin_url(SAM_MODULE.'/contracts/contract/' . $invoice->contract_id .'/'.$invoice->sam_id); ?>" target="_blank">
                            <?php echo $invoice->contract_id; ?>
                        </a>
                    
                    </div>
                <?php } ?>
                <?php $this->load->view(SAM_MODULE.'/invoices/invoice_template',$data); ?>
            </div>
            <?php echo form_close(); ?>
            <?php $this->load->view(SAM_MODULE.'/invoice_items/item'); ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
$(function() {
    validate_invoice_form();
    // Init accountacy currency symbol
    init_currency();
    // Project ajax search
    init_ajax_project_search_by_customer_id();
    // Maybe items ajax search
    init_ajax_search('items', '#item_select.ajax-search', undefined, admin_url + 'items/search');
});
</script>
</body>

</html>