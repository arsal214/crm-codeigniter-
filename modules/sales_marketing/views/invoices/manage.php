<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div id="vueApp">
			<div class="row">
				<?php //include_once(APPPATH.'views/admin/invoices/filter_params.php'); ?>
				<?php 
                $data['invoiceid'] = $invoiceid;
                $this->load->view(SAM_MODULE.'/invoices/list_template',$data); 
                ?>
			</div>
		</div>
	</div>
</div>
<?php $this->load->view('admin/includes/modals/sales_attach_file'); ?>
<div id="modal-wrapper"></div>
<script>var hidden_columns = [2,6,7,8];</script>
<?php init_tail(); ?>
<script>
$(function(){
	init_invoice();
});

// Init single invoice
function init_invoice(id) {
  load_small_table_item(
    id,
    "#invoice",
    "invoiceid",
    "<?=SAM_MODULE?>/invoices/get_invoice_data_ajax",
    ".table-invoices"
  );
}

</script>
</body>
</html>