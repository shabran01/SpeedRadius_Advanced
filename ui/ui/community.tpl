{include file="sections/header.tpl"}

<center><a href="https://speedcomwifi.xyz" target="_blank"><img src="https://raw.githubusercontent.com/shabran01/SpeedRadius_Advanced/refs/heads/main/install/img/logo.png" class="img-responsive"></a></center>
<br><br>

<div class="row">
    <div class="col-sm-6">
        <div class="box box-hovered mb20 box-primary">
            <div class="box-header">
                <h3 class="box-title">Whatsapp Gateway</h3>
            </div>
            <div class="box-body">Whatsapp API gateway service for sending and receiving messages, notification, scheduler, reminder, tracking and store platform for your business. </div>
            <div class="box-footer">
                <div class="btn-group btn-group-justified" role="group" aria-label="...">
                    <a href="https://wa.nux.my.id/login" target="_blank"
                        class="btn btn-primary btn-sm btn-block"><i class="ion ion-chatboxes"></i> Whatsapp Gateway </a>
                    <a href="https://chat.whatsapp.com/HjnLYIEN6h0A0KMXbfNYP5" target="_blank" class="btn btn-primary btn-sm btn-block"><i
                            class="ion ion-chatboxes"></i> Whatsapp Group</a>
                </div>
            </div>
        </div>
    </div>
   

    <div class="col-sm-6">
        <div class="box box-hovered mb20 box-primary">
            <div class="box-header">
                <h3 class="box-title">Donations</h3>
            </div>
            <div class="box-body">
                Donations will help to continue Speedcom development
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <tbody>
                        <tr>
                            <td>PayPal</td>
                            <td>Pay1@cloudpoa.co.ke</td>
                        </tr>
                        <tr>
                            <td>Mpesa Kenya</td>
                            <td>0718167262</td>
                        </tr>
                     
                       
                    </tbody>
                </table>
            </div>
            <div class="box-footer">
                <div class="btn-group btn-group-justified" role="group" aria-label="...">
                    <a href="https://www.paypal.com/ncp/payment/P9N25YSCKMVAS" target="_blank"
                        class="btn btn-primary btn-sm btn-block">Paypal Donate</a>
                    <a href="https://www.paypal.com/ncp/payment/Y9JS7KVJ5PZJG" target="_blank"
                        class="btn btn-primary btn-sm btn-block">BUY</a>
                </div>
            </div>
        </div>
    </div>
</div>

    <div class="col-sm-6" id="update">
        <div class="box box-primary box-hovered mb20 activities">
            <div class="box-header">
                <h3 class="box-title">SPEEDRADIUS</h3>
            </div>
            <div class="box-body">
                <b>SpeedRadius</b> is a billing Hotspot ,Static IP and PPPOE for Mikrotik using PHP and Mikrotik API to comunicate with router. If you get more profit with this application, please donate us.<br>Watch project <a href="https://chat.whatsapp.com/HjnLYIEN6h0A0KMXbfNYP5" target="_blank">in here</a>
            </div>
            <div class="box-footer" id="currentVersion">ver : <b><?= $version ?></b> </div>
            <div class="box-footer" id="latestVersion">ver : <b><?= $version ?></b> </div>
            <div class="box-footer">

                <div class="btn-group btn-group-justified" role="group" aria-label="...">
                    <a href="/update.php?step=4" class="btn btn-success btn-sm btn-block">Update Database</a>
                    <a href="./update.php" target="_blank"
                        class="btn btn-warning btn-sm btn-block text-black">Install Latest Version</a>
                </div>
                
            <div class="box-footer">
                <div class="btn-group btn-group-justified" role="group" aria-label="...">
                    <a href="./CHANGELOG.md" target="_blank" class="btn btn-default btn-sm btn-block">Current
                        Changelog</a>
                    <a href="https://github.com/shabran01/SpeedRadius_Advanced/blob/main/CHANGELOG.md" target="_blank"
                        class="btn btn-default btn-sm btn-block">Repo Changelog</a>
                </div>
            </div>
            <div class="box-footer">
               
			   
            </div>
        </div>
    </div>
</div>
<script>
    window.addEventListener('DOMContentLoaded', function() {
        $.getJSON("./version.json?" + Math.random(), function(data) {
            $('#currentVersion').html('Current Version: ' + data.version);
        });
        $.getJSON("https://github.com/shabran01/SpeedRadius_Advanced/blob/main/version.json?" + Math
            .random(),
            function(data) {
                $('#latestVersion').html('Latest Version: ' + data.version);
            });
    });
</script>
{include file="sections/footer.tpl"}
