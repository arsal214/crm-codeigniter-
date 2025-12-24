<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="panel-table-full">
                <?php 
                (isset($proposal)) ? $data['proposal'] = $proposal : $data['proposal'] = null;
                $this->load->view(SAM_MODULE.'/proposals/list_template',$data); 
                ?>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('admin/includes/modals/sales_attach_file'); ?>
<script>
var hidden_columns = [4, 5, 6, 7, 8];
</script>
<?php init_tail(); ?>
<div id="convert_helper"></div>
<script>
var proposal_id = '<?=$proposal_id?>';  
$(function() {
    var Proposals_ServerParams = {};
    $.each($('._hidden_inputs._filters input'), function() {
        Proposals_ServerParams[$(this).attr('name')] = '[name="' + $(this).attr('name') + '"]';
    });
/*    initDataTable('.table-proposals', admin_url + '<?=SAM_MODULE?>/proposals/table', ['undefined'], ['undefined'],
        Proposals_ServerParams, [8, 'desc']);  */
    init_proposal();
});

// Init single proposal
function init_proposal(id) {
  load_small_table_item(
    id,
    "#proposal",
    "proposal_id",
    "proposals/get_proposal_data_ajax",
    ".table-proposals"
  );
}

function load_small_table_item(id, selector, input_name, url, table) {
  var _tmpID = $('input[name="' + input_name + '"]').val();
  // Check if id passed from url, hash is prioritized becuase is last
  if (_tmpID !== "" && !window.location.hash) {   
    id = _tmpID;
    // Clear the current id value in case user click on the left sidebar credit_note_ids
    $('input[name="' + input_name + '"]').val("");
  } else {
    // check first if hash exists and not id is passed, becuase id is prioritized
    if (window.location.hash && !id) {
      id = window.location.hash.substring(1); //Puts hash in variable, and removes the # character   
    }
  }
  if (typeof id == "undefined" || id === "") {
    return;
  }
  destroy_dynamic_scripts_in_element($(selector));
  if (!$("body").hasClass("small-table")) {
    toggle_small_view(table, selector);
  }
  $('input[name="' + input_name + '"]').val(id);
  do_hash_helper(id);
  $(selector).load(admin_url + "<?=SAM_MODULE?>/" + url + "/" + id)

  $("html, body").animate(
    {
      scrollTop: $(selector).offset().top + (is_mobile() ? 150 : 0),
    },
    600
  );
}

</script>
</body>

</html>