<div class="modal-header" style="padding:0.7em">
    <button type="button" class="close" data-dismiss="modal" data-rel-id="1" data-rel-type="sam" aria-label="Close" value="" fdprocessedid="qy876g">
        <span aria-hidden="true">×</span>
    </button>
    <h4 class="modal-title" id="myModalLabel">
        Create New Invoice                   
    </h4>
</div>
<div class="panel_s">
    <div class="panel-body" style="padding:0.3em">
        <div class="row">
            <form action="http://localhost/perfex_crm/admin/sales_marketing/invoices/invoice/0/25" id="invoice-form" class="_transaction_form invoice-form" method="post" accept-charset="utf-8" novalidate="novalidate">                                                                                                 <input type="hidden" name="csrf_token_name" value="29ae38ab42ac10b8e4bd0cacda034bf9">
                <div class="col-md-12">
                    <div class="panel_s invoice accounting-template">
                        <div class="additional"></div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="f_client_id">
                                        <div class="form-group">
                                            <label for="clientid" class="control-label"> <small class="req text-danger">* </small>Customer</label>
                                            <div class="dropdown bootstrap-select bs3" style="width: 100%;"><select id="clientid" name="clientid" data-live-search="true" data-width="100%" class="selectpicker" data-none-selected-text="Nothing selected" tabindex="-98">
                                                <option value="2">Roshan Telecom</option>
                                            </select><button type="button" class="btn dropdown-toggle btn-default" data-toggle="dropdown" role="combobox" aria-owns="bs-select-1" aria-haspopup="listbox" aria-expanded="false" data-id="clientid" title="Roshan Telecom" fdprocessedid="er1h7"><div class="filter-option"><div class="filter-option-inner"><div class="filter-option-inner-inner">Roshan Telecom</div></div> </div><span class="bs-caret"><span class="caret"></span></span></button><div class="dropdown-menu open"><div class="bs-searchbox"><input type="search" class="form-control" autocomplete="off" role="combobox" aria-label="Search" aria-controls="bs-select-1" aria-autocomplete="list"></div><div class="inner open" role="listbox" id="bs-select-1" tabindex="-1"><ul class="dropdown-menu inner " role="presentation"></ul></div></div></div>
                                        </div>
                                    </div>
                                                    <div class="form-group projects-wrapper hide">
                                        <label for="project_id">Project</label>
                                        <div id="project_ajax_search_wrapper">
                                            <div class="dropdown bootstrap-select projects ajax-search bs3" style="width: 100%;"><select name="project_id" id="project_id" class="projects ajax-search" data-live-search="true" data-width="100%" data-none-selected-text="Nothing selected" tabindex="-98" title="Select and begin typing"><option class="bs-title-option" value=""></option>
                                                                        </select><button type="button" class="btn dropdown-toggle bs-placeholder btn-default" data-toggle="dropdown" role="combobox" aria-owns="bs-select-16" aria-haspopup="listbox" aria-expanded="false" data-id="project_id" title="Select and begin typing"><div class="filter-option"><div class="filter-option-inner"><div class="filter-option-inner-inner">Select and begin typing</div></div> </div><span class="bs-caret"><span class="caret"></span></span></button><div class="dropdown-menu open" style="min-height: 55px; max-height: 379.8px; overflow: hidden;"><div class="bs-searchbox"><input type="search" class="form-control" autocomplete="off" role="combobox" aria-label="Search" aria-controls="bs-select-16" aria-autocomplete="list" placeholder="Type to search..."></div><div class="inner open" role="listbox" id="bs-select-16" tabindex="-1" style="min-height: 0px; max-height: 314.8px; overflow-y: auto;"><ul class="dropdown-menu inner " role="presentation"></ul></div><div class="status" style="">Start typing to search</div></div></div>
                                        </div>
                                    </div>
                                                    <div class="row">
                                        <div class="col-md-12">
                                            <hr class="hr-10">
                                            <a href="#" class="edit_shipping_billing_info" data-toggle="modal" data-target="#billing_and_shipping_details"><i class="fa-regular fa-pen-to-square"></i></a>
                                            <div class="modal fade" id="billing_and_shipping_details" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <div class="row">
                                                            <div class="col-md-12">
                                            <div id="billing_details">
                                                                            <div class="form-group" app-field-wrapper="billing_street"><label for="billing_street" class="control-label">Street</label><textarea id="billing_street" name="billing_street" class="form-control" rows="4"></textarea></div>                                                        <div class="form-group" app-field-wrapper="billing_city"><label for="billing_city" class="control-label">City</label><input type="text" id="billing_city" name="billing_city" class="form-control" value=""></div>                                                        <div class="form-group" app-field-wrapper="billing_state"><label for="billing_state" class="control-label">State</label><input type="text" id="billing_state" name="billing_state" class="form-control" value=""></div>                                                        <div class="form-group" app-field-wrapper="billing_zip"><label for="billing_zip" class="control-label">Zip Code</label><input type="text" id="billing_zip" name="billing_zip" class="form-control" value=""></div>                                                        <div class="form-group" app-field-wrapper="billing_country"><label for="billing_country" class="control-label">Country</label><div class="dropdown bootstrap-select bs3" style="width: 100%;"><select id="billing_country" name="billing_country" class="selectpicker" data-width="100%" data-none-selected-text="Nothing selected" data-live-search="true" tabindex="-98"><option value=""></option><option value="1" data-subtext="AF">Afghanistan</option><option value="2" data-subtext="AX">Aland Islands</option><option value="3" data-subtext="AL">Albania</option><option value="4" data-subtext="DZ">Algeria</option><option value="5" data-subtext="AS">American Samoa</option><option value="6" data-subtext="AD">Andorra</option><option value="7" data-subtext="AO">Angola</option><option value="8" data-subtext="AI">Anguilla</option><option value="9" data-subtext="AQ">Antarctica</option><option value="10" data-subtext="AG">Antigua and Barbuda</option><option value="11" data-subtext="AR">Argentina</option><option value="12" data-subtext="AM">Armenia</option><option value="13" data-subtext="AW">Aruba</option><option value="14" data-subtext="AU">Australia</option><option value="15" data-subtext="AT">Austria</option><option value="16" data-subtext="AZ">Azerbaijan</option><option value="17" data-subtext="BS">Bahamas</option><option value="18" data-subtext="BH">Bahrain</option><option value="19" data-subtext="BD">Bangladesh</option><option value="20" data-subtext="BB">Barbados</option><option value="21" data-subtext="BY">Belarus</option><option value="22" data-subtext="BE">Belgium</option><option value="23" data-subtext="BZ">Belize</option><option value="24" data-subtext="BJ">Benin</option><option value="25" data-subtext="BM">Bermuda</option><option value="26" data-subtext="BT">Bhutan</option><option value="27" data-subtext="BO">Bolivia</option><option value="28" data-subtext="BQ">Bonaire, Sint Eustatius and Saba</option><option value="29" data-subtext="BA">Bosnia and Herzegovina</option><option value="30" data-subtext="BW">Botswana</option><option value="31" data-subtext="BV">Bouvet Island</option><option value="32" data-subtext="BR">Brazil</option><option value="33" data-subtext="IO">British Indian Ocean Territory</option><option value="34" data-subtext="BN">Brunei</option><option value="35" data-subtext="BG">Bulgaria</option><option value="36" data-subtext="BF">Burkina Faso</option><option value="37" data-subtext="BI">Burundi</option><option value="38" data-subtext="KH">Cambodia</option><option value="39" data-subtext="CM">Cameroon</option><option value="40" data-subtext="CA">Canada</option><option value="41" data-subtext="CV">Cape Verde</option><option value="42" data-subtext="KY">Cayman Islands</option><option value="43" data-subtext="CF">Central African Republic</option><option value="44" data-subtext="TD">Chad</option><option value="45" data-subtext="CL">Chile</option><option value="46" data-subtext="CN">China</option><option value="47" data-subtext="CX">Christmas Island</option><option value="48" data-subtext="CC">Cocos (Keeling) Islands</option><option value="49" data-subtext="CO">Colombia</option><option value="50" data-subtext="KM">Comoros</option><option value="51" data-subtext="CG">Congo</option><option value="52" data-subtext="CK">Cook Islands</option><option value="53" data-subtext="CR">Costa Rica</option><option value="54" data-subtext="CI">Cote d'ivoire (Ivory Coast)</option><option value="55" data-subtext="HR">Croatia</option><option value="56" data-subtext="CU">Cuba</option><option value="57" data-subtext="CW">Curacao</option><option value="58" data-subtext="CY">Cyprus</option><option value="59" data-subtext="CZ">Czech Republic</option><option value="60" data-subtext="CD">Democratic Republic of the Congo</option><option value="61" data-subtext="DK">Denmark</option><option value="62" data-subtext="DJ">Djibouti</option><option value="63" data-subtext="DM">Dominica</option><option value="64" data-subtext="DO">Dominican Republic</option><option value="65" data-subtext="EC">Ecuador</option><option value="66" data-subtext="EG">Egypt</option><option value="67" data-subtext="SV">El Salvador</option><option value="68" data-subtext="GQ">Equatorial Guinea</option><option value="69" data-subtext="ER">Eritrea</option><option value="70" data-subtext="EE">Estonia</option><option value="71" data-subtext="ET">Ethiopia</option><option value="72" data-subtext="FK">Falkland Islands (Malvinas)</option><option value="73" data-subtext="FO">Faroe Islands</option><option value="74" data-subtext="FJ">Fiji</option><option value="75" data-subtext="FI">Finland</option><option value="76" data-subtext="FR">France</option><option value="77" data-subtext="GF">French Guiana</option><option value="78" data-subtext="PF">French Polynesia</option><option value="79" data-subtext="TF">French Southern Territories</option><option value="80" data-subtext="GA">Gabon</option><option value="81" data-subtext="GM">Gambia</option><option value="82" data-subtext="GE">Georgia</option><option value="83" data-subtext="DE">Germany</option><option value="84" data-subtext="GH">Ghana</option><option value="85" data-subtext="GI">Gibraltar</option><option value="86" data-subtext="GR">Greece</option><option value="87" data-subtext="GL">Greenland</option><option value="88" data-subtext="GD">Grenada</option><option value="89" data-subtext="GP">Guadaloupe</option><option value="90" data-subtext="GU">Guam</option><option value="91" data-subtext="GT">Guatemala</option><option value="92" data-subtext="GG">Guernsey</option><option value="93" data-subtext="GN">Guinea</option><option value="94" data-subtext="GW">Guinea-Bissau</option><option value="95" data-subtext="GY">Guyana</option><option value="96" data-subtext="HT">Haiti</option><option value="97" data-subtext="HM">Heard Island and McDonald Islands</option><option value="98" data-subtext="HN">Honduras</option><option value="99" data-subtext="HK">Hong Kong</option><option value="100" data-subtext="HU">Hungary</option><option value="101" data-subtext="IS">Iceland</option><option value="102" data-subtext="IN">India</option><option value="103" data-subtext="ID">Indonesia</option><option value="104" data-subtext="IR">Iran</option><option value="105" data-subtext="IQ">Iraq</option><option value="106" data-subtext="IE">Ireland</option><option value="107" data-subtext="IM">Isle of Man</option><option value="108" data-subtext="IL">Israel</option><option value="109" data-subtext="IT">Italy</option><option value="110" data-subtext="JM">Jamaica</option><option value="111" data-subtext="JP">Japan</option><option value="112" data-subtext="JE">Jersey</option><option value="113" data-subtext="JO">Jordan</option><option value="114" data-subtext="KZ">Kazakhstan</option><option value="115" data-subtext="KE">Kenya</option><option value="116" data-subtext="KI">Kiribati</option><option value="117" data-subtext="XK">Kosovo</option><option value="118" data-subtext="KW">Kuwait</option><option value="119" data-subtext="KG">Kyrgyzstan</option><option value="120" data-subtext="LA">Laos</option><option value="121" data-subtext="LV">Latvia</option><option value="122" data-subtext="LB">Lebanon</option><option value="123" data-subtext="LS">Lesotho</option><option value="124" data-subtext="LR">Liberia</option><option value="125" data-subtext="LY">Libya</option><option value="126" data-subtext="LI">Liechtenstein</option><option value="127" data-subtext="LT">Lithuania</option><option value="128" data-subtext="LU">Luxembourg</option><option value="129" data-subtext="MO">Macao</option><option value="131" data-subtext="MG">Madagascar</option><option value="132" data-subtext="MW">Malawi</option><option value="133" data-subtext="MY">Malaysia</option><option value="134" data-subtext="MV">Maldives</option><option value="135" data-subtext="ML">Mali</option><option value="136" data-subtext="MT">Malta</option><option value="137" data-subtext="MH">Marshall Islands</option><option value="138" data-subtext="MQ">Martinique</option><option value="139" data-subtext="MR">Mauritania</option><option value="140" data-subtext="MU">Mauritius</option><option value="141" data-subtext="YT">Mayotte</option><option value="142" data-subtext="MX">Mexico</option><option value="143" data-subtext="FM">Micronesia</option><option value="144" data-subtext="MD">Moldava</option><option value="145" data-subtext="MC">Monaco</option><option value="146" data-subtext="MN">Mongolia</option><option value="147" data-subtext="ME">Montenegro</option><option value="148" data-subtext="MS">Montserrat</option><option value="149" data-subtext="MA">Morocco</option><option value="150" data-subtext="MZ">Mozambique</option><option value="151" data-subtext="MM">Myanmar (Burma)</option><option value="152" data-subtext="NA">Namibia</option><option value="153" data-subtext="NR">Nauru</option><option value="154" data-subtext="NP">Nepal</option><option value="155" data-subtext="NL">Netherlands</option><option value="156" data-subtext="NC">New Caledonia</option><option value="157" data-subtext="NZ">New Zealand</option><option value="158" data-subtext="NI">Nicaragua</option><option value="159" data-subtext="NE">Niger</option><option value="160" data-subtext="NG">Nigeria</option><option value="161" data-subtext="NU">Niue</option><option value="162" data-subtext="NF">Norfolk Island</option><option value="163" data-subtext="KP">North Korea</option><option value="130" data-subtext="MK">North Macedonia</option><option value="164" data-subtext="MP">Northern Mariana Islands</option><option value="165" data-subtext="NO">Norway</option><option value="166" data-subtext="OM">Oman</option><option value="167" data-subtext="PK">Pakistan</option><option value="168" data-subtext="PW">Palau</option><option value="169" data-subtext="PS">Palestine</option><option value="170" data-subtext="PA">Panama</option><option value="171" data-subtext="PG">Papua New Guinea</option><option value="172" data-subtext="PY">Paraguay</option><option value="173" data-subtext="PE">Peru</option><option value="174" data-subtext="PH">Philippines</option><option value="175" data-subtext="PN">Pitcairn</option><option value="176" data-subtext="PL">Poland</option><option value="177" data-subtext="PT">Portugal</option><option value="178" data-subtext="PR">Puerto Rico</option><option value="179" data-subtext="QA">Qatar</option><option value="180" data-subtext="RE">Reunion</option><option value="181" data-subtext="RO">Romania</option><option value="182" data-subtext="RU">Russia</option><option value="183" data-subtext="RW">Rwanda</option><option value="184" data-subtext="BL">Saint Barthelemy</option><option value="185" data-subtext="SH">Saint Helena</option><option value="186" data-subtext="KN">Saint Kitts and Nevis</option><option value="187" data-subtext="LC">Saint Lucia</option><option value="188" data-subtext="MF">Saint Martin</option><option value="189" data-subtext="PM">Saint Pierre and Miquelon</option><option value="190" data-subtext="VC">Saint Vincent and the Grenadines</option><option value="191" data-subtext="WS">Samoa</option><option value="192" data-subtext="SM">San Marino</option><option value="193" data-subtext="ST">Sao Tome and Principe</option><option value="194" data-subtext="SA">Saudi Arabia</option><option value="195" data-subtext="SN">Senegal</option><option value="196" data-subtext="RS">Serbia</option><option value="197" data-subtext="SC">Seychelles</option><option value="198" data-subtext="SL">Sierra Leone</option><option value="199" data-subtext="SG">Singapore</option><option value="200" data-subtext="SX">Sint Maarten</option><option value="201" data-subtext="SK">Slovakia</option><option value="202" data-subtext="SI">Slovenia</option><option value="203" data-subtext="SB">Solomon Islands</option><option value="204" data-subtext="SO">Somalia</option><option value="205" data-subtext="ZA">South Africa</option><option value="206" data-subtext="GS">South Georgia and the South Sandwich Islands</option><option value="207" data-subtext="KR">South Korea</option><option value="208" data-subtext="SS">South Sudan</option><option value="209" data-subtext="ES">Spain</option><option value="210" data-subtext="LK">Sri Lanka</option><option value="211" data-subtext="SD">Sudan</option><option value="212" data-subtext="SR">Suriname</option><option value="213" data-subtext="SJ">Svalbard and Jan Mayen</option><option value="214" data-subtext="SZ">Swaziland</option><option value="215" data-subtext="SE">Sweden</option><option value="216" data-subtext="CH">Switzerland</option><option value="217" data-subtext="SY">Syria</option><option value="218" data-subtext="TW">Taiwan</option><option value="219" data-subtext="TJ">Tajikistan</option><option value="220" data-subtext="TZ">Tanzania</option><option value="221" data-subtext="TH">Thailand</option><option value="222" data-subtext="TL">Timor-Leste (East Timor)</option><option value="223" data-subtext="TG">Togo</option><option value="224" data-subtext="TK">Tokelau</option><option value="225" data-subtext="TO">Tonga</option><option value="226" data-subtext="TT">Trinidad and Tobago</option><option value="227" data-subtext="TN">Tunisia</option><option value="228" data-subtext="TR">Turkey</option><option value="229" data-subtext="TM">Turkmenistan</option><option value="230" data-subtext="TC">Turks and Caicos Islands</option><option value="231" data-subtext="TV">Tuvalu</option><option value="232" data-subtext="UG">Uganda</option><option value="233" data-subtext="UA">Ukraine</option><option value="234" data-subtext="AE">United Arab Emirates</option><option value="235" data-subtext="GB">United Kingdom</option><option value="236" data-subtext="US">United States</option><option value="237" data-subtext="UM">United States Minor Outlying Islands</option><option value="238" data-subtext="UY">Uruguay</option><option value="239" data-subtext="UZ">Uzbekistan</option><option value="240" data-subtext="VU">Vanuatu</option><option value="241" data-subtext="VA">Vatican City</option><option value="242" data-subtext="VE">Venezuela</option><option value="243" data-subtext="VN">Vietnam</option><option value="244" data-subtext="VG">Virgin Islands, British</option><option value="245" data-subtext="VI">Virgin Islands, US</option><option value="246" data-subtext="WF">Wallis and Futuna</option><option value="247" data-subtext="EH">Western Sahara</option><option value="248" data-subtext="YE">Yemen</option><option value="249" data-subtext="ZM">Zambia</option><option value="250" data-subtext="ZW">Zimbabwe</option></select><button type="button" class="btn dropdown-toggle btn-default bs-placeholder" data-toggle="dropdown" role="combobox" aria-owns="bs-select-2" aria-haspopup="listbox" aria-expanded="false" data-id="billing_country" title="Nothing selected"><div class="filter-option"><div class="filter-option-inner"><div class="filter-option-inner-inner">Nothing selected</div></div> </div><span class="bs-caret"><span class="caret"></span></span></button><div class="dropdown-menu open"><div class="bs-searchbox"><input type="search" class="form-control" autocomplete="off" role="combobox" aria-label="Search" aria-controls="bs-select-2" aria-autocomplete="list"></div><div class="inner open" role="listbox" id="bs-select-2" tabindex="-1"><ul class="dropdown-menu inner " role="presentation"></ul></div></div></div></div>                        </div>
                                        </div>
                                        <div class="col-md-12">
                                            <hr>
                                            <a href="#" class="pull-right" id="get_shipping_from_customer_profile" data-placement="left" data-toggle="tooltip" title="Get shipping details from customer profile"><i class="fa fa-user"></i></a>
                                            <div class="clearfix"></div>
                                          <div class="form-group no-mbot">
                                                <div class="checkbox checkbox-primary checkbox-inline">
                                                <input type="checkbox" id="include_shipping" name="include_shipping">
                                                <label for="include_shipping">Shipping Address</label>
                                            </div>
                                          </div>
                                            <div id="shipping_details" class="hide">
                                              <div class="form-group">
                                                    <div class="checkbox checkbox-primary checkbox-inline">
                                                    <input type="checkbox" id="show_shipping_on_invoice" name="show_shipping_on_invoice" checked="">
                                                    <label for="show_shipping_on_invoice">Show shipping details in invoice</label>
                                                </div>
                                              </div>
                                                                            <div class="form-group" app-field-wrapper="shipping_street"><label for="shipping_street" class="control-label">Street</label><textarea id="shipping_street" name="shipping_street" class="form-control" rows="4"></textarea></div>                                                        <div class="form-group" app-field-wrapper="shipping_city"><label for="shipping_city" class="control-label">City</label><input type="text" id="shipping_city" name="shipping_city" class="form-control" value=""></div>                                                        <div class="form-group" app-field-wrapper="shipping_state"><label for="shipping_state" class="control-label">State</label><input type="text" id="shipping_state" name="shipping_state" class="form-control" value=""></div>                                                        <div class="form-group" app-field-wrapper="shipping_zip"><label for="shipping_zip" class="control-label">Zip Code</label><input type="text" id="shipping_zip" name="shipping_zip" class="form-control" value=""></div>                                                        <div class="form-group" app-field-wrapper="shipping_country"><label for="shipping_country" class="control-label">Country</label><div class="dropdown bootstrap-select bs3" style="width: 100%;"><select id="shipping_country" name="shipping_country" class="selectpicker" data-width="100%" data-none-selected-text="Nothing selected" data-live-search="true" tabindex="-98"><option value=""></option><option value="1" data-subtext="AF">Afghanistan</option><option value="2" data-subtext="AX">Aland Islands</option><option value="3" data-subtext="AL">Albania</option><option value="4" data-subtext="DZ">Algeria</option><option value="5" data-subtext="AS">American Samoa</option><option value="6" data-subtext="AD">Andorra</option><option value="7" data-subtext="AO">Angola</option><option value="8" data-subtext="AI">Anguilla</option><option value="9" data-subtext="AQ">Antarctica</option><option value="10" data-subtext="AG">Antigua and Barbuda</option><option value="11" data-subtext="AR">Argentina</option><option value="12" data-subtext="AM">Armenia</option><option value="13" data-subtext="AW">Aruba</option><option value="14" data-subtext="AU">Australia</option><option value="15" data-subtext="AT">Austria</option><option value="16" data-subtext="AZ">Azerbaijan</option><option value="17" data-subtext="BS">Bahamas</option><option value="18" data-subtext="BH">Bahrain</option><option value="19" data-subtext="BD">Bangladesh</option><option value="20" data-subtext="BB">Barbados</option><option value="21" data-subtext="BY">Belarus</option><option value="22" data-subtext="BE">Belgium</option><option value="23" data-subtext="BZ">Belize</option><option value="24" data-subtext="BJ">Benin</option><option value="25" data-subtext="BM">Bermuda</option><option value="26" data-subtext="BT">Bhutan</option><option value="27" data-subtext="BO">Bolivia</option><option value="28" data-subtext="BQ">Bonaire, Sint Eustatius and Saba</option><option value="29" data-subtext="BA">Bosnia and Herzegovina</option><option value="30" data-subtext="BW">Botswana</option><option value="31" data-subtext="BV">Bouvet Island</option><option value="32" data-subtext="BR">Brazil</option><option value="33" data-subtext="IO">British Indian Ocean Territory</option><option value="34" data-subtext="BN">Brunei</option><option value="35" data-subtext="BG">Bulgaria</option><option value="36" data-subtext="BF">Burkina Faso</option><option value="37" data-subtext="BI">Burundi</option><option value="38" data-subtext="KH">Cambodia</option><option value="39" data-subtext="CM">Cameroon</option><option value="40" data-subtext="CA">Canada</option><option value="41" data-subtext="CV">Cape Verde</option><option value="42" data-subtext="KY">Cayman Islands</option><option value="43" data-subtext="CF">Central African Republic</option><option value="44" data-subtext="TD">Chad</option><option value="45" data-subtext="CL">Chile</option><option value="46" data-subtext="CN">China</option><option value="47" data-subtext="CX">Christmas Island</option><option value="48" data-subtext="CC">Cocos (Keeling) Islands</option><option value="49" data-subtext="CO">Colombia</option><option value="50" data-subtext="KM">Comoros</option><option value="51" data-subtext="CG">Congo</option><option value="52" data-subtext="CK">Cook Islands</option><option value="53" data-subtext="CR">Costa Rica</option><option value="54" data-subtext="CI">Cote d'ivoire (Ivory Coast)</option><option value="55" data-subtext="HR">Croatia</option><option value="56" data-subtext="CU">Cuba</option><option value="57" data-subtext="CW">Curacao</option><option value="58" data-subtext="CY">Cyprus</option><option value="59" data-subtext="CZ">Czech Republic</option><option value="60" data-subtext="CD">Democratic Republic of the Congo</option><option value="61" data-subtext="DK">Denmark</option><option value="62" data-subtext="DJ">Djibouti</option><option value="63" data-subtext="DM">Dominica</option><option value="64" data-subtext="DO">Dominican Republic</option><option value="65" data-subtext="EC">Ecuador</option><option value="66" data-subtext="EG">Egypt</option><option value="67" data-subtext="SV">El Salvador</option><option value="68" data-subtext="GQ">Equatorial Guinea</option><option value="69" data-subtext="ER">Eritrea</option><option value="70" data-subtext="EE">Estonia</option><option value="71" data-subtext="ET">Ethiopia</option><option value="72" data-subtext="FK">Falkland Islands (Malvinas)</option><option value="73" data-subtext="FO">Faroe Islands</option><option value="74" data-subtext="FJ">Fiji</option><option value="75" data-subtext="FI">Finland</option><option value="76" data-subtext="FR">France</option><option value="77" data-subtext="GF">French Guiana</option><option value="78" data-subtext="PF">French Polynesia</option><option value="79" data-subtext="TF">French Southern Territories</option><option value="80" data-subtext="GA">Gabon</option><option value="81" data-subtext="GM">Gambia</option><option value="82" data-subtext="GE">Georgia</option><option value="83" data-subtext="DE">Germany</option><option value="84" data-subtext="GH">Ghana</option><option value="85" data-subtext="GI">Gibraltar</option><option value="86" data-subtext="GR">Greece</option><option value="87" data-subtext="GL">Greenland</option><option value="88" data-subtext="GD">Grenada</option><option value="89" data-subtext="GP">Guadaloupe</option><option value="90" data-subtext="GU">Guam</option><option value="91" data-subtext="GT">Guatemala</option><option value="92" data-subtext="GG">Guernsey</option><option value="93" data-subtext="GN">Guinea</option><option value="94" data-subtext="GW">Guinea-Bissau</option><option value="95" data-subtext="GY">Guyana</option><option value="96" data-subtext="HT">Haiti</option><option value="97" data-subtext="HM">Heard Island and McDonald Islands</option><option value="98" data-subtext="HN">Honduras</option><option value="99" data-subtext="HK">Hong Kong</option><option value="100" data-subtext="HU">Hungary</option><option value="101" data-subtext="IS">Iceland</option><option value="102" data-subtext="IN">India</option><option value="103" data-subtext="ID">Indonesia</option><option value="104" data-subtext="IR">Iran</option><option value="105" data-subtext="IQ">Iraq</option><option value="106" data-subtext="IE">Ireland</option><option value="107" data-subtext="IM">Isle of Man</option><option value="108" data-subtext="IL">Israel</option><option value="109" data-subtext="IT">Italy</option><option value="110" data-subtext="JM">Jamaica</option><option value="111" data-subtext="JP">Japan</option><option value="112" data-subtext="JE">Jersey</option><option value="113" data-subtext="JO">Jordan</option><option value="114" data-subtext="KZ">Kazakhstan</option><option value="115" data-subtext="KE">Kenya</option><option value="116" data-subtext="KI">Kiribati</option><option value="117" data-subtext="XK">Kosovo</option><option value="118" data-subtext="KW">Kuwait</option><option value="119" data-subtext="KG">Kyrgyzstan</option><option value="120" data-subtext="LA">Laos</option><option value="121" data-subtext="LV">Latvia</option><option value="122" data-subtext="LB">Lebanon</option><option value="123" data-subtext="LS">Lesotho</option><option value="124" data-subtext="LR">Liberia</option><option value="125" data-subtext="LY">Libya</option><option value="126" data-subtext="LI">Liechtenstein</option><option value="127" data-subtext="LT">Lithuania</option><option value="128" data-subtext="LU">Luxembourg</option><option value="129" data-subtext="MO">Macao</option><option value="131" data-subtext="MG">Madagascar</option><option value="132" data-subtext="MW">Malawi</option><option value="133" data-subtext="MY">Malaysia</option><option value="134" data-subtext="MV">Maldives</option><option value="135" data-subtext="ML">Mali</option><option value="136" data-subtext="MT">Malta</option><option value="137" data-subtext="MH">Marshall Islands</option><option value="138" data-subtext="MQ">Martinique</option><option value="139" data-subtext="MR">Mauritania</option><option value="140" data-subtext="MU">Mauritius</option><option value="141" data-subtext="YT">Mayotte</option><option value="142" data-subtext="MX">Mexico</option><option value="143" data-subtext="FM">Micronesia</option><option value="144" data-subtext="MD">Moldava</option><option value="145" data-subtext="MC">Monaco</option><option value="146" data-subtext="MN">Mongolia</option><option value="147" data-subtext="ME">Montenegro</option><option value="148" data-subtext="MS">Montserrat</option><option value="149" data-subtext="MA">Morocco</option><option value="150" data-subtext="MZ">Mozambique</option><option value="151" data-subtext="MM">Myanmar (Burma)</option><option value="152" data-subtext="NA">Namibia</option><option value="153" data-subtext="NR">Nauru</option><option value="154" data-subtext="NP">Nepal</option><option value="155" data-subtext="NL">Netherlands</option><option value="156" data-subtext="NC">New Caledonia</option><option value="157" data-subtext="NZ">New Zealand</option><option value="158" data-subtext="NI">Nicaragua</option><option value="159" data-subtext="NE">Niger</option><option value="160" data-subtext="NG">Nigeria</option><option value="161" data-subtext="NU">Niue</option><option value="162" data-subtext="NF">Norfolk Island</option><option value="163" data-subtext="KP">North Korea</option><option value="130" data-subtext="MK">North Macedonia</option><option value="164" data-subtext="MP">Northern Mariana Islands</option><option value="165" data-subtext="NO">Norway</option><option value="166" data-subtext="OM">Oman</option><option value="167" data-subtext="PK">Pakistan</option><option value="168" data-subtext="PW">Palau</option><option value="169" data-subtext="PS">Palestine</option><option value="170" data-subtext="PA">Panama</option><option value="171" data-subtext="PG">Papua New Guinea</option><option value="172" data-subtext="PY">Paraguay</option><option value="173" data-subtext="PE">Peru</option><option value="174" data-subtext="PH">Philippines</option><option value="175" data-subtext="PN">Pitcairn</option><option value="176" data-subtext="PL">Poland</option><option value="177" data-subtext="PT">Portugal</option><option value="178" data-subtext="PR">Puerto Rico</option><option value="179" data-subtext="QA">Qatar</option><option value="180" data-subtext="RE">Reunion</option><option value="181" data-subtext="RO">Romania</option><option value="182" data-subtext="RU">Russia</option><option value="183" data-subtext="RW">Rwanda</option><option value="184" data-subtext="BL">Saint Barthelemy</option><option value="185" data-subtext="SH">Saint Helena</option><option value="186" data-subtext="KN">Saint Kitts and Nevis</option><option value="187" data-subtext="LC">Saint Lucia</option><option value="188" data-subtext="MF">Saint Martin</option><option value="189" data-subtext="PM">Saint Pierre and Miquelon</option><option value="190" data-subtext="VC">Saint Vincent and the Grenadines</option><option value="191" data-subtext="WS">Samoa</option><option value="192" data-subtext="SM">San Marino</option><option value="193" data-subtext="ST">Sao Tome and Principe</option><option value="194" data-subtext="SA">Saudi Arabia</option><option value="195" data-subtext="SN">Senegal</option><option value="196" data-subtext="RS">Serbia</option><option value="197" data-subtext="SC">Seychelles</option><option value="198" data-subtext="SL">Sierra Leone</option><option value="199" data-subtext="SG">Singapore</option><option value="200" data-subtext="SX">Sint Maarten</option><option value="201" data-subtext="SK">Slovakia</option><option value="202" data-subtext="SI">Slovenia</option><option value="203" data-subtext="SB">Solomon Islands</option><option value="204" data-subtext="SO">Somalia</option><option value="205" data-subtext="ZA">South Africa</option><option value="206" data-subtext="GS">South Georgia and the South Sandwich Islands</option><option value="207" data-subtext="KR">South Korea</option><option value="208" data-subtext="SS">South Sudan</option><option value="209" data-subtext="ES">Spain</option><option value="210" data-subtext="LK">Sri Lanka</option><option value="211" data-subtext="SD">Sudan</option><option value="212" data-subtext="SR">Suriname</option><option value="213" data-subtext="SJ">Svalbard and Jan Mayen</option><option value="214" data-subtext="SZ">Swaziland</option><option value="215" data-subtext="SE">Sweden</option><option value="216" data-subtext="CH">Switzerland</option><option value="217" data-subtext="SY">Syria</option><option value="218" data-subtext="TW">Taiwan</option><option value="219" data-subtext="TJ">Tajikistan</option><option value="220" data-subtext="TZ">Tanzania</option><option value="221" data-subtext="TH">Thailand</option><option value="222" data-subtext="TL">Timor-Leste (East Timor)</option><option value="223" data-subtext="TG">Togo</option><option value="224" data-subtext="TK">Tokelau</option><option value="225" data-subtext="TO">Tonga</option><option value="226" data-subtext="TT">Trinidad and Tobago</option><option value="227" data-subtext="TN">Tunisia</option><option value="228" data-subtext="TR">Turkey</option><option value="229" data-subtext="TM">Turkmenistan</option><option value="230" data-subtext="TC">Turks and Caicos Islands</option><option value="231" data-subtext="TV">Tuvalu</option><option value="232" data-subtext="UG">Uganda</option><option value="233" data-subtext="UA">Ukraine</option><option value="234" data-subtext="AE">United Arab Emirates</option><option value="235" data-subtext="GB">United Kingdom</option><option value="236" data-subtext="US">United States</option><option value="237" data-subtext="UM">United States Minor Outlying Islands</option><option value="238" data-subtext="UY">Uruguay</option><option value="239" data-subtext="UZ">Uzbekistan</option><option value="240" data-subtext="VU">Vanuatu</option><option value="241" data-subtext="VA">Vatican City</option><option value="242" data-subtext="VE">Venezuela</option><option value="243" data-subtext="VN">Vietnam</option><option value="244" data-subtext="VG">Virgin Islands, British</option><option value="245" data-subtext="VI">Virgin Islands, US</option><option value="246" data-subtext="WF">Wallis and Futuna</option><option value="247" data-subtext="EH">Western Sahara</option><option value="248" data-subtext="YE">Yemen</option><option value="249" data-subtext="ZM">Zambia</option><option value="250" data-subtext="ZW">Zimbabwe</option></select><button type="button" class="btn dropdown-toggle btn-default bs-placeholder" data-toggle="dropdown" role="combobox" aria-owns="bs-select-3" aria-haspopup="listbox" aria-expanded="false" data-id="shipping_country" title="Nothing selected"><div class="filter-option"><div class="filter-option-inner"><div class="filter-option-inner-inner">Nothing selected</div></div> </div><span class="bs-caret"><span class="caret"></span></span></button><div class="dropdown-menu open"><div class="bs-searchbox"><input type="search" class="form-control" autocomplete="off" role="combobox" aria-label="Search" aria-controls="bs-select-3" aria-autocomplete="list"></div><div class="inner open" role="listbox" id="bs-select-3" tabindex="-1"><ul class="dropdown-menu inner " role="presentation"></ul></div></div></div></div>                        </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer modal-not-full-width">
                                    <a href="#" class="btn btn-primary save-shipping-billing">Apply</a>
                                </div>
                            </div>
                        </div>
                    </div>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="bold">Bill To</p>
                                            <address>
                                                <span class="billing_street">
                                                                                                                    --</span><br>
                                                <span class="billing_city">
                                                                                                                    --</span>,
                                                <span class="billing_state">
                                                                                                                    --</span>
                                                <br>
                                                <span class="billing_country">
                                                                                                                    --</span>,
                                                <span class="billing_zip">
                                                                                                                    --</span>
                                            </address>
                                        </div>
                                        <div class="col-md-6">
                                            <p class="bold">Ship to</p>
                                            <address>
                                                <span class="shipping_street">
                                                                                                                    --</span><br>
                                                <span class="shipping_city">
                                                                                                                    --</span>,
                                                <span class="shipping_state">
                                                                                                                    --</span>
                                                <br>
                                                <span class="shipping_country">
                                                                                                                    --</span>,
                                                <span class="shipping_zip">
                                                                                                                    --</span>
                                            </address>
                                        </div>
                                    </div>
                                                    <div class="form-group">
                                        <label for="number"> <small class="req text-danger">* </small>
                                            Invoice Number                        <i class="fa-regular fa-circle-question" data-toggle="tooltip" data-title="If the invoice is saved as draft, the number won't be applied, instead, the next invoice number will be given when the invoice is sent to the customer or is marked as sent." data-placement="top"></i>
                                        </label>
                                        <div class="input-group">
                                            <span class="input-group-addon">
                                                INV-                        </span>
                                            <input type="text" name="number" class="form-control" value="000002" data-isedit="false" data-original-number="false" fdprocessedid="2jy3rs">
                                                                </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                                                    <div class="form-group" app-field-wrapper="date"><label for="date" class="control-label"> <small class="req text-danger">* </small>Invoice Date</label><div class="input-group date"><input type="text" id="date" name="date" class="form-control datepicker" value="2024-09-02" autocomplete="off" fdprocessedid="6wfqs9"><div class="input-group-addon">
                        <i class="fa-regular fa-calendar calendar-icon"></i>
                    </div></div></div>                    </div>
                                        <div class="col-md-6">
                                                                    <div class="form-group" app-field-wrapper="duedate"><label for="duedate" class="control-label">Due Date</label><div class="input-group date"><input type="text" id="duedate" name="duedate" class="form-control datepicker" value="2024-10-02" autocomplete="off" fdprocessedid="rbiq19"><div class="input-group-addon">
                        <i class="fa-regular fa-calendar calendar-icon"></i>
                    </div></div></div>                    </div>
                                    </div>
                                                    <div class="form-group">
                                        <div class="checkbox checkbox-danger">
                                            <input type="checkbox" id="cancel_overdue_reminders" name="cancel_overdue_reminders">
                                            <label for="cancel_overdue_reminders">Prevent sending overdue reminders for this invoice</label>
                                        </div>
                                    </div>
                                                                                                </div>
                                <div class="col-md-6">
                                    <div class="tw-ml-3">
                                        <div class="form-group">
                                            <label for="tags" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i>
                                                Tags</label>
                                            <input type="text" class="tagsinput" id="tags" name="tags" value="" data-role="tagsinput">
                                        </div>
                                        <div class="form-group mbot15">
                                            <label for="allowed_payment_modes" class="control-label">Allowed payment modes for this invoice</label>
                                            <br>
                                                                    <div class="dropdown bootstrap-select show-tick bs3" style="width: 100%;"><select class="selectpicker" data-toggle="" name="allowed_payment_modes[]" data-actions-box="true" multiple="true" data-width="100%" data-title="Nothing selected" tabindex="-98">
                                                                            <option value="1" selected="">
                                                    Bank</option>
                                                                        </select><button type="button" class="btn dropdown-toggle btn-default" data-toggle="dropdown" role="combobox" aria-owns="bs-select-4" aria-haspopup="listbox" aria-expanded="false" title="Bank" fdprocessedid="zp516w"><div class="filter-option"><div class="filter-option-inner"><div class="filter-option-inner-inner">Bank</div></div> </div><span class="bs-caret"><span class="caret"></span></span></button><div class="dropdown-menu open"><div class="bs-actionsbox"><div class="btn-group btn-group-sm btn-block"><button type="button" class="actions-btn bs-select-all btn btn-default">Select All</button><button type="button" class="actions-btn bs-deselect-all btn btn-default">Deselect All</button></div></div><div class="inner open" role="listbox" id="bs-select-4" tabindex="-1" aria-multiselectable="true"><ul class="dropdown-menu inner " role="presentation"></ul></div></div></div>
                                                                </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                                            <div class="form-group" app-field-wrapper="currency"><label for="currency" class="control-label"> <small class="req text-danger">* </small>Currency</label><div class="dropdown bootstrap-select disabled bs3" style="width: 100%;"><select id="currency" name="currency" class="selectpicker" disabled="1" data-show-subtext="1" data-base="1" data-width="100%" data-none-selected-text="Nothing selected" data-live-search="true" tabindex="-98"><option value=""></option><option value="1" selected="" data-subtext="$">USD</option><option value="2" data-subtext="€">EUR</option></select><button type="button" class="btn dropdown-toggle disabled btn-default" data-toggle="dropdown" role="combobox" aria-owns="bs-select-5" aria-haspopup="listbox" aria-expanded="false" data-id="currency" tabindex="-1" aria-disabled="true" title="USD $"><div class="filter-option"><div class="filter-option-inner"><div class="filter-option-inner-inner">USD<small class="text-muted"> $</small></div></div> </div><span class="bs-caret"><span class="caret"></span></span></button><div class="dropdown-menu open"><div class="bs-searchbox"><input type="search" class="form-control" autocomplete="off" role="combobox" aria-label="Search" aria-controls="bs-select-5" aria-autocomplete="list"></div><div class="inner open" role="listbox" id="bs-select-5" tabindex="-1"><ul class="dropdown-menu inner " role="presentation"></ul></div></div></div></div>                        </div>
                                            <div class="col-md-6">
                                                <div class="form-group" app-field-wrapper="sale_agent"><label for="sale_agent" class="control-label">Sale Agent</label><div class="dropdown bootstrap-select bs3" style="width: 100%;"><select id="sale_agent" name="sale_agent" class="selectpicker" data-width="100%" data-none-selected-text="Nothing selected" data-live-search="true" tabindex="-98"><option value=""></option><option value="1" selected="">Lutufullah Usmani</option><option value="3">Javid Afzalpoor</option><option value="2">Ahmad Ali</option></select><button type="button" class="btn dropdown-toggle btn-default" data-toggle="dropdown" role="combobox" aria-owns="bs-select-6" aria-haspopup="listbox" aria-expanded="false" data-id="sale_agent" title="Lutufullah Usmani" fdprocessedid="8f7e1k"><div class="filter-option"><div class="filter-option-inner"><div class="filter-option-inner-inner">Lutufullah Usmani</div></div> </div><span class="bs-caret"><span class="caret"></span></span></button><div class="dropdown-menu open"><div class="bs-searchbox"><input type="search" class="form-control" autocomplete="off" role="combobox" aria-label="Search" aria-controls="bs-select-6" aria-autocomplete="list"></div><div class="inner open" role="listbox" id="bs-select-6" tabindex="-1"><ul class="dropdown-menu inner " role="presentation"></ul></div></div></div></div>                        </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="recurring" class="control-label">
                                                        Recurring Invoice?                                </label>
                                                    <div class="dropdown bootstrap-select bs3" style="width: 100%;"><select class="selectpicker" data-width="100%" name="recurring" data-none-selected-text="Nothing selected" tabindex="-98">
                                                                                                                                <option value="0">
                                                            No</option>
                                                                                                                                <option value="1">
                                                            Every 1 month</option>
                                                                                                                                <option value="2">
                                                            Every 2 months</option>
                                                                                                                                <option value="3">
                                                            Every 3 months</option>
                                                                                                                                <option value="4">
                                                            Every 4 months</option>
                                                                                                                                <option value="5">
                                                            Every 5 months</option>
                                                                                                                                <option value="6">
                                                            Every 6 months</option>
                                                                                                                                <option value="7">
                                                            Every 7 months</option>
                                                                                                                                <option value="8">
                                                            Every 8 months</option>
                                                                                                                                <option value="9">
                                                            Every 9 months</option>
                                                                                                                                <option value="10">
                                                            Every 10 months</option>
                                                                                                                                <option value="11">
                                                            Every 11 months</option>
                                                                                                                                <option value="12">
                                                            Every 12 months</option>
                                                                                            <option value="custom">Custom</option>
                                                    </select><button type="button" class="btn dropdown-toggle btn-default" data-toggle="dropdown" role="combobox" aria-owns="bs-select-7" aria-haspopup="listbox" aria-expanded="false" title="No" fdprocessedid="so2an8"><div class="filter-option"><div class="filter-option-inner"><div class="filter-option-inner-inner">No</div></div> </div><span class="bs-caret"><span class="caret"></span></span></button><div class="dropdown-menu open"><div class="inner open" role="listbox" id="bs-select-7" tabindex="-1"><ul class="dropdown-menu inner " role="presentation"></ul></div></div></div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    <label for="discount_type" class="control-label">Discount Type</label>
                                                    <div class="dropdown bootstrap-select bs3" style="width: 100%;"><select name="discount_type" class="selectpicker" data-width="100%" data-none-selected-text="Nothing selected" tabindex="-98">
                                                        <option value="" selected="">No discount</option>
                                                        <option value="before_tax">Before Tax</option>
                                                        <option value="after_tax">After Tax</option>
                                                    </select><button type="button" class="btn dropdown-toggle btn-default" data-toggle="dropdown" role="combobox" aria-owns="bs-select-8" aria-haspopup="listbox" aria-expanded="false" title="No discount" fdprocessedid="34w4fs"><div class="filter-option"><div class="filter-option-inner"><div class="filter-option-inner-inner">No discount</div></div> </div><span class="bs-caret"><span class="caret"></span></span></button><div class="dropdown-menu open"><div class="inner open" role="listbox" id="bs-select-8" tabindex="-1"><ul class="dropdown-menu inner " role="presentation"></ul></div></div></div>
                                                </div>
                                            </div>
                                            <div class="recurring_custom hide">
                                                <div class="col-md-6">
                                                                                    <div class="form-group" app-field-wrapper="repeat_every_custom"><input type="number" id="repeat_every_custom" name="repeat_every_custom" class="form-control" min="1" value="1"></div>                            </div>
                                                <div class="col-md-6">
                                                    <div class="dropdown bootstrap-select bs3" style="width: 100%;"><select name="repeat_type_custom" id="repeat_type_custom" class="selectpicker" data-width="100%" data-none-selected-text="Nothing selected" tabindex="-98">
                                                        <option value="day">Day(s)</option>
                                                        <option value="week">Week(s)</option>
                                                        <option value="month">Month(s)</option>
                                                        <option value="year">Years(s)</option>
                                                    </select><button type="button" class="btn dropdown-toggle btn-default" data-toggle="dropdown" role="combobox" aria-owns="bs-select-9" aria-haspopup="listbox" aria-expanded="false" data-id="repeat_type_custom" title="Day(s)"><div class="filter-option"><div class="filter-option-inner"><div class="filter-option-inner-inner">Day(s)</div></div> </div><span class="bs-caret"><span class="caret"></span></span></button><div class="dropdown-menu open"><div class="inner open" role="listbox" id="bs-select-9" tabindex="-1"><ul class="dropdown-menu inner " role="presentation"></ul></div></div></div>
                                                </div>
                                            </div>
                                            <div id="cycles_wrapper" class=" hide">
                                                <div class="col-md-12">
                                                                                    <div class="form-group recurring-cycles">
                                                        <label for="cycles">Total Cycles                                                                            </label>
                                                        <div class="input-group">
                                                            <input type="number" class="form-control" disabled="" name="cycles" id="cycles" value="0">
                                                            <div class="input-group-addon">
                                                                <div class="checkbox">
                                                                    <input type="checkbox" checked="" id="unlimited_cycles">
                                                                    <label for="unlimited_cycles">Infinity</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                                            <div class="form-group" app-field-wrapper="adminnote"><label for="adminnote" class="control-label">Admin Note</label><textarea id="adminnote" name="adminnote" class="form-control" rows="4"></textarea></div>
                                    </div>
                                </div>
                            </div>
                    </div>

                    <hr class="hr-panel-separator">

                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group mbot25 items-wrapper input-group-select">
                                    <div class="input-group input-group-select">
        <div class="items-select-wrapper">
            <div class="dropdown bootstrap-select no-margin _select_input_group bs3" style="max-width: 220px; width: 100%;"><select name="item_select" class="selectpicker no-margin _select_input_group" data-width="false" id="item_select" data-none-selected-text="Add Item" data-live-search="true" tabindex="-98">
                <option value=""></option>
                                <optgroup data-group-id="0" label="">
                                        <option value="1" data-subtext="item 1 long desc...">
                        (5.00) item 1 desc</option>
                                    </optgroup>
                            </select><button type="button" class="btn dropdown-toggle btn-default bs-placeholder" data-toggle="dropdown" role="combobox" aria-owns="bs-select-10" aria-haspopup="listbox" aria-expanded="false" data-id="item_select" title="Add Item" fdprocessedid="0619oe"><div class="filter-option"><div class="filter-option-inner"><div class="filter-option-inner-inner">Add Item</div></div> </div><span class="bs-caret"><span class="caret"></span></span></button><div class="dropdown-menu open"><div class="bs-searchbox"><input type="search" class="form-control" autocomplete="off" role="combobox" aria-label="Search" aria-controls="bs-select-10" aria-autocomplete="list"></div><div class="inner open" role="listbox" id="bs-select-10" tabindex="-1"><ul class="dropdown-menu inner " role="presentation"></ul></div></div></div>
        </div>
                <div class="input-group-btn">
            <a href="#" data-toggle="modal" class="btn btn-default" data-target="#sales_item_modal">
                <i class="fa fa-plus"></i>
            </a>
        </div>
            </div>
</div>
            </div>
                        <div class="col-md-3">
                <div class="form-group input-group-select form-group-select-task_select popover-250">
                    <div class="input-group input-group-select">
                        <div class="dropdown bootstrap-select input-group-btn no-margin _select_input_group bs3 bs3-has-addon" style="width: 100%;"><select name="task_select" data-live-search="true" id="task_select" class="selectpicker no-margin _select_input_group" data-width="100%" data-none-selected-text="Bill Tasks" tabindex="-98">
                            <option value=""></option>
                                                    </select><button type="button" class="btn dropdown-toggle btn-default bs-placeholder" data-toggle="dropdown" role="combobox" aria-owns="bs-select-11" aria-haspopup="listbox" aria-expanded="false" data-id="task_select" title="Bill Tasks" fdprocessedid="fj3m1l"><div class="filter-option"><div class="filter-option-inner"><div class="filter-option-inner-inner">Bill Tasks</div></div> </div><span class="bs-caret"><span class="caret"></span></span><div class="filter-expand">Bill Tasks</div></button><div class="dropdown-menu open"><div class="bs-searchbox"><input type="search" class="form-control" autocomplete="off" role="combobox" aria-label="Search" aria-controls="bs-select-11" aria-autocomplete="list"></div><div class="inner open" role="listbox" id="bs-select-11" tabindex="-1"><ul class="dropdown-menu inner " role="presentation"></ul></div></div></div>
                        <div class="input-group-addon input-group-addon-bill-tasks-help" style="opacity: 1;">
                            <span class="pointer popover-invoker" data-container=".form-group-select-task_select" data-trigger="click" data-placement="top" data-toggle="popover" data-content="Projects tasks are not included in this list.">
                      <i class="fa-regular fa-circle-question"></i></span>                        </div>
                    </div>
                </div>
            </div>
                        <div class="col-md-5 text-right show_quantity_as_wrapper">
                <div class="mtop10">
                    <span>Show quantity as: </span>
                    <div class="radio radio-primary radio-inline">
                        <input type="radio" value="1" id="sq_1" name="show_quantity_as" data-text="Qty" checked="">
                        <label for="sq_1">Qty</label>
                    </div>
                    <div class="radio radio-primary radio-inline">
                        <input type="radio" value="2" id="sq_2" name="show_quantity_as" data-text="Hours">
                        <label for="sq_2">Hours</label>
                    </div>
                    <div class="radio radio-primary radio-inline">
                        <input type="radio" value="3" id="sq_3" name="show_quantity_as" data-text="Qty/Hours">
                        <label for="sq_3">Qty/Hours</label>
                    </div>
                </div>
            </div>
        </div>
                <div class="table-responsive s_table" style="overflow-x:auto">
            <table class="table invoice-items-table items table-main-invoice-edit has-calculations no-mtop">
                <thead>
                    <tr>
                        <th></th>
                        <th width="20%" align="left"><i class="fa-solid fa-circle-exclamation tw-mr-1" aria-hidden="true" data-toggle="tooltip" data-title="New lines are not supported for item description. Use the item long description instead." data-original-title="" title=""></i>
                            Item</th>
                        <th width="25%" align="left">Description</th>
                                                <th width="10%" align="right" class="qty">Qty</th>
                        <th width="15%" align="right">Rate</th>
                        <th width="20%" align="right">Tax</th>
                        <th width="10%" align="right">Amount</th>
                        <th align="center"><i class="fa fa-cog"></i></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="main">
                        <td></td>
                        <td>
                            <textarea name="description" class="form-control" rows="4" placeholder="Description"></textarea>
                        </td>
                        <td>
                            <textarea name="long_description" rows="4" class="form-control" placeholder="Long description"></textarea>
                        </td>
                                                <td>
                            <input type="number" name="quantity" min="0" value="1" class="form-control" placeholder="Quantity" fdprocessedid="64pmza">
                            <input type="text" placeholder="Unit" data-toggle="tooltip" data-title="e.q kg, lots, packs" name="unit" class="form-control input-transparent text-right" fdprocessedid="xtr8vq">
                        </td>
                        <td>
                            <input type="number" name="rate" class="form-control" placeholder="Rate" fdprocessedid="p1zrvb">
                        </td>
                        <td>
                            <div class="dropdown bootstrap-select show-tick display-block tax main-tax bs3" style="width: 100%;"><select class="selectpicker display-block tax main-tax" data-width="100%" name="taxname" multiple="" data-none-selected-text="No Tax" tabindex="-98"><option value="gov|10.00" data-taxrate="10.00" data-taxname="gov" data-subtext="gov">10.00%</option></select><button type="button" class="btn dropdown-toggle btn-default bs-placeholder" data-toggle="dropdown" role="combobox" aria-owns="bs-select-12" aria-haspopup="listbox" aria-expanded="false" title="No Tax" fdprocessedid="7zemt"><div class="filter-option"><div class="filter-option-inner"><div class="filter-option-inner-inner">No Tax</div></div> </div><span class="bs-caret"><span class="caret"></span></span></button><div class="dropdown-menu open"><div class="inner open" role="listbox" id="bs-select-12" tabindex="-1" aria-multiselectable="true"><ul class="dropdown-menu inner " role="presentation"></ul></div></div></div>                        </td>
                        <td></td>
                        <td>
                                                        <button type="button" onclick="add_item_to_table('undefined','undefined',undefined); return false;" class="btn pull-right btn-primary" fdprocessedid="8yaaz4"><i class="fa fa-check"></i></button>
                        </td>
                    </tr>
                                    </tbody>
            </table>
        </div>
        <div class="col-md-8 col-md-offset-4">
            <table class="table text-right">
                <tbody>
                    <tr id="subtotal">
                        <td>
                            <span class="bold tw-text-neutral-700">Sub Total :</span>
                        </td>
                        <td class="subtotal">$0.00<input type="hidden" name="subtotal" value="0.00"></td>
                    </tr>
                    <tr id="discount_area">
                        <td>
                            <div class="row">
                                <div class="col-md-7">
                                    <span class="bold tw-text-neutral-700">
                                        Discount                                    </span>
                                </div>
                                <div class="col-md-5">
                                    <div class="input-group" id="discount-total">

                                        <input type="number" value="0" class="form-control pull-left input-discount-percent" min="0" max="100" name="discount_percent" fdprocessedid="z1n09">

                                        <input type="number" data-toggle="tooltip" data-title="The number in the input field is not formatted while edit/add item and should remain not formatted do not try to format it manually in here." value="0" class="form-control pull-left input-discount-fixed hide" min="0" name="discount_total">

                                        <div class="input-group-addon">
                                            <div class="dropdown">
                                                <a class="dropdown-toggle" href="#" id="dropdown_menu_tax_total_type" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                                    <span class="discount-total-type-selected">
                                                        %                                                    </span>
                                                    <span class="caret"></span>
                                                </a>
                                                <ul class="dropdown-menu" id="discount-total-type-dropdown" aria-labelledby="dropdown_menu_tax_total_type">
                                                    <li>
                                                        <a href="#" class="discount-total-type discount-type-percent selected">%</a>
                                                    </li>
                                                    <li><a href="#" class="discount-total-type discount-type-fixed">
                                                            Fixed Amount                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="discount-total">-$0.00</td>
                    </tr>
                    <tr>
                        <td>
                            <div class="row">
                                <div class="col-md-7">
                                    <span class="bold tw-text-neutral-700">Adjustment</span>
                                </div>
                                <div class="col-md-5">
                                    <input type="number" data-toggle="tooltip" data-title="The number in the input field is not formatted while edit/add item and should remain not formatted do not try to format it manually in here." value="0" class="form-control pull-left" name="adjustment" fdprocessedid="b1zxj">
                                </div>
                            </div>
                        </td>
                        <td class="adjustment">$0.00</td>
                    </tr>
                    <tr>
                        <td><span class="bold tw-text-neutral-700">Total :</span>
                        </td>
                        <td class="total">$0.00<input type="hidden" name="total" value="0.00"></td>
                    </tr>
                                    </tbody>
            </table>

        </div>


        <div id="removed-items"></div>
        <div id="billed-tasks"></div>
        <div id="billed-expenses"></div>
        
<input type="hidden" name="task_id" value="">
        
<input type="hidden" name="expense_id" value="">

    </div>

    <hr class="hr-panel-separator">

    <div class="panel-body">
                <div class="form-group" app-field-wrapper="clientnote"><label for="clientnote" class="control-label">Client Note</label><textarea id="clientnote" name="clientnote" class="form-control" rows="4"></textarea></div>                <div class="form-group mtop15" app-field-wrapper="terms"><label for="terms" class="control-label">Terms &amp; Conditions</label><textarea id="terms" name="terms" class="form-control" rows="4"></textarea></div>    </div>

    </div>

<div class="btn-bottom-pusher"></div>
<div class="btn-bottom-toolbar text-right">
        <button class="btn-tr btn btn-default mright5 text-right invoice-form-submit save-as-draft transaction-submit">
        Save as Draft    </button>
        <div class="btn-group dropup">
        <button type="button" class="btn-tr btn btn-primary invoice-form-submit transaction-submit">Save</button>
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right width200">
            <li>
                <a href="#" class="invoice-form-submit save-and-send transaction-submit">
                    Save &amp; Send                </a>
            </li>
                        <li>
                <a href="#" class="invoice-form-submit save-and-send-later transaction-submit">
                    Save and Send Later                </a>
            </li>
            <li>
                <a href="#" class="invoice-form-submit save-and-record-payment transaction-submit">
                    Save &amp; Record Payment                </a>
            </li>
                    </ul>
    </div>
</div>
            </div>
            </form>            <div class="modal fade" id="sales_item_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title">Edit Item</span>
                    <span class="add-title">Add New Item</span>
                </h4>
            </div>
            <form action="http://localhost/perfex_crm/sales_marketing/invoices/manage" id="invoice_item_form" method="post" accept-charset="utf-8" novalidate="novalidate">
<input type="hidden" name="csrf_token_name" value="29ae38ab42ac10b8e4bd0cacda034bf9">                                                                                     
            
<input type="hidden" name="itemid" value="">
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-warning affect-warning hide">
                            Changing item info won't affect on the created invoices/estimates/proposals/credit notes.                        </div>
                        <div class="form-group" app-field-wrapper="description"><label for="description" class="control-label"> <small class="req text-danger">* </small>Description</label><input type="text" id="description" name="description" class="form-control" value=""></div>                        <div class="form-group" app-field-wrapper="long_description"><label for="long_description" class="control-label">Long Description</label><textarea id="long_description" name="long_description" class="form-control" rows="4"></textarea></div>                        <div class="form-group">
                        <label for="rate" class="control-label"> <small class="req text-danger">* </small>
                            Rate - USD <small>(Base Currency)</small></label>
                            <input type="number" id="rate" name="rate" class="form-control" value="">
                        </div>
                                                <div class="row">
                            <div class="col-md-6">
                             <div class="form-group">
                                <label class="control-label" for="tax">Tax 1</label>
                                <div class="dropdown bootstrap-select display-block bs3" style="width: 100%;"><select class="selectpicker display-block" data-width="100%" name="tax" data-none-selected-text="No Tax" tabindex="-98">
                                    <option value=""></option>
                                                                        <option value="1" data-subtext="gov">
                                        10.00%
                                    </option>
                                                                    </select><button type="button" class="btn dropdown-toggle btn-default bs-placeholder" data-toggle="dropdown" role="combobox" aria-owns="bs-select-13" aria-haspopup="listbox" aria-expanded="false" title="No Tax"><div class="filter-option"><div class="filter-option-inner"><div class="filter-option-inner-inner">No Tax</div></div> </div><span class="bs-caret"><span class="caret"></span></span></button><div class="dropdown-menu open"><div class="inner open" role="listbox" id="bs-select-13" tabindex="-1"><ul class="dropdown-menu inner " role="presentation"></ul></div></div></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                         <div class="form-group">
                            <label class="control-label" for="tax2">Tax 2</label>
                            <div class="dropdown bootstrap-select disabled display-block bs3" style="width: 100%;"><select class="selectpicker display-block" disabled="" data-width="100%" name="tax2" data-none-selected-text="No Tax" tabindex="-98">
                                <option value=""></option>
                                                                <option value="1" data-subtext="gov">
                                    10.00%
                                </option>
                                                            </select><button type="button" class="btn dropdown-toggle disabled btn-default bs-placeholder" data-toggle="dropdown" role="combobox" aria-owns="bs-select-14" aria-haspopup="listbox" aria-expanded="false" tabindex="-1" aria-disabled="true" title="No Tax"><div class="filter-option"><div class="filter-option-inner"><div class="filter-option-inner-inner">No Tax</div></div> </div><span class="bs-caret"><span class="caret"></span></span></button><div class="dropdown-menu open"><div class="inner open" role="listbox" id="bs-select-14" tabindex="-1"><ul class="dropdown-menu inner " role="presentation"></ul></div></div></div>
                        </div>
                    </div>
                </div>
                <div class="clearfix mbot15"></div>
                <div class="form-group" app-field-wrapper="unit"><label for="unit" class="control-label">Unit</label><input type="text" id="unit" name="unit" class="form-control" value=""></div>                <div id="custom_fields_items">
                                    </div>
                <div class="form-group" app-field-wrapper="group_id"><label for="group_id" class="control-label">Item Group</label><div class="dropdown bootstrap-select bs3" style="width: 100%;"><select id="group_id" name="group_id" class="selectpicker" data-width="100%" data-none-selected-text="Nothing selected" data-live-search="true" tabindex="-98"><option value=""></option></select><button type="button" class="btn dropdown-toggle btn-default bs-placeholder" data-toggle="dropdown" role="combobox" aria-owns="bs-select-15" aria-haspopup="listbox" aria-expanded="false" data-id="group_id" title="Nothing selected"><div class="filter-option"><div class="filter-option-inner"><div class="filter-option-inner-inner">Nothing selected</div></div> </div><span class="bs-caret"><span class="caret"></span></span></button><div class="dropdown-menu open"><div class="bs-searchbox"><input type="search" class="form-control" autocomplete="off" role="combobox" aria-label="Search" aria-controls="bs-select-15" aria-autocomplete="list"></div><div class="inner open" role="listbox" id="bs-select-15" tabindex="-1"><ul class="dropdown-menu inner " role="presentation"></ul></div></div></div></div>                            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save</button>
            </div></form>
</div>
</div>
</div>
<script>
    // Maybe in modal? Eq convert to invoice or convert proposal to estimate/invoice
    if(typeof(jQuery) != 'undefined'){
        init_item_js();
    } else {
     window.addEventListener('load', function () {
       var initItemsJsInterval = setInterval(function(){
            if(typeof(jQuery) != 'undefined') {
                init_item_js();
                clearInterval(initItemsJsInterval);
            }
         }, 1000);
     });
  }
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
            alert_float('success', response.message);
        }
        $('#sales_item_modal').modal('hide');
    }).fail(function (data) {
        alert_float('danger', data.responseText);
    });
    return false;
}
function init_item_js() {
     // Add item to preview from the dropdown for invoices estimates
    $("body").on('change', 'select[name="item_select"]', function () {
        var itemid = $(this).selectpicker('val');
        if (itemid != '') {
            add_item_to_preview(itemid);
        }
    });

    // Items modal show action
    $("body").on('show.bs.modal', '#sales_item_modal', function (event) {

        $('.affect-warning').addClass('hide');

        var $itemModal = $('#sales_item_modal');
        $('input[name="itemid"]').val('');
        $itemModal.find('input').not('input[type="hidden"]').val('');
        $itemModal.find('textarea').val('');
        $itemModal.find('select').selectpicker('val', '').selectpicker('refresh');
        $('select[name="tax2"]').selectpicker('val', '').change();
        $('select[name="tax"]').selectpicker('val', '').change();
        $itemModal.find('.add-title').removeClass('hide');
        $itemModal.find('.edit-title').addClass('hide');

        var id = $(event.relatedTarget).data('id');
        // If id found get the text from the datatable
        if (typeof (id) !== 'undefined') {

            $('.affect-warning').removeClass('hide');
            $('input[name="itemid"]').val(id);

            requestGetJSON('invoice_items/get_item_by_id/' + id).done(function (response) {
                $itemModal.find('input[name="description"]').val(response.description);
                $itemModal.find('textarea[name="long_description"]').val(response.long_description.replace(/(<|<)br\s*\/*(>|>)/g, " "));
                $itemModal.find('input[name="rate"]').val(response.rate);
                $itemModal.find('input[name="unit"]').val(response.unit);
                $('select[name="tax"]').selectpicker('val', response.taxid).change();
                $('select[name="tax2"]').selectpicker('val', response.taxid_2).change();
                $itemModal.find('#group_id').selectpicker('val', response.group_id);
                $.each(response, function (column, value) {
                    if (column.indexOf('rate_currency_') > -1) {
                        $itemModal.find('input[name="' + column + '"]').val(value);
                    }
                });

                $('#custom_fields_items').html(response.custom_fields_html);

                init_selectpicker();
                init_color_pickers();
                init_datepicker();

                $itemModal.find('.add-title').addClass('hide');
                $itemModal.find('.edit-title').removeClass('hide');
                validate_item_form();
            });

        }
    });

    $("body").on("hidden.bs.modal", '#sales_item_modal', function (event) {
        $('#item_select').selectpicker('val', '');
    });

   validate_item_form();
}
function validate_item_form(){
    // Set validation for invoice item form
    appValidateForm($('#invoice_item_form'), {
        description: 'required',
        rate: {
            required: true,
        }
    }, manage_invoice_items);
}
</script>
        </div>                  
    </div>
</div>

<script>

init_datepicker(); 
//init_selectpicker();
$(function() { 
    $('body').find('select.selectpicker').not('.ajax-search').selectpicker({
        showSubtext: true,
    }); 
    
    //init_selectpicker();
    
    //$("#myModal").attr("style","max-width: 80%");
    
    validate_invoice_form();
    // Init accountacy currency symbol
    init_currency();
    // Project ajax search
    init_ajax_project_search_by_customer_id();
    // Maybe items ajax search
    init_ajax_search('items', '#item_select.ajax-search', undefined, admin_url + 'items/search');
});

</script>
</div>