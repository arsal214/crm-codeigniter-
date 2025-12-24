<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="go-back-btn" style="margin-bottom:10px">
                    <a href="https://crm.mazajnet.com/admin/sales_marketing/dashboard/statistics" class="btn btn-primary">
                        Back
                    </a>
                </div>
                <div class="panel_s">
                    <div class="panel-body panel-table-full">
                        <?php if (count($customers) > 0) { ?>
                            <table class="table" id="customers_table" data-order-col="3" data-order-type="asc">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Company</th>
                                        <th>Communicate</th>
                                        <th>Date</th>
                                        <th>Time Ago</th> 
                                    </tr>
                                </thead>
                                <tbody>
                                   <?php 
                                    function timeAgo($date) {
                                        $timestamp = strtotime($date);
                                        $current_time = time();
                                        $time_difference = $current_time - $timestamp;

                                        if ($time_difference < 60) {
                                            return 'Just now';
                                        } elseif ($time_difference < 3600) {
                                            return floor($time_difference / 60) . ' min' . (floor($time_difference / 60) > 1 ? 's' : '') . ' ago';
                                        } elseif ($time_difference < 86400) {
                                            return floor($time_difference / 3600) . ' hour' . (floor($time_difference / 3600) > 1 ? 's' : '') . ' ago';
                                        } elseif ($time_difference < 2592000) { // 30 دن
                                            return floor($time_difference / 86400) . ' day' . (floor($time_difference / 86400) > 1 ? 's' : '') . ' ago';
                                        } elseif ($time_difference < 31536000) { // 12 مہینے
                                            return floor($time_difference / 2592000) . ' month' . (floor($time_difference / 2592000) > 1 ? 's' : '') . ' ago';
                                        } else {
                                            return floor($time_difference / 31536000) . ' year' . (floor($time_difference / 31536000) > 1 ? 's' : '') . ' ago';
                                        }
                                    }

                                    function multipleTimeAgo($dates) {
                                        $date_array = explode(',', $dates); // تمام تاریخوں کو الگ کریں
                                        $time_ago_array = array_map('timeAgo', $date_array); // ہر تاریخ پر timeAgo لگائیں
                                        return implode(', ', $time_ago_array); // سب کو string میں جوڑیں
                                    }

                                    $serial = 1;
                                    $total_calls = 0;
                                    foreach ($customers as $customer):
                                        $customer_id = $customer['customer_id'];
                                        $total_calls += $customer['call_count'];
                                    ?>
                                        <tr>
                                            <td><?php echo $serial++; ?></td>
                                            <td>
                                                <span class="label label-default mleft5 customer-group-list pointer">
                                                    <a href="https://crm.mazajnet.com/admin/sales_marketing/details/<?php echo $customer['module_field_id']. '/call'; ?>" target="_blank">
                                                        <?php echo $customer['company']; ?>
                                                    </a>
                                                </span>
                                            </td>
                                            <td><?php echo $customer['call_count'] . ' ' . ($customer['call_count'] == 1 ? 'Time' : 'Times'); ?></td>
                                            <td><?php echo $customer['dates']; ?></td>
                                            <td><?php echo multipleTimeAgo($customer['dates']); ?></td> <!-- نیا کالم -->
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                            </table>
                        <?php } else { ?>
                            <p class="no-margin"><?php echo _l('COMMUNICATION NOT FOUND'); ?></p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include jQuery and DataTables CSS and JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.13.1/css/jquery.dataTables.min.css">
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>

<script>
$(document).ready(function() {
    $('#customers_table').DataTable({
        "order": [[ 0, 'asc' ]], // S.No کے مطابق ترتیب دینا
        "pageLength": 25 // فی صفحہ 25 ریکارڈز دکھائیں
    });
});
</script>
</body>
</html>
