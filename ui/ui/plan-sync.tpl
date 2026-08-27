{include file="sections/header.tpl"}

<script src="ui/ui/scripts/jquery.min.js"></script>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-primary panel-hovered mb20 panel-stacked">
            <div class="panel-heading">
                <div class="btn-group pull-right">
                    <a class="btn btn-danger btn-xs" title="back" href="{$_url}plan/list"><span class="glyphicon glyphicon-arrow-left"></span> {Lang::T('Back')}</a>
                </div>
                {Lang::T('Sync Users to Router')}
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> {Lang::T('This will sync active customers to their respective routers. Total users to sync')}: <strong id="totalUsersLabel">{$totalUsers}</strong>
                        </div>

                        {if !$isViewer}
                        <div class="form-group">
                            <label for="routerSelect"><strong>Sync Scope</strong></label>
                            <select id="routerSelect" class="form-control" style="max-width:400px;">
                                <option value=""{if $syncRouter eq ''} selected{/if}>All Routers</option>
                                {foreach $allRouters as $r}
                                <option value="{$r}"{if $syncRouter eq $r} selected{/if}>{$r}</option>
                                {/foreach}
                            </select>
                            <p class="help-block">Choose a specific router to sync only its customers, or leave as <em>All Routers</em>.</p>
                        </div>

                        <div class="form-group">
                            <label for="typeSelect"><strong>Service Type</strong></label>
                            <select id="typeSelect" class="form-control" style="max-width:400px;">
                                <option value=""{if $syncType eq ''} selected{/if}>All Types</option>
                                <option value="Hotspot"{if $syncType eq 'Hotspot'} selected{/if}>Hotspot Only</option>
                                <option value="PPPOE"{if $syncType eq 'PPPOE'} selected{/if}>PPPoE Only</option>
                            </select>
                            <p class="help-block">Choose a service type to sync only those customers, or leave as <em>All Types</em>.</p>
                        </div>
                        {/if}
                        
                        {if $isViewer}
                            <div class="alert alert-warning">
                                <i class="fa fa-eye"></i> <strong>Read-Only Mode:</strong> You can view the sync interface but cannot start the sync process as a Viewer.
                            </div>
                        {/if}
                        
                        <div id="syncProgress" style="display:none;">
                            <div class="progress">
                                <div class="progress-bar progress-bar-striped active" role="progressbar" 
                                     id="progressBar" style="width: 0%">
                                    <span id="progressText">0%</span>
                                </div>
                            </div>
                            
                            <div class="alert alert-info" id="statusMessage">
                                <i class="fa fa-spinner fa-spin"></i> Initializing sync...
                            </div>
                            
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h4 class="panel-title">Sync Statistics</h4>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <h3 class="text-primary" id="processedCount">0</h3>
                                                <p>Processed</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <h3 class="text-success" id="successCount">0</h3>
                                                <p>Success</p>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="text-center">
                                                <h3 class="text-danger" id="errorCount">0</h3>
                                                <p>Errors</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div id="syncResults" style="max-height: 400px; overflow-y: auto; margin-top: 20px;">
                                <!-- Results will be appended here -->
                            </div>
                        </div>
                        
                        <div id="syncControls">
                            {if $isViewer}
                                <button type="button" class="btn btn-default btn-lg btn-block" disabled>
                                    <i class="fa fa-eye"></i> Read-Only Mode - Cannot Start Sync
                                </button>
                            {else}
                                <button type="button" class="btn btn-primary btn-lg btn-block" id="startSyncBtn">
                                    <i class="fa fa-refresh"></i> Start Sync
                                </button>
                            {/if}
                        </div>
                        
                        <div id="syncComplete" style="display:none;">
                            <div class="alert alert-success">
                                <i class="fa fa-check-circle"></i> <strong>Sync completed!</strong>
                                <div id="finalStats"></div>
                            </div>
                            <a href="{$_url}plan/list" class="btn btn-primary">
                                <i class="fa fa-list"></i> Go to Customer List
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let totalProcessed = 0;
    let totalSuccess = 0;
    let totalErrors = 0;
    let offset = 0;
    let totalUsers = {$totalUsers};
    let isSyncing = false;
    let selectedRouter = '{$syncRouter|escape:"javascript"}';
    let selectedType = '{$syncType|escape:"javascript"}';
    
    // Update banner count when router dropdown changes
    $('#routerSelect').on('change', function() {
        selectedRouter = $(this).val();
        let router = selectedRouter;
        let type = $('#typeSelect').length ? $('#typeSelect').val() : selectedType;
        $.getJSON('{$_url}plan/sync-process', { offset: 0, limit: 0, router: router, type: type, count_only: 1 })
            .done(function(data) {
                if (data && data.stats) {
                    totalUsers = data.stats.total;
                    $('#totalUsersLabel').text(totalUsers);
                }
            });
    });

    // Update banner count when service type dropdown changes
    $('#typeSelect').on('change', function() {
        selectedType = $(this).val();
        let type = selectedType;
        let router = $('#routerSelect').length ? $('#routerSelect').val() : selectedRouter;
        $.getJSON('{$_url}plan/sync-process', { offset: 0, limit: 0, router: router, type: type, count_only: 1 })
            .done(function(data) {
                if (data && data.stats) {
                    totalUsers = data.stats.total;
                    $('#totalUsersLabel').text(totalUsers);
                }
            });
    });

    // Test if jQuery is working
    console.log('jQuery version:', $.fn.jquery);
    console.log('Document ready, button found:', $('#startSyncBtn').length);
    
    $('#startSyncBtn').click(function(e) {
        e.preventDefault();
        console.log('Sync button clicked');
        
        if (isSyncing) {
            console.log('Already syncing, returning');
            return;
        }
        
        selectedRouter = $('#routerSelect').length ? $('#routerSelect').val() : selectedRouter;
        selectedType = $('#typeSelect').length ? $('#typeSelect').val() : selectedType;
        let typeLabel = selectedType ? (selectedType == 'PPPOE' ? 'PPPoE' : selectedType) : 'All Types';
        let confirmMsg = selectedRouter
            ? 'Sync active ' + typeLabel + ' users on router [' + selectedRouter + '] to Mikrotik?'
            : 'Sync ALL active ' + typeLabel + ' users across all routers to Mikrotik? This may take several minutes.';
        if (!confirm(confirmMsg)) {
            console.log('User cancelled sync');
            return;
        }
        
        // Lock the dropdowns during sync
        $('#routerSelect').prop('disabled', true);
        $('#typeSelect').prop('disabled', true);
        console.log('Starting sync process, router filter:', selectedRouter || 'ALL', '| type filter:', selectedType || 'ALL');
        isSyncing = true;
        $('#syncControls').hide();
        $('#syncProgress').show();
        syncNextBatch();
    });
    
    function syncNextBatch() {
        console.log('Starting syncNextBatch, offset:', offset);
        let ajaxData = { offset: offset };
        if (selectedRouter) { ajaxData.router = selectedRouter; }
        if (selectedType) { ajaxData.type = selectedType; }
        $.ajax({
            url: '{$_url}plan/sync-process',
            method: 'GET',
            data: ajaxData,
            dataType: 'json',
            timeout: 60000, // 60 seconds timeout per batch
            success: function(response) {
                console.log('AJAX success:', response);
                if (response.success) {
                    // Sync totalUsers from server (handles router filter applied after page load)
                    totalUsers = response.stats.total;
                    $('#totalUsersLabel').text(totalUsers);

                    // Update statistics
                    totalProcessed += response.stats.processed;
                    totalSuccess += response.stats.success;
                    totalErrors += response.stats.errors;
                    
                    $('#processedCount').text(totalProcessed);
                    $('#successCount').text(totalSuccess);
                    $('#errorCount').text(totalErrors);
                    
                    // Update progress bar
                    let percentage = totalUsers > 0 ? Math.round((totalProcessed / totalUsers) * 100) : 100;
                    $('#progressBar').css('width', percentage + '%');
                    $('#progressText').text(percentage + '%');
                    
                    // Update status message
                    $('#statusMessage').html(
                        '<i class="fa fa-spinner fa-spin"></i> Processing: ' + 
                        totalProcessed + ' of ' + totalUsers + ' users...'
                    );
                    
                    // Display results
                    response.results.forEach(function(result) {
                        let alertClass = result.status === 'success' ? 'alert-success' : 'alert-danger';
                        let icon = result.status === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
                        
                        let resultHtml = '<div class="alert ' + alertClass + ' alert-dismissible" style="margin-bottom: 5px;">' +
                            '<button type="button" class="close" data-dismiss="alert">&times;</button>' +
                            '<i class="fa ' + icon + '"></i> <strong>' + result.username + '</strong>: ' + 
                            result.message;
                        
                        if (result.plan) {
                            resultHtml += ' | Plan: ' + result.plan;
                        }
                        if (result.router) {
                            resultHtml += ' | Router: ' + result.router;
                        }
                        
                        resultHtml += '</div>';
                        $('#syncResults').append(resultHtml);
                    });
                    
                    // Auto-scroll to bottom
                    $('#syncResults').scrollTop($('#syncResults')[0].scrollHeight);
                    
                    // Check if more batches to process
                    if (response.stats.hasMore) {
                        offset += response.stats.processed;
                        setTimeout(syncNextBatch, 500); // Small delay between batches
                    } else {
                        // Sync complete
                        completeSyncProcess();
                    }
                } else {
                    showError('Sync failed: ' + (response.message || 'Unknown error'));
                }
            },
            error: function(xhr, status, error) {
                console.log('AJAX error:', xhr, status, error);
                console.log('Response text:', xhr.responseText);
                let errorMsg = 'Connection error: ';
                if (status === 'timeout') {
                    errorMsg += 'Request timeout. Will retry...';
                    // Retry after timeout
                    setTimeout(syncNextBatch, 2000);
                } else {
                    errorMsg += error;
                    showError(errorMsg);
                }
            }
        });
    }
    
    function completeSyncProcess() {
        isSyncing = false;
        $('#routerSelect').prop('disabled', false);
        $('#progressBar').removeClass('active');
        $('#statusMessage').html(
            '<i class="fa fa-check-circle"></i> Sync completed successfully!'
        ).removeClass('alert-info').addClass('alert-success');
        
        let statsHtml = '<ul>' +
            '<li>Total Processed: ' + totalProcessed + '</li>' +
            '<li>Successful: ' + totalSuccess + '</li>' +
            '<li>Errors: ' + totalErrors + '</li>' +
            '</ul>';
        
        $('#finalStats').html(statsHtml);
        $('#syncComplete').show();
    }
    
    function showError(message) {
        isSyncing = false;
        $('#routerSelect').prop('disabled', false);
        $('#statusMessage').html(
            '<i class="fa fa-exclamation-triangle"></i> ' + message
        ).removeClass('alert-info').addClass('alert-danger');
        
        $('#syncControls').show();
        $('#startSyncBtn').html('<i class="fa fa-refresh"></i> Retry Sync');
    }
});
</script>

{include file="sections/footer.tpl"}
