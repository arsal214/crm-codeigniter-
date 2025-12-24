<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
         <div class="go-back-btn">
                <a href="https://crm.mazajnet.com/admin/sales_marketing/dashboard/statistics" class="btn btn-primary">
                    Back
                </a>
            </div>
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body panel-table-full">
                        <?php if (count($customers) > 0) { ?>
                            <table class="table" id="customers_table" data-order-col="3" data-order-type="asc">
                                <thead>
                                    <tr>
                                        <th>S.No</th>
                                        <th>Company</th>
                                        <th>Group</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $serial = 1;
                                    foreach ($customers as $customer):
                                        $customer_id = $customer['customer_id'];
                                        $rel_id = $customer_id;
                                        $related_id = $this->dashboard_model->get_related_id_from_tbl_sam($rel_id);
                                    ?>
                                        <tr>
                                            <td><?php echo $serial++; ?></td>
                                            <td>
                                                <span class="label label-default mleft5 customer-group-list pointer">
                                                    <?php if ($related_id): ?>
                                                        <a href="https://crm.mazajnet.com/admin/sales_marketing/details/<?php echo $related_id; ?>" target="_blank">
                                                            <?php echo $customer['company']; ?> &nbsp; &nbsp; &nbsp;<span style="font-size:10px; color:green;">AVAILABLE</span>
                                                        </a>
                                                    <?php else: ?>
                                                        <?php echo $customer['company']; ?> &nbsp; &nbsp; &nbsp;<span style="font-size:10px; color:red;">NO SALE AND MARKETING AVAILABLE</span>
                                                    <?php endif; ?>
                                                </span>
                                            </td>

                                            <td><span style="color:#3D8D7A; font-weight:bold;"><?php echo $customer['name']; ?></span></td>
                                        </tr>
                                    <?php endforeach; ?>
                                    </tbody>
                            </table>
                        <?php } else { ?>
                            <p class="no-margin"><?php echo _l('GROUP NOT FOUND'); ?></p>
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
            "order": [[ 2, 'asc' ]], // Sort by the "Group" column by default
            "pageLength": 25 // Number of records per page
        });
    });
</script>
</body>
</html>
