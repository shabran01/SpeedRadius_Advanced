{include file="sections/header.tpl"}

<style>
.ads-header {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    padding: 28px 30px;
    border-radius: 14px;
    margin-bottom: 28px;
    box-shadow: 0 8px 24px rgba(17,153,142,0.25);
    position: relative;
    overflow: hidden;
}
.ads-header::before {
    content: '';
    position: absolute;
    inset: 0;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="d" width="20" height="20" patternUnits="userSpaceOnUse"><circle cx="10" cy="10" r="1" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23d)"/></svg>');
}
.ads-header h1, .ads-header p { position: relative; z-index: 2; margin: 0; }
.ads-header h1 { font-size: 22px; font-weight: 700; margin-bottom: 6px; }
.ads-header p  { font-size: 13px; opacity: 0.9; }
.ads-header .hicon { position: absolute; right: 28px; top: 50%; transform: translateY(-50%); font-size: 60px; opacity: 0.15; }

.card-m { background: white; border-radius: 14px; box-shadow: 0 4px 18px rgba(0,0,0,.08); overflow: hidden; margin-bottom: 22px; }
.card-m .ch { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 14px 20px; font-size: 14px; font-weight: 600; display: flex; align-items: center; justify-content: space-between; }
.card-m .cb { padding: 22px; }

.btn-add { background: linear-gradient(135deg, #11998e, #38ef7d); color: #fff; border: none; border-radius: 20px; padding: 7px 18px; font-size: 12px; font-weight: 700; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; }
.btn-add:hover { color: #fff; text-decoration: none; filter: brightness(1.08); }

.btn-sm-edit   { background: #1a73e8; color: #fff; border: none; border-radius: 12px; padding: 4px 12px; font-size: 11px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
.btn-sm-del    { background: #dc3545; color: #fff; border: none; border-radius: 12px; padding: 4px 12px; font-size: 11px; font-weight: 600; text-decoration: none; display: inline-flex; align-items: center; gap: 4px; }
.btn-sm-edit:hover, .btn-sm-del:hover { color: #fff; text-decoration: none; filter: brightness(1.1); }

.badge-on  { background: #d4edda; color: #155724; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 700; }
.badge-off { background: #f8d7da; color: #721c24; padding: 3px 10px; border-radius: 12px; font-size: 11px; font-weight: 700; }

.toggle-switch { position: relative; display: inline-block; width: 46px; height: 24px; }
.toggle-switch input { opacity: 0; width: 0; height: 0; }
.toggle-slider { position: absolute; cursor: pointer; inset: 0; background: #ccc; border-radius: 24px; transition: .3s; }
.toggle-slider:before { content: ""; position: absolute; width: 18px; height: 18px; left: 3px; bottom: 3px; background: white; border-radius: 50%; transition: .3s; }
input:checked + .toggle-slider { background: #11998e; }
input:checked + .toggle-slider:before { transform: translateX(22px); }

.type-badge { padding: 2px 8px; border-radius: 8px; font-size: 10px; font-weight: 700; text-transform: uppercase; }
.type-text  { background: #e3f2fd; color: #1565c0; }
.type-image { background: #f3e5f5; color: #7b1fa2; }
.type-gif   { background: #fff3e0; color: #e65100; }
.type-video { background: #fce4ec; color: #880e4f; }

.ad-preview { max-width: 80px; max-height: 50px; border-radius: 6px; object-fit: cover; }
video.ad-preview { max-width: 80px; max-height: 50px; }

.fl { font-weight: 600; font-size: 12px; color: #495057; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 6px; display: block; }
.fc { border: 2px solid #e9ecef; border-radius: 8px; padding: 9px 13px; font-size: 14px; color: #495057; transition: border-color .25s; width: 100%; }
.fc:focus { outline: none; border-color: #11998e; box-shadow: 0 0 0 3px rgba(17,153,142,.12); }
.hint { font-size: 11px; color: #6c757d; margin-top: 4px; }
.btn-save { background: linear-gradient(135deg, #11998e, #38ef7d); color: #fff; border: none; border-radius: 22px; padding: 11px 30px; font-size: 14px; font-weight: 700; cursor: pointer; transition: all .3s; display: inline-flex; align-items: center; gap: 8px; }
.btn-save:hover { filter: brightness(1.07); transform: translateY(-1px); }
.btn-cancel { background: #6c757d; color: #fff; border: none; border-radius: 22px; padding: 11px 22px; font-size: 14px; font-weight: 600; cursor: pointer; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; }
.btn-cancel:hover { color: #fff; text-decoration: none; background: #5a6268; }

table.ads-table { width: 100%; border-collapse: separate; border-spacing: 0; }
table.ads-table thead th { background: #f8f9fa; color: #495057; font-weight: 700; font-size: 11px; text-transform: uppercase; letter-spacing: .5px; padding: 10px 14px; border-bottom: 2px solid #e9ecef; }
table.ads-table tbody td { padding: 11px 14px; border-bottom: 1px solid #f1f3f5; vertical-align: middle; font-size: 13px; }
table.ads-table tbody tr:hover { background: #f8f9fa; }

.empty-state { text-align: center; padding: 50px 20px; color: #6c757d; }
.empty-state i { font-size: 50px; color: #dee2e6; margin-bottom: 12px; display: block; }
</style>

<div class="row">
<div class="col-sm-12">

    <div class="ads-header">
        <i class="fa fa-bullhorn hicon"></i>
        <h1><i class="fa fa-bullhorn"></i> Hotspot Advertisement Manager</h1>
        <p>Control ads shown on the hotspot login page — text, image, GIF or video</p>
    </div>

    {if $view == 'list'}
        <div class="card-m">
            <div class="ch">
                <span><i class="fa fa-list"></i> All Advertisements</span>
                <a href="{$_url}plugin/hotspot_ads/add" class="btn-add"><i class="fa fa-plus"></i> Add New Ad</a>
            </div>
            <div class="cb">
                {if count($ads) > 0}
                <div class="table-responsive">
                    <table class="ads-table">
                        <thead>
                            <tr>
                                <th>Preview</th>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Order</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        {foreach $ads as $ad}
                            <tr>
                                <td>
                                    {if $ad.type == 'text'}
                                        <span style="font-size:11px;color:#6c757d;font-style:italic;">
                                            {$ad.content|truncate:40}
                                        </span>
                                    {elseif $ad.type == 'video'}
                                        <video class="ad-preview" muted>
                                            <source src="{$ad.content}">
                                        </video>
                                    {else}
                                        <img src="{$ad.content}" class="ad-preview" alt="ad preview">
                                    {/if}
                                </td>
                                <td><strong>{$ad.title}</strong></td>
                                <td>
                                    <span class="type-badge type-{$ad.type}">{$ad.type}</span>
                                </td>
                                <td>{$ad.sort_order}</td>
                                <td>
                                    <a href="{$_url}plugin/hotspot_ads/toggle/{$ad.id}" title="Click to toggle">
                                        <label class="toggle-switch" style="cursor:pointer;" onclick="return true;">
                                            <input type="checkbox" {if $ad.status == 'on'}checked{/if} disabled>
                                            <span class="toggle-slider"></span>
                                        </label>
                                    </a>
                                    &nbsp;
                                    {if $ad.status == 'on'}
                                        <span class="badge-on">ON</span>
                                    {else}
                                        <span class="badge-off">OFF</span>
                                    {/if}
                                </td>
                                <td>
                                    <a href="{$_url}plugin/hotspot_ads/edit/{$ad.id}" class="btn-sm-edit"><i class="fa fa-pencil"></i> Edit</a>
                                    &nbsp;
                                    <a href="{$_url}plugin/hotspot_ads/delete/{$ad.id}"
                                       class="btn-sm-del"
                                       onclick="return confirm('Delete this advertisement?');"><i class="fa fa-trash"></i> Delete</a>
                                </td>
                            </tr>
                        {/foreach}
                        </tbody>
                    </table>
                </div>
                {else}
                    <div class="empty-state">
                        <i class="fa fa-bullhorn"></i>
                        <h4>No advertisements yet</h4>
                        <p>Click <strong>Add New Ad</strong> to create your first hotspot advertisement.</p>
                    </div>
                {/if}
            </div>
        </div>

    {elseif $view == 'form'}
        <div class="card-m">
            <div class="ch">
                <span><i class="fa fa-{if $ad}pencil{else}plus{/if}"></i> {if $ad}Edit{else}Add New{/if} Advertisement</span>
            </div>
            <div class="cb">
                <form method="POST" action="{$_url}plugin/hotspot_ads/save" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="{if $ad}{$ad.id}{else}0{/if}">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group" style="margin-bottom:18px;">
                                <label class="fl">Title / Label</label>
                                <input type="text" name="title" class="fc"
                                       value="{if $ad}{$ad.title}{/if}" required maxlength="255">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group" style="margin-bottom:18px;">
                                <label class="fl">Ad Type</label>
                                <select name="type" class="fc" id="adTypeSelect" onchange="toggleFields()">
                                    <option value="text"  {if $ad && $ad.type=='text'}selected{/if}>Text / Message</option>
                                    <option value="image" {if $ad && $ad.type=='image'}selected{/if}>Image (JPG/PNG)</option>
                                    <option value="gif"   {if $ad && $ad.type=='gif'}selected{/if}>GIF (Animated)</option>
                                    <option value="video" {if $ad && $ad.type=='video'}selected{/if}>Video (MP4)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group" style="margin-bottom:18px;">
                                <label class="fl">Sort Order</label>
                                <input type="number" name="sort_order" class="fc"
                                       value="{if $ad}{$ad.sort_order}{else}0{/if}" min="0">
                                <div class="hint">Lower = shown first</div>
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group" style="margin-bottom:18px;">
                                <label class="fl">Status</label>
                                <select name="status" class="fc">
                                    <option value="on"  {if $ad && $ad.status=='on'}selected{/if}>ON</option>
                                    <option value="off" {if !$ad || $ad.status=='off'}selected{/if}>OFF</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Text content -->
                    <div id="textField" class="form-group" style="margin-bottom:18px;">
                        <label class="fl">Advertisement Text</label>
                        <textarea name="content" class="fc" rows="4"
                                  placeholder="Write your promotion or announcement here...">{if $ad && $ad.type=='text'}{$ad.content}{/if}</textarea>
                        <div class="hint">This text will be displayed as a styled banner on the hotspot page.</div>
                    </div>

                    <!-- Media upload -->
                    <div id="mediaField" class="form-group" style="margin-bottom:18px; display:none;">
                        <label class="fl">Upload Media File</label>
                        <input type="file" name="media_file" class="fc" id="mediaInput" accept="image/*,video/*">
                        <div class="hint" id="mediaHint"></div>

                        {if $ad && $ad.type != 'text' && $ad.content}
                            <div style="margin-top:12px;">
                                <p class="hint">Current file:</p>
                                {if $ad.type == 'video'}
                                    <video controls style="max-width:200px;border-radius:8px;">
                                        <source src="{$ad.content}">
                                    </video>
                                {else}
                                    <img src="{$ad.content}" style="max-width:200px;border-radius:8px;" alt="current">
                                {/if}
                                <div class="hint">Leave file input empty to keep current media.</div>
                            </div>
                        {/if}
                    </div>

                    <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:10px;">
                        <button type="submit" class="btn-save"><i class="fa fa-save"></i> Save Advertisement</button>
                        <a href="{$_url}plugin/hotspot_ads" class="btn-cancel"><i class="fa fa-times"></i> Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    {/if}

</div>
</div>

<script>
function toggleFields() {
    var type = document.getElementById('adTypeSelect').value;
    var textField  = document.getElementById('textField');
    var mediaField = document.getElementById('mediaField');
    var mediaHint  = document.getElementById('mediaHint');
    var mediaInput = document.getElementById('mediaInput');

    if (type === 'text') {
        textField.style.display  = 'block';
        mediaField.style.display = 'none';
        mediaInput.removeAttribute('required');
    } else {
        textField.style.display  = 'none';
        mediaField.style.display = 'block';
        var hints = {
            image: 'Accepted: JPG, PNG, WEBP — max 20 MB',
            gif:   'Accepted: GIF (animated) — max 20 MB',
            video: 'Accepted: MP4, WEBM, OGG — max 20 MB'
        };
        mediaHint.textContent = hints[type] || '';
        mediaInput.setAttribute('accept', type === 'video' ? 'video/*' : 'image/*');
    }
}
// Run on page load for edit page
toggleFields();
</script>

{include file="sections/footer.tpl"}
