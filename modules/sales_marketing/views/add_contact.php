<form action="<?=admin_url(SAM_MODULE.'/clients/save_contact/'.$client_res['userid'])?>" id="contact-form" autocomplete="off" method="post" enctype="multipart/form-data" accept-charset="utf-8" novalidate="novalidate">                                                                                                               
    <input type="hidden" class="" name="<?= $this->security->get_csrf_token_name(); ?>" value="<?= $this->security->get_csrf_hash(); ?>">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
        <div class="tw-flex">
            <div class="tw-mr-4 tw-flex-shrink-0 tw-relative">
                                    
        </div>
        <div>
            <h4 class="modal-title tw-mb-0">Add new contact</h4>
            <p class="tw-mb-0">
                <?=$client_res['company']?>                        
            </p>
        </div>
    </div>
    </div>
    <div class="modal-body">
        <div class="row">
            <div class="col-md-12">
                <div id="contact-profile-image" class="form-group">
                    <label for="profile_image" class="profile-image">Profile image</label>
                    <input type="file" name="profile_image" class="form-control" id="profile_image">
                </div>
                <input type="hidden" name="contactid" value="">
                <div class="form-group" app-field-wrapper="firstname">
                    <label for="firstname" class="control-label"> 
                        <small class="req text-danger">* </small>
                        First Name
                    </label>
                    <input type="text" id="firstname" name="firstname" class="form-control" value="">
                </div>                                                
                <div class="form-group" app-field-wrapper="lastname">
                    <label for="lastname" class="control-label"> 
                        <small class="req text-danger">* </small>
                        Last Name
                    </label>
                    <input type="text" id="lastname" name="lastname" class="form-control" value="">
                </div>                                                
                <div class="form-group" app-field-wrapper="position">
                    <label for="position" class="control-label">
                        Position
                    </label>
                    <input type="text" id="title" name="title" class="form-control" value="">
                </div>                                                
                <div class="form-group" app-field-wrapper="email">
                    <label for="email" class="control-label"> 
                        <small class="req text-danger">* </small>
                        Email
                    </label>
                    <input type="email" id="email" name="email" class="form-control" value="">
                </div>                                                
                <div class="form-group" app-field-wrapper="phonenumber">
                    <label for="phonenumber" class="control-label">Phone</label>
                    <input type="text" id="phonenumber" name="phonenumber" class="form-control" autocomplete="off" value="">
                </div>                        
                <div class="form-group contact-direction-option">
                    <label for="direction">Direction</label>
                    <div class="dropdown bootstrap-select bs3 dropup" style="width: 100%;">
                        <select class="selectpicker" data-none-selected-text="System Default" data-width="100%" name="direction" id="direction" tabindex="-98">
                            <option value=""></option>
                            <option value="ltr">LTR</option>
                            <option value="rtl">RTL</option>
                        </select>
                    </div>
                </div>
                                            

                <!-- fake fields are a workaround for chrome autofill getting the wrong fields -->
                <input type="text" class="fake-autofill-field" name="fakeusernameremembered" value="" tabindex="-1" aria-invalid="false">
                <input type="password" class="fake-autofill-field" name="fakepasswordremembered" value="" tabindex="-1" aria-invalid="false">

                <div class="client_password_set_wrapper">
                    <label for="password" class="control-label"> 
                        <small class="req text-danger">* </small>
                        Password                            
                    </label>
                    <div class="input-group">
                        <input type="password" class="form-control password" name="password" autocomplete="false">
                        <span class="input-group-addon tw-border-l-0">
                            <a href="#password" class="show_password" onclick="showPassword('password'); return false;"><i class="fa fa-eye"></i></a>
                        </span>
                        <span class="input-group-addon">
                            <a href="#" class="generate_password" onclick="generatePassword(this);return false;"><i class="fa fa-refresh"></i></a>
                        </span>
                    </div>                              
                </div>
                <hr>
                <div class="checkbox checkbox-primary">
                    <input type="checkbox" name="is_primary" id="contact_primary">
                    <label for="contact_primary">
                        Primary Contact                            
                    </label>
                </div>
                                            
                <div class="checkbox checkbox-primary">
                    <input type="checkbox" name="donotsendwelcomeemail" id="donotsendwelcomeemail">
                    <label for="donotsendwelcomeemail">
                        Do not send welcome email                            
                    </label>
                </div>                                               
                <div class="checkbox checkbox-primary">
                    <input type="checkbox" name="send_set_password_email" id="send_set_password_email">
                    <label for="send_set_password_email">
                        Send SET password email                            
                    </label>
                </div>                       
                <hr>
                    <p class="bold">Permissions</p>
                    <p class="text-danger">Make sure to set appropriate permissions for this contact</p>
                                                                    
                    <div class="col-md-6 row">
                        <div class="row">
                            <div class="col-md-6 mtop10 border-right">
                                <span>Invoices</span>
                            </div>
                            <div class="col-md-6 mtop10">
                                <div class="onoffswitch">
                                    <input type="checkbox" id="1" class="onoffswitch-checkbox" checked="" value="1" name="permissions[]">
                                    <label class="onoffswitch-label" for="1"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                                            
                    <div class="col-md-6 row">
                        <div class="row">
                            <div class="col-md-6 mtop10 border-right">
                                <span>Estimates</span>
                            </div>
                            <div class="col-md-6 mtop10">
                                <div class="onoffswitch">
                                    <input type="checkbox" id="2" class="onoffswitch-checkbox" checked="" value="2" name="permissions[]">
                                    <label class="onoffswitch-label" for="2"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                                            
                    <div class="col-md-6 row">
                        <div class="row">
                            <div class="col-md-6 mtop10 border-right">
                                <span>Contracts</span>
                            </div>
                            <div class="col-md-6 mtop10">
                                <div class="onoffswitch">
                                    <input type="checkbox" id="3" class="onoffswitch-checkbox" checked="" value="3" name="permissions[]">
                                    <label class="onoffswitch-label" for="3"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                                            
                    <div class="col-md-6 row">
                        <div class="row">
                            <div class="col-md-6 mtop10 border-right">
                                <span>Proposals</span>
                            </div>
                            <div class="col-md-6 mtop10">
                                <div class="onoffswitch">
                                    <input type="checkbox" id="4" class="onoffswitch-checkbox" checked="" value="4" name="permissions[]">
                                    <label class="onoffswitch-label" for="4"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                                            
                    <div class="col-md-6 row">
                        <div class="row">
                            <div class="col-md-6 mtop10 border-right">
                                <span>Support</span>
                            </div>
                            <div class="col-md-6 mtop10">
                                <div class="onoffswitch">
                                    <input type="checkbox" id="5" class="onoffswitch-checkbox" checked="" value="5" name="permissions[]">
                                    <label class="onoffswitch-label" for="5"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                                            
                    <div class="col-md-6 row">
                        <div class="row">
                            <div class="col-md-6 mtop10 border-right">
                                <span>Projects</span>
                            </div>
                            <div class="col-md-6 mtop10">
                                <div class="onoffswitch">
                                    <input type="checkbox" id="6" class="onoffswitch-checkbox" checked="" value="6" name="permissions[]">
                                    <label class="onoffswitch-label" for="6"></label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                                            
                    <hr>
                    <p class="bold">Email Notifications</p>
                    <div id="contact_email_notifications">
                        <div class="col-md-6 row">
                            <div class="row">
                                <div class="col-md-6 mtop10 border-right">
                                    <span>Invoice</span>
                                </div>
                                <div class="col-md-6 mtop10">
                                    <div class="onoffswitch">
                                        <input type="checkbox" id="invoice_emails" data-perm-id="1" class="onoffswitch-checkbox" value="invoice_emails" name="invoice_emails">
                                        <label class="onoffswitch-label" for="invoice_emails"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 row">
                            <div class="row">
                                <div class="col-md-6 mtop10 border-right">
                                    <span>Estimate</span>
                                </div>
                                <div class="col-md-6 mtop10">
                                    <div class="onoffswitch">
                                        <input type="checkbox" id="estimate_emails" data-perm-id="2" class="onoffswitch-checkbox" value="estimate_emails" name="estimate_emails">
                                        <label class="onoffswitch-label" for="estimate_emails"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 row">
                            <div class="row">
                                <div class="col-md-6 mtop10 border-right">
                                    <span>Credit Note</span>
                                </div>
                                <div class="col-md-6 mtop10">
                                    <div class="onoffswitch">
                                        <input type="checkbox" id="credit_note_emails" data-perm-id="1" class="onoffswitch-checkbox" value="credit_note_emails" name="credit_note_emails">
                                        <label class="onoffswitch-label" for="credit_note_emails"></label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 row">
                            <div class="row">
                                <div class="col-md-6 mtop10 border-right">
                                    <span>Project</span>
                                </div>
                                <div class="col-md-6 mtop10">
                                    <div class="onoffswitch">
                                        <input type="checkbox" id="project_emails" data-perm-id="6" class="onoffswitch-checkbox" value="project_emails" name="project_emails">
                                        <label class="onoffswitch-label" for="project_emails"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 row">
                            <div class="row">
                                <div class="col-md-6 mtop10 border-right">
                                    <span>Tickets</span>
                                </div>
                                <div class="col-md-6 mtop10">
                                    <div class="onoffswitch">
                                        <input type="checkbox" id="ticket_emails" data-perm-id="5" class="onoffswitch-checkbox" value="ticket_emails" name="ticket_emails">
                                        <label class="onoffswitch-label" for="ticket_emails"></label>
                                    </div>
                                </div>
                                <div class="col-md-6 mtop10 border-right">
                                    <span><i class="fa-regular fa-circle-question" data-toggle="tooltip" data-title="Only project related tasks"></i>
                                        Task</span>
                                </div>
                                <div class="col-md-6 mtop10">
                                    <div class="onoffswitch">
                                        <input type="checkbox" id="task_emails" data-perm-id="6" class="onoffswitch-checkbox" value="task_emails" name="task_emails">
                                        <label class="onoffswitch-label" for="task_emails"></label>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="col-md-6 row">
                            <div class="row">
                                <div class="col-md-6 mtop10 border-right">
                                    <span>Contract</span>
                                </div>
                                <div class="col-md-6 mtop10">
                                    <div class="onoffswitch">
                                        <input type="checkbox" id="contract_emails" data-perm-id="3" class="onoffswitch-checkbox" value="contract_emails" name="contract_emails">
                                        <label class="onoffswitch-label" for="contract_emails"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>               
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary" data-loading-text="Please wait..." autocomplete="off" data-form="#contact-form">Save</button>
    </div>
</form>
<script>

$('body').find('select.selectpicker').not('.ajax-search').selectpicker({
    showSubtext: true,
}); 

$(function() {
    validate_contact_form2();    
});

function validate_contact_form2() {
    appValidateForm('#contact-form', {
        firstname: 'required',
        lastname: 'required',
        password: {
            required: {
                depends: function(element) {

                    var $sentSetPassword = $('input[name="send_set_password_email"]');

                    if ($('#contact input[name="contactid"]').val() == '' && $sentSetPassword.prop(
                            'checked') == false) {
                        return true;
                    }
                }
            }
        },
        email: {
            required: true,
            email: true,
            // Use this hook only if the contacts are not logging into the customers area and you are not using support tickets piping.
            remote: {
                url: admin_url + "sales_marketing/clients/contact_email_exists",
                type: 'post',
                data: {
                    email: function() {
                        return $('#contact-form input[name="email"]').val();
                    },
                    userid: function() {
                        return $('body').find('input[name="contactid"]').val();
                    }
                }
            }
        }
    }, contactFormHandler2);
}
function contactFormHandler2(form) {
    $('#contact input[name="is_primary"]').prop('disabled', false);

    $("#contact input[type=file]").each(function() {
        if ($(this).val() === "") {
            $(this).prop('disabled', true);
        }
    });

    var formURL = $(form).attr("action");
    var formData = new FormData($(form)[0]);

    $.ajax({
        type: 'POST',
        data: formData,
        mimeType: "multipart/form-data",
        contentType: false,
        cache: false,
        processData: false,
        url: formURL
    }).done(function(response) {
        response = JSON.parse(response);
        if (response.success) {
            alert_float('success', response.message);
            contact_id = response.contact_id; 
            $('#contact_id').append(new Option(response.contact_name, contact_id));
            $('#contact_id option[value="'+contact_id+'"]').attr('selected','selected');
            $('#contact_id').selectpicker('refresh'); 
            $("#myModal").modal('hide'); 
        }

    }).fail(function(error) {
        $("#myModal").modal('hide');
        alert_float('danger', JSON.parse(error.responseText));
    });
    
    return false;
}       
        
</script>