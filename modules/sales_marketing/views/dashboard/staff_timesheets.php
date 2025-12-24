<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php //echo "<pre>"; print_r($_SERVER['HTTP_USER_AGENT']); exit;?>
<div id="wrapper" style="">  
    <div class="content">
        <div class="row">
            <div class="col-md-9">
                <div class="col-md-3 mbot10">
                    <div class="select-placeholder">
                        <select name="range" id="range" class="selectpicker" data-width="100%">
                            <option value="today" selected>
                                <?php echo _l('sam_today'); ?>
                            </option>
                            <option value="last_seven_days">
                                <?php echo _l('sam_last_7days'); ?>
                            </option>
                            <option value="this_week">
                                <?php echo _l('sam_this_week'); ?>
                            </option>
                            <option value="last_week">
                                <?php echo _l('sam_last_week'); ?>
                            </option>
                            <option value="this_month" selected="selected">
                                <?php echo _l('sam_this_month'); ?>
                            </option>
                            <option value="last_month">
                                <?php echo _l('sam_last_month'); ?>
                            </option>
                            <option value="this_year">
                                <?php echo _l('sam_this_year'); ?>
                            </option>
                            <option value="last_year">
                                <?php echo _l('sam_last_year'); ?>
                            </option>                               
                            <option value="period">
                                <?php echo _l('sam_period'); ?>
                            </option>
                            <option value="all">
                                <?php echo _l('sam_all'); ?>
                            </option>
                        </select>
                    </div> 
                </div>
                <div class="col-md-3 mbot10">
                    <div class="select-placeholder">
                        <select name="staff_id" id="staff_id" class="selectpicker" data-width="100%">                                                    
                            <?php
                            if($all_staff==""){
                            ?>
                                <option value="<?=get_staff_user_id()?>"><?=get_staff_full_name(get_staff_user_id())?></option>
                            <?php    
                            }
                            else{
                                echo "<option value=''>"._l('sam_all_staff')."</option>";    
                                foreach($all_staff as $s){
                                    echo "<option value='".$s['staffid']."'>".$s['fullname']."</option>";
                                }
                            } 
                            ?>
                        </select>
                    </div>  
                </div>
                <div class="col-md-3 mbot10">
                    <div class="select-placeholder" style="margin-bottom:2px">
                        <select id="pipeline_id" name="pipeline_id" data-live-search="true" data-width="100%"
                            class="selectpicker" data-empty-title="<?php echo _l('pipeline'); ?>"
                            data-none-selected-text="<?php echo _l('pipeline'); ?>">
                            <option value=''><?=_l('sam_all_pipeline')?></option>
                            <?php
                            if($pipelines){
                                foreach($pipelines as $val){
                            ?>
                                    <option value="<?=$val['pipeline_id']?>"><?=$val['pipeline_name']?></option>
                            <?php
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-3" id="apply-btn1">
                    <a href="#" id="apply_filters_timesheets" onclick="apply_filters_timesheets()" class="btn btn-primary pull-left">
                        <?php echo _l('apply'); ?>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="row hide" id="period-div" style="margin-top:5px;">
            <div class="col-md-9 mbot10" style="">         
                <div class="col-md-3">
                    From Date <?php //echo render_date_input('period-from'); ?> 
                    <div class="form-group mbot10" app-field-wrapper="period-from">
                        <div class="input-group date">
                            <input type="text" id="period-from" name="period-from" class="form-control datepicker" value="" autocomplete="off">
                            <div class="input-group-addon">
                                <i class="fa-regular fa-calendar calendar-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    To Date <?php //echo render_date_input('period-to'); ?>
                    <div class="form-group mbot10" app-field-wrapper="period-to">
                        <div class="input-group date">
                            <input type="text" id="period-to" name="period-to" class="form-control datepicker" value="" autocomplete="off">
                            <div class="input-group-addon">
                                <i class="fa-regular fa-calendar calendar-icon"></i>
                            </div>
                        </div>
                    </div>
                </div> 
                
                <div class="col-md-3 mtop20">
                    <a href="#" id="apply_filters_timesheets" onclick="apply_filters_timesheets()" class="btn btn-primary pull-left">
                        <?php echo _l('apply'); ?>
                    </a>
                </div>     
            </div>
                
        </div>

        <div class="row" style="background:#f1f5f9">
        
            <div class="col-md-8 ui-sortable" data-container="left-8" style="top:1em"> 
                <div class="widget relative" id="widget-top_stats" data-name="Quick Statistics">
                    <div class="row">
                        <div class="quick-stats-invoices col-xs-12 col-md-12 col-sm-12 col-lg-12 tw-mb-2 sm:tw-mb-0 mtop15 mbot15">              
                            <div class="top_stats_wrapper">                                  
                                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">                      
                                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate mbot10">                          
                                        <span class="tw-truncate" style="font-size:1.2em">                              
                                            <i class="fa-regular fa-clock"></i> Time Spent On Each Customer                        
                                        </span>                       
                                    </div>                   
                                </div>                              
                                <div class="table-responsive" id="searched_spenttime_on_customer_div" style="border:none">
                                    <table class="table dt-table" id="searched_spenttime_on_customer_table" data-order-col="1" data-order-type="asc" data-page-length='10' style="font-size:0.9em">
                                        <thead>
                                            <tr>
                                                <th class="" style="width:40%">Customer Name</th>    
                                                <th class="" style="width:30%">Total Time Spent</th>
                                                <th class="" style="width:30%">Total Deal Value</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>               
                            </div>          
                        </div>
                    </div>  
                    <div class="row">
                        <div class="quick-stats-invoices col-xs-12 col-md-12 col-sm-12 col-lg-12 tw-mb-2 sm:tw-mb-0 mtop5 mbot15">              
                            <div class="top_stats_wrapper">                                  
                                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">                      
                                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate mbot10">                          
                                        <span class="tw-truncate" style="font-size:1.2em">                              
                                            <i class="fa-regular fa-clock"></i> All Reminders                        
                                        </span>                       
                                    </div>                   
                                </div>                              
                                <div class="table-responsive" id="searched_reminder_div" style="border:none">
                                    <table class="table dt-table" id="searched_reminder_table" data-order-col="3" data-order-type="desc" data-page-length='10' style="font-size:0.9em">
                                        <thead>
                                            <tr>
                                                <th class="">Customer</th>    
                                                <th class="">Employee</th>
                                                <th class="">Title</th>
                                                <th class="">DateTime</th>
                                                <th class="">IN</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>               
                            </div>          
                        </div>
                    </div> 
                </div>  
            </div>
            
            
            <div class="col-md-4 ui-sortable mtop15" data-container="right-4" style="top:1em"> 
                <div class="widget" id="widget-leads_chart" data-name="Time Spent By Employees">          
                    <div class="row">
                        <div class="quick-stats-invoices col-xs-12 col-md-12 col-sm-12 col-lg-12 tw-mb-2 sm:tw-mb-0 mbot20">              
                            <div class="top_stats_wrapper">                                  
                                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">                      
                                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">                          
                                        <span class="tw-truncate" id="">                              
                                            <i class="far fa-file-alt"></i> 
                                            Time Spent By Employees                       
                                        </span>                       
                                    </div>                   
                                </div>                              
                                <div class="table-responsive" style="border:none">
                                    <table class="table align-items-center mb-0" id="searched_timesheets_data_table" style="font-size:0.9em">
                                        <thead>
                                            <tr>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="width:55%">Staff Name</th>    
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7" style="width:45%">Total Time Spent</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>               
                            </div>          
                        </div> 
                    </div>  
                    <div class="row">
                        <div class="quick-stats-invoices col-xs-12 col-md-12 col-sm-12 col-lg-12 tw-mb-2 sm:tw-mb-0 mbot20">              
                            <div class="top_stats_wrapper">                                  
                                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">                      
                                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">                          
                                        <span class="tw-truncate" id="">                              
                                            <i class="far fa-file-alt"></i> 
                                            Employee Activity on Leads                       
                                        </span>                       
                                    </div>                   
                                </div>                              
                                <div class="table-responsive" style="border:none">
                                    <table class="table align-items-center mb-0" id="searched_leads_data_table" style="font-size:0.9em">
                                        <thead>
                                            <tr>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder" style="width:55%">Staff Name</th>    
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder" style="width:45%">Total Activities</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>               
                            </div>          
                        </div>
                    </div> 
                    <div class="row">
                        <div class="quick-stats-invoices col-xs-12 col-md-12 col-sm-12 col-lg-12 tw-mb-2 sm:tw-mb-0 mbot20">              
                            <div class="top_stats_wrapper">                                  
                                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">                      
                                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">                          
                                        <span class="tw-truncate" id="">                              
                                            <i class="fas fa-chart-pie"></i> 
                                            Status Detail                      
                                        </span>                       
                                    </div>                   
                                </div>                              
                                <div class="table-responsive" style="border:none">
                                    <table class="table align-items-center mb-0" id="searched_deals_status_data_table" style="font-size:0.9em">
                                        <thead>
                                            <tr>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder" style="width:55%">Status</th>    
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder" style="width:45%">No Of Deals</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>               
                            </div>          
                        </div>
                    </div> 
                    <!-- Status Summary -->
<!--<div class="row">-->
<!--    <div class="quick-stats-invoices col-xs-12 col-md-12 col-sm-12 col-lg-12 tw-mb-2 sm:tw-mb-0 mbot20">              -->
<!--        <div class="top_stats_wrapper">                                  -->
<!--            <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">                      -->
<!--                <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">                          -->
<!--                    <span class="tw-truncate" id="">                              -->
<!--                        <i class="fas fa-chart-pie"></i> -->
<!--                        Status Summary                       -->
<!--                    </span>                       -->
<!--                </div>                   -->
<!--            </div>                              -->
<!--            <div class="table-responsive" style="border:none">-->
<!--            <table class="table align-items-center mb-0" id="status_summary_table" style="font-size:0.9em">-->
<!--                <thead>-->
<!--                    <tr>-->
<!--                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder" style="width:70%">Status</th>-->
<!--                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder" style="width:30%">No of Deals</th>-->
<!--                    </tr>-->
<!--                </thead>-->
<!--                <tbody>-->
<!--                    <?php if (!empty($status_summary)): ?>-->
<!--                        <?php foreach ($status_summary as $status): ?>-->
<!--                            <tr>-->
<!--                                <td>-->
<!--                                    <a href="https://crm.mazajnet.com/admin/sales_marketing?deal_status=<?= urlencode($status['status_id']) ?>" -->
<!--                                       target="_blank" -->
<!--                                       style="text-decoration: none; color: <?= htmlspecialchars($status['color']) ?>;">-->
<!--                                       <?= htmlspecialchars($status['status_name']) ?>-->
<!--                                    </a>-->
<!--                                </td>-->
<!--                                <td><?= htmlspecialchars($status['total_count']) ?></td>-->
<!--                            </tr>-->
<!--                        <?php endforeach; ?>-->
<!--                    <?php else: ?>-->
<!--                        <tr>-->
<!--                            <td colspan="2" class="text-center">No status data available</td>-->
<!--                        </tr>-->
<!--                    <?php endif; ?>-->
<!--                </tbody>-->
<!--            </table>-->

<!--            </div>               -->
<!--        </div>          -->
<!--    </div>-->
<!--</div>-->


                    
                    <div class="row">
                        <div class="quick-stats-invoices col-xs-12 col-md-12 col-sm-12 col-lg-12 tw-mb-2 sm:tw-mb-0 mbot20">              
                            <div class="top_stats_wrapper">                                  
                                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">                      
                                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">                          
                                        <span class="tw-truncate" id="">                              
                                            <i class="far fa-file-alt"></i> 
                                            Proposals Amount of Money                       
                                        </span>                       
                                    </div>                   
                                </div>                              
                                <div class="table-responsive" style="border:none">
                                    <table class="table align-items-center mb-0" id="searched_proposal_amount_table" style="font-size:0.9em">
                                        <thead>
                                            <tr>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder" style="width:55%">Total Sent</th>    
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder" style="width:45%">Total Accepted</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>               
                            </div>          
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="quick-stats-invoices col-xs-12 col-md-12 col-sm-12 col-lg-12 tw-mb-2 sm:tw-mb-0 mbot20">              
                            <div class="top_stats_wrapper">                                  
                                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">                      
                                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">                          
                                        <span class="tw-truncate" id="">                              
                                            <i class="far fa-file-alt"></i> 
                                            Proposals as Count                       
                                        </span>                       
                                    </div>                   
                                </div>                              
                                <div class="table-responsive" style="border:none">
                                    <table class="table align-items-center mb-0" id="searched_proposal_count_table" style="font-size:0.9em">
                                        <thead>
                                            <tr>
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder" style="width:55%">Total Sent</th>    
                                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder" style="width:45%">Total Accepted</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                </div>               
                            </div>          
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="quick-stats-invoices col-xs-12 col-md-12 col-sm-12 col-lg-12 tw-mb-2 sm:tw-mb-0 mbot20">              
                            <div class="top_stats_wrapper">                                  
                                <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">                      
                                    <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">                          
                                        <span class="tw-truncate" id="deal_value_title">                              
                                            <i class="far fa-file-alt"></i> Active Deals - All Pipeline                       
                                        </span>                       
                                    </div>                   
                                </div>                              
                                <div class="table-responsive" style="border:none">
                                    <table class="table align-items-center mb-0" id="searched_active_deal_table" style="font-size:0.9em;margin-top:0">
                                        <tbody></tbody>
                                    </table>
                                </div>               
                            </div>          
                        </div>
                    
                    </div> 
                    
                    <div class="row">          
                        <div class="col-md-12">              
                            <div class="panel_s">                  
                                <div class="panel-body padding-10">                      
                                    <div class=""></div>                        
                                    <p class="tw-font-medium tw-flex tw-items-center tw-mb-0 tw-space-x-1.5 rtl:tw-space-x-reverse tw-p-1.5">                          
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="tw-w-6 tw-h-6 tw-text-neutral-500">                              
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z">
                                            </path>                          
                                        </svg>                            
                                        <span class="tw-text-neutral-700">                              
                                            Leads and Deals Overview                        
                                        </span>                      
                                    </p>                        
                                    <hr class="-tw-mx-3 tw-mt-3 tw-mb-6">                        
                                    <div class="relative" style="height:250px">
                                        <iframe class="chartjs-hidden-iframe" tabindex="-1" style="display: block; overflow: hidden; border: 0px; margin: 0px; inset: 0px; height: 100%; width: 100%; position: absolute; pointer-events: none; z-index: -1;"></iframe>                          
                                        <canvas class="chart" height="312" id="deals_leads_status_stats" width="460" style="display: block; height: 250px; width: 368px;">
                                        </canvas>                      
                                    </div>                  
                                </div>              
                            </div>          
                        </div>      
                    </div>      
                </div> 
            </div>
            
        </div>
        <div class="clearfix"></div>
    </div>
</div>
<?php init_tail(); ?>
<script> 

$("body").on("change", 'select[name="range"]', (function() {
    //$("#apply-btn1").addClass("hide");     
    var t = $("#period-div");
    t.attr("style","display:block");
    if($(this).val() == "period"){
        t.removeClass("hide"); 
        $("#apply-btn1").addClass("hide");   
    }
    else{
        t.addClass("hide");   
        $("#apply-btn1").removeClass("hide");
    }
    //"period" == $(this).val() ? t.removeClass("hide") : (t.addClass("hide"));
    t.find("input").val("");
    
}
));

<?php

if(isset($leads_status_stats)){ 
?>  
    var my_chart;  
    Deal_Lead_Chart('<?=$leads_status_stats?>');    
<?php
}
if(isset($timesheets_data)){
?>
    $("#searched_timesheets_data_table tbody").html('<?=$timesheets_data?>');
<?php    
}
if(isset($leads_data)){
?>    
    $("#searched_leads_data_table tbody").html('<?=$leads_data?>');  
<?php    
}
if(isset($deals_status_data)){
?>    
    $("#searched_deals_status_data_table tbody").html('<?=$deals_status_data?>');  
<?php    
}
if(isset($proposal_data)){
?>
    proposal_data = "<?=$proposal_data?>";
    proposal_data = proposal_data.split('###');
    $("#searched_proposal_amount_table tbody").html(proposal_data[0]);
    $("#searched_proposal_count_table tbody").html(proposal_data[1]);
<?php    
}
if(isset($deal_value_table)){
?>
    $("#searched_active_deal_table tbody").html("<?=$deal_value_table?>");
<?php    
}
if(isset($spent_time_on_cust)){
?>
    $("#searched_spenttime_on_customer_table tbody").html('<?=$spent_time_on_cust?>'); 
<?php
}
if(isset($reminder_list)){
?>
    $("#searched_reminder_table tbody").html(`<?=$reminder_list?>`); 
/*    $("table#searched_reminder_table").DataTable({
        order: [[3, 'desc']]    
    });*/ 
<?php    
}
?>
//const table_timespent = $("table#searched_spenttime_on_customer_table");  

function Deal_Lead_Chart(chart_data){           
    var leads_chart = $('#deals_leads_status_stats');
    if (leads_chart.length > 0) { 
        // Leads overview status
        my_chart = new Chart(leads_chart, {
            type: 'pie',
            data: JSON.parse(chart_data),
            options: {
                maintainAspectRatio: false,
                onClick: function(evt) {
                    onChartClickRedirect(evt, this);
                }
            }
        });
    }                   
}
$(document).ready(function() {
    apply_filters_timesheets(); 
});
function apply_filters_timesheets() {
    data = {};                
    data.range = $('#range').val();
    data.staff_id = $('#staff_id').val();
    data.clientid = $('#clientid').val();
    data.pipeline_id = $('#pipeline_id').val();
    data.periodfrom = $('#period-from').val();
    data.periodto = $('#period-to').val();

    $.post(admin_url + '<?=SAM_MODULE?>/dashboard/filter_timesheets_data', data).done(function(response) {  
        response = JSON.parse(response);                                  
        if (response != false) {                                              
            timesheets = response[0];   
            leads_data = response[1];   
            deals_status_data = response[2];   
            leads_chart_data = response[3];   
            proposal_amount_table = response[4];   
            proposal_count_table = response[5];   
            deal_value_table = response[6];   
            pipeline_name = response[7];   
            spent_time_on_cust = response[8];   
            reminder_list = response[9]; 

            $('#deal_value_title').html('<i class="far fa-file-alt"></i> Active Deals - ' + pipeline_name); 
            $("#searched_timesheets_data_table tbody").html(timesheets);
            $("#searched_leads_data_table tbody").html(leads_data);
            $("#searched_deals_status_data_table tbody").html(deals_status_data);
            $("#searched_proposal_amount_table tbody").html(proposal_amount_table);
            $("#searched_proposal_count_table tbody").html(proposal_count_table);
            $("#searched_active_deal_table tbody").html(deal_value_table);
            
            $("#searched_spenttime_on_customer_div").html(spent_time_on_cust); 
            $("#searched_reminder_div").html(reminder_list); 
            
            if (leads_chart_data != "") { 
                my_chart.destroy(); 
                Deal_Lead_Chart(leads_chart_data); 
            }
        } else {
            // Handle the case where no data is returned
            $("#searched_timesheets_data_table tbody").html('');
            $("#searched_leads_data_table tbody").html('');
            $("#searched_deals_status_data_table tbody").html('');
            $("#searched_proposal_amount_table tbody").html('');
            $("#searched_proposal_count_table tbody").html('');
            $("#searched_active_deal_table tbody").html('');
            $('#deal_value_title').html('<i class="far fa-file-alt"></i> Active Deals - All Pipeline');
            $("#searched_spenttime_on_customer_div").html(''); 
            $("#searched_reminder_div").html(''); 
        }                                                                                                                
    });    
}

// Function to escape HTML special characters
function htmlspecialchars(str) {
    var element = document.createElement('div');
    if (str) {
        element.innerText = str;
        element.textContent = str;
    }
    return element.innerHTML;
}

/*$('#apply_filters_timesheets').on('click', function(e) {       

}); */

</script>