<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content accounting-template proposal">
        <div class="row">
            <?php
            $customer_name = "";
            if($customer_id!=""){
                $customer_info = get_client($customer_id);
                if($customer_info && isset($customer_info->company)){
                    $customer_name = $customer_info->company;
                }
            }
         if (isset($proposal)) {
             echo form_hidden('isedit', $proposal->id);
         }
         $rel_type  = '';
            $rel_id = '';
            if (isset($proposal) || ($this->input->get('rel_id') && $this->input->get('rel_type'))) {
                if ($this->input->get('rel_id')) {
                    $rel_id   = $this->input->get('rel_id');
                    $rel_type = $this->input->get('rel_type');
                } else {
                    $rel_id   = $proposal->rel_id;
                    $rel_type = $proposal->rel_type;
                }
            }
            ?>
            <?php
         echo form_open($this->uri->uri_string(), ['id' => 'proposal-form', 'class' => '_transaction_form proposal-form']);

         if ($this->input->get('estimate_request_id')) {
             echo form_hidden('estimate_request_id', $this->input->get('estimate_request_id'));
             echo form_hidden('sam_id', $sam_id);
         }
         ?>

            <div class="col-md-12">
                <h4
                    class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700 tw-flex tw-items-center tw-space-x-2">
                    <span>
                        <?php echo e(isset($proposal) ? format_proposal_number($proposal->id) : _l('new_proposal')); ?>
                    </span>
                    <?php echo isset($proposal) ? format_proposal_status($proposal->status) : ''; ?>  
                </h4>
                <?php if(isset($proposal)) {?>
                    <div class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700 tw-flex tw-items-center tw-space-x-2">
                        <?php echo "Contract #: " ?>
                        <a href="<?php echo admin_url(SAM_MODULE.'/contracts/contract/' . $proposal->contract_id .'/'.$proposal->sam_id); ?>" target="_blank">
                            <?php echo $proposal->contract_id; ?>
                        </a>  
                    </div>
                <?php } ?>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6 border-right">
                                <?php $value = (isset($proposal) ? $proposal->subject : ''); ?>
                                <?php $attrs = (isset($proposal) ? [] : ['autofocus' => true]); ?>
                                <?php echo render_input('subject', 'proposal_subject', $value, 'text', $attrs); ?>
                                <div class="form-group select-placeholder">
                                    <label for="rel_type"
                                        class="control-label"><?php echo _l('proposal_related'); ?></label>
                                    <select name="rel_type" id="rel_type" class="selectpicker" data-width="100%"
                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        <option value="customer" <?php if ($rel_type == 'customer') {echo 'selected';}?>>
                                        <?php echo _l('proposal_for_customer'); ?></option>
                                    </select>
                                </div>  
                                
                                <div class="form-group select-placeholder">
                                    <label for="rel_id"
                                        class="control-label"><?php echo _l('proposal_related'); ?></label>
                                    <select name="rel_id" id="rel_id" class="selectpicker" data-width="100%"
                                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                        <option value="<?=$customer_id?>"><?=$customer_name?></option>
                                    </select>
                                </div> 
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php $value = (isset($proposal) ? _d($proposal->date) : _d(date('Y-m-d'))) ?>
                                        <?php echo render_date_input('date', 'proposal_date', $value); ?>
                                    </div>
                                    <div class="col-md-6">
                                        <?php
                        $value = '';
                        if (isset($proposal)) {
                            $value = _d($proposal->open_till);
                        } else {
                            if (get_option('proposal_due_after') != 0) {
                                $value = _d(date('Y-m-d', strtotime('+' . get_option('proposal_due_after') . ' DAY', strtotime(date('Y-m-d')))));
                            }
                        }
                        echo render_date_input('open_till', 'proposal_open_till', $value); ?>
                                    </div>
                                </div>
                                <?php
                           $selected      = '';
                           $currency_attr = ['data-show-subtext' => true];
                           foreach ($currencies as $currency) {
                               if ($currency['isdefault'] == 1) {
                                   $currency_attr['data-base'] = $currency['id'];
                               }
                               if (isset($proposal)) {
                                   if ($currency['id'] == $proposal->currency) {
                                       $selected = $currency['id'];
                                   }
                                   if ($proposal->rel_type == 'customer') {
                                       $currency_attr['disabled'] = true;
                                   }
                               } else {
                                   if ($rel_type == 'customer') {
                                       $customer_currency = $this->clients_model->get_customer_default_currency($rel_id);
                                       if ($customer_currency != 0) {
                                           $selected = $customer_currency;
                                       } else {
                                           if ($currency['isdefault'] == 1) {
                                               $selected = $currency['id'];
                                           }
                                       }
                                       $currency_attr['disabled'] = true;
                                   } else {
                                       if ($currency['isdefault'] == 1) {
                                           $selected = $currency['id'];
                                       }
                                   }
                               }
                           }
                           $currency_attr = apply_filters_deprecated('proposal_currency_disabled', [$currency_attr], '2.3.0', 'proposal_currency_attributes');
                           $currency_attr = hooks()->apply_filters('proposal_currency_attributes', $currency_attr);
                           ?>
                                <div class="row">
                                    <div class="col-md-6">
                                        <?php
                              echo render_select('currency', $currencies, ['id', 'name', 'symbol'], 'proposal_currency', $selected, $currency_attr);
                              ?>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group select-placeholder">
                                            <label for="discount_type"
                                                class="control-label"><?php echo _l('discount_type'); ?></label>
                                            <select name="discount_type" class="selectpicker" data-width="100%"
                                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                <option value="" selected><?php echo _l('no_discount'); ?></option>
                                                <option value="before_tax" <?php
                                  if (isset($estimate)) {
                                      if ($estimate->discount_type == 'before_tax') {
                                          echo 'selected';
                                      }
                                  }?>><?php echo _l('discount_type_before_tax'); ?></option>
                                                <option value="after_tax" <?php if (isset($estimate)) {
                                      if ($estimate->discount_type == 'after_tax') {
                                          echo 'selected';
                                      }
                                  } ?>><?php echo _l('discount_type_after_tax'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <?php $fc_rel_id = (isset($proposal) ? $proposal->id : false); ?>
                                <?php echo render_custom_fields('proposal', $fc_rel_id); ?>
                                <div class="form-group no-mbot">
                                    <label for="tags" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i>
                                        <?php echo _l('tags'); ?></label>
                                    <input type="text" class="tagsinput" id="tags" name="tags"
                                        value="<?php echo(isset($proposal) ? prep_tags_input(get_tags_in($proposal->id, 'proposal')) : ''); ?>"
                                        data-role="tagsinput">
                                </div>
                                <div class="form-group mtop10 no-mbot">
                                    <p><?php echo _l('proposal_allow_comments'); ?></p>
                                    <div class="onoffswitch">
                                        <input type="checkbox" id="allow_comments" class="onoffswitch-checkbox" <?php if ((isset($proposal) && $proposal->allow_comments == 1) || !isset($proposal)) {
                                      echo 'checked';
                                  }; ?> value="on" name="allow_comments">
                                        <label class="onoffswitch-label" for="allow_comments" data-toggle="tooltip"
                                            title="<?php echo _l('proposal_allow_comments_help'); ?>"></label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group select-placeholder">
                                            <label for="status"
                                                class="control-label"><?php echo _l('proposal_status'); ?></label>
                                            <?php
                                    $disabled = '';
                                    if (isset($proposal)) {
                                        if ($proposal->estimate_id != null || $proposal->invoice_id != null) {
                                            $disabled = 'disabled';
                                        }
                                    }
                                    ?>
                                            <select name="status" class="selectpicker" data-width="100%"
                                                <?php echo e($disabled); ?>
                                                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                                <?php foreach ($statuses as $status) { ?>
                                                <option value="<?php echo e($status); ?>" <?php if ((isset($proposal) && $proposal->status == $status) || (!isset($proposal) && $status == 0)) {
                                        echo 'selected';
                                    } ?>><?php echo format_proposal_status($status, '', false); ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                    
                                    </div>
                                </div>                                                                
                            </div>
                        </div>
                        <div
                            class="btn-bottom-toolbar bottom-transaction text-right sm:tw-flex sm:tw-items-center sm:tw-justify-between">
                            <p class="no-mbot pull-left mtop5 btn-toolbar-notice tw-hidden sm:tw-block">
                                <?php echo _l('include_proposal_items_merge_field_help', '<b>{proposal_items}</b>'); ?>
                            </p>
                            <div>
<!--                                <button type="button"
                                    class="btn btn-default mleft10 proposal-form-submit save-and-send transaction-submit">
                                    <?php //echo _l('save_and_send'); ?>
                                </button> -->
                                <button class="btn btn-primary mleft5 proposal-form-submit transaction-submit"
                                    type="button">
                                    <?php echo _l('submit'); ?>
                                </button>
                            </div>
                        </div>
                    </div>
                    <hr class="hr-panel-separator" />
                    <?php $this->load->view(SAM_MODULE.'/proposals/_add_edit_items'); ?>
                </div>
            </div>
            <?php echo form_close(); ?>
            <?php $this->load->view(SAM_MODULE.'/invoice_items/item'); ?>
        </div>
        <div class="btn-bottom-pusher"></div>
    </div>
</div>
<?php init_tail(); ?>
<script>
var _rel_id = $('#rel_id'),
    _rel_type = $('#rel_type'),
    _rel_id_wrapper = $('#rel_id_wrapper'),
    _project_wrapper = $('.projects-wrapper'),
    data = {};

$(function() {
    <?php if (isset($proposal) && $proposal->rel_type === 'customer') { ?>
    init_proposal_project_select('select#project_id')
    <?php } ?>
    $('body').on('change', '#rel_type', function() {
        if (_rel_type.val() != 'customer') {
            _project_wrapper.addClass('hide')
        }
    });

    $('body').on('change', '#rel_id', function() {
        if (_rel_type.val() == 'customer') {
            //console.log('working')
            var projectAjax = $('select#project_id');
            var clonedProjectsAjaxSearchSelect = projectAjax.html('').clone();
            projectAjax.selectpicker('destroy').remove();
            projectAjax = clonedProjectsAjaxSearchSelect;
            $('#project_ajax_search_wrapper').append(clonedProjectsAjaxSearchSelect);
            init_proposal_project_select(projectAjax);
            _project_wrapper.removeClass('hide')
        }
    });

    init_currency();
    // Maybe items ajax search
    init_ajax_search('items', '#item_select.ajax-search', undefined, admin_url + 'items/search');
    validate_proposal_form();
    $('body').on('change', '#rel_id', function() {
        if ($(this).val() != '') {
            $.get(admin_url + 'proposals/get_relation_data_values/' + $(this).val() + '/' + _rel_type
                .val(),
                function(response) {
                    $('input[name="proposal_to"]').val(response.to);
                    $('textarea[name="address"]').val(response.address);
                    $('input[name="email"]').val(response.email);
                    $('input[name="phone"]').val(response.phone);
                    $('input[name="city"]').val(response.city);
                    $('input[name="state"]').val(response.state);
                    $('input[name="zip"]').val(response.zip);
                    $('select[name="country"]').selectpicker('val', response.country);
                    var currency_selector = $('#currency');
                    if (_rel_type.val() == 'customer') {
                        if (typeof(currency_selector.attr('multi-currency')) == 'undefined') {
                            currency_selector.attr('disabled', true);
                        }

                    } else {
                        currency_selector.attr('disabled', false);
                    }
                    var proposal_to_wrapper = $('[app-field-wrapper="proposal_to"]');
                    if (response.is_using_company == false && !empty(response.company)) {
                        proposal_to_wrapper.find('#use_company_name').remove();
                        proposal_to_wrapper.find('#use_company_help').remove();
                        proposal_to_wrapper.append('<div id="use_company_help" class="hide">' +
                            response.company + '</div>');
                        proposal_to_wrapper.find('label')
                            .prepend(
                                "<a href=\"#\" id=\"use_company_name\" data-toggle=\"tooltip\" data-title=\"<?php echo _l('use_company_name_instead'); ?>\" onclick='document.getElementById(\"proposal_to\").value = document.getElementById(\"use_company_help\").innerHTML.trim(); this.remove();'><i class=\"fa fa-building-o\"></i></a> "
                            );
                    } else {
                        proposal_to_wrapper.find('label #use_company_name').remove();
                        proposal_to_wrapper.find('label #use_company_help').remove();
                    }
                    /* Check if customer default currency is passed */
                    if (response.currency) {
                        currency_selector.selectpicker('val', response.currency);
                    } else {
                        /* Revert back to base currency */
                        currency_selector.selectpicker('val', currency_selector.data('base'));
                    }
                    currency_selector.selectpicker('refresh');
                    currency_selector.change();
                }, 'json');
        }
    });
    $('.rel_id_label').html(_rel_type.find('option:selected').text());
    _rel_type.on('change', function() {
        var clonedSelect = _rel_id.html('').clone();
        _rel_id.selectpicker('destroy').remove();
        _rel_id = clonedSelect;
        $('#rel_id_select').append(clonedSelect);
        proposal_rel_id_select();
        if ($(this).val() != '') {
            _rel_id_wrapper.removeClass('hide');
        } else {
            _rel_id_wrapper.addClass('hide');
        }
        $('.rel_id_label').html(_rel_type.find('option:selected').text());
    });
    proposal_rel_id_select();
    <?php if (!isset($proposal) && $rel_id != '') { ?>
    _rel_id.change();
    <?php } ?>
});

function init_proposal_project_select(selector) {
    init_ajax_search('project', selector, {
        customer_id: function() {
            return $('#rel_id').val();
        }
    })
}

function proposal_rel_id_select() {
    var serverData = {};
    serverData.rel_id = _rel_id.val();
    data.type = _rel_type.val();
    init_ajax_search(_rel_type.val(), _rel_id, serverData);
}

function validate_proposal_form() {
    appValidateForm($('#proposal-form'), {
        subject: 'required',
        //proposal_to: 'required',
        rel_type: 'required',
        //rel_id: 'required',
        date: 'required',
        email: {
            email: true,
            required: true
        },
        currency: 'required',
    });
}   

$("body").on('show.bs.modal', '#sales_item_modal', function (event) {  
/*    setTimeout(function(){
    $(".items-select-wrapper .bs3").css("width", "100%"); alert();   
    },2000); */ 
                    
}); 

// Items add/edit
function manage_invoice_items(form) {
    var data = $(form).serialize();

    var url = form.action;
    $.post(url, data).done(function (response) {
        response = JSON.parse(response);
        if (response.success == true) {
            var item_select = $('#item_select');
            if ($("body").find('.accounting-template').length > 0) {
                if (!item_select.hasClass('ajax-search')) {
                    var group = item_select.find('[data-group-id="' + response.item.group_id + '"]');
                    if (group.length == 0) {
                        var _option = '<optgroup label="' + (response.item.group_name == null ? '' : response.item.group_name) + '" data-group-id="' + response.item.group_id + '">' + _option + '</optgroup>';
                        if (item_select.find('[data-group-id="0"]').length == 0) {
                            item_select.find('option:first-child').after(_option);
                        } else {
                            item_select.find('[data-group-id="0"]').after(_option);
                        }
                    } else {
                        group.prepend('<option data-subtext="' + response.item.long_description + '" value="' + response.item.itemid + '">(' + accounting.formatNumber(response.item.rate) + ') ' + response.item.description + '</option>');
                    }
                }
                if (!item_select.hasClass('ajax-search')) {
                    item_select.selectpicker('refresh');
                } else {

                    item_select.contents().filter(function () {
                        return !$(this).is('.newitem') && !$(this).is('.newitem-divider');
                    }).remove();

                    var clonedItemsAjaxSearchSelect = item_select.clone();
                    item_select.selectpicker('destroy').remove();
                    $("body").find('.items-select-wrapper').append(clonedItemsAjaxSearchSelect);
                    init_ajax_search('items', '#item_select.ajax-search', undefined, admin_url + 'items/search');
                }

                add_item_to_preview(response.item.itemid);

            } else {
                // Is general items view
                $('.table-invoice-items').DataTable().ajax.reload(null, false);
            }
            
            $(".items-select-wrapper .bs3").css("width", "100%");
            
            alert_float('success', response.message);
        }
        $('#sales_item_modal').modal('hide');
    }).fail(function (data) {
        alert_float('danger', data.responseText);
    });
    return false;
} 

</script>
</body>

</html>
