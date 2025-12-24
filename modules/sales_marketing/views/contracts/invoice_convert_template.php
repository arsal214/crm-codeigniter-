<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade proposal-convert-modal" id="convert_to_invoice" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-xxl" role="document">
        <?php echo form_open(SAM_MODULE.'/contracts/convert_to_invoice/' . $proposal->id .'/'. $proposal->sam_id, ['id' => 'contract_convert_to_invoice_form', 'class' => '_transaction_form invoice-form']); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" onclick="close_modal_manually('#convert_to_invoice')" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('proposal_convert_to_invoice'); ?></span>
                </h4> 
                <?php if(isset($proposal)) {?>
                    <div class="row-options" style="font-size:1.25em">
                        <?php echo "Proposal #: " ?>
                        <a href="<?php echo admin_url(SAM_MODULE.'/proposals/#' . $proposal->id); ?>" target="_blank">
                            <?php echo e(format_proposal_number($proposal->id)); ?>
                        </a>
                         | <?php echo "Contract #: " ?>
                        <a href="<?php echo admin_url(SAM_MODULE.'/contracts/contract/' . $proposal->contract_id .'/'.$proposal->sam_id); ?>" target="_blank">
                            <?php echo $proposal->contract_id; ?>
                        </a>
                    
                    </div>
                <?php } ?>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php 
                        $data['proposal'] = $proposal; 
                        $this->load->view(SAM_MODULE.'/invoices/invoice_template',$data); ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-default invoice-form-submit save-as-draft transaction-submit">
                    <?php echo _l('save_as_draft'); ?>
                </button>
                <button class="btn btn-primary invoice-form-submit transaction-submit">
                    <?php echo _l('submit'); ?>
                </button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
<?php $this->load->view(SAM_MODULE.'/invoice_items/item'); ?>
<script>
    init_ajax_search('customer','#clientid.ajax-search');
    init_ajax_search('items','#item_select.ajax-search',undefined,admin_url+'items/search');
    custom_fields_hyperlink();
    init_selectpicker();
    init_tags_inputs();
    init_datepicker();
    init_color_pickers();
    init_items_sortable();
    validate_invoice_form('#contract_convert_to_invoice_form');
    <?php if ($proposal->assigned != 0) { ?>
     $('#convert_to_invoice #sale_agent').selectpicker('val',<?php echo e($proposal->assigned); ?>);
    <?php } ?>
    $('select[name="discount_type"]').selectpicker('val','<?php echo e($proposal->discount_type); ?>');
    $('input[name="discount_percent"]').val('<?php echo e($proposal->discount_percent); ?>');
    $('input[name="discount_total"]').val('<?php echo e($proposal->discount_total); ?>');
    <?php if (is_sale_discount($proposal, 'fixed')) { ?>
        $('.discount-total-type.discount-type-fixed').click();
    <?php } ?>
    $('input[name="adjustment"]').val('<?php echo e($proposal->adjustment); ?>');
    $('input[name="show_quantity_as"][value="<?php echo e($proposal->show_quantity_as); ?>"]').prop('checked',true).change();
    <?php if (!isset($project_id) || !$project_id) { ?>
        $('#convert_to_invoice #clientid').change();
    <?php } else { ?>
        $('#convert_to_invoice select#currency').val("<?php echo $proposal->currency ?>").trigger('change');
        init_ajax_project_search_by_customer_id('select#project_id');
    <?php } ?>
    // Trigger item select width fix
    $('#convert_to_invoice').on('shown.bs.modal', function(){
        $('#item_select').trigger('change')
    })
</script>
