<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php  
if(!isset($sam_id)){ $sam_id = $this->uri->segment(4); }
$condition = array('sam_id' => $sam_id);
if(!is_admin() && staff_cant('view', 'proposals')){  
    $condition['proposals.addedfrom'] = get_staff_user_id();    
}                                                 
$proposal_rec = $this->sam_proposals_model->get('',$condition);

$this->load->model('currencies_model');
$base_currency = $this->currencies_model->get_base_currency();
//echo "<pre>"; print_r($proposal_rec); exit;
?>   
 
    
<div class="table-responsive">
    <div class="tw-mb-2 sm:tw-mb-6">
        <div class="_buttons">
            <?php if (1) { ?>
            <a href="<?php echo admin_url(SAM_MODULE.'/proposals/proposal/0/'.$sam_id); ?>"
                class="btn btn-primary pull-left display-block new-proposal-btn">
                <i class="fa-regular fa-plus tw-mr-1"></i>
                <?php echo _l('sam_new_proposal'); ?>
            </a>
            <?php } ?> 
        </div>  
    </div>
    <table data-last-order-identifier="proposals" data-default-order="" id="proposals" class="table table-proposals dataTable no-footer" role="grid" aria-describedby="proposals_info">
        <thead>
            <tr role="row">
                <th class="sorting" tabindex="0" aria-controls="proposals" rowspan="1" colspan="1" aria-label="Proposal # activate to sort column ascending">
                <?=_l('sam_proposal_num')?>
                </th>
                <th class="sorting" tabindex="0" aria-controls="proposals" rowspan="1" colspan="1" aria-label="Subject activate to sort column ascending">
                <?=_l('sam_proposal_subject')?>
                </th>
                <th class="sorting" tabindex="0" aria-controls="proposals" rowspan="1" colspan="1" aria-label="Total activate to sort column ascending">
                <?=_l('sam_proposal_total')?>
                </th>
                <th class="sorting" tabindex="0" aria-controls="proposals" rowspan="1" colspan="1" aria-label="Date activate to sort column ascending">
                <?=_l('sam_proposal_date')?>
                </th>
                <th class="sorting" tabindex="0" aria-controls="proposals" rowspan="1" colspan="1" aria-label="Open Till activate to sort column ascending">
                <?=_l('sam_proposal_opentill')?>
                </th> 
                <th class="sorting sorting_desc" tabindex="0" aria-controls="proposals" rowspan="1" colspan="1" aria-sort="descending" aria-label="Date Created activate to sort column ascending">
                <?=_l('sam_proposal_created_date')?>
                </th>
                <th class="sorting" tabindex="0" aria-controls="proposals" rowspan="1" colspan="1" aria-label="Status activate to sort column ascending">
                <?=_l('sam_proposal_status')?>
                </th>
            </tr>
        </thead>
        <tbody>
            <?php
            if($proposal_rec){
                foreach($proposal_rec as $key => $val){                                                       
                    $pro_id = e(format_proposal_number($val['id']));
            ?>    
                    <tr class="has-row-options odd">
                        <td>
                            <a href="<?=admin_url().SAM_MODULE?>/proposals#<?=$val['id']?>" target="_blank">
                                <?=$pro_id?>
                            </a>
                            <div class="row-options">
                                <a href="<?=site_url()?>proposal/<?=$val['id'].'/'.$val['hash']?>" target="_blank">
                                View
                                </a>
                                 | 
                                <a href="<?=admin_url().SAM_MODULE?>/proposals/proposal/<?=$val['id'].'/'.$sam_id?>">
                                Edit
                                </a>
                            </div>
                        </td>
                        <td>
                            <a href="<?=admin_url().SAM_MODULE?>/proposals#<?=$val['id']?>" target="_blank">
                            <?=$val['subject']?>
                            </a>
                        </td>    
                        <td>
                        <?php
                            if($base_currency && isset($base_currency->symbol)){
                                echo e(app_format_money($val['total'], $base_currency));
                                //echo e($base_currency->symbol).$v['contract_value'];    
                            }
                            else{
                                echo $val['total'];
                            }
                        ?>
                        </td>
                        <td><?=$val['date']?></td>
                        <td><?=$val['open_till']?></td>                     
                        <td class="sorting_1"><?=$val['datecreated']?></td>
                        <td>                          
                            <?=format_proposal_status($val['status'], 'mtop5 inline-block');?>
                        </td>
                    </tr>    
            <?php        
                }
            }
            ?>
        </tbody>
    </table>
</div>


<script>
var hidden_columns = [4, 5, 6, 7, 8];
</script>
<?php init_tail(); ?>
<div id="convert_helper"></div>
<script>
/*var proposal_id;
$(function() {
    var Proposals_ServerParams = {};
    $.each($('._hidden_inputs._filters input'), function() {
        Proposals_ServerParams[$(this).attr('name')] = '[name="' + $(this).attr('name') + '"]';
    });
    initDataTable('.table-proposals', admin_url + 'proposals/table', ['undefined'], ['undefined'],
        Proposals_ServerParams, [8, 'desc']);
    init_proposal();
}); */

function init_sam_proposal(e) {
    load_small_table_item(e, "#proposal", "proposal_id", "<?=SAM_MODULE?>/proposals/get_proposal_data_ajax", ".table-proposals")
}

function load_small_table_item(e, t, a, i, n) {
    var s = $('input[name="' + a + '"]').val();
    "" === s || window.location.hash ? window.location.hash && !e && (e = window.location.hash.substring(1)) : (e = s,
    $('input[name="' + a + '"]').val("")),
    void 0 !== e && "" !== e && (destroy_dynamic_scripts_in_element($(t)),
    $("body").hasClass("small-table") || toggle_small_view(n, t),
    $('input[name="' + a + '"]').val(e),
    do_hash_helper(e),
    $(t).load(admin_url + i + "/" + e),
    $("html, body").animate({
        scrollTop: $(t).offset().top + (is_mobile() ? 150 : 0)
    }, 600))
}

function toggle_small_view(e, t) {
    if (!is_mobile() && $("#small-table").hasClass("hide") && $(".small-table-right-col").hasClass("col-md-12"))
        return $("#small-table").toggleClass("hide"),
        $(".small-table-right-col").toggleClass("col-md-12 col-md-7"),
        void $(window).trigger("resize");
    $("body").toggleClass("small-table");
    var a = $("#small-table");
    if (0 !== a.length) {
        var i = !1;
        a.hasClass("col-md-5") ? (a.removeClass("col-md-5").addClass("col-md-12"),
        i = !0,
        $(".toggle-small-view").find("i").removeClass("fa fa-angle-double-right").addClass("fa fa-angle-double-left")) : (a.addClass("col-md-5").removeClass("col-md-12"),
        $(".toggle-small-view").find("i").removeClass("fa fa-angle-double-left").addClass("fa fa-angle-double-right"));
        var n = $(e).DataTable();
        n.columns(hidden_columns).visible(i, !1),
        n.columns.adjust(),
        $(t).toggleClass("hide"),
        $(window).trigger("resize")
    }
}

function destroy_dynamic_scripts_in_element(t)
{
    t.find("input.tagsinput").tagit("destroy").find(".manual-popover").popover("destroy").find(".datepicker").datetimepicker("destroy").find("select").selectpicker("destroy")
}
</script>