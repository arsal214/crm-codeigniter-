<!--<link rel="stylesheet" href="<?=site_url()?>/modules/sales_marketing/assets/css/datatables.css"> -->
<table class="table dt-table" data-order-col="1" data-order-type="asc" id="searched_spenttime_on_customer_table" data-page-length='10' style="font-size:0.9em">
    <thead>
        <tr>
            <th class="" style="width:40%">Customer Name</th>    
            <th class="" style="width:30%">Total Time Spent</th>
            <th class="" style="width:30%">Total Deal Value</th>
        </tr>
    </thead>
    <tbody>
        <?php
            if($data){
                foreach($data as $key => $val){
                    $client_data = get_client($val['customer_id']);
                    $client_name = "";
                    if($client_data){
                        $client_name = $client_data->company; 
                    }
            ?>
                    <tr>
                        <td><a href="<?=admin_url(SAM_MODULE.'/details/'.$val['sam_id'])?>"><?=$client_name?></td>
                        <td><?=_l('time_h').': '.e(seconds_to_time_format($val['total_time_spent']))?></td>
                        <td><?=$val['total_deal_value']?></td>
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
    $('#searched_spenttime_on_customer_table').DataTable({
        paging: true,
        searching: true,
    });
    

    
});

</script>