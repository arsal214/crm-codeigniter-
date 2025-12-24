<div class="panel-custom">
    <div class="panel-heading">
        <button type="button" class="close" data-dismiss="modal" fdprocessedid="g4g18">
            <span aria-hidden="true">×</span><span class="sr-only">Close</span>
        </button>
        <h3 class="panel-title">Customer Details</h3>
    </div>
    <div class="panel-body">

        <div class="row">
            <div class="col-md-12">
                <div class="form-group" app-field-wrapper="company">
                    <div class="form-group" app-field-wrapper="company">
                        <label for="company" class="control-label"> 
                            <small class="req text-danger">* </small>
                            Company
                        </label>
                        <input type="text" id="company" name="company" class="form-control" autofocus="1" value="" fdprocessedid="d5b2qi">
                        
                    </div>                                                                                                              
                    <div class="form-group" app-field-wrapper="vat">
                        <label for="vat" class="control-label">VAT Number</label>
                        <input type="text" id="vat" name="vat" class="form-control" value="" fdprocessedid="1csdds">
                    </div>                                                                                                
                    <div class="form-group" app-field-wrapper="phonenumber">
                        <label for="phonenumber" class="control-label">Phone</label>
                        <input type="text" id="phonenumber" name="phonenumber" class="form-control" value="" fdprocessedid="yvezl6">
                    </div>                                                                
                    <div class="form-group" app-field-wrapper="website">
                        <label for="website" class="control-label">Website</label>
                        <input type="text" id="website" name="website" class="form-control" value="" fdprocessedid="sqntke">
                    </div>
                    <div class="select-placeholder form-group form-group-select-input-groups_in[] input-group-select">
                        <label for="groups_in[]" class="control-label">Groups</label>
                        <div class="input-group input-group-select select-groups_in[]" app-field-wrapper="groups_in[]">
                            <select id="groups_in" name="groups_in" class="selectpicker  _select_input_group" multiple="1" data-actions-box="1" data-width="100%" data-none-selected-text="Nothing selected" data-live-search="true">
                            <?php
                            
                            if($groups){
                                foreach($groups as $g){
                                    echo "<option value='".$g['id']."'>".$g['name']."</option>";    
                                }
                            }
                            ?>
                            </select>
                            <div class="input-group-btn">
                                <a href="#" class="btn btn-default" data-toggle="modal" data-target="#customer_group_modal">
                                    <i class="fa fa-plus"></i>
                                </a>
                            </div>
                        </div>
                    </div>                                
                <div class="row">
                    <div class="col-md-6">
                        <i class="fa-regular fa-circle-question pull-left tw-mt-0.5 tw-mr-1" data-toggle="tooltip" data-title="If the customer use other currency then the base currency make sure you select the appropriate currency for this customer. Changing the currency is not possible after transactions are recorded.">
                        </i>
                    
                        <div class="select-placeholder form-group" app-field-wrapper="default_currency">
                            <label for="default_currency" class="control-label">Currency</label>
                            <select id="default_currency" name="default_currency" class="selectpicker" data-none-selected-text="System Default" data-width="100%" data-live-search="true">
                                <option value=""></option>
                                <?php
                                if($currencies){
                                    foreach($currencies as $c){
                                        echo "<option value='".$c['id']."' data-subtext='".$c['symbol']."'>".$c['name']."</option>";
                                    }
                                }
                                ?>                                               
                            </select>
                        </div>                                    
                    </div>
                    <div class="col-md-6">
                        <div class="select-placeholder form-group" app-field-wrapper="default_language">
                            <label for="default_language" class="control-label">Default Language</label>
                            <select id="default_language" name="default_language" class="form-control selectpicker" data-none-selected-text="System Default" data-width="100%" data-live-search="true">
                                <option value=""></option>
                                <?php
                                if(1){
                                    foreach ($this->app->get_available_languages() as $availableLanguage){
                                        echo "<option value='".e($availableLanguage)."'>".e(ucfirst($availableLanguage))."</option>";
                                    }
                                }
                                ?>                                               
                            </select>
                        </div>                                    
                    </div>
                </div>
                <hr>

                <div class="form-group" app-field-wrapper="address">
                    <label for="address" class="control-label">Address</label>
                    <textarea id="address" name="address" class="form-control" rows="4"></textarea>
                </div>                                                                
                <div class="form-group" app-field-wrapper="city">
                    <label for="city" class="control-label">City</label>
                    <input type="text" id="city" name="city" class="form-control" value="" fdprocessedid="87v89c">
                </div>                                                                
                <div class="form-group" app-field-wrapper="state">
                    <label for="state" class="control-label">State</label>
                    <input type="text" id="state" name="state" class="form-control" value="" fdprocessedid="zwozta">
                </div>                                                                
                <div class="form-group" app-field-wrapper="zip">
                    <label for="zip" class="control-label">Zip Code</label>
                    <input type="text" id="zip" name="zip" class="form-control" value="" fdprocessedid="kuki2e">
                </div>
                <div class="form-group" app-field-wrapper="country">
                    <?php
                    $countries       = get_all_countries();
                    $customer_default_country = get_option('customer_default_country'); 
                    ?>
                    <label for="zip" class="control-label">Country</label>
                    <select id="country" name="country" class="selectpicker" data-none-selected-text="Nothing selected" data-width="100%" data-live-search="true" tabindex="-98">
                        <option value=""></option>
                        <?php
                        if($countries){
                            foreach($countries as $v){
                                echo "<option value='".$v['country_id']."'>".$v['short_name']."</option>";
                            }
                        }
                        ?>
                    </select>
                </div>                                                            
            </div>
        </div>
        </div>
        <div class="panel-footer text-right tw-space-x-1" id="profile-save-section">     
            <button type="button" class="btn btn-primary" onclick="AddNewClient()">
                Save                        
            </button>
        </div>
        
    </div>
</div>
<?php //init_tail(); ?>
<script>

$('body').find('select.selectpicker').not('.ajax-search').selectpicker({
    showSubtext: true,
});

function AddNewClient(){
    
    data = {};
    data.company = $('#company').val();
    data.vat = $('#vat').val();
    data.phonenumber = $('#phonenumber').val();
    data.country = $('#country').val();
    data.city = $('#city').val();
    data.zip = $('#zip').val();
    data.state = $('#state').val();
    data.address = $('#address').val();
    data.website = $('#website').val();
    data.default_currency = $('#default_currency').val();
    data.default_language = $('#default_language').val();
    data.groups_in = $('#groups_in').val();
    //console.log($('#groups_in').val());
    $.post('<?=admin_url(SAM_MODULE.'/clients/save_client')?>',data).done(function (response){
        response = JSON.parse(response); 
        if(response.success){
            userid = response.userid;
            $('#rel_id2').append(new Option(response.company, userid));
            $('#rel_id2 option[value="'+userid+'"]').attr('selected','selected');
            $('#rel_id2').selectpicker('refresh'); 
            
            getClientContacts();
            //$('#contact_wrapper').hide();  
            //$('#contact_id').html('');                        
        }
        else{                                       
        }
        alert_float(response.type,response.message);
        $("#myModal").modal('hide');
            
    });
}
        
</script>