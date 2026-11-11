<?php

/**
 * Hotspot Advertisement Manager Plugin
 * Manage ads (text, image, GIF, video) shown on the hotspot login page.
 * Each ad can be individually switched on/off.
 */

// Auto-create table and upload folder on every load
function hotspot_ads_ensure_setup()
{
    try {
        $db = ORM::getDb();
        $db->exec("CREATE TABLE IF NOT EXISTS tbl_hotspot_ads (
            id          INT(11) NOT NULL AUTO_INCREMENT,
            title       VARCHAR(255) NOT NULL,
            type        ENUM('text','image','gif','video') NOT NULL DEFAULT 'text',
            content     TEXT NOT NULL,
            status      ENUM('on','off') NOT NULL DEFAULT 'off',
            sort_order  INT(11) NOT NULL DEFAULT 0,
            created_at  DATETIME NOT NULL,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    } catch (Exception $e) {
        // silent - table may already exist
    }

    $uploadDir = realpath(__DIR__ . '/../uploads') . DIRECTORY_SEPARATOR . 'hotspot_ads';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
}

hotspot_ads_ensure_setup();

register_menu("Hotspot Ads", true, "hotspot_ads", "AFTER_SETTINGS", "ion ion-images", "New", "green", ["Admin", "SuperAdmin"]);

function hotspot_ads()
{
    global $ui, $routes;

    $action = $routes['2'] ?? 'list';
    $id     = isset($routes['3']) ? (int)$routes['3'] : null;

    // Public endpoint — accessible without admin login (called by hotspot page JS)
    if ($action === 'get_active') {
        hotspot_ads_get_active();
        return;
    }

    _admin();

    $ui->assign('_title', 'Hotspot Advertisement Manager');
    $ui->assign('_system_menu', 'hotspot_ads');
    $ui->assign('_admin', Admin::_info());

    switch ($action) {
        case 'add':    hotspot_ads_form(null);  break;
        case 'edit':   hotspot_ads_form($id);   break;
        case 'save':   hotspot_ads_save();      break;
        case 'delete': hotspot_ads_delete($id); break;
        case 'toggle': hotspot_ads_toggle($id); break;
        default:       hotspot_ads_list();      break;
    }
}

// ─── Public JSON endpoint (called by hotspot page JS) ────────────────────────
function hotspot_ads_get_active()
{
    $ads = ORM::for_table('tbl_hotspot_ads')
        ->where('status', 'on')
        ->order_by_asc('sort_order')
        ->order_by_asc('id')
        ->find_array();

    $result = [];
    foreach ($ads as $ad) {
        $result[] = [
            'type'    => $ad['type'],
            'content' => $ad['content'],
            'title'   => $ad['title'],
        ];
    }

    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    echo json_encode($result);
    exit;
}

// ─── List ─────────────────────────────────────────────────────────────────────
function hotspot_ads_list()
{
    global $ui;
    $ads = ORM::for_table('tbl_hotspot_ads')->order_by_asc('sort_order')->order_by_asc('id')->find_array();
    $ui->assign('ads', $ads);
    $ui->assign('view', 'list');
    $ui->display('hotspot_ads.tpl');
}

// ─── Form (add / edit) ───────────────────────────────────────────────────────
function hotspot_ads_form($id)
{
    global $ui;
    $ad = $id ? ORM::for_table('tbl_hotspot_ads')->find_one($id) : null;
    $ui->assign('ad', $ad ? $ad->as_array() : null);
    $ui->assign('view', 'form');
    $ui->display('hotspot_ads.tpl');
}

// ─── Save (insert / update) ──────────────────────────────────────────────────
function hotspot_ads_save()
{
    $id      = (int)_post('id');
    $title   = trim(strip_tags(_post('title')));
    $type    = _post('type');
    $status  = _post('status', 'off');
    $order   = (int)_post('sort_order');

    if (!in_array($type, ['text', 'image', 'gif', 'video'])) {
        r2(U . 'plugin/hotspot_ads', 'e', 'Invalid ad type.');
        return;
    }
    if (empty($title)) {
        r2(U . 'plugin/hotspot_ads', 'e', 'Title is required.');
        return;
    }

    $content = '';

    if ($type === 'text') {
        // Text — sanitise but allow basic formatting
        $content = htmlspecialchars(trim(_post('content')), ENT_QUOTES, 'UTF-8');
        if (empty($content)) {
            r2(U . 'plugin/hotspot_ads', 'e', 'Content is required for text ads.');
            return;
        }
    } else {
        // File upload (image / gif / video)
        if (!empty($_FILES['media_file']['name'])) {
            $uploadResult = hotspot_ads_upload_file($type);
            if ($uploadResult['ok']) {
                $content = $uploadResult['url'];
            } else {
                r2(U . 'plugin/hotspot_ads', 'e', $uploadResult['error']);
                return;
            }
        } elseif ($id) {
            // No new file — keep existing content
            $existing = ORM::for_table('tbl_hotspot_ads')->find_one($id);
            $content  = $existing ? $existing->content : '';
        } else {
            r2(U . 'plugin/hotspot_ads', 'e', 'Please upload a media file.');
            return;
        }
    }

    $ad = $id
        ? ORM::for_table('tbl_hotspot_ads')->find_one($id)
        : ORM::for_table('tbl_hotspot_ads')->create();

    if (!$ad) {
        r2(U . 'plugin/hotspot_ads', 'e', 'Ad not found.');
        return;
    }

    $ad->title      = $title;
    $ad->type       = $type;
    $ad->content    = $content;
    $ad->status     = $status === 'on' ? 'on' : 'off';
    $ad->sort_order = $order;
    if (!$id) {
        $ad->created_at = date('Y-m-d H:i:s');
    }
    $ad->save();

    r2(U . 'plugin/hotspot_ads', 's', 'Advertisement saved successfully.');
}

// ─── Delete ──────────────────────────────────────────────────────────────────
function hotspot_ads_delete($id)
{
    $ad = ORM::for_table('tbl_hotspot_ads')->find_one($id);
    if ($ad) {
        // Delete uploaded file if not a text ad
        if (in_array($ad->type, ['image', 'gif', 'video'])) {
            $uploadDir = realpath(__DIR__ . '/../uploads') . DIRECTORY_SEPARATOR . 'hotspot_ads' . DIRECTORY_SEPARATOR;
            $filename  = basename($ad->content);
            $filepath  = $uploadDir . $filename;
            if (is_file($filepath)) {
                unlink($filepath);
            }
        }
        $ad->delete();
    }
    r2(U . 'plugin/hotspot_ads', 's', 'Advertisement deleted.');
}

// ─── Toggle on/off ───────────────────────────────────────────────────────────
function hotspot_ads_toggle($id)
{
    $ad = ORM::for_table('tbl_hotspot_ads')->find_one($id);
    if ($ad) {
        $ad->status = ($ad->status === 'on') ? 'off' : 'on';
        $ad->save();
    }
    r2(U . 'plugin/hotspot_ads', 's', 'Status updated.');
}

// ─── File upload helper ───────────────────────────────────────────────────────
function hotspot_ads_upload_file($type)
{
    $allowedTypes = [
        'image' => ['image/jpeg', 'image/png', 'image/webp'],
        'gif'   => ['image/gif'],
        'video' => ['video/mp4', 'video/webm', 'video/ogg'],
    ];

    $allowed = $allowedTypes[$type] ?? [];
    $file    = $_FILES['media_file'];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['ok' => false, 'error' => 'File upload error code: ' . $file['error']];
    }

    $maxSize = 20 * 1024 * 1024; // 20 MB
    if ($file['size'] > $maxSize) {
        return ['ok' => false, 'error' => 'File too large. Maximum size is 20 MB.'];
    }

    // Validate MIME type using finfo (not user-supplied)
    $finfo    = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);

    if (!in_array($mimeType, $allowed, true)) {
        return ['ok' => false, 'error' => 'Invalid file type "' . htmlspecialchars($mimeType) . '" for ad type "' . $type . '".'];
    }

    $ext       = pathinfo($file['name'], PATHINFO_EXTENSION);
    $safeName  = bin2hex(random_bytes(8)) . '_' . time() . '.' . strtolower($ext);
    $uploadDir = realpath(__DIR__ . '/../uploads') . DIRECTORY_SEPARATOR . 'hotspot_ads' . DIRECTORY_SEPARATOR;
    $destPath  = $uploadDir . $safeName;

    if (!move_uploaded_file($file['tmp_name'], $destPath)) {
        return ['ok' => false, 'error' => 'Failed to save uploaded file.'];
    }

    $url = APP_URL . '/system/uploads/hotspot_ads/' . $safeName;
    return ['ok' => true, 'url' => $url];
}
