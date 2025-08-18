<?php
/* Smarty version 4.5.3, created on 2025-08-17 10:57:04
  from '/var/www/html/ISP/ui/ui/customers-edit.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '4.5.3',
  'unifunc' => 'content_68a18b504a2138_76541096',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ea1f2f43639ed471f6ca9ef81f5aff6d73f2b4eb' => 
    array (
      0 => '/var/www/html/ISP/ui/ui/customers-edit.tpl',
      1 => 1755417194,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:sections/header.tpl' => 1,
    'file:sections/footer.tpl' => 1,
  ),
),false)) {
function content_68a18b504a2138_76541096 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:sections/header.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<form class="form-horizontal" enctype="multipart/form-data" method="post" role="form" action="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/edit-post">
    <input type="hidden" name="csrf_token" value="<?php echo $_smarty_tpl->tpl_vars['csrf_token']->value;?>
">
    <div class="row">
        <div class="col-md-6">
            <div
                class="panel panel-<?php if ($_smarty_tpl->tpl_vars['d']->value['status'] == 'Active') {?>primary<?php } else { ?>danger<?php }?> panel-hovered panel-stacked mb30">
                <div class="panel-heading"><?php echo Lang::T('Edit Contact');?>
</div>
                <div class="panel-body">
                    <center>
                        <img src="<?php echo $_smarty_tpl->tpl_vars['UPLOAD_PATH']->value;
echo $_smarty_tpl->tpl_vars['d']->value['photo'];?>
.thumb.jpg" width="200"
                            onerror="this.src='<?php echo $_smarty_tpl->tpl_vars['UPLOAD_PATH']->value;?>
/user.default.jpg'" class="img-circle img-responsive"
                            alt="Foto" onclick="return deletePhoto(<?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
)">
                    </center><br>
                    <input type="hidden" name="id" value="<?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
">
                    <div class="form-group">
                        <label class="col-md-3 col-xs-12 control-label"><?php echo Lang::T('Photo');?>
</label>
                        <div class="col-md-6 col-xs-8">
                            <input type="file" class="form-control" name="photo" accept="image/*">
                        </div>
                        <div class="form-group col-md-3 col-xs-4" title="Not always Working">
                            <label class=""><input type="checkbox" checked name="faceDetect" value="yes"> Facedetect</label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo Lang::T('Username');?>
</label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <?php if ($_smarty_tpl->tpl_vars['_c']->value['country_code_phone'] != '') {?>
                                    <span class="input-group-addon" id="basic-addon1"><i
                                            class="glyphicon glyphicon-phone-alt"></i></span>
                                <?php } else { ?>
                                    <span class="input-group-addon" id="basic-addon1"><i
                                            class="glyphicon glyphicon-user"></i></span>
                                <?php }?>
                                <input type="text" class="form-control" name="username" value="<?php echo $_smarty_tpl->tpl_vars['d']->value['username'];?>
"
                                    required
                                    placeholder="<?php if ($_smarty_tpl->tpl_vars['_c']->value['country_code_phone'] != '') {
echo $_smarty_tpl->tpl_vars['_c']->value['country_code_phone'];?>
 <?php echo Lang::T('Phone Number');
} else {
echo Lang::T('Username');
}?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo Lang::T('Full Name');?>
</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="fullname" name="fullname"
                                value="<?php echo $_smarty_tpl->tpl_vars['d']->value['fullname'];?>
">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo Lang::T('Email');?>
</label>
                        <div class="col-md-9">
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo $_smarty_tpl->tpl_vars['d']->value['email'];?>
">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo Lang::T('Phone Number');?>
</label>
                        <div class="col-md-9">
                            <div class="input-group">
                                <?php if ($_smarty_tpl->tpl_vars['_c']->value['country_code_phone'] != '') {?>
                                    <span class="input-group-addon" id="basic-addon1">+</span>
                                <?php } else { ?>
                                    <span class="input-group-addon" id="basic-addon1"><i
                                            class="glyphicon glyphicon-phone-alt"></i></span>
                                <?php }?>
                                <input type="text" class="form-control" name="phonenumber" value="<?php echo $_smarty_tpl->tpl_vars['d']->value['phonenumber'];?>
"
                                    placeholder="<?php if ($_smarty_tpl->tpl_vars['_c']->value['country_code_phone'] != '') {
echo $_smarty_tpl->tpl_vars['_c']->value['country_code_phone'];
}?> <?php echo Lang::T('Phone Number');?>
">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo Lang::T('Password');?>
</label>
                        <div class="col-md-9">
                            <input type="password" autocomplete="off" class="form-control" id="password" name="password"
                                onmouseleave="this.type = 'password'" onmouseenter="this.type = 'text'"
                                value="<?php echo $_smarty_tpl->tpl_vars['d']->value['password'];?>
">
                            <span class="help-block"><?php echo Lang::T('Keep Blank to do not change Password');?>
</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo Lang::T('Address');?>
</label>
                        <div class="col-md-9">
                            <textarea name="address" id="address" class="form-control"><?php echo $_smarty_tpl->tpl_vars['d']->value['address'];?>
</textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo Lang::T('Service Type');?>
</label>
                        <div class="col-md-9">
                            <select class="form-control" id="service_type" name="service_type">
                                <option value="Hotspot" <?php if ($_smarty_tpl->tpl_vars['d']->value['service_type'] == 'Hotspot') {?>selected<?php }?>>Hotspot
                                </option>
                                <option value="PPPoE" <?php if ($_smarty_tpl->tpl_vars['d']->value['service_type'] == 'PPPoE') {?>selected<?php }?>>PPPoE</option>
                                <option value="Others" <?php if ($_smarty_tpl->tpl_vars['d']->value['service_type'] == 'Others') {?>selected<?php }?>>Others</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo Lang::T('Account Type');?>
</label>
                        <div class="col-md-9">
                            <select class="form-control" id="account_type" name="account_type">
                                <option value="Personal" <?php if ($_smarty_tpl->tpl_vars['d']->value['account_type'] == 'Personal') {?>selected<?php }?>>Personal
                                </option>
                                <option value="Business" <?php if ($_smarty_tpl->tpl_vars['d']->value['account_type'] == 'Business') {?>selected<?php }?>>Business
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo Lang::T('Coordinates');?>
</label>
                        <div class="col-md-9">
                            <input name="coordinates" id="coordinates" class="form-control" value="<?php echo $_smarty_tpl->tpl_vars['d']->value['coordinates'];?>
"
                                placeholder="6.465422, 3.406448">
                            <div id="map" style="width: '100%'; height: 200px; min-height: 150px;"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo Lang::T('Status');?>
</label>
                        <div class="col-md-9">
                            <select class="form-control" id="status" name="status">
                                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['statuses']->value, 'status');
$_smarty_tpl->tpl_vars['status']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['status']->value) {
$_smarty_tpl->tpl_vars['status']->do_else = false;
?>
                                    <option value="<?php echo $_smarty_tpl->tpl_vars['status']->value;?>
" <?php if ($_smarty_tpl->tpl_vars['d']->value['status'] == $_smarty_tpl->tpl_vars['status']->value) {?>selected<?php }?>><?php echo Lang::T($_smarty_tpl->tpl_vars['status']->value);?>

                                    </option>
                                <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                            </select>
                            <span class="help-block">
                                <?php echo Lang::T('Banned');?>
: <?php echo Lang::T('Customer cannot login again');?>
.<br>
                                <?php echo Lang::T('Disabled');?>
:
                                <?php echo Lang::T('Customer can login but cannot buy internet plan, Admin cannot recharge customer');?>
.<br>
                                <?php echo Lang::T('Don\'t forget to deactivate all active plan too');?>
.
                            </span>
                        </div>
                    </div>
                </div>
                <div class="panel-heading">PPPOE</div>
                <div class="panel-body">
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo Lang::T('Username');?>
 <span class="label label-danger"
                                id="warning_username"></span></label>
                        <div class="col-md-9">
                            <input type="username" class="form-control" id="pppoe_username" name="pppoe_username"
                                onkeyup="checkUsername(this, <?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
)" value="<?php echo $_smarty_tpl->tpl_vars['d']->value['pppoe_username'];?>
">
                            <span class="help-block"><?php echo Lang::T('Not Working with Freeradius Mysql');?>
</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo Lang::T('Password');?>
</label>
                        <div class="col-md-9">
                            <input type="password" class="form-control" id="pppoe_password" name="pppoe_password"
                                value="<?php echo $_smarty_tpl->tpl_vars['d']->value['pppoe_password'];?>
" onmouseleave="this.type = 'password'"
                                onmouseenter="this.type = 'text'">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Remote IP <span class="label label-danger"
                                id="warning_ip"></span></label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="pppoe_ip" name="pppoe_ip"
                                onkeyup="checkIP(this, <?php echo $_smarty_tpl->tpl_vars['d']->value['id'];?>
)" value="<?php echo $_smarty_tpl->tpl_vars['d']->value['pppoe_ip'];?>
">
                            <span class="help-block"><?php echo Lang::T('Not Working with Freeradius Mysql');?>
</span>
                        </div>
                    </div>
                    <span class="help-block">
                        <?php echo Lang::T('User Cannot change this, only admin. if it Empty it will use Customer Credentials');?>

                    </span>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-primary panel-hovered panel-stacked mb30">
                <div class="panel-heading"><?php echo Lang::T('Attributes');?>
</div>
                <div class="panel-body">
                    <!--Customers Attributes edit start -->
                    <?php if ($_smarty_tpl->tpl_vars['customFields']->value) {?>
                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['customFields']->value, 'customField');
$_smarty_tpl->tpl_vars['customField']->do_else = true;
if ($_from !== null) foreach ($_from as $_smarty_tpl->tpl_vars['customField']->value) {
$_smarty_tpl->tpl_vars['customField']->do_else = false;
?>
                            <div class="form-group">
                                <label class="col-md-4 control-label"
                                    for="<?php echo $_smarty_tpl->tpl_vars['customField']->value['field_name'];?>
"><?php echo $_smarty_tpl->tpl_vars['customField']->value['field_name'];?>
</label>
                                <div class="col-md-6">
                                    <input class="form-control" type="text" name="custom_fields[<?php echo $_smarty_tpl->tpl_vars['customField']->value['field_name'];?>
]"
                                        id="<?php echo $_smarty_tpl->tpl_vars['customField']->value['field_name'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['customField']->value['field_value'];?>
">
                                </div>
                                <label class="col-md-2">
                                    <input type="checkbox" name="delete_custom_fields[]" value="<?php echo $_smarty_tpl->tpl_vars['customField']->value['field_name'];?>
">
                                    <?php echo Lang::T('Delete');?>

                                </label>
                            </div>
                        <?php
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                    <?php }?>
                    <!--Customers Attributes edit end -->
                    <!-- Customers Attributes add start -->
                    <div id="custom-fields-container">
                    </div>
                    <!-- Customers Attributes add end -->
                </div>
                <div class="panel-footer">
                    <button class="btn btn-success btn-block" type="button"
                        id="add-custom-field"><?php echo Lang::T('Add');?>
</button>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="box box-primary box-solid collapsed-box">
                <div class="box-header with-border">
                    <h3 class="box-title"><?php echo Lang::T('Additional Information');?>
</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-plus"></i>
                        </button>
                    </div>
                </div>
                <div class="box-body" style="display: none;">
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo Lang::T('City');?>
</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="city" name="city" value="<?php echo $_smarty_tpl->tpl_vars['d']->value['city'];?>
">
                            <small class="form-text text-muted"><?php echo Lang::T('City of Resident');?>
</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo Lang::T('District');?>
</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="district" name="district"
                                value="<?php echo $_smarty_tpl->tpl_vars['d']->value['district'];?>
">
                            <small class="form-text text-muted"><?php echo Lang::T('District');?>
</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo Lang::T('State');?>
</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="state" name="state" value="<?php echo $_smarty_tpl->tpl_vars['d']->value['state'];?>
">
                            <small class="form-text text-muted"><?php echo Lang::T('State of Resident');?>
</small>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label"><?php echo Lang::T('Zip');?>
</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" id="zip" name="zip" value="<?php echo $_smarty_tpl->tpl_vars['d']->value['zip'];?>
">
                            <small class="form-text text-muted"><?php echo Lang::T('Zip Code');?>
</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <center>
        <button class="btn btn-primary" onclick="return ask(this, 'Continue the Customer Data change process?')"
            type="submit">
            <?php echo Lang::T('Save Changes');?>

        </button>
        <br><a href="<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/list" class="btn btn-link"><?php echo Lang::T('Cancel');?>
</a>
    </center>
</form>


    <?php echo '<script'; ?>
 type="text/javascript">
        document.addEventListener("DOMContentLoaded", function() {
            var customFieldsContainer = document.getElementById('custom-fields-container');
            var addCustomFieldButton = document.getElementById('add-custom-field');

            addCustomFieldButton.addEventListener('click', function() {
                var fieldIndex = customFieldsContainer.children.length;
                var newField = document.createElement('div');
                newField.className = 'form-group';
                newField.innerHTML = `
                <div class="col-md-4">
                    <input type="text" class="form-control" name="custom_field_name[]" placeholder="Name">
                </div>
                <div class="col-md-6">
                    <input type="text" class="form-control" name="custom_field_value[]" placeholder="Value">
                </div>
                <div class="col-md-2">
                    <button type="button" class="remove-custom-field btn btn-danger btn-sm">-</button>
                </div>
            `;
                customFieldsContainer.appendChild(newField);
            });

            customFieldsContainer.addEventListener('click', function(event) {
                if (event.target.classList.contains('remove-custom-field')) {
                    var fieldContainer = event.target.parentNode.parentNode;
                    fieldContainer.parentNode.removeChild(fieldContainer);
                }
            });
        });
    <?php echo '</script'; ?>
>

    <?php echo '<script'; ?>
 src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"><?php echo '</script'; ?>
>
    <?php echo '<script'; ?>
>
        function getLocation() {
            if (window.location.protocol == "https:" && navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showPosition);
            } else {
                setupMap(51.505, -0.09);
            }
        }

        function showPosition(position) {
            setupMap(position.coords.latitude, position.coords.longitude);
        }

        function setupMap(lat, lon) {
            var map = L.map('map').setView([lat, lon], 13);
            L.tileLayer('https://{s}.basemaps.cartocdn.com/rastertiles/light_all/{z}/{x}/{y}.png', {
            attribution:
                '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors &copy; <a href="https://carto.com/attributions">CARTO</a>',
                subdomains: 'abcd',
                maxZoom: 20
        }).addTo(map);
        var marker = L.marker([lat, lon]).addTo(map);
        map.on('click', function(e) {
            var coord = e.latlng;
            var lat = coord.lat;
            var lng = coord.lng;
            var newLatLng = new L.LatLng(lat, lng);
            marker.setLatLng(newLatLng);
            $('#coordinates').val(lat + ',' + lng);
        });
        }
        window.onload = function() {
        
        <?php if ($_smarty_tpl->tpl_vars['d']->value['coordinates']) {?>
            setupMap(<?php echo $_smarty_tpl->tpl_vars['d']->value['coordinates'];?>
);
        <?php } else { ?>
            getLocation();
        <?php }?>
        
        }
    <?php echo '</script'; ?>
>


<?php echo '<script'; ?>
>
    function deletePhoto(id) {
        if (confirm('Delete photo?')) {
            if (confirm('Are you sure to delete photo?')) {
                window.location.href = '<?php echo $_smarty_tpl->tpl_vars['_url']->value;?>
customers/edit/'+id+'/deletePhoto'
            }
        }
    }
<?php echo '</script'; ?>
>

<?php $_smarty_tpl->_subTemplateRender("file:sections/footer.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
