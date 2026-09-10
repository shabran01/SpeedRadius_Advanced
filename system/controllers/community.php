<?php
/**
 *  PHP Mikrotik Billing (https://github.com/hotspotbilling/phpnuxbill/)
 *  by https://t.me/ibnux
 **/

_admin();
$ui->assign('_title', 'Community');
$ui->assign('_system_menu', 'community');

$action = $routes['1'];
$ui->assign('_admin', $admin);

// Auto-detect current version from CHANGELOG.md (single source of truth)
$version = '';
$changelogFile = dirname(__DIR__, 2) . '/CHANGELOG.md';
if (is_readable($changelogFile)) {
    $head = file_get_contents($changelogFile, false, null, 0, 4096);
    if ($head && preg_match('/^##\s*\[([0-9]+\.[0-9]+\.[0-9]+)\]/m', $head, $m)) {
        $version = $m[1];
    }
}
if ($version === '') {
    $vj = json_decode(@file_get_contents(dirname(__DIR__, 2) . '/version.json'), true);
    $version = (is_array($vj) && !empty($vj['version'])) ? $vj['version'] : '';
}
$ui->assign('version', $version);

switch ($action) {
    case 'rollback':
        $ui->assign('_title', 'Rollback Update');
        $masters = json_decode(Http::getData("https://api.github.com/repos/hotspotbilling/phpnuxbill/commits?per_page=100",['User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:125.0) Gecko/20100101 Firefox/125.0']), true);
        $devs = json_decode(Http::getData("https://api.github.com/repos/hotspotbilling/phpnuxbill/commits?sha=Development&per_page=100",['User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10.15; rv:125.0) Gecko/20100101 Firefox/125.0']), true);

        $ui->assign('masters', $masters);
        $ui->assign('devs', $devs);
        $ui->display('community-rollback.tpl');
        break;
    default:
        $ui->display('community.tpl');
}