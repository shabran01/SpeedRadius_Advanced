<?php

/**
 * Apply pending database migrations listed in system/updates.json.
 *
 * @return array{applied: int, statements: int, skipped: int}
 */
function apply_database_updates()
{
    global $db_host, $db_name, $db_user, $db_pass, $db_password, $root_path, $CACHE_PATH;

    $password = !empty($db_pass) ? $db_pass : $db_password;
    $updatesFile = $root_path . 'system' . DIRECTORY_SEPARATOR . 'updates.json';
    $cachePath = !empty($CACHE_PATH)
        ? $CACHE_PATH
        : $root_path . 'system' . DIRECTORY_SEPARATOR . 'cache';
    $doneFile = $cachePath . DIRECTORY_SEPARATOR . 'updates.done.json';

    if (!is_readable($updatesFile)) {
        throw new RuntimeException('Database update file is missing or unreadable.');
    }
    if (!is_dir($cachePath) || !is_writable($cachePath)) {
        throw new RuntimeException('The system cache directory is not writable.');
    }

    $updates = json_decode(file_get_contents($updatesFile), true);
    if (!is_array($updates)) {
        throw new RuntimeException('Database update file contains invalid JSON.');
    }

    $dones = [];
    if (is_readable($doneFile)) {
        $saved = json_decode(file_get_contents($doneFile), true);
        if (is_array($saved)) {
            $dones = $saved;
        }
    }

    $db = new PDO(
        "mysql:host={$db_host};dbname={$db_name}",
        $db_user,
        $password,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $applied = 0;
    $statements = 0;
    $skipped = 0;
    foreach ($updates as $version => $queries) {
        if (in_array($version, $dones, true)) {
            continue;
        }
        if ($version === '2026.9.14-unique-customer-username') {
            $duplicates = $db->query(
                "SELECT username, COUNT(*) AS total
                 FROM tbl_customers
                 GROUP BY username
                 HAVING COUNT(*) > 1
                 ORDER BY total DESC, username ASC
                 LIMIT 10"
            )->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($duplicates)) {
                $names = array_map(function ($row) {
                    return $row['username'] . ' (' . $row['total'] . ')';
                }, $duplicates);
                throw new RuntimeException(
                    'Duplicate customer usernames must be resolved before the unique index can be added: '
                    . implode(', ', $names)
                );
            }
        }
        foreach ((array) $queries as $query) {
            try {
                $db->exec($query);
                $statements++;
            } catch (PDOException $e) {
                if ($version === '2026.9.14-unique-customer-username') {
                    throw $e;
                }
                // Existing-schema errors are expected for some legacy migrations.
                $skipped++;
            }
        }
        $dones[] = $version;
        $applied++;
    }

    if (file_put_contents($doneFile, json_encode(array_values(array_unique($dones)), JSON_PRETTY_PRINT)) === false) {
        throw new RuntimeException('Unable to record completed database updates.');
    }

    return [
        'applied' => $applied,
        'statements' => $statements,
        'skipped' => $skipped,
    ];
}
