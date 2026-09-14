{if in_array($_admin['user_type'], ['SuperAdmin', 'Admin'])}
<div class="row">
    <!-- CPU Usage -->
    <div class="col-md-4">
        <div class="small-box" style="background: linear-gradient(135deg, #4158D0 0%, #C850C0 100%); color: white;">
            <div class="inner">
                <h4 class="text-bold mb-0" style="font-size: 24px; font-family: 'Segoe UI', sans-serif;">
                    <span class="amount">{$server_stats.cpu.load_1min}%</span>
                </h4>
                <p style="font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">{Lang::T('CPU Load')}</p>
                <div class="progress" style="height: 4px; margin-bottom: 4px; background: rgba(255,255,255,0.2);">
                    <div class="progress-bar" style="width: {$server_stats.cpu.load_1min}%; background: rgba(255,255,255,0.8);"></div>
                </div>
                <small style="color: rgba(255,255,255,0.9); font-size: 11px;">
                    1min: {$server_stats.cpu.load_1min}% | 5min: {$server_stats.cpu.load_5min}% | 15min: {$server_stats.cpu.load_15min}%
                </small>
            </div>
            <div class="icon" style="color: rgba(255,255,255,0.2);">
                <i class="fas fa-microchip"></i>
            </div>
        </div>
    </div>
    
    <!-- Memory Usage -->
    <div class="col-md-4">
        <div class="small-box" style="background: linear-gradient(135deg, #43CBFF 0%, #9708CC 100%); color: white;">
            <div class="inner">
                <h4 class="text-bold mb-0" style="font-size: 24px; font-family: 'Segoe UI', sans-serif;">
                    <span class="amount">{$server_stats.memory.percentage}%</span>
                </h4>
                <p style="font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">{Lang::T('Memory Usage')}</p>
                <div class="progress" style="height: 4px; margin-bottom: 4px; background: rgba(255,255,255,0.2);">
                    <div class="progress-bar" style="width: {$server_stats.memory.percentage}%; background: rgba(255,255,255,0.8);"></div>
                </div>
                <small style="color: rgba(255,255,255,0.9); font-size: 11px;">
                    {Lang::T('Used')}: {$server_stats.memory.used}MB | {Lang::T('Free')}: {$server_stats.memory.free}MB
                </small>
            </div>
            <div class="icon" style="color: rgba(255,255,255,0.2);">
                <i class="fas fa-memory"></i>
            </div>
        </div>
    </div>
    
    <!-- Disk Usage -->
    <div class="col-md-4">
        <div class="small-box" style="background: linear-gradient(135deg, #00B4DB 0%, #0083B0 100%); color: white;">
            <div class="inner">
                <h4 class="text-bold mb-0" style="font-size: 24px; font-family: 'Segoe UI', sans-serif;">
                    <span class="amount">{$server_stats.disk.percentage}%</span>
                </h4>
                <p style="font-size: 14px; text-transform: uppercase; letter-spacing: 1px;">{Lang::T('Disk Usage')}</p>
                <div class="progress" style="height: 4px; margin-bottom: 4px; background: rgba(255,255,255,0.2);">
                    <div class="progress-bar" style="width: {$server_stats.disk.percentage}%; background: rgba(255,255,255,0.8);"></div>
                </div>
                <small style="color: rgba(255,255,255,0.9); font-size: 11px;">
                    {Lang::T('Used')}: {$server_stats.disk.used}GB | {Lang::T('Free')}: {$server_stats.disk.free}GB
                </small>
            </div>
            <div class="icon" style="color: rgba(255,255,255,0.2);">
                <i class="fas fa-hdd"></i>
            </div>
        </div>
    </div>
</div>
{/if}
