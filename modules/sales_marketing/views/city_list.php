<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="tw-mb-2 sm:tw-mb-4">
                    <a href="#" onclick="new_city(); return false;" class="btn btn-primary">
                        <i class="fa-regular fa-plus tw-mr-1"></i>
                        <?php 
                        echo _l('sam_add_new_city'); 
                        ?>
                    </a>
                </div>
                <div class="panel_s">
                    <div class="panel-body panel-table-full">
                        <?php if (count($cities) > 0) { ?>
                            <table class="table dt-table" data-order-col="1" data-order-type="asc">
                                <thead>
                                    <th><?php echo _l('id'); ?></th>
                                    <th><?php echo _l('sam_city_name');?></th>
                                    <th><?php echo _l('sam_country_name');?></th>
                                    <th><?php echo _l('options'); ?></th>
                                </thead>
                                <tbody>
                                <?php foreach ($cities as $value) { ?>
                                    <tr>
                                        <td><?php echo $value['city_id']; ?></td>
                                        <td><a href="#"
                                               onclick="edit_city(this,<?php echo $value['city_id']; ?>); return false"
                                               data-name="<?php echo $value['name']; ?>"><?php echo $value['name']; ?></a><br/>
                                            <span class="text-muted">
                                            <?php //echo _l('Total', total_rows(db_prefix() . '_sam_cities', ['city_id' => $value['city_id']])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="text-muted">
                                            <?php echo sam_getData('tbl_sam_countries','name',array('country_id' => $value['country_id'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="tw-flex tw-items-center tw-space-x-3">
                                                <a href="#"
                                                   onclick="edit_city(this,<?php echo $value['city_id']; ?>,<?php echo $value['country_id']; ?>); return false"
                                                   data-name="<?php echo $value['name']; ?>"
                                                   class="tw-text-neutral-500 hover:tw-text-neutral-700 focus:tw-text-neutral-700">
                                                    <i class="fa-regular fa-pen-to-square fa-lg"></i>
                                                </a>
                                                <a href="<?php echo admin_url(SAM_MODULE.'/delete_city/' . $value['city_id']); ?>"
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
                            <p class="no-margin"><?php echo _l('sam_no_record_found'); ?></p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="city" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url(SAM_MODULE.'/addOrUpdateCity')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('sam_edit_city'); ?></span>
                    <span class="add-title"><?php echo _l('sam_add_new_city'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php
                        $selected = '';
                        /* if(!empty($cities)){
                            $selected_country = sam_getData('tbl_sam_cities','country_id',array('city_id' => $value['city_id']));
                            if ($selected_country!='') {
                                //$selected = $selected_country;
                            }
                        }
                        else{
                            $selected = get_option('default_sam_country');    
                        } */
                        //$selected = get_option('default_sam_country');  
                        $attributes = array('required' => true, 'onchange' => 'enableCityInput(this.value)');
                        echo render_select('country_id', $countries, ['country_id', 'name'], _l('sam_country'), $selected,$attributes);
                        ?>
                    </div>
                </div>
                <div class="row" id="city_textbox">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php 
                        echo render_input('name', _l('sam_city_name')); 
                        ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('sam_close'); ?></button>
                <button type="submit" class="btn btn-primary"><?php echo _l('sam_save'); ?></button>
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
        }, manage_sam_cities);
        $('#city').on('hidden.bs.modal', function (event) {
            $('#additional').html('');
            $('#city input[name="name"]').val('');
            $('.add-title').removeClass('hide');
            $('.edit-title').removeClass('hide');
        });
        let countryID = $('select[name="country_id"]').val();
        if(countryID==''){
            $('#name').attr('disabled','disabled');    
        }
    });

    function manage_sam_cities(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function (response) {
            window.location.reload();
        });
        return false;
    }

    function new_city() {
        $('#city').modal('show');
        $('.edit-title').addClass('hide');
    }

    function edit_city(invoker, id,country_id) {
        var name = $(invoker).data('name');
        $('#additional').append(hidden_input('id', id));
        $('#city input[name="name"]').val(name);
        //$('#city input[name="country_id"]').val(country_id);
        $('#city').modal('show');
        $('.add-title').addClass('hide');
    }
    
    function enableCityInput(val){
        if(val==''){
            $('#name').attr('disabled','disabled');
        }
        else{
            $('#name').removeAttr('disabled');
        }    
    }
</script>
</body>

</html>