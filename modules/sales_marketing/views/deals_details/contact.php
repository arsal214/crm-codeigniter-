<?php
$edited = has_permission('sam', '', 'edit');
?>
<div class="input-group-btn">
    <a href="<?= admin_url(SAM_MODULE.'/clients/addContact/'.$deals_details->customers['id'].'/'.$deals_details->id) ?>" id="add_contact_link" data-toggle="modal" data-target="#myModal">
        <i class="btn btn-primary">+ New Contact</i>
    </a>
</div>
<table class="table dt-table" data-order-col="3" data-order-type="asc">
    <thead>
        <tr>
            <th style="width: 200px;"><?= _l('Full Name') ?></th>
            <th><?= _l('Email') ?></th>
            <th><?= _l('Phone') ?></th>
            <th><?= _l('Active') ?></th>
            <th><?= _l('Last Login') ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        if (!empty($deals_details->contacts)) {
            foreach ($deals_details->contacts as $contact) {
                ?>
                <tr>
                    <td><?= !empty($contact['full_name']) ? $contact['full_name'] : '-' ?></td>
                    <td><?= !empty($contact['email']) ? $contact['email'] : '-' ?></td>
                    <td><?= !empty($contact['phonenumber']) ? $contact['phonenumber'] : '-' ?></td>
                    <td>
                        <?php
                        if (!empty($contact['active'])) {
                            if ($contact['active'] == 1) {
                                echo '<span class="text-success" style="font-weight:bold">Yes</span>';
                            } else {
                                echo '<span class="text-danger" style="font-weight:bold">No</span>';
                            }
                        } else {
                            echo '-';
                        }
                        ?>
                    </td>
                    <td><?= !empty($contact['last_login']) ? $contact['last_login'] : '-' ?></td>
                </tr>
                <?php
            }
        }
        ?>
    </tbody>
</table>

