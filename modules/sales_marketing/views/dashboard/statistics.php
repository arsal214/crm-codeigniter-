<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php //echo "<pre>"; print_r($_SERVER['HTTP_USER_AGENT']); exit;?>
<style>
.progress-container {
    width: 100%;
    background-color: #f3f3f3;
    border-radius: 5px;
    overflow: hidden;
}

.progress-bar {
    height: 20px;
    line-height: 20px;
    color: white;
    text-align: center;
    border-radius: 5px;
}

.progress-bar-low {
    background-color: #ff4d4d; /* Red for low percentage */
}

.progress-bar-medium {
    background-color: #ffcc00; /* Yellow for medium percentage */
}

.progress-bar-high {
    background-color: #4CAF50; /* Green for high percentage */
}
.kpi-container {
    display: flex;
    flex-direction: column;  /* Stack items vertically, one pair per row */
    gap: 20px; /* Space between rows */
}

.kpi-row {
    display: flex;
    align-items: flex-start; /* Prevents flex items from stretching */
    gap: 10px; /* Space between columns in a row */
    padding: 10px;
    border-radius: 5px;
    background-color: white;
}


.kpi-item {
    flex: 1 1 48%; /* Each item will take up 48% of the container width */
    border: 1px solid #ddd;
    padding: 10px;
    border-radius: 5px;
    background-color: #F3F3F3; /* Grey background */
}

.kpi-name, .kpi-percentage {
    font-weight: bold;
}

.kpi-item table {
    width: 100%;
    margin-top: 10px;
    border-collapse: collapse;
}

.kpi-item table th, .kpi-item table td {
    padding: 8px;
    border-bottom: 1px solid #ddd;
}

.kpi-item .progress-container {
    margin-top: 5px;
}

.progress-bar {
    height: 8px;
    border-radius: 5px;
}
.chart-container {
    display: flex;
    justify-content: space-around;
    width: 50%;
}
.canvas {
    width: 100%;
    height: 100px;
}

</style>

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
                            <option value="yesterday" selected>
                                <?php echo _l('Yesterday'); ?>
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
                
               <div class="row">
                    <div class="col-md-1" id="apply-btn1">
                        <a href="#" id="apply_filters_timesheets" onclick="apply_filters_timesheets(); loadStatisticChart();" class="btn btn-primary">
                            <?php echo _l('apply'); ?>
                        </a>
                    </div> 
                     <?php if (is_admin()) { ?>  
                        <div class="col-md-2">
                            <a href="#" id="run_cron_job" class="btn btn-success">
                                Run Cron Job
                            </a>
                        </div> 
                    <?php } ?>
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
                    <a href="#" id="apply_filters_timesheets" onclick="apply_filters_timesheets(); loadStatisticChart();" class="btn btn-primary pull-left">
                        <?php echo _l('apply'); ?>
                    </a>
                </div>    
            </div>
                
        </div>

       <div class="row" style="background:#f1f5f9">
                <div class="col-md-8 ui-sortable" data-container="left-8" style="top:1em"> 
                    <div class="tabs-container">
                        <ul class="nav nav-tabs" id="performanceTabs">
                           <li class="nav-item">
                                <a class="nav-link active" data-toggle="tab" href="#performance">
                                    <i class="fas fa-chart-bar"></i> Performance
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#invoice" onclick="loadInvoiceContent()">
                                    <i class="fas fa-file-invoice-dollar"></i> Invoice
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#statistic_chart" onclick="loadStatisticChart()">
                                    <i class="fas fa-chart-line"></i> Statistic Chart
                                </a>
                            </li>
                        </ul>
                        <div class="widget relative" id="widget-top_stats" data-name="Quick Statistics">
                            <div class="tab-content">
                                <div id="performance" class="tab-pane fade show active">
                                    <div class="col-md-12 text-stats-wrapper">
                                        <?php if (!empty($kpi_data)): ?>
                                            <div class="performance-header">
                                                <div class="tw-font-medium">
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div id="invoice" class="tab-pane fade">
                                    <div class="col-md-12 text-stats-wrapper">
                                        <div class="invoice-header">
                                            <div class="tw-font-medium">
                                                <i class="fa-solid fa-file-invoice"></i> Invoice
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="statistic_chart" class="tab-pane fade">
                                    <div class="col-md-12">
                                        <div class="chart-container">
                                            <canvas class="top_stats_wrapper" style="margin-right:20px; margin-bottom:20px" id="invoiceCount"></canvas>
                                            <canvas class="top_stats_wrapper" style="margin-bottom:20px" id="totalActivityChart"></canvas>
                                        </div>
                                        <div class="chart-container">
                                             <canvas class="top_stats_wrapper" style="margin-right:20px; margin-bottom:20px" id="customer_countChart"></canvas>
                                             <canvas class="top_stats_wrapper" style="margin-bottom:20px" id="contact_countChart"></canvas>
                                        </div>
                                        <div class="chart-container">
                                            <canvas class="top_stats_wrapper" style="margin-right:20px; margin-bottom:20px" id="uniranks_schools_count"></canvas>
                                            <canvas class="top_stats_wrapper" style="margin-bottom:20px" id="uniranks_agents_count"></canvas>
                                        </div>
                                        <div class="chart-container">
                                            <canvas class="top_stats_wrapper" style="margin-right:20px; margin-bottom:20px" id="uniranks_students_count"></canvas>
                                            <canvas class="top_stats_wrapper" style="margin-bottom:20px" id="uniranks_progress_count"></canvas>
                                        </div>
                                        <div class="chart-container">
                                            <canvas class="top_stats_wrapper" style="margin-right:20px; margin-bottom:20px" id="uniranks_coupon_count"></canvas>
                                            <canvas class="top_stats_wrapper" style="margin-bottom:20px" id="uniranks_fair_count"></canvas>
                                        </div>
                                        <div class="chart-container">
                                            <canvas class="top_stats_wrapper" style="margin-right:20px; margin-bottom:20px" id="uniranks_events_count"></canvas>
                                            <canvas class="top_stats_wrapper" style="margin-bottom:20px" id="total_time_sheet"></canvas>
                                        </div>
                                       <div class="chart-container">
                                            <canvas class="top_stats_wrapper"id="total_desk_time"></canvas>
                                            <canvas></canvas>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            
                <!-- Right Column (4 Columns, with stacked 2 sections) -->
                <div class="col-md-4 d-flex flex-column">
                    <div class="quick-stats-invoices col-12 mb-3">              
                        <div class="top_stats_wrapper">                                  
                            <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">                      
                                <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">                          
                                    <span class="tw-truncate">
                                        <i class="far fa-user"></i> 
                                        Groups                       
                                    </span>                       
                                </div>                   
                            </div>                              
                            <div class="table-responsive" style="border:none">
                                <table class="table align-items-center mb-0" id="searched_group_data_table" style="font-size:0.9em">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder" style="width:55%">Group Name</th>    
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder" style="width:45%">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>               
                        </div>          
                    </div>
                    <div class="quick-stats-invoices col-12 mtop10">              
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
                    <div class="quick-stats-invoices col-12 mtop10">              
                        <div class="top_stats_wrapper">                                  
                            <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">                      
                                <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">                          
                                    <span class="tw-truncate">
                                        <i class="fa-solid fa-university"></i> 
                                        UNIRANKS University                   
                                    </span>                      
                                </div>                   
                            </div>                              
                            <div class="table-responsive" style="border:none">
                                <table class="table align-items-center mb-0" id="searched_university_data_table" style="font-size:0.9em">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder"> Country</th>    
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder"> University</th>    
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder"> Claimed</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder"> Not Claimed</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder"> %</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>               
                        </div>          
                    </div>
                    <div class="quick-stats-invoices col-12 mtop10">              
                        <div class="top_stats_wrapper">                                  
                            <div class="tw-text-neutral-800 mtop5 tw-flex tw-items-center tw-justify-between">                      
                                <div class="tw-font-medium tw-inline-flex text-neutral-600 tw-items-center tw-truncate">                          
                                    <span class="tw-truncate">
                                        <i class="fa-solid fa-school"></i> 
                                        UNIRANKS Schools                   
                                    </span>                       
                                </div>                   
                            </div>                              
                            <div class="table-responsive" style="border:none">
                                <table class="table align-items-center mb-0" id="searched_school_data_table" style="font-size:0.9em">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder"> Country</th>    
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder"> School</th>    
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder"> Claimed</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder"> Not Claimed</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder"> %</th>
                                        </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>
                            </div>               
                        </div>          
                    </div>

                </div>
            </div>
        <div class="clearfix"></div>
    </div>
</div>
<?php init_tail(); ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script> 
$("body").on("change", 'select[name="range"]', (function()
{
    var t = $("#period-div");
    t.attr("style","display:block");
    if($(this).val() == "period")
    {
        t.removeClass("hide"); 
        $("#apply-btn1").addClass("hide");   
    }
    else
    {
        t.addClass("hide");   
        $("#apply-btn1").removeClass("hide");
    }
    //"period" == $(this).val() ? t.removeClass("hide") : (t.addClass("hide"));
    t.find("input").val("");
    
}
));
$(document).ready(function()
{
    apply_filters_timesheets(); 
});
$(document).ready(function() {
    $('#performanceTabs a[href="#performance"]').tab('show');

    $('#performanceTabs a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
        var target = $(e.target).attr("href"); // Get the href attribute of the clicked tab
        if (target === "#invoice") {
            $('.text-stats-wrapper').hide();
            loadInvoiceContent();
        } else if (target === "#performance") {
            $('.text-stats-wrapper').show();
        } else if (target === "#statistic_chart") {
            $('.text-stats-wrapper').hide();
        }
    });
});


<?php if(isset($groups_data)) { ?>    
    $("#searched_group_data_table tbody").html('<?=$groups_data?>');
<?php  } ?>



function apply_filters_timesheets() {
    let data = {};
    data.range = $('#range').val();
    data.staff_id = $('#staff_id').val();
    data.clientid = $('#clientid').val();
    data.periodfrom = $('#period-from').val();
    data.periodto = $('#period-to').val();

    $.post(admin_url + '<?=SAM_MODULE?>/dashboard/filter_statistics_data', data).done(function(response) {
        response = JSON.parse(response);
        if (response) {
            let percent_data = response.percent_data;
            let groups_data = response.table1;
            let university_data = response.table2;
            let deals_status_data = response.table3;
            let school_data = response.table4;
            let overall_performance = response.overall_performance;

            $("#searched_group_data_table tbody").html(groups_data);
            $("#searched_university_data_table tbody").html(university_data);
            $("#searched_deals_status_data_table tbody").html(deals_status_data);
            $("#searched_school_data_table tbody").html(school_data);

            if (percent_data !== undefined && percent_data.length > 0) {
                $(".text-stats-wrapper").html('');
                let totalPercentage = 0;
                let totalKPIs = percent_data.length;

                $.each(percent_data, function(index, kpi) {
                    let overall_percent_val = (typeof kpi.percentage.percentage === "object" && kpi.percentage.percentage !== null)
                        ? kpi.percentage.percentage
                        : kpi.percentage.percentage;
                    totalPercentage += parseFloat(overall_percent_val);
                });

                let overallPerformance = (totalPercentage / totalKPIs).toFixed(2);
                let staff_names = [];
                let staff_name_display = data.staff_id ? $("#staff_id option:selected").text() : "Company";
                let performanceHtml = '<div class="performance-header">';

                performanceHtml += '<div class="overall-performance top_stats_wrapper" style="margin-bottom:20px;">';
                performanceHtml += '<span style="font-weight: bold">';
                performanceHtml += '<i class="fas fa-chart-bar" style="margin-bottom:12px"></i> Performance</span>';
                performanceHtml += '<div class="top_stats_wrapper" style="width:102%; margin-left: -1%; background-color: #F3F3F3; padding:10px;">';
                performanceHtml += '<span style="font-weight: bold;">' + staff_name_display + ' - Overall Performance</span>';
                performanceHtml += '<span style="color: #007bff; font-size: 18px; float: right;">' + overall_performance.toFixed(2) + '%</span>';
                performanceHtml += '<div class="progress-container" style="margin-top: 10px; width: 100%; height: 10px; background-color: #ddd; border-radius: 5px;">';
                performanceHtml += '<div class="progress-bar ' +
                    (overall_performance >= 70 ? 'progress-bar-high' : (overall_performance >= 30 ? 'progress-bar-medium' : 'progress-bar-low')) +
                    '" style="width: ' + overall_performance + '%; height: 10px; background-color: ' +
                    (overall_performance >= 70 ? '#4CAF50' : (overall_performance >= 30 ? '#FFA500' : '#F44336')) + '; border-radius: 5px;"></div>';
                performanceHtml += '</div></div></div>';

                let groupedKPIs = {};
                $.each(percent_data, function(index, kpi) {
                    let catId = kpi.category_id;
                    if (!groupedKPIs[catId]) {
                        groupedKPIs[catId] = {
                            category_name: kpi.category_name,
                            items: []
                        };
                    }
                    groupedKPIs[catId].items.push(kpi);
                });

                performanceHtml += '<div class="kpi-container">';

                $.each(groupedKPIs, function(catId, group) {
                    performanceHtml += '<div class="kpi-group" style="margin-bottom: 5px; background: #fff; border: 1px solid #ddd; border-radius: 5px;">';
                    performanceHtml += '<div style="margin-top: 15px; margin-left: 20px; margin-bottom: 2px; font-weight: bold">' +
                        '<i class="fas fa-chart-bar" style="margin-right: 5px;"></i>' +
                        htmlspecialchars(group.category_name) +
                        '</div>';

                    // Separate Sales KPIs from others
                    let salesItems = group.items.filter(kpi => kpi.kpi_name === "Sales");
                    let otherItems = group.items.filter(kpi => kpi.kpi_name !== "Sales");

                     if (salesItems.length > 0) {
                          let totalSalesPercentage = salesItems.reduce((sum, kpi) => {
                                let overall_percent_val = (typeof kpi.percentage.percentage === "object" && kpi.percentage.percentage !== null)
                                    ? kpi.percentage.percentage
                                    : kpi.percentage.percentage;
                                return sum + parseFloat(overall_percent_val || 0);
                            }, 0);
                        performanceHtml += '<div class="kpi-row" style="margin-bottom: 10px;">';
                        performanceHtml += '<div class="kpi-item" style="background: #F3F3F3; padding: 10px; border-radius: 5px;">';
                        performanceHtml += '<span style="font-weight: bold;">Sales</span>:';
                        performanceHtml += '<span class="kpi-percentage" style="float: right;">' + totalSalesPercentage.toFixed(2) + '%</span>';
                
                        // Display Sales data row-wise
                        $.each(salesItems, function(index, kpi) {
                            let overall_percent_val = kpi.percentage.percentage || 0;

                            performanceHtml += '<div style="margin-top: 5px;">' +
                                (kpi.staff_name ? kpi.staff_name + ' ' : '  ') + 
                                (kpi.currency_symbol ? kpi.currency_symbol + ' ' : '  ')  +
                                (kpi.percentage.actions_count !== null ? kpi.percentage.actions_count : 0) +
                                ' / ' + kpi.formatted_actions +
                                '</div>';

                        performanceHtml += '<div class="progress-container" style="margin-top: 10px; width: 100%; height: 10px; background-color: #ddd; border-radius: 5px;">';
                            performanceHtml += '<div class="progress-bar ' +
                                (overall_percent_val >= 70 ? 'progress-bar-high' : (overall_percent_val >= 30 ? 'progress-bar-medium' : 'progress-bar-low')) +
                                '" style="width: ' + overall_percent_val + '%; height: 10px; background-color: ' +
                                (overall_percent_val >= 70 ? '#4CAF50' : (overall_percent_val >= 30 ? '#FFA500' : '#F44336')) + '; border-radius: 5px;"></div>';
                            performanceHtml += '</div>';
                        });
                
                        performanceHtml += '</div>'; // End Sales KPI container
                        performanceHtml += '</div>'; // End KPI row
                    }

                    for (let i = 0; i < otherItems.length; i++) {
                        if (i % 2 === 0) {
                            performanceHtml += '<div class="kpi-row" style="display: flex; gap: 20px; margin-bottom: 10px;">';
                        }

                        let kpi = otherItems[i];
                        let overall_percent_val;
                        if (kpi.kpi_name === "Used Coupons") {
                            overall_percent_val = kpi.percentage.percentage; // Coupon ke liye specific percentage
                        } else {
                            overall_percent_val = (typeof kpi.overall_percentage === "object" && kpi.overall_percentage !== null)
                                ? kpi.overall_percentage
                                : kpi.overall_percentage;
                        }


                        let kpiNameHtml;
                        if (kpi.kpi_name === "New Customers") {
                            kpiNameHtml = `<a href="https://crm.mazajnet.com/admin/sales_marketing/dashboard/view_customers?staffid=${kpi.staffid}&range=${kpi.percentage.range}" target="_blank" style="color: #3D8D7A; font-weight: bold;">${htmlspecialchars(kpi.kpi_name)}</a>`;
                        } 
                        else if (kpi.kpi_name === "New Contacts") {
                            kpiNameHtml = `<a href="https://crm.mazajnet.com/admin/sales_marketing/dashboard/view_contacts?staffid=${kpi.staffid}&range=${kpi.percentage.range}" target="_blank" style="color: #3D8D7A; font-weight: bold;">${htmlspecialchars(kpi.kpi_name)}</a>`;
                        }
                        else if (kpi.kpi_name === "Communication") {
                            kpiNameHtml = `<a href="https://crm.mazajnet.com/admin/sales_marketing/dashboard/view_communication?staffid=${kpi.staffid}&range=${kpi.percentage.range}" target="_blank" style="color: #3D8D7A; font-weight: bold;">${htmlspecialchars(kpi.kpi_name)}</a>`;
                        } 
                        else
                        {
                            kpiNameHtml = `<span style="font-weight: bold;">${htmlspecialchars(kpi.kpi_name)}</span>`;
                        }

                        performanceHtml += '<div class="kpi-item" style="flex: 1; background: #F3F3F3; padding: 10px; border-radius: 5px;">';
                        performanceHtml += kpiNameHtml + ':';
                        performanceHtml += '<span class="kpi-percentage" style="float: right;">' + overall_percent_val + '%</span>';
                        performanceHtml += '<div style="margin-top: 5px;">' +
                            (kpi.currency_symbol ? kpi.currency_symbol + ' ' : '') +
                            (kpi.percentage.actions_count !== null ? kpi.percentage.actions_count : 0) +
                            ' / ' + parseInt(kpi.formatted_actions)
                             +
                            '</div>';
                        performanceHtml += '<div class="progress-container" style="margin-top: 10px; width: 100%; height: 10px; background-color: #ddd; border-radius: 5px;">';
                        performanceHtml += '<div class="progress-bar ' +
                            (overall_percent_val >= 70 ? 'progress-bar-high' : (overall_percent_val >= 30 ? 'progress-bar-medium' : 'progress-bar-low')) +
                            '" style="width: ' + overall_percent_val + '%; height: 10px; background-color: ' +
                            (overall_percent_val >= 70 ? '#4CAF50' : (overall_percent_val >= 30 ? '#FFA500' : '#F44336')) + '; border-radius: 5px;"></div>';
                        performanceHtml += '</div>';
                        performanceHtml += '</div>'; // Close KPI item

                        if (i % 2 !== 0 || i === otherItems.length - 1) {
                            performanceHtml += '</div>'; // End row
                        }
                    }

                    performanceHtml += '</div>'; // End group container (with category name inside)
                });

                performanceHtml += '</div>'; // Close main kpi-container

                $("#performance .text-stats-wrapper").html(performanceHtml);
                loadInvoiceContent();
            } else {
                $(".text-stats-wrapper").html('<p>No performance data available.</p>');
            }
        }
    });
}

function htmlspecialchars(str) 
{
    var element                     = document.createElement('div');
    if (str) 
    {
        element.innerText           = str;
        element.textContent         = str;
    }
    return element.innerHTML;
}
function loadInvoiceContent()
{
    $.ajax({
        url: 'https://crm.mazajnet.com/admin/sales_marketing/dashboard/finance_overview', // Call the controller method
        type: 'GET',
        success: function(response)
        {
            $('#invoice').html(response);
        },
        error: function()
        {
            alert("Error loading the invoice content.");
        }
    });
}
  $(document).ready(function () {
        $("#run_cron_job").click(function (e) {
            e.preventDefault(); // Default action roko

            $.ajax({
                url: "https://crm.mazajnet.com/admin/sales_marketing/DailyStatsCron/saveDailyStats",
                type: "POST",
                success: function (response) {
                    // Success Message
                    Swal.fire({
                        title: "Success!",
                        text: "Cron job has been executed successfully.",
                        icon: "success",
                        confirmButtonText: "OK"
                    });

                    // Apply Filters Automatically
                    apply_filters_timesheets();
                },
                error: function () {
                    Swal.fire({
                        title: "Error!",
                        text: "Failed to execute cron job.",
                        icon: "error",
                        confirmButtonText: "Try Again"
                    });
                }
            });
        });
    });

 var chartInstances = {};
    
    function renderCharts(data) {
        var canvasIds = [
            "invoiceCount", "totalActivityChart", "customer_countChart", "contact_countChart",
            "uniranks_schools_count", "uniranks_agents_count", "uniranks_students_count",
            "uniranks_progress_count", "uniranks_coupon_count", "uniranks_fair_count",
            "uniranks_events_count", "total_time_sheet", "total_desk_time"
        ];
    
        var labels = data.map(item => item.date_created);
    
        var datasets = {
            invoiceCount: {
                label: "Sales",
                data: data.map(item => item.total_invoice),
                borderColor: "rgba(75, 192, 192, 1)",
                backgroundColor: "rgba(75, 192, 192, 0.2)",
                borderWidth: 2,
                pointRadius: 5
            },
            totalActivity: {
                label: "Communication",
                data: data.map(item => item.total_activity_count),
                borderColor: "rgba(0, 123, 255, 1)",
                backgroundColor: "rgba(0, 123, 255, 0.2)",
                borderWidth: 2,
                pointRadius: 5
            },
            newCustomers: {
                label: "New Customers",
                data: data.map(item => item.customer_count),
                borderColor: "rgba(255, 99, 132, 1)",
                backgroundColor: "rgba(255, 99, 132, 0.2)",
                borderWidth: 2,
                pointRadius: 5
            },
            newContacts: {
                label: "New Contacts",
                data: data.map(item => item.contact_count),
                borderColor: "rgba(75, 192, 75, 1)",
                backgroundColor: "rgba(75, 192, 75, 0.2)",
                borderWidth: 2,
                pointRadius: 5
            },
            uniranksSchool: {
                label: "School Registrations",
                data: data.map(item => item.uniranks_schools_count),
                borderColor: "rgba(255, 159, 64, 1)",
                backgroundColor: "rgba(255, 159, 64, 0.2)",
                borderWidth: 2,
                pointRadius: 5
            },
            uniranksAgent: {
                label: "Registered Agents",
                data: data.map(item => item.uniranks_agents_count),
                borderColor: "rgba(153, 102, 255, 1)",
                backgroundColor: "rgba(153, 102, 255, 0.2)",
                borderWidth: 2,
                pointRadius: 5
            },
            uniranksStudent: {
                label: "Students Registrations",
                data: data.map(item => item.uniranks_students_count),
                borderColor: "rgba(54, 162, 235, 1)",
                backgroundColor: "rgba(54, 162, 235, 0.2)",
                borderWidth: 2,
                pointRadius: 5
            },
            uniranksProgress: {
                label: "Profile Progress",
                data: data.map(item => item.uniranks_progress_count),
                borderColor: "rgba(255, 205, 86, 1)",
                backgroundColor: "rgba(255, 205, 86, 0.2)",
                borderWidth: 2,
                pointRadius: 5
            },
            uniranksCoupon: {
                label: "Used Coupons",
                data: data.map(item => item.uniranks_coupon_count),
                borderColor: "rgba(80, 80, 80, 1)",
                backgroundColor: "rgba(80, 80, 80, 0.2)",
                borderWidth: 2,
                pointRadius: 5
            },
            uniranksFair: {
                label: "University Fair",
                data: data.map(item => item.uniranks_fair_count),
                borderColor: "rgba(255, 99, 71, 1)",
                backgroundColor: "rgba(255, 99, 71, 0.2)",
                borderWidth: 2,
                pointRadius: 5
            },
            uniranksEvent: {
                label: "Career Talk",
                data: data.map(item => item.uniranks_events_count),
                borderColor: "rgba(147, 112, 219, 1)",
                backgroundColor: "rgba(147, 112, 219, 0.2)",
                borderWidth: 2,
                pointRadius: 5
            },
            uniranksTimesheet: {
                label: "Time Sheet",
                data: data.map(item => parseFloat(item.total_time_sheet_hhmm)),
                borderColor: "rgba(255, 165, 0, 1)",
                backgroundColor: "rgba(255, 165, 0, 0.2)",
                borderWidth: 2,
                pointRadius: 5
            },
            uniranksDesktime: {
                label: "Desktime",
                data: data.map(item => parseFloat(item.total_desk_time_hhmm)),
                borderColor: "rgba(0, 128, 255, 1)",
                backgroundColor: "rgba(0, 128, 255, 0.2)",
                borderWidth: 2,
                pointRadius: 5
            }
        };
    
        function createChart(canvasId, dataset) {
            var canvas = document.getElementById(canvasId);
            if (canvas && canvas instanceof HTMLCanvasElement) {
                // Destroy existing chart instance if it exists
                if (chartInstances[canvasId]) {
                    chartInstances[canvasId].destroy();
                }
                // Create new chart instance
                chartInstances[canvasId] = new Chart(canvas.getContext("2d"), {
                    type: "line",
                    data: {
                        labels: labels,
                        datasets: [dataset]
                    },
                    options: {
                        responsive: true,
                        scales: {
                            x: { title: { display: true, text: "Date" } },
                            y: { title: { display: true, text: "Count" }, beginAtZero: true }
                        }
                    }
                });
            } else {
                console.error("Canvas not found:", canvasId);
            }
        }
    
        // Render all charts
        createChart("invoiceCount", datasets.invoiceCount);
        createChart("totalActivityChart", datasets.totalActivity);
        createChart("customer_countChart", datasets.newCustomers);
        createChart("contact_countChart", datasets.newContacts);
        createChart("uniranks_schools_count", datasets.uniranksSchool);
        createChart("uniranks_agents_count", datasets.uniranksAgent);
        createChart("uniranks_students_count", datasets.uniranksStudent);
        createChart("uniranks_progress_count", datasets.uniranksProgress);
        createChart("uniranks_coupon_count", datasets.uniranksCoupon);
        createChart("uniranks_fair_count", datasets.uniranksFair);
        createChart("uniranks_events_count", datasets.uniranksEvent);
        createChart("total_time_sheet", datasets.uniranksTimesheet);
        createChart("total_desk_time", datasets.uniranksDesktime);
    }
    function loadStatisticChart() {
    var staff_id = $("#staff_id").val();
    var range = $("#range").val();
    var periodfrom = $('#period-from').val();
    var periodto = $('#period-to').val();

    $.ajax({
        url: 'https://crm.mazajnet.com/admin/sales_marketing/dashboard/get_chart_data',
        type: "POST",
        data: { staff_id: staff_id, range: range, periodfrom: periodfrom, periodto: periodto },
        dataType: "json",
        success: function (response) {
            if (response.status === "success") {
                setTimeout(() => renderCharts(response.data), 500);
            } else {
                alert("No data available.");
            }
        },
        error: function () {
            alert("Error fetching data.");
        }
    });
}


</script>