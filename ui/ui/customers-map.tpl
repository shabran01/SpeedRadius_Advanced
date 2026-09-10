{include file="sections/header.tpl"}

<!-- Modern Customer Map -->
<div class="add-customer-modern">

    <div class="page-header">
        <a href="{$_url}customers/list" class="back-link"><i class="fa fa-arrow-left"></i></a>
        <h3><i class="fa fa-map-marker" style="color:#667eea;margin-right:8px;"></i>{Lang::T('Customer Geo Location Information')}</h3>
        <span class="map-count-badge">{$totalCount} {Lang::T('Customers')}</span>
    </div>

    <!-- Search Bar -->
    <form method="post" action="{$_url}map/customer/" class="map-search-form">
        <input type="hidden" name="_route" value="map/customer">
        <div class="search-row">
            <i class="fa fa-search search-icon"></i>
            <input type="text" name="search" class="search-input" value="{$search}" placeholder="{Lang::T('Search by name, username, email or phone')}...">
            <button class="search-btn" type="submit">{Lang::T('Search')}</button>
            {if $search}
                <a href="{$_url}map/customer/" class="search-clear"><i class="fa fa-times"></i> {Lang::T('Clear')}</a>
            {/if}
        </div>
    </form>

    <!-- Map -->
    <div class="map-card">
        <div id="map" class="map-container"></div>
        {if empty($customers)}
            <div class="map-empty">
                <i class="fa fa-map-o"></i>
                <p>{Lang::T('No customers with location data found')}</p>
                <small>{Lang::T('Add coordinates to customers to see them on the map')}</small>
            </div>
        {/if}
    </div>

</div>

<style>
.add-customer-modern { padding: 0 15px 30px; max-width: 100%; margin: 0 auto; }
.add-customer-modern * { box-sizing: border-box; }

.page-header { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; flex-wrap: wrap; }
.page-header h3 { margin: 0; font-weight: 700; color: #1a1a2e; font-size: 16px; }
.back-link { display: inline-flex; align-items: center; justify-content: center; width: 36px; height: 36px; border-radius: 10px; background: #f1f5f9; color: #475569; font-size: 14px; text-decoration: none; transition: all .2s; }
.back-link:hover { background: #e2e8f0; color: #1e293b; }

.map-count-badge { font-size: 12px; font-weight: 700; padding: 4px 12px; border-radius: 20px; background: #eef2ff; color: #4f46e5; }

/* Search */
.map-search-form { margin-bottom: 14px; }
.search-row { display: flex; align-items: center; gap: 0; background: #fff; border-radius: 10px; box-shadow: 0 2px 12px rgba(0,0,0,0.06); overflow: hidden; }
.search-icon { color: #94a3b8; padding: 0 12px; font-size: 14px; flex-shrink: 0; }
.search-input { flex: 1; border: none; padding: 10px 8px; font-size: 13px; color: #1e293b; background: transparent; outline: none; min-width: 0; }
.search-btn { border: none; background: #667eea; color: #fff; padding: 10px 18px; font-size: 12px; font-weight: 700; cursor: pointer; transition: background .2s; white-space: nowrap; }
.search-btn:hover { background: #5a6fd6; }
.search-clear { padding: 10px 14px; font-size: 12px; color: #ef4444; text-decoration: none; white-space: nowrap; }
.search-clear:hover { color: #dc2626; background: #fef2f2; }

/* Map Card */
.map-card { position: relative; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.06); background: #f8fafc; }
.map-container { width: 100%; height: 70vh; min-height: 400px; z-index: 1; position: relative; }

/* Empty state overlay */
.map-empty { position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); text-align: center; color: #94a3b8; z-index: 0; }
.map-empty i { font-size: 48px; display: block; margin-bottom: 12px; }
.map-empty p { font-size: 15px; font-weight: 600; margin: 0 0 4px; }
.map-empty small { font-size: 12px; }

@media (max-width: 768px) {
    .map-container { height: 50vh; }
    .search-row { flex-wrap: wrap; }
    .search-btn { border-radius: 0; }
}
</style>

{literal}
<script>
var _baseUrl = '{/literal}{$_url}{literal}';

function getLocation() {
    if (window.location.protocol == "https:" && navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition);
    } else {
        setupMap(-1.2921, 36.8219);
    }
}

function showPosition(position) {
    setupMap(position.coords.latitude, position.coords.longitude);
}

function setupMap(lat, lon) {
    var mapEl = document.getElementById('map');
    if (!mapEl) return;

    var map = L.map('map').setView([lat, lon], 13);
    var group = L.featureGroup().addTo(map);

    var customers = {/literal}{$customers|json_encode}{literal};

    // Satellite tiles (Google Maps)
    L.tileLayer('https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}', {
        attribution: '&copy; Google',
        maxZoom: 20
    }).addTo(map);

    if (!customers || customers.length === 0) {
        return;
    }

    customers.forEach(function(customer) {
        var cs = JSON.parse(customer.coordinates);
        var popupContent =
            "<strong>" + customer.name + "</strong><br>" +
            "<small>" + customer.info + "</small><br>" +
            "<strong>{/literal}{Lang::T('Balance')}{literal}:</strong> " + customer.balance + "<br>" +
            "<strong>{/literal}{Lang::T('Address')}{literal}:</strong> " + customer.address + "<br>" +
            "<a href='" + _baseUrl + "customers/view/" + customer.id + "'>{/literal}{Lang::T('More Info')}{literal}</a> &bull; " +
            "<a href='https://www.google.com/maps/dir//" + customer.direction + "' target='_blank'>{/literal}{Lang::T('Get Direction')}{literal}</a>";

        var marker = L.marker(cs).addTo(group);
        marker.bindTooltip(customer.name, { permanent: true, direction: 'top' }).bindPopup(popupContent);
    });

    map.fitBounds(group.getBounds().pad(0.1));
}

window.onload = function() {
    getLocation();
}
</script>
{/literal}

{include file="sections/footer.tpl"}