<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="tw-mb-2 sm:tw-mb-4">
                    <a href="#" onclick="new_category(); return false;" class="btn btn-primary">
                        <i class="fa-regular fa-plus tw-mr-1"></i>
                        <?php echo 'New Category';?>
                    </a>
                </div>
                <div class="panel_s">
                    <div class="panel-body panel-table-full">
                        <?php if (count($categories) > 0) { ?>
                            <table class="table dt-table" data-order-col="2" data-order-type="asc">
                                <thead>
                                    <th><?php echo _l('id'); ?></th>
                                    <th><?php echo 'Category Name'; ?></th>
                                    <th><?php echo _l('options'); ?></th>
                                </thead>
                                <tbody>
                                    <?php foreach ($categories as $category) { ?>
                                        <tr>
                                            <td><?php echo $category->category_id; ?></td>
                                            <td>
                                                <a href="#"
                                                   onclick="edit_category(this, <?php echo $category->category_id; ?>); return false"
                                                   data-name="<?php echo $category->category_name; ?>">
                                                    <?php echo $category->category_name; ?>
                                                </a>
                                            </td>

                                            <td>
                                                <div class="tw-flex tw-items-center tw-space-x-3">
                                                    <a href="#"
                                                       onclick="edit_category(this, <?php echo $category->category_id; ?>); return false"
                                                       data-name="<?php echo $category->category_name; ?>"
                                                       class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700">
                                                        <i class="fa-regular fa-pen-to-square fa-lg"></i>
                                                    </a>
                                                    <a href="<?php echo admin_url('sales_marketing/delete_category/' . $category->category_id); ?>"
                                                       class="tw-mt-px tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700 _delete">
                                                        <i class="fa-regular fa-trash-can fa-lg"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        <?php } else { ?>
                            <p class="no-margin"><?php echo _l('Category Not Found'); ?></p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="category" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('sales_marketing/category')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo 'Edit Category';?></span>
                    <span class="add-title"><?php echo 'New Category'; ?></span>
                </h4>
            </div>
            <div class="modal-body">
               <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php echo render_input('name', 'Category Name'); ?>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-primary"><?php echo _l('submit'); ?></button>
            </div>
        </div>
        <!-- /.modal-content -->
        <?php echo form_close(); ?>
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<?php init_tail(); ?>
<script>
    'use strict';
    $(function () {
        appValidateForm($('form'), {
            name: 'required'
        }, manage_deals_category);
        $('#category').on('hidden.bs.modal', function (event) {
            $('#additional').html('');
            $('#category input[name="name"]').val('');
            $('.add-title').removeClass('hide');
            $('.edit-title').removeClass('hide');
        });
    });

    function manage_deals_category(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function (response) {
            window.location.reload();
        });
        return false;
    }

    function new_category() {
        $('#category').modal('show');
        $('.edit-title').addClass('hide');
    }

  function edit_category(invoker, id) {
    let name = $(invoker).data('name');
    $('#additional').append(hidden_input('id', id));
    $('#category input[name="name"]').val(name);
    $('#category').modal('show');
    $('.add-title').addClass('hide');
}

</script>
</body>

</html>