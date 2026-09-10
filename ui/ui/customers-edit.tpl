{include file="sections/header.tpl"}

<!-- Modern Edit Customer -->
<div class="add-customer-modern">

    <div class="page-header">
        <a href="{$_url}customers/list" class="back-link"><i class="fa fa-arrow-left"></i></a>
        <h3><i class="fa fa-user-edit" style="color:#667eea;margin-right:8px;"></i>{Lang::T('Edit Contact')} - {$d['username']}</h3>
        <span class="status-badge status-{$d['status']|lower}">{$d['status']}</span>
    </div>

    <form method="post" enctype="multipart/form-data" action="{$_url}customers/edit-post">
        <input type="hidden" name="csrf_token" value="{$csrf_token}">
        <input type="hidden" name="id" value="{$d['id']}">

        <div class="form-grid">
            <!-- Left Column -->
            <div class="form-col">

                <!-- Contact Info Card -->
                <div class="form-card">
                    <h4 class="card-title"><i class="fa fa-user"></i> {Lang::T('Contact Information')}</h4>

                    <!-- Photo Upload -->
                    <div class="photo-row">
                        <img src="{$UPLOAD_PATH}{$d['photo']}.thumb.jpg" class="photo-thumb"
                            onerror="this.src='{$UPLOAD_PATH}/user.default.jpg'"
                            alt="Foto" onclick="return deletePhoto({$d['id']})">
                        <div class="photo-upload">
                            <label class="form-label">{Lang::T('Photo')}</label>
                            <input type="file" class="form-input" name="photo" accept="image/*">
                            <label class="checkbox-inline"><input type="checkbox" checked name="faceDetect" value="yes"> {Lang::T('Face Detect')}</label>
                        </div>
                    </div>

                    <div class="form-group-modern">
                        <label class="form-label">{Lang::T('Username')} <span class="required">*</span></label>
                        <input type="text" class="form-input" name="username" value="{$d['username']}" required placeholder="{Lang::T('Username')}">
                    </div>
                    <div class="form-group-modern">
                        <label class="form-label">{Lang::T('Full Name')} <span class="required">*</span></label>
                        <input type="text" class="form-input" id="fullname" name="fullname" value="{$d['fullname']}" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group-modern">
                            <label class="form-label">{Lang::T('Email')}</label>
                            <input type="email" class="form-input" id="email" name="email" value="{$d['email']}" placeholder="user@example.com">
                        </div>
                        <div class="form-group-modern">
                            <label class="form-label">{Lang::T('Phone Number')}</label>
                            <input type="text" class="form-input" name="phonenumber" value="{$d['phonenumber']}" placeholder="{if $_c['country_code_phone']!= ''}+{$_c['country_code_phone']} {/if}{Lang::T('Phone Number')}">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group-modern">
                            <label class="form-label">{Lang::T('Password')}</label>
                            <input type="password" class="form-input" autocomplete="off" id="password" name="password" value="{$d['password']}" onmouseleave="this.type = 'password'" onmouseenter="this.type = 'text'">
                            <div class="form-hint">{Lang::T('Keep Blank to do not change Password')}</div>
                        </div>
                        <div class="form-group-modern">
                            <label class="form-label">{Lang::T('Service Type')}</label>
                            <select class="form-input" id="service_type" name="service_type">
                                <option value="Hotspot" {if $d['service_type'] eq 'Hotspot'}selected{/if}>Hotspot</option>
                                <option value="PPPoE" {if $d['service_type'] eq 'PPPoE'}selected{/if}>PPPoE</option>
                                <option value="Others" {if $d['service_type'] eq 'Others'}selected{/if}>Others</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group-modern" style="max-width:calc(50% - 5px);">
                        <label class="form-label">{Lang::T('Account Type')}</label>
                        <select class="form-input" id="account_type" name="account_type">
                            <option value="Personal" {if $d['account_type'] eq 'Personal'}selected{/if}>Personal</option>
                            <option value="Business" {if $d['account_type'] eq 'Business'}selected{/if}>Business</option>
                        </select>
                    </div>
                    <div class="form-group-modern">
                        <label class="form-label">{Lang::T('Address')}</label>
                        <textarea name="address" id="address" class="form-input form-textarea" rows="2">{$d['address']}</textarea>
                    </div>
                    <div class="form-group-modern">
                        <label class="form-label">{Lang::T('Coordinates')}</label>
                        <input name="coordinates" id="coordinates" class="form-input" value="{$d['coordinates']}" placeholder="6.465422, 3.406448">
                        <div id="map" class="map-preview"></div>
                    </div>
                </div>

                <!-- PPPoE Card -->
                <div class="form-card">
                    <h4 class="card-title"><i class="fa fa-plug"></i> PPPoE {Lang::T('Settings')}</h4>
                    <div class="form-group-modern">
                        <label class="form-label">{Lang::T('PPPoE Username')} <span id="warning_username" class="warn-badge"></span></label>
                        <input type="text" class="form-input" id="pppoe_username" name="pppoe_username" onkeyup="checkUsername(this, {$d['id']})" value="{$d['pppoe_username']}">
                        <div class="form-hint">{Lang::T('Not Working with Freeradius Mysql')}</div>
                    </div>
                    <div class="form-group-modern">
                        <label class="form-label">{Lang::T('PPPoE Password')}</label>
                        <input type="password" class="form-input" id="pppoe_password" name="pppoe_password" value="{$d['pppoe_password']}" onmouseleave="this.type = 'password'" onmouseenter="this.type = 'text'">
                    </div>
                    <div class="form-hint">{Lang::T('User Cannot change this, only admin. if it Empty it will use Customer Credentials')}</div>
                </div>

            </div>

            <!-- Right Column -->
            <div class="form-col">

                <!-- Status Card -->
                <div class="form-card">
                    <h4 class="card-title"><i class="fa fa-toggle-on"></i> {Lang::T('Status')}</h4>
                    <div class="form-group-modern">
                        <select class="form-input" id="status" name="status">
                            {foreach $statuses as $status}
                                <option value="{$status}" {if $d['status'] eq $status}selected{/if}>{Lang::T($status)}</option>
                            {/foreach}
                        </select>
                        <div class="form-hint" style="margin-top:8px;">
                            <strong>{Lang::T('Banned')}:</strong> {Lang::T('Customer cannot login again')}.<br>
                            <strong>{Lang::T('Disabled')}:</strong> {Lang::T('Customer can login but cannot buy internet plan, Admin cannot recharge customer')}.<br>
                            {Lang::T('Don\'t forget to deactivate all active plan too')}.
                        </div>
                    </div>
                </div>

                <!-- Attributes Card -->
                <div class="form-card">
                    <h4 class="card-title"><i class="fa fa-tags"></i> {Lang::T('Attributes')}</h4>
                    {if $customFields}
                        {foreach $customFields as $customField}
                            <div class="attr-row">
                                <input class="form-input" type="text" value="{$customField.field_name}" disabled>
                                <input class="form-input" type="text" name="custom_fields[{$customField.field_name}]" value="{$customField.field_value}">
                                <label class="attr-delete"><input type="checkbox" name="delete_custom_fields[]" value="{$customField.field_name}"> {Lang::T('Del')}</label>
                            </div>
                        {/foreach}
                    {/if}
                    <div id="custom-fields-container"></div>
                    <button class="btn-add-field" type="button" id="add-custom-field">
                        <i class="fa fa-plus-circle"></i> {Lang::T('Add Custom Field')}
                    </button>
                </div>

                <!-- Additional Info Card (Collapsible) -->
                <div class="form-card collapsible-card">
                    <div class="collapsible-header" onclick="this.parentElement.classList.toggle('open')">
                        <h4 class="card-title" style="margin:0;border:none;padding:0;"><i class="fa fa-info-circle"></i> {Lang::T('Additional Information')}</h4>
                        <i class="fa fa-chevron-down collapsible-arrow"></i>
                    </div>
                    <div class="collapsible-body">
                        <div class="form-row">
                            <div class="form-group-modern">
                                <label class="form-label">{Lang::T('City')}</label>
                                <input type="text" class="form-input" id="city" name="city" value="{$d['city']}" placeholder="{Lang::T('City of Resident')}">
                            </div>
                            <div class="form-group-modern">
                                <label class="form-label">{Lang::T('District')}</label>
                                <input type="text" class="form-input" id="district" name="district" value="{$d['district']}" placeholder="{Lang::T('District')}">
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group-modern">
                                <label class="form-label">{Lang::T('State')}</label>
                                <input type="text" class="form-input" id="state" name="state" value="{$d['state']}" placeholder="{Lang::T('State of Resident')}">
                            </div>
                            <div class="form-group-modern">
                                <label class="form-label">{Lang::T('Zip Code')}</label>
                                <input type="text" class="form-input" id="zip" name="zip" value="{$d['zip']}" placeholder="{Lang::T('Zip Code')}">
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Form Actions -->
        <div class="form-actions-bar">
            <button type="submit" class="btn-submit" onclick="return ask(this, '{Lang::T('Continue the Customer Data change process?')}')">
                <i class="fa fa-check"></i> {Lang::T('Save Changes')}
            </button>
            <a href="{$_url}customers/list" class="btn-cancel">{Lang::T('Cancel')}</a>
        </div>
    </form>

</div>

<style>
.add-customer-modern { padding: 0 15px 30px; max-width: 1100px; margin: 0 auto; }
.add-customer-modern * { box-sizing: border-box; }

.page-header { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; flex-wrap: wrap; }
.page-header h3 { margin: 0; font-weight: 700; color: #1a1a2e; }
.back-link { display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 10px; background: #f1f5f9; color: #475569; font-size: 14px; text-decoration: none; transition: all .2s; }
.back-link:hover { background: #e2e8f0; color: #1e293b; }

.status-badge { font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 20px; text-transform: uppercase; }
.status-active { background: #dcfce7; color: #16a34a; }
.status-disabled { background: #fef3c7; color: #d97706; }
.status-banned { background: #fee2e2; color: #dc2626; }
.status-quarantine { background: #fce7f3; color: #db2777; }

.add-customer-modern .form-grid { display: grid !important; grid-template-columns: 1fr 1fr !important; gap: 14px !important; margin-bottom: 20px !important; }

.add-customer-modern .form-card {
    background: #fff; border-radius: 12px; box-shadow: 0 2px 12px rgba(0,0,0,0.06);
    padding: 14px 18px; margin-bottom: 12px;
}
.card-title {
    font-size: 13px; font-weight: 700; color: #1e293b; margin: 0 0 10px;
    padding-bottom: 8px; border-bottom: 1px solid #f1f5f9;
    display: flex; align-items: center; gap: 8px;
}
.card-title i { color: #667eea; }

.add-customer-modern .form-group-modern { margin-bottom: 10px !important; }
.add-customer-modern .form-label { display: block !important; font-size: 12px !important; font-weight: 700 !important; color: #475569 !important; margin-bottom: 3px !important; }
.required { color: #ef4444; }

.add-customer-modern .form-input, .add-customer-modern .form-textarea, .add-customer-modern input.form-input, .add-customer-modern select.form-input, .add-customer-modern textarea.form-input {
    width: 100% !important; border: 1px solid #cbd5e1 !important; border-radius: 6px !important;
    padding: 5px 8px !important; font-size: 13px !important; color: #1e293b !important;
    background: #fafbfc !important; line-height: 1.4 !important; min-height: 32px !important;
    display: block !important; box-sizing: border-box !important; margin: 0 !important;
    max-width: 100% !important;
}
.add-customer-modern .form-textarea { resize: vertical !important; min-height: 50px !important; }
.add-customer-modern .form-input:focus, .add-customer-modern .form-textarea:focus, .add-customer-modern input.form-input:focus, .add-customer-modern select.form-input:focus, .add-customer-modern textarea.form-input:focus {
    outline: none !important; border-color: #667eea !important; box-shadow: 0 0 0 3px rgba(102,126,234,0.1) !important;
}
.form-hint { font-size: 11px; color: #94a3b8; margin-top: 4px; }

.add-customer-modern .form-row { display: flex !important; gap: 10px !important; flex-wrap: nowrap !important; margin: 0 0 10px 0 !important; }
.add-customer-modern .form-row .form-group-modern { flex: 1 !important; min-width: 0 !important; margin-bottom: 0 !important; overflow: hidden !important; }
.add-customer-modern select.form-input { appearance: auto !important; -webkit-appearance: auto !important; -moz-appearance: auto !important; }

/* Photo */
.photo-row { display: flex; gap: 14px; align-items: flex-start; margin-bottom: 14px; }
.photo-thumb { width: 80px; height: 80px; border-radius: 50%; object-fit: cover; border: 3px solid #e2e8f0; cursor: pointer; flex-shrink: 0; }
.photo-upload { flex: 1; }
.photo-upload .form-input { min-height: auto !important; padding: 3px 6px !important; }
.checkbox-inline { font-size: 12px; color: #64748b; margin-top: 4px; display: flex; align-items: center; gap: 4px; }

/* Attributes */
.attr-row { display: flex; gap: 6px; align-items: center; margin-bottom: 8px; }
.attr-row input[disabled] { background: #f1f5f9 !important; color: #94a3b8 !important; max-width: 100px !important; }
.attr-row .form-input { flex: 1; }
.attr-delete { font-size: 11px; color: #ef4444; white-space: nowrap; display: flex; align-items: center; gap: 2px; }

.warn-badge { font-size: 10px; font-weight: 700; color: #ef4444; }

.map-preview { width: 100%; height: 200px; border-radius: 8px; margin-top: 8px; border: 1px solid #e2e8f0; background: #f8fafc; overflow: hidden; position: relative; z-index: 1; }

/* Toggle Switch */
.toggle-row { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
.toggle-switch { position: relative; display: inline-block; width: 46px; height: 26px; }
.toggle-switch input { opacity: 0; width: 0; height: 0; }
.toggle-slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background: #e2e8f0; border-radius: 26px; transition: .3s; }
.toggle-slider:before { content: ""; position: absolute; height: 20px; width: 20px; left: 3px; bottom: 3px; background: #fff; border-radius: 50%; transition: .3s; }
.toggle-switch input:checked + .toggle-slider { background: #667eea; }
.toggle-switch input:checked + .toggle-slider:before { transform: translateX(20px); }

.btn-add-field {
    width: 100%; background: transparent; border: 2px dashed #e2e8f0; border-radius: 10px;
    padding: 10px; font-size: 13px; font-weight: 600; color: #64748b; cursor: pointer;
    transition: all .2s; margin-top: 8px;
}
.btn-add-field:hover { border-color: #667eea; color: #667eea; background: #f8fafc; }

/* Collapsible */
.collapsible-card .collapsible-header { display: flex; align-items: center; justify-content: space-between; cursor: pointer; }
.collapsible-arrow { color: #94a3b8; transition: transform .3s; }
.collapsible-card .collapsible-body { display: none; padding-top: 16px; }
.collapsible-card.open .collapsible-body { display: block; }
.collapsible-card.open .collapsible-arrow { transform: rotate(180deg); }

/* Custom field rows */
#custom-fields-container .attr-row { display: flex; gap: 6px; margin-bottom: 8px; }
#custom-fields-container .attr-row input { flex: 1; }
#custom-fields-container .attr-row .btn-danger { padding: 4px 10px; font-size: 13px; }

/* Actions Bar */
.form-actions-bar { display: flex; align-items: center; gap: 12px; padding-top: 8px; }
.btn-submit {
    display: inline-flex; align-items: center; gap: 6px;
    background: #667eea; color: #fff; border: none; border-radius: 8px;
    padding: 11px 28px; font-size: 14px; font-weight: 700; cursor: pointer; transition: background .2s;
}
.btn-submit:hover { background: #5a6fd6; }
.btn-cancel {
    padding: 11px 20px; border-radius: 8px; font-size: 14px; font-weight: 600;
    color: #64748b; text-decoration: none; transition: all .2s;
}
.btn-cancel:hover { background: #f1f5f9; color: #334155; }

@media (max-width: 768px) {
    .add-customer-modern .form-grid { grid-template-columns: 1fr !important; }
    .add-customer-modern .form-row { flex-direction: column !important; gap: 0 !important; }
    .photo-row { flex-direction: column; }
}
</style>

{literal}
<script>
document.addEventListener('DOMContentLoaded', function() {
    var ctr = document.getElementById('custom-fields-container');
    document.getElementById('add-custom-field').addEventListener('click', function() {
        var d = document.createElement('div');
        d.className = 'attr-row';
        d.innerHTML = '<input type="text" class="form-input" name="custom_field_name[]" placeholder="Name"><input type="text" class="form-input" name="custom_field_value[]" placeholder="Value"><button type="button" class="btn btn-danger btn-sm remove-custom-field">-</button>';
        d.querySelector('.remove-custom-field').addEventListener('click', function() { d.remove(); });
        ctr.appendChild(d);
    });
});
</script>
{/literal}

<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.3/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet@1.9.3/dist/leaflet.js"></script>
{literal}
<script>
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
    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: '&copy; <a href="https://www.esri.com/">Esri</a>',
        maxZoom: 20
    }).addTo(map);
    var marker = L.marker([lat, lon]).addTo(map);
    map.on('click', function(e) {
        var coord = e.latlng;
        marker.setLatLng(coord);
        document.getElementById('coordinates').value = coord.lat + ',' + coord.lng;
    });
}
window.onload = function() {
{/literal}
{if $d['coordinates']}
    setupMap({$d['coordinates']});
{else}
    getLocation();
{/if}
{literal}
}
</script>
{/literal}

<script>
function deletePhoto(id) {
    if (confirm('Delete photo?')) {
        if (confirm('Are you sure to delete photo?')) {
            window.location.href = '{$_url}customers/edit/'+id+'/deletePhoto';
        }
    }
}
</script>

{include file="sections/footer.tpl"}