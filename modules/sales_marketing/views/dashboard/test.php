<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper" style="">  
    <div class="content">
        <div class="row" style="margin-bottom:2em">
            <div class="col-md-5ths">
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
                <div class="row">     
                    <div class="col-md-12 period hide" style="margin-top:5px">
                    From Date <?php echo render_date_input('period-from'); ?> 
                    </div>
                    <div class="col-md-12 period hide">
                    To Date <?php echo render_date_input('period-to'); ?>
                    </div>       
                </div>
            </div>
            
            <div class="col-md-5ths">
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
            <div class="col-md-5ths">
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
            
            <div class="col-md-5ths">
                <a href="#" id="apply_filters_timesheets" class="btn btn-primary pull-left">
                    <?php echo _l('apply'); ?>
                </a>
            </div>
            
<!--            <div class="row mtop15" style="display:inline-block;margin-left:0">     
                <div class="col-md-3 period hide">
                   From Date <?php echo render_date_input('period-from'); ?> 
                </div>
                <div class="col-md-3 period hide">
                   To Date <?php echo render_date_input('period-to'); ?>
                </div>       
            </div> -->  
        </div>

        <div class="row" style="background:#f1f5f9">
            <div class="col-md-8 ui-sortable" data-container="left-8"> 
                <div class="widget" id="widget-timespentoncustomer" data-name="Time Spent On Each Customer">          
                    <div class="row" id="timespentoncustomer_report">          
                        <div class="col-md-12">
                            <div class="panel_s">                  
                                <div class="panel-body padding-10">                      
                                    <div class="widget-dragger ui-sortable-handle"></div>
                                    
                                    <div class="tw-flex tw-justify-between tw-items-center tw-p-1.5">                          
                                        <p class="tw-font-medium tw-flex tw-items-center tw-mb-0 tw-space-x-1.5 rtl:tw-space-x-reverse">                              
                                            <i class="fa-regular fa-clock"></i>                                 
                                            <span class="tw-text-neutral-700">                                  
                                                <?=_l('sam_timespent_on_eachcustomer')?>                           
                                            </span>                          
                                        </p>                          
                                    </div>
                                    <hr class="-tw-mx-3 tw-mt-2 tw-mb-4">
                                    <div class="table-responsive" id="searched_spenttime_on_customer_div" style="border:none">
                                        <table class="table dt-table" id="searched_spenttime_on_customer_table" data-order-col="1" data-order-type="asc" data-page-length='10' style="font-size:0.9em">
                                            <thead>
                                                <tr>
                                                    <th class="" style="width:40%"><?=_l('sam_customer_name')?></th>    
                                                    <th class="" style="width:30%"><?=_l('sam_total_timespent')?></th>
                                                    <th class="" style="width:30%"><?=_l('sam_total_dealvalue')?></th>
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
                <div class="widget" id="widget-allreminders" data-name="All Reminders">          
                    <div class="row" id="allreminders_report">          
                        <div class="col-md-12">
                            <div class="panel_s">                  
                                <div class="panel-body padding-10">                      
                                    <div class="widget-dragger ui-sortable-handle"></div>
                                    
                                    <div class="tw-flex tw-justify-between tw-items-center tw-p-1.5">                          
                                        <p class="tw-font-medium tw-flex tw-items-center tw-mb-0 tw-space-x-1.5 rtl:tw-space-x-reverse">                              
                                            <i class="fa-regular fa-clock"></i>                                 
                                            <span class="tw-text-neutral-700">                                  
                                                All Reminders                           
                                            </span>                          
                                        </p>                          
                                    </div>
                                    <hr class="-tw-mx-3 tw-mt-2 tw-mb-4">
                                    <div class="table-responsive" id="searched_reminder_div" style="border:none">
                                        <table class="table dt-table" id="searched_reminder_table" data-order-col="3" data-order-type="desc" data-page-length='10' style="font-size:0.9em">
                                            <thead>
                                               <tr>
                                                    <th class=""><?=_l('sam_customer')?></th>    
                                                    <th class=""><?=_l('sam_employee')?></th>
                                                    <th class=""><?=_l('title')?></th>
                                                    <th class=""><?=_l('sam_datetime')?></th>
                                                    <th class=""><?=_l('sam_IN')?></th>
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
            </div>
            
            <div class="col-md-4 ui-sortable" data-container="right-4">
                <div class="widget" id="widget-timespentbyemployees" data-name="Time Spent By Employees">          
                    <div class="row" id="timespentbyemployees_report">          
                        <div class="col-md-12">
                            <div class="panel_s">                  
                                <div class="panel-body padding-10">                      
                                    <div class="widget-dragger ui-sortable-handle"></div>
                                    
                                    <div class="tw-flex tw-justify-between tw-items-center tw-p-1.5">                          
                                        <p class="tw-font-medium tw-flex tw-items-center tw-mb-0 tw-space-x-1.5 rtl:tw-space-x-reverse">                              
                                            <i class="far fa-file-alt"></i>                                 
                                            <span class="tw-text-neutral-700">                                  
                                                <?=_l('sam_timespent_by_employees')?>                          
                                            </span>                          
                                        </p>                          
                                    </div>
                                    <hr class="-tw-mx-3 tw-mt-2 tw-mb-0">
                                    <div class="table-responsive" style="border:none">
                                        <table class="table align-items-center mb-0" id="searched_timesheets_data_table" style="font-size:0.9em;margin-top:1em">
                                            <thead>
                                                <tr>
                                                    <th class="" style="width:55%"><?=_l('sam_staffname')?></th>    
                                                    <th class="" style="width:45%"><?=_l('sam_total_timespent')?></th>
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
                <div class="widget" id="widget-activityonleads" data-name="Employee Activity on Leads">          
                    <div class="row" id="activityonleads_report">          
                        <div class="col-md-12">
                            <div class="panel_s">                  
                                <div class="panel-body padding-10">                      
                                    <div class="widget-dragger ui-sortable-handle"></div>
                                    
                                    <div class="tw-flex tw-justify-between tw-items-center tw-p-1.5">                          
                                        <p class="tw-font-medium tw-flex tw-items-center tw-mb-0 tw-space-x-1.5 rtl:tw-space-x-reverse">                              
                                            <i class="far fa-file-alt"></i>                                 
                                            <span class="tw-text-neutral-700">                                  
                                                <?=_l('sam_employee_activity_onleads')?>                           
                                            </span>                          
                                        </p>                          
                                    </div>
                                    <hr class="-tw-mx-3 tw-mt-2 tw-mb-0">
                                    <div class="table-responsive" style="border:none">
                                       <table class="table align-items-center mb-0" id="searched_leads_data_table" style="font-size:0.9em;margin-top:1em">
                                            <thead>
                                                <tr>
                                                    <th class="" style="width:55%"><?=_l('sam_staffname')?></th>    
                                                    <th class="" style="width:45%"><?=_l('sam_total_activities')?></th>
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
                <div class="widget" id="widget-proposal_amount" data-name="Proposals Amount of Money">          
                    <div class="row" id="proposal_amount_report">          
                        <div class="col-md-12">
                            <div class="panel_s">                  
                                <div class="panel-body padding-10">                      
                                    <div class="widget-dragger ui-sortable-handle"></div>
                                    
                                    <div class="tw-flex tw-justify-between tw-items-center tw-p-1.5">                          
                                        <p class="tw-font-medium tw-flex tw-items-center tw-mb-0 tw-space-x-1.5 rtl:tw-space-x-reverse">                              
                                            <i class="far fa-file-alt"></i>                                 
                                            <span class="tw-text-neutral-700">                                  
                                                <?=_l('sam_proposal_amount_money')?>                           
                                            </span>                          
                                        </p>                          
                                    </div>
                                    <hr class="-tw-mx-3 tw-mt-2 tw-mb-0">
                                    <div class="table-responsive" style="border:none">
                                        <table class="table align-items-center mb-0" id="searched_proposal_amount_table" style="font-size:0.9em;margin-top:1em">
                                            <thead>
                                                <tr>
                                                    <th class="" style="width:55%"><?=_l('sam_total_sent')?></th>    
                                                    <th class="" style="width:45%"><?=_l('sam_total_accepted')?></th>
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
                <div class="widget" id="widget-proposal_count" data-name="Proposals as Count">          
                    <div class="row" id="proposal_count_report">          
                        <div class="col-md-12">
                            <div class="panel_s">                  
                                <div class="panel-body padding-10">                      
                                    <div class="widget-dragger ui-sortable-handle"></div>
                                    
                                    <div class="tw-flex tw-justify-between tw-items-center tw-p-1.5">                          
                                        <p class="tw-font-medium tw-flex tw-items-center tw-mb-0 tw-space-x-1.5 rtl:tw-space-x-reverse">                              
                                            <i class="far fa-file-alt"></i>                                 
                                            <span class="tw-text-neutral-700">                                  
                                                <?=_l('sam_proposal_as_count')?>                           
                                            </span>                          
                                        </p>                          
                                    </div>
                                    <hr class="-tw-mx-3 tw-mt-2 tw-mb-0">
                                    <div class="table-responsive" style="border:none">
                                        <table class="table align-items-center mb-0" id="searched_proposal_count_table" style="font-size:0.9em;margin-top:1em">
                                            <thead>
                                                <tr>
                                                    <th class="" style="width:55%"><?=_l('sam_total_sent')?></th>    
                                                    <th class="" style="width:45%"><?=_l('sam_total_accepted')?></th>
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
                <div class="widget" id="widget-active_deals" data-name="Active Deals">          
                    <div class="row" id="active_deals_report">          
                        <div class="col-md-12">
                            <div class="panel_s">                  
                                <div class="panel-body padding-10">                      
                                    <div class="widget-dragger ui-sortable-handle"></div>
                                    
                                    <div class="tw-flex tw-justify-between tw-items-center tw-p-1.5">                          
                                        <p class="tw-font-medium tw-flex tw-items-center tw-mb-0 tw-space-x-1.5 rtl:tw-space-x-reverse">                              
                                            <i class="far fa-file-alt"></i>                                 
                                            <span class="tw-text-neutral-700">                                  
                                                <?=_l('sam_active_deals_allpipline')?>                          
                                            </span>                          
                                        </p>                          
                                    </div>                               
                                    <div class="table-responsive" style="border:none">
                                        <table class="table align-items-center mb-0" id="searched_active_deal_table" style="font-size:0.9em;margin-top:0">
                                            <tbody></tbody>
                                        </table>
                                    </div>     
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="widget" id="widget-leadsdealsoverview" data-name="Leads and Deals Overview">          
                    <div class="row" id="leadsdealsoverview_report">          
                        <div class="col-md-12">
                            <div class="panel_s">                  
                                <div class="panel-body padding-10">                      
                                    <div class="widget-dragger ui-sortable-handle"></div>
                                    
                                    <div class="tw-flex tw-justify-between tw-items-center tw-p-1.5">                          
                                        <p class="tw-font-medium tw-flex tw-items-center tw-mb-0 tw-space-x-1.5 rtl:tw-space-x-reverse">                              
                                            <i class="far fa-file-alt"></i>                                 
                                            <span class="tw-text-neutral-700">                                  
                                                <?=_l('sam_leads_deals_overview')?>                          
                                            </span>                          
                                        </p>                          
                                    </div>
                                    <hr class="-tw-mx-3 tw-mt-2 tw-mb-0">
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
<?php $this->load->view('admin/utilities/calendar_template'); ?>
<?php $this->load->view(SAM_MODULE.'/dashboard/dashboard_js'); ?>
<script> 
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
    $("#searched_reminder_table tbody").html('<?=$reminder_list?>'); 
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

$('#apply_filters_timesheets').on('click', function(e) { 
    e.preventDefault();                            
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
            leads_chart_data = response[2];   
            proposal_amount_table = response[3];   
            proposal_count_table = response[4];   
            deal_value_table = response[5];   
            pipeline_name = response[6];   
            spent_time_on_cust = response[7];   
            reminder_list = response[8];   
            $('#deal_value_title').html('<i class="far fa-file-alt"></i> Active Deals - '+pipeline_name); 
            $("#searched_timesheets_data_table tbody").html(timesheets);
            $("#searched_leads_data_table tbody").html(leads_data);
            $("#searched_proposal_amount_table tbody").html(proposal_amount_table);
            $("#searched_proposal_count_table tbody").html(proposal_count_table);
            $("#searched_active_deal_table tbody").html(deal_value_table);
            
            //$("#searched_spenttime_on_customer_table tbody").html(spent_time_on_cust); 
            $("#searched_spenttime_on_customer_div").html(spent_time_on_cust); 
            $("#searched_reminder_div").html(reminder_list); 
            //$("#searched_reminder_table").DataTable('refresh');
            
            if(leads_chart_data!=""){ 
                my_chart.destroy();                                
                //console.log(JSON.parse(leads_chart_data));
                Deal_Lead_Chart(leads_chart_data); 
            }     

        } else {
            $("#searched_timesheets_data_table tbody").html('');
            $("#searched_leads_data_table tbody").html('');
            $("#searched_proposal_amount_table tbody").html('');
            $("#searched_proposal_count_table tbody").html('');
            $("#searched_active_deal_table tbody").html('');
            $('#deal_value_title').html('<i class="far fa-file-alt"></i> Active Deals - All Pipeline');
            $("#searched_spenttime_on_customer_table tbody").html(''); 
            $("#searched_spenttime_on_customer_div").html(''); 
            $("#searched_reminder_div").html(''); 
        }                                                                                                                
    });
});

</script>