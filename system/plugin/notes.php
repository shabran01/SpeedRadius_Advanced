<?php
/**
 * Notes Plugin for PHP Mikrotik Billing
 * Allows administrators to save notes with title and description
 */

// Auto-create table if not exists
function notes_ensure_table_exists() {
    try {
        $db = ORM::getDb();
        $db->exec("CREATE TABLE IF NOT EXISTS tbl_notes (
            id INT(11) NOT NULL AUTO_INCREMENT,
            title VARCHAR(255) NOT NULL,
            description TEXT,
            created_at DATETIME NOT NULL,
            updated_at DATETIME NOT NULL,
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    } catch (Exception $e) {
        // Silent fail - table might already exist or DB not available
    }
}

// Ensure table exists on every load
notes_ensure_table_exists();

register_menu(" Notepad", true, "notes_ui", 'AFTER_SETTINGS', 'ion ion-clipboard', "New", "blue");

function notes_ui()
{
    global $ui, $routes;
    _admin();
    $ui->assign('_title', 'Notepad');
    $ui->assign('_system_menu', 'settings');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);

    $action = $routes['2'] ?? 'list';
    $noteId = $routes['3'] ?? null;

    switch ($action) {
        case 'add':
            notes_add();
            break;
        case 'edit':
            notes_edit($noteId);
            break;
        case 'delete':
            notes_delete($noteId);
            break;
        case 'view':
            notes_view($noteId);
            break;
        default:
            notes_list();
            break;
    }
}

function notes_list()
{
    global $ui;
    
    $notes = ORM::for_table('tbl_notes')
        ->order_by_desc('created_at')
        ->find_many();
    
    $ui->assign('notes', $notes);
    $ui->display('notes.tpl');
}

function notes_add()
{
    global $ui;
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = _post('title');
        $description = _post('description');
        
        if (empty($title)) {
            _alert(Lang::T('Title is required'), 'danger', "notes/ui");
        }
        
        $note = ORM::for_table('tbl_notes')->create();
        $note->title = $title;
        $note->description = $description;
        $note->created_at = date('Y-m-d H:i:s');
        $note->updated_at = date('Y-m-d H:i:s');
        $note->save();
        
        _log('Added note: ' . $title, 'Admin', Admin::getID());
        _alert(Lang::T('Note added successfully'), 'success', "notes/ui");
    }
    
    $ui->display('notes_add.tpl');
}

function notes_edit($id)
{
    global $ui;
    
    $note = ORM::for_table('tbl_notes')->find_one($id);
    
    if (!$note) {
        _alert(Lang::T('Note not found'), 'danger', "notes/ui");
    }
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $title = _post('title');
        $description = _post('description');
        
        if (empty($title)) {
            _alert(Lang::T('Title is required'), 'danger', "notes/ui/edit/$id");
        }
        
        $note->title = $title;
        $note->description = $description;
        $note->updated_at = date('Y-m-d H:i:s');
        $note->save();
        
        _log('Updated note: ' . $title, 'Admin', Admin::getID());
        _alert(Lang::T('Note updated successfully'), 'success', "notes/ui");
    }
    
    $ui->assign('note', $note);
    $ui->display('notes_edit.tpl');
}

function notes_delete($id)
{
    $note = ORM::for_table('tbl_notes')->find_one($id);
    
    if (!$note) {
        _alert(Lang::T('Note not found'), 'danger', "notes/ui");
    }
    
    $title = $note->title;
    $note->delete();
    
    _log('Deleted note: ' . $title, 'Admin', Admin::getID());
    _alert(Lang::T('Note deleted successfully'), 'success', "notes/ui");
}

function notes_view($id)
{
    global $ui;
    
    $note = ORM::for_table('tbl_notes')->find_one($id);
    
    if (!$note) {
        _alert(Lang::T('Note not found'), 'danger', "notes/ui");
    }
    
    $ui->assign('note', $note);
    $ui->display('notes_view.tpl');
}
