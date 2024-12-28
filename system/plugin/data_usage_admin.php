<?php
register_menu("User Data Usage", true, "UserDataUsageAdmin",
    'SERVICES', 'fa fa-bar-chart');

function UserDataUsageAdmin()
{
    global $ui;
    _admin();
    $ui->assign('_title', 'DataUsage');
    $ui->assign('_system_menu', '');
    $admin = Admin::_info();
    $ui->assign('_admin', $admin);
    $search = $_POST['q'] ?? '';
    $page = !isset($_GET['page']) ? 1 : (int)$_GET['page'];
    $perPage = 10;

    $data = fetch_user_in_out_data_admin($search, $page, $perPage);
    $total = count_user_in_out_data_admin($search);
    $pagination = create_pagination_admin($page, $perPage, $total);

    $ui->assign('q', $search);
    $ui->assign('data', $data);
    $ui->assign('pagination', $pagination);
    $ui->display('data_usage_admin.tpl');
}

function fetch_user_in_out_data_admin($search = '', $page = 1, $perPage = 10)
{
     if(isTableExist('rad_acct')){
    $query = ORM::for_table('rad_acct')->whereNotEqual('acctoutputoctets', 0);
     }else{
         $query = ORM::for_table('radacct')->whereNotEqual('acctoutputoctets', 0);
     }
    if ($search) {
        $query->where_like('username', '%' . $search . '%');
    }

    $query->limit($perPage)->offset(($page - 1) * $perPage);
    $data = Paginator::findMany($query, [], $perPage);

    foreach ($data as &$row) {
        $row->acctOutputOctets = convert_bytes_admin(floatval($row->acctoutputoctets));
        $row->acctInputOctets = convert_bytes_admin(floatval($row->acctinputoctets));
        $row->totalBytes = convert_bytes_admin(floatval($row->acctoutputoctets)+ floatval($row->acctunputoctets));
         if(isTableExist('rad_acct')){
        $lastRecord = ORM::for_table('rad_acct');
        }else{
          $lastRecord = ORM::for_table('radacct')
         }
            ->where('username', $row->username)->whereNotEqual('acctoutputoctets', 0)
            ->order_by_desc('acctstatustype')
            ->find_one();

        if ($lastRecord && $lastRecord->acctstatustype == 'Start') {
            $row->status = '<span class="badge btn-success">Connected</span>';
        } else {
            $row->status = '<span class="badge btn-danger">Disconnected</span>';
        }
    }

    return $data;
}

function count_user_in_out_data_admin($search = '')
{
    if(isTableExist('rad_acct')){
        $query = ORM::for_table('rad_acct');
    }else{
          $query = ORM::for_table('radacct');
    }
    if ($search) {
        $query->where_like('username', '%' . $search . '%');
    }
    return $query->count();
}

function create_pagination_admin($page, $perPage, $total)
{
    $pages = ceil($total / $perPage);
    return [
        'current' => $page,
        'total' => $pages,
        'previous' => ($page > 1) ? $page - 1 : null,
        'next' => ($page < $pages) ? $page + 1 : null,
    ];
}

function convert_bytes_admin($bytes)
{
    if ($bytes >= 1073741824) {
        $bytes = number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        $bytes = number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        $bytes = number_format($bytes / 1024, 2) . ' KB';
    } elseif ($bytes > 1) {
        $bytes = $bytes . ' bytes';
    } elseif ($bytes == 1) {
        $bytes = $bytes . ' byte';
    } else {
        $bytes = '0 bytes';
    }

    return $bytes;
}
