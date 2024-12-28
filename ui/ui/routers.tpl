{include file="sections/header.tpl"}

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-hovered mb20 panel">
            <div class="panel-heading">{Lang::T('Routers')}</div>
            <div class="panel-body">
                <div class="md-whiteframe-z1 mb20 text-center" style="padding: 15px">
                    <div class="col-md-8">
                        <form id="site-search" method="post" action="{$_url}routers/list/">
                            <div class="input-group">
                                <div class="input-group-addon">
                                    <span class="fa fa-search"></span>
                                </div>
                                <input type="text" name="name" class="form-control"
                                    placeholder="{Lang::T('Search by Name')}...">
                                <div class="input-group-btn">
                                    <button class="btn btn-success" type="submit">{Lang::T('Search')}</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="col-md-4">
                        <a href="{$_url}routers/add" class="btn btn-primary btn-block"><i
                                class="ion ion-android-add"> </i> {Lang::T('New Router')}</a>
                    </div>&nbsp;
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-condensed">
                        <thead>
                            <tr>
                                <th>{Lang::T('Router Name')}</th>
                                <th>{Lang::T('IP Address')}</th>
                                <th>{Lang::T('Status')}</th>
                                <th>{Lang::T('Uptime')}</th>
                                <th>{Lang::T('Free Memory')}</th>
                                <th>{Lang::T('CPU Load')}</th>
                                <th>{Lang::T('Manage')}</th>
                                <th>ID</th>
                                <th>{Lang::T('Download')}</th>
                            </tr>
                        </thead>
                        <tbody>
                            {foreach $d as $ds}
                                <tr {if $ds['enabled'] != 1}class="danger" title="disabled" {/if}>
                                    <td>{$ds['name']}</td>
                                    <td>{$ds['ip_address']}</td>
                                    <td class="router-status" data-router-id="{$ds['id']}"></td>
                                    <td class="router-uptime" data-router-id="{$ds['id']}"></td>
                                    <td class="router-used-memory" data-router-id="{$ds['id']}"></td>
                                    <td class="router-cpu-load" data-router-id="{$ds['id']}"></td>
                                    <td>
                                        <a href="{$_url}routers/edit/{$ds['id']}" class="btn btn-info btn-xs">{Lang::T('Edit')}</a>
                                        <a href="{$_url}routers/delete/{$ds['id']}" id="{$ds['id']}"
                                            onclick="return confirm('{Lang::T('Delete')}?')"
                                            class="btn btn-danger btn-xs"><i class="glyphicon glyphicon-trash"></i></a>
                                        <button class="btn btn-warning btn-xs btn-reboot" data-router-id="{$ds['id']}"><i class="fa fa-refresh"></i> {Lang::T('Reboot')}</button>
                                    </td>
                                    <td>{$ds['id']}</td>
                                    <td>
                                        <form action="{$_url}routers/download" method="post" style="display:inline;">
                                            <input type="hidden" name="router_id" value="{$ds['id']}">
                                            <input type="hidden" name="router_name" value="{$ds['name']}">
                                            <button type="submit" class="btn btn-success btn-xs">
                                                <i class="fa fa-download"></i> {Lang::T('Download')}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    </table>
                </div>
                {include file="pagination.tpl"}
            </div>
        </div>
    </div>
</div>

{include file="sections/footer.tpl"}

<style>
    .loader {
        display: inline-block;
        animation: loading 1s infinite;
        margin: 0; /* Added this line */
    }

    @keyframes loading {
        0% {
            content: ".";
        }
        33% {
            content: "..";
        }
        66% {
            content: "...";
        }
    }

    .router-uptime {
        color: #28a745; /* Green for uptime */
	font-weight: bold;
    }

    .router-used-memory {
        color: #fd7e14; /* Orange for memory */
	font-weight: bold;
    }

    .router-cpu-load {
        color: #d63384; /* Pink for CPU load */
	font-weight: bold;
    }

    .router-status {
        font-weight: bold;
    }

    .router-status .status {
        display: inline-block;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 12px;
    }

    .router-status .online {
        background-color: #28a745;
        color: white;
    }

    .router-status .offline {
        background-color: #dc3545;
        color: white;
    }
</style>

<script>
$(document).ready(function() {
    // Fetch router resources asynchronously
    $('.router-uptime, .router-used-memory, .router-total-memory, .router-cpu-load, .router-status').each(function() {
        var routerId = $(this).data('router-id');
        var elementClass = $(this).attr('class').split(' ')[0];
        var row = $(this).closest('tr');

        $.ajax({
            url: '{$_url}routers/get_resources',
            data: { router_id: routerId },
            dataType: 'json',
            success: function(resources) {
                if (resources) {
                    if (elementClass === 'router-uptime') {
                        row.find('.router-uptime').html(resources.uptime);
                    } else if (elementClass === 'router-used-memory') {
                        row.find('.router-used-memory').html(resources.freeMemory);
                    } else if (elementClass === 'router-total-memory') {
                        row.find('.router-total-memory').html(resources.totalMemory);
                    } else if (elementClass === 'router-cpu-load') {
                        row.find('.router-cpu-load').html(resources.cpuLoad);
                    }

                    // Update router status
                    var statusElement = row.find('.router-status');
                    if (resources.status === 'Online') {
                        statusElement.html('<span class="status online">Online</span>');
                    } else {
                        statusElement.html('<span class="status offline">Offline</span>');
                    }
                } else {
                    row.find('.' + elementClass).html('N/A');
                    // Set router status to offline if resources are not available
                    var statusElement = row.find('.router-status');
                    statusElement.html('<span class="status offline">Offline</span>');
                }
            },
            error: function(xhr, status, error) {
                // Handle error cases
                console.error(xhr.responseText);
            }
        });
    });

    // Reboot router
    $('.btn-reboot').on('click', function() {
        var routerId = $(this).data('router-id');
        if (confirm('Are you sure you want to reboot this router?')) {
            $.ajax({
                url: '{$_url}routers/reboot',
                data: { router_id: routerId },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'Rebooting') {
                        alert(response.message);
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(xhr.responseText);
                    alert('Failed to send reboot command. Please try again.');
                }
            });
        }
    });
});
</script>
