<div>
    <div class="activity-feed">
        <?php foreach ($activity_log2 as $log) { ?>
            <div class="feed-item">
                <div class="date">
                    <span class="text-has-action" data-toggle="tooltip"
                          data-title="<?php echo _dt($log['reg_date']); ?>">
                        <?php echo time_ago($log['reg_date']); ?>
                    </span>
                </div>
                <div class="text">
                    <?php if ($log['staff_id'] != 0) { ?>
                        <a href="<?php echo admin_url('profile/' . $log['staff_id']); ?>">
                            <?php echo staff_profile_image($log['staff_id'], ['staff-profile-xs-image pull-left mright5']);
                            ?>
                        </a>
                        <?php
                    }
                    $additional_data = '';
                    if (!empty($log['transaction_type'])) {
                        //$additional_data = $log['transaction_type'];
                        echo ($log['staff_id'] == 0) ? _l($log['transaction_type']) : get_staff_full_name($log['staff_id']) . ' - ' . _l($log['transaction_type']);
                    } else {
                        echo  get_staff_full_name($log['staff_id']) . ' - ';
                    }
                    ?>
                </div>
            </div>
        <?php } ?>
    </div>
    <div class="clearfix"></div>
</div>
