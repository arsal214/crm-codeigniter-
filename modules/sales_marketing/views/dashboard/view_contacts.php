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
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $serial = 1;
                                    foreach ($customers as $customer):
                                        $customer_id = $customer['customer_id'];
                                    ?>
                                        <tr>
                                            <td><?php echo $serial++; ?></td>
                                            <td>
                                                <span class="label label-default mleft5 customer-group-list pointer">
                                                    <a href="https://crm.mazajnet.com/admin/clients/client/<?php echo $customer['userid']; ?>?group=contacts" target="_blank">
                                                        <?php echo $customer['firstname']; ?> <?php echo $customer['lastname']; ?>
                                                    </a>
                                                </span>
                                            </td>
                                            <td><?php echo $customer['email']; ?></td>
                                            <td><?php echo $customer['phonenumber']; ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                            </table>
                        <?php } else { ?>
                            <p class="no-margin"><?php echo _l('CONTACT NOT FOUND'); ?></p>
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
