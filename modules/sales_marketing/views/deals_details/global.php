<style>
#button {
  position: relative;
  float: right;
}

#addEventForm {
  position: relative;
  float: right;
  padding-left: 10px;
}
</style>
<link rel="stylesheet" id="color-opt" href="<?= module_dir_url(SAM_MODULE) ?>assets/css/style.css">
<?php


$propability = 0;

$all_stages = get_sam_order_by('tbl_sam_stages', array('pipeline_id' => $deals_details->pipeline_id), 'stage_order', 'asc');
$statuses   = get_sam_result('tbl_sam_status', null);
// total stages
if (!empty($all_stages)) {
    $total_stages = count($all_stages);
    foreach ($all_stages as $stage) {
        $res = round(100 / $total_stages);
        $propability += $res;
        if ($stage->stage_id == $deals_details->stage_id) {
            break;
        }
    }
}
if ($deals_details->status === 'won') {
    $propability = 100;
}
if ($deals_details->status === 'lost') {
    $propability = 0;
}


$outputStatus = '';

$outputStatus .= '<span class="" task-status-table="' . $deals_details->full_name . '">';

$outputStatus .= $deals_details->full_name;


$outputStatus .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
$outputStatus .= '<a href="#" style="font-size:14px;vertical-align:middle;" class="dropdown-toggle text-dark" id="tableTaskStatus-' . $deals_details->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
$outputStatus .= '<span data-toggle="tooltip" title="' . _l('change_deal_owner') . '"><i class="fa-solid fa-chevron-down tw-opacity-70"></i></span>';
$outputStatus .= '</a>';

$outputStatus .= '<ul class="dropdown-menu dropdown-menu-right" style="right:auto;left:auto">';
foreach ($staff as $assignee) {
    if ($deals_details->default_deal_owner != $assignee['staffid']) {
        $outputStatus .= '<li>
                  <a href="#" onclick="change_deal_owner(' . $assignee['staffid'] . ',' . $deals_details->id . '); return false;">
                     ' . $assignee['full_name'] . '
                  </a>
               </li>';
    }
}
$outputStatus .= '<li>
                  <a href="#" onclick="change_deal_owner(0,' . $deals_details->id . '); return false;">
                     ' . _l('no_owner') . '
                  </a>
               </li>';

$outputStatus .= '</ul>';
$outputStatus .= '</div>';


$outputStatus .= '</span>';


$stage = '';

$stage .= '<span class="">';

$stage .= (!empty($deals_details->pipeline_name) ? $deals_details->pipeline_name : '-') . ' <i class="fa fa-angle-right"></i> ' . (!empty($deals_details->stage_name) ? $deals_details->stage_name : '');


$stage .= '<div class="dropdown inline-block mleft5 table-export-exclude">';
$stage .= '<a href="#" style="font-size:14px;vertical-align:middle;" class="dropdown-toggle text-dark" id="tableTaskStatus-' . $deals_details->id . '" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">';
$stage .= '<span data-toggle="tooltip" title="' . _l('change_deal_owner') . '"><i class="fa-solid fa-chevron-down tw-opacity-70"></i></span>';
$stage .= '</a>';

$stage .= '<ul class="dropdown-menu width300" >';
if (!empty($all_stages)) {
    foreach ($all_stages as $key => $vstage) {
        $stage .= '<li
                    ' . ($deals_details->stage_id == $vstage->stage_id ? 'class="active"' : '') . '
>
                  <a href="' . base_url('admin/sales_marketing/changeStage/' . $deals_details->id . '/' . $vstage->stage_id) . '">
                     ' . $vstage->stage_name . '
                  </a>
               </li>';

    }
}
$stage .= '</ul>';
$stage .= '</div>';


$stage .= '</span>';

?>

<div class="tw-relative">
    <div class="tw-rounded-lg tw-border tw-border-neutral-200 tw-bg-white tw-shadow-sm tw-dark:border-neutral-700 tw-dark:bg-neutral-900">
        <div class="tw-bg-white tw-px-3 tw-py-4 dark:tw-bg-neutral-900 sm:tw-p-6">
            <div class="tw-flex tw-grow tw-flex-col ">
                <div class="row">
                    <div class="col-xs-3">
                        <div class="left-side">
                            <div class="distribution-content">
                                <h3 class="distribution mb-0"><?= $deals_details->title ?>
                                    <span class="products"><?=
                                        total_rows('tbl_sam_items', array('deals_id' => $id)) . ' ' . _l('products') ?></span>
                                </h3>
                                <?= $stage ?>
                                <p><?= _l('created') . ' ' . _l('at') . ':' . date('F j, Y', strtotime($deals_details->created_at)) . ' '?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-6" style="top:20px;margin-bottom:20px">
                        <span>
                            <a href="<?=admin_url()?>sales_marketing/tasks/get_task_data/<?=$deals_details->id .'/'.$deals_details->pipeline_id.'/'.$deals_details->stage_id?>" 
                                data-toggle="modal" data-target="#myModal" class="btn btn-default btn-sm rounded px-5 mr-3" 
                                data-title="<?php echo _l('task_timesheets'); ?>" style="margin-bottom:2px">
                                <i class="fa fa-th-list"></i>
                            </a> 
                        </span>
                            <?php
                            if(check_timer_status($deals_details->id,$deals_details->pipeline_id,$deals_details->stage_id)) {
                            ?>
                            <span>    
                                <a href="#" id="lead_timer" class="btn btn-danger btn-sm rounded px-5 mr-3 mb-3" style="margin-bottom:2px" onclick="start_lead_timer(1); return false;">
                                    <i class="fa-regular fa-clock"></i> 
                                    Stop Timer 
                                </a>
                            </span> 
                            <?php    
                            } 
                            else{
                            ?>
                            <span>
                                <a href="#" id="lead_timer" class="btn btn-success btn-sm rounded px-5 mr-3 mb-3" style="margin-bottom:2px" onclick="start_lead_timer(0); return false;">
                                    <i class="fa-regular fa-plus tw-mr-1"></i> 
                                    Start Timer 
                                </a>
                            </span>     
                            <?php    
                            } 
                            if ($deals_details->status == 'won' || $deals_details->status == 'lost') {
                                ?>
                            <span>
                                <a href="<?= base_url('admin/sales_marketing/changedStatus/' . $id . '/open') ?>"
                                    class="btn btn-warning btn-sm rounded px-5 mr-3 mb-3" style="margin-bottom:2px">
                                    <i class="fa fa-repeat"></i>
                                    <?= _l('reopen') ?></a>
                            </span>
                                <?php
                            }
                            if ($deals_details->status == 'open' || $deals_details->status == 'lost') {
                                ?>
                            <span>
                                <a data-toggle="modal" data-target="#myModal"
                                    href="<?= base_url('admin/sales_marketing/changeStatus/' . $id . '/won') ?>"
                                    class="btn btn-success btn-sm rounded px-5 mr-3 mb-3" style="margin-bottom:2px">
                                    <i class="fa fa-check"></i>
                                    <?= _l('won') ?></a>
                            </span>
                                <?php
                            }
                            if ($deals_details->status == 'open' || $deals_details->status == 'won') {
                                ?>
                            <span>
                                <a data-toggle="modal" data-target="#myModal"
                                    href="<?= base_url('admin/sales_marketing/changeStatus/' . $id . '/lost') ?>"
                                    class="btn btn-danger btn-sm rounded px-5 mr-3 mb-3" style="margin-bottom:2px">
                                    <i class="fa fa-times"></i>
                                    <?= _l('lost') ?></a>
                            </span>
                            <?php } ?>
                            <?php if (has_permission('tasks', '', 'create')) { ?>
                            <span>
                                <a href="#"
                                    onclick="new_task_from_relation(undefined,'deals',<?php echo $deals_details->id; ?>); return false;"
                                    class="btn btn-primary btn-sm" style="margin-bottom:2px">
                                    <i class="fa-regular fa-plus tw-mr-1"></i>
                                    <?php echo _l('new_task'); ?>
                                </a>
                                
                            </span>
                            <?php 
                            } 
                            ?>
                          <span>
                            <select aria-label="Status" class="selectpicker px-1" id="statusDropdown" name="status"
                                    onchange="updateDealStatus(<?= $deals_details->id ?>, this.value)">
                                <option value="" disabled selected>Select status</option> <!-- Default placeholder -->
                                <?php foreach ($statuses as $status): ?>
                                    <option value="<?= $status->status_id ?>" <?= $deals_details->deal_status == $status->status_id ? 'selected' : '' ?>>
                                        <?= $status->status_name ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </span>

                    </div>
                    <div class="col-xs-3" style="display:inline-table">        
                        <div class="right-side">
                            <div class="inside-con">
                                <div class="inline ">
                                    <div class="easypiechart text-success" data-percent="<?= $propability ?>"
                                         data-line-width="5"
                                         data-track-Color="#f0f0f0" data-bar-color="#<?php
                                    if ($propability == 100) {
                                        echo '8ec165';
                                    } elseif ($propability >= 40 && $propability <= 50) {
                                        echo '5d9cec';
                                    } elseif ($propability >= 51 && $propability <= 99) {
                                        echo '7266ba';
                                    } else {
                                        echo 'fb6b5b';
                                    }
                                    ?>" data-rotate="270" data-scale-Color="false" data-size="50" data-animate="2000">
                                        <span class="small "><?= $propability ?>%</span>
                                    </div>
                                </div>


                                <?php
                                echo $outputStatus;
                                ?>
                                                           
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tw-mt-5">
                <ul class="progress-custom">
                    <?php
                    // bg-none-color color-other
                    $active_stage = 0;
                    $class = '';
                    if (!empty($all_stages)) {
                        $active_stage_key = array_search($deals_details->stage_id, array_column($all_stages, 'stage_id'));

                        for ($skey = 0; $skey <= $active_stage_key; $skey++) {
                            $all_stages[$skey]->active = true;
                        }
                        $nextStage = $active_stage_key + 1;
                        if (!empty($all_stages[$nextStage])) {
                            $all_stages[$nextStage]->next = true;
                        }
                        // if status == 'won'

                        if ($deals_details->status == 'won') {
                            $dstatus = 'active';
                        } elseif ($deals_details->status == 'lost') {
                            $dstatus = 'lost';
                        }
                        if (!empty($dstatus)) {
                            foreach ($all_stages as $stage) {
                                $stage->$dstatus = true;
                            }
                        }
                    }
                    $icon = '<svg xmlns="http://www.w3.org/2000/svg"
                                                                           fill="none"
                                                                           viewBox="0 0 24 24" width="1.75rem"
                                                                           height="1.75rem" stroke-width="1.5"
                                                                           stroke="currentColor" aria-hidden="true"
                                                                           class="h-6 w-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M4.5 12.75l6 6 9-13.5"></path>
                                    
                                </svg>';
                    if (!empty($all_stages)) {
                        foreach ($all_stages as $key => $stage) {
                            if (!empty($stage->lost)) {
                                $class = 'bg-lost-color';
                                $icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" class="h-6 w-6 text-white"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>';
                            } else if (!empty($stage->active)) {
                                $class = '';
                            } elseif (!empty($stage->next)) {
                                $class = 'bg-none-color';
                            } else {
                                $class = 'bg-none-color color-other';
                                $icon = '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" class="h-5 w-5 text-neutral-500 dark:text-neutral-100"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"></path></svg>';
                            }
                            ?>
                            <li>
                                <a href="<?= base_url('admin/sales_marketing/changeStage/' . $id . '/' . $stage->stage_id) ?>"
                                   class="process-svg <?= $class ?>">

                                <span><span class="rounded-svg rounded-full ">
                                        <?= $icon ?>
                                    </span></span>
                                    <span class="svg-title tw-mt-2"><?= $stage->stage_name ?></span>
                                </a>
                                <div class="svg-stock" aria-hidden="true">
                                    <svg class="h-full w-full text-neutral-300 dark:text-neutral-600" width="1.75rem"
                                         height="3.3rem" viewBox="0 0 22 80" fill="none" preserveAspectRatio="none">
                                        <path d="M0 -2L20 40L0 82" vector-effect="non-scaling-stroke"
                                              stroke="currentcolor"
                                              stroke-linejoin="round"></path>
                                    </svg>
                                </div>
                            </li>
                            <?php
                        }
                    }
                    ?>
                </ul>
            </div>
        </div>
    </div>
</div>
<?php
if (!empty($deals_details->rel_id) && !empty($deals_details->rel_type)) {
    $task_rel_data = get_relation_data($deals_details->rel_type, $deals_details->rel_id);
    $task_rel_value = get_relation_values($task_rel_data, $deals_details->rel_type);
    echo '<br />' . _l('task_related_to') . ' ' . _l($deals_details->rel_type) . ': <a class="text-muted" href="' . $task_rel_value['link'] . '">' . $task_rel_value['name'] . '</a>';
    if($deals_details->rel_type=='customer'){
        echo '<br /> Related To contact: <a class="text-muted" href="#">' . get_contact_full_name($deals_details->contact_id) . '</a>';    
    }
}
?>
<div id="addEventForm">
    <a href="<?= site_url('admin/sales_marketing'); ?>" class="btn btn-secondary">
                            <?php 
                            // echo _l('back'); 
                            echo 'Back'; 
                            ?>
                        </a>
</div>
<script src="<?= module_dir_url(SAM_MODULE) ?>assets/easypiechart/jquery.easy-pie-chart.js"></script>
<script type="text/javascript">
    'use strict';

    function change_deal_owner(staffId, dealId) {
        $("body").append('<div class="dt-loader"></div>');
        requestGetJSON('<?= admin_url('sales_marketing/change_deal_owner') ?>' + '/' + staffId + '/' + dealId).done(function (response) {
            $("body").find(".dt-loader").remove();
            if (response.success === true || response.success == "true" || response.success == 1) {
                alert_float('success', response.message);
                window.location.reload();
            } else {
                alert_float('danger', response.message);
            }
        });
    }

    $('.easypiechart').each(function () {
        var $this = $(this), $data = $this.data(), $step = $this.find('.step'),
            $target_value = parseInt($($data.target).text()), $value = 0;
        $data.barColor || ($data.barColor = function ($percent) {
            $percent /= 100;
            return "rgb(" + Math.round(200 * $percent) + ", 200, " + Math.round(200 * (1 - $percent)) + ")";
        });
        $data.onStep = function (value) {
            $value = value;
            $step.text(parseInt(value));
            $data.target && $($data.target).text(parseInt(value) + $target_value);
        }
        $data.onStop = function () {
            $target_value = parseInt($($data.target).text());
            $data.update && setTimeout(function () {
                $this.data('easyPieChart').update(100 - $value);
            }, $data.update);
        }
        $(this).easyPieChart($data);
    });
    
    
/*    
function timer_action2(e, t, a, i) {
    a = void 0 === a ? "" : a;
    var n = $("#timer-select-task");
    if ("" !== t || !n.is(":visible")) {
        if ("" !== a && "0" == t) {
            var s = {
                content: ""
            };
            return s.content += '<div class="row">',
            s.content += '<div class="form-group">',
            "1" == app.options.has_permission_create_task && (s.content += '<div class="input-group" style="margin:0 auto;width:60%;">'),
            s.content += '<select id="timer_add_task_id" data-empty-title="' + app.lang.search_tasks + '" data-width="60%" class="ajax-search" data-live-search="true">',
            s.content += "</select>",
            "1" == app.options.has_permission_create_task && (s.content += '<div class="input-group-addon" style="opacity: 1;">',
            s.content += '<a href="#" onclick="new_task(\'tasks/task\',' + a + '); return false;"><i class="fa fa-plus"></i></a>',
            s.content += "</div>"),
            s.content += "</div></div>",
            s.content += '<div class="form-group">',
            s.content += '<textarea id="timesheet_note" placeholder="' + app.lang.note + '" style="margin:0 auto;width:60%;" rows="4" class="form-control"></textarea>',
            s.content += "</div>",
            s.content += "<button type='button' onclick='timer_action2(this,document.getElementById(\"timer_add_task_id\").value," + a + ");return false;' class='btn btn-primary'>" + app.lang.confirm + "</button>",
            s.message = app.lang.task_stop_timer,
            system_popup(s).attr("id", "timer-select-task"),
            init_ajax_search("tasks", "#timer_add_task_id", void 0, admin_url + "sales_marketing/tasks/ajax_search_assign_task_to_timer"),
            !1
        }
        $(e).addClass("disabled");
        var o = {};
        o.task_id = t,
        o.timer_id = a,
        o.note = $("body").find("#timesheet_note").val(),
        o.note || (o.note = "");
        var l = $("#task-modal").is(":visible")
          , d = admin_url + "sales_marketing/tasks/timer_tracking/<?=$deals_details->id.'/'.$deals_details->pipeline_id.'/'.$deals_details->stage_id?>?single_task=" + l;
        i && (d += "&admin_stop=" + i),
        $.post(d, o).done((function(e) {
            e = JSON.parse(e),
            $("body").hasClass("member") && window.location.reload(),
            l && _task_append_html(e.taskHtml),
            n.is(":visible") && n.find(".system-popup-close").click(),
            _init_timers_top_html(JSON.parse(e.timers)),
            $(".popover-top-timer-note").popover("hide"),
            reload_tasks_tables()
        }
        ))
    }
} */

function start_lead_timer(timer_status){
    $.ajax({
        type    : 'GET',
        url     : admin_url + "sales_marketing/tasks/timer_tracking/<?=$deals_details->id.'/'.$deals_details->pipeline_id.'/'.$deals_details->stage_id?>?single_task=",
        data    : '',
        success : function(response){
            console.log(response);  
            //enable stop timer
            if(timer_status==0){
                //$('#lead_timer').html("<a href='#' id='lead_timer' class='btn btn-danger btn-sm rounded px-5 mr-1' onclick='start_lead_timer(1)'><i class='fa-regular fa-clock'></i> Stop Timer</a>");  
                $('#lead_timer').html("<i class='fa-regular fa-clock'></i> Stop Timer");
                $('#lead_timer').attr('class','btn btn-danger btn-sm rounded px-5 mr-1');
                $('#lead_timer').attr('onclick','start_lead_timer(1)');
            }   
            //enable start timer
            else if(timer_status==1){
                //$('#lead_timer').html("<a href='#' class='btn btn-success btn-sm rounded px-5 mr-1' onclick='start_lead_timer(0)'><i class='fa-regular fa-plus tw-mr-1'></i> Start Timer</a>");     
                //$('#lead_timer').html("<a href='#' id='lead_timer' class='btn btn-success btn-sm rounded px-5 mr-1' onclick='start_lead_timer(0)'><i class='fa-regular fa-plus tw-mr-1'></i> Start Timer</a>");     
                $('#lead_timer').html("<i class='fa-regular fa-plus tw-mr-1'></i> Start Timer");
                $('#lead_timer').attr('class','btn btn-success btn-sm rounded px-5 mr-1');
                $('#lead_timer').attr('onclick','start_lead_timer(0)');
            }
        }        
    });  
}

function updateDealStatus(dealId, statusId) {
    $.ajax({
        url: '<?= base_url("sales_marketing/update_deal_status") ?>',
        type: 'POST',
        data: {
            deal_id: dealId,
            status_id: statusId
        },
        success: function(response) {
            // Debug the response
            console.log(response);

            // Parse the response if necessary
            try {
                var jsonResponse = typeof response === "object" ? response : JSON.parse(response);
                if (jsonResponse.success) {
                    alert('Deal status updated successfully!');
                     window.location.reload();      
                } else {
                    alert('Failed to update deal status.');
                }
            } catch (error) {
                alert('Error parsing response: ' + error.message);
            }
        },
        error: function() {
            alert('An error occurred while updating the deal status.');
        }
    });
}


</script>