<?php

$query = isset($_GET['query']) ? trim($_GET['query']) : '';

if (!empty($query)) {
    $escaped = ORM::get_db()->quote('%' . $query . '%');
    $results = ORM::for_table('tbl_customers')
        ->whereRaw(
            "username LIKE $escaped OR fullname LIKE $escaped OR phonenumber LIKE $escaped"
        )
        ->limit(20)
        ->find_many();

    if ($results) {
        echo '<ul>';
        foreach ($results as $user) {
            $username  = htmlspecialchars($user->username,    ENT_QUOTES, 'UTF-8');
            $fullname  = htmlspecialchars($user->fullname,    ENT_QUOTES, 'UTF-8');
            $phone     = htmlspecialchars($user->phonenumber, ENT_QUOTES, 'UTF-8');
            $label = $username;
            if (!empty($fullname)) $label .= ' &mdash; ' . $fullname;
            if (!empty($phone))    $label .= ' (' . $phone . ')';
            echo '<li><a href="' . $_url . '?_route=customers/view/' . $user->id . '">' . $label . '</a></li>';
        }
        echo '</ul>';
    } else {
        echo '<p>' . Lang::T('No users found.') . '</p>';
    }
} else {
    echo '<p>' . Lang::T('Please enter a search term.') . '</p>';
}
