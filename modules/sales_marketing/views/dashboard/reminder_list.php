<!--<link rel="stylesheet" href="<?=site_url()?>/modules/sales_marketing/assets/css/datatables.css"> -->
<table class="table dt-table" data-order-col="3" data-order-type="desc" id="searched_reminder_table" data-page-length='10' style="font-size:0.9em">
    <thead>
        <tr>
            <th class="">Customer</th>    
            <th class="">Employee</th>
            <th class="">Title</th>
            <th class="">DateTime</th>
            <th class="">IN</th>
        </tr>
    </thead>
    <tbody>
        <?php
            $current_time = time();
            if($reminder_data){
                foreach($reminder_data as $key => $val){
                    $client_data = get_client($val['customer_id']);
                    $client_name = "";
                    if($client_data){
                        $client_name = $client_data->company; 
                    }
                    $date = $val['date'];
                    $trstyle = "";
                    $tdstyle = "";
                    if($current_time > strtotime($date)){
                        $trstyle = 'style="background-color:#ff0000c4;font-weight:bold"';
                        $tdstyle = 'style="color:#fff"';
                    }
            ?>
                    <tr <?=$trstyle?>>
                        <td <?=$tdstyle?>><a href="<?=admin_url(SAM_MODULE.'/details/'.$val['sam_id'])?>" <?=$tdstyle?> target="_blank"><?=$client_name?></a></td>
                        <td <?=$tdstyle?>><?=get_staff_full_name($val['staff'])?></td>
                        <td <?=$tdstyle?>><?=$val['title']?></td>
                        <td <?=$tdstyle?>><?=$val['date']?></td>
                        <td <?=$tdstyle?>><?=timeRemainString($date)?></td>
                    </tr>            
            <?php
                }
            }
        ?>
    </tbody>
</table>  

<script type="text/javascript" id="" src="<?=site_url()?>/assets/plugins/datatables/datatables.min.js?v=3.1.6"></script>
<script>   
$(document).ready(function () {  

    //app.lang.datatables = {"emptyTable":"No entries found","info":"Showing _START_ to _END_ of _TOTAL_ entries","infoEmpty":"Showing 0 to 0 of 0 entries","infoFiltered":"(filtered from _MAX_ total entries)","lengthMenu":"_MENU_","loadingRecords":"Loading...","processing":"<div class=\"dt-loader\"><\/div>","search":"<div class=\"input-group\"><span class=\"input-group-addon\"><span class=\"fa fa-search\"><\/span><\/span>","searchPlaceholder":"Search...","zeroRecords":"No matching records found","paginate":{"first":"First","last":"Last","next":"Next","previous":"Previous"},"aria":{"sortAscending":" activate to sort column ascending","sortDescending":" activate to sort column descending"}}; 
    $('#searched_reminder_table').DataTable({
        //dom: 'Bfrtip',
        paging: true,
        searching: true,
        order: [[3, 'desc']]
    });
    
});

</script>