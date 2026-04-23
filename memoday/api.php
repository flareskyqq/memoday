<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

$dbFile = '/disk1/www/fs/memoday/data.json';

function loadData() {
    global $dbFile;
    if (!file_exists($dbFile)) {
        return ['event_types' => [], 'event_records' => []];
    }
    $json = file_get_contents($dbFile);
    return json_decode($json, true) ?: ['event_types' => [], 'event_records' => []];
}

function saveData($data) {
    global $dbFile;
    $json = json_encode($data, JSON_UNESCAPED_UNICODE);
    if (json_last_error() !== JSON_ERROR_NONE) {
        // 如果失败，尝试修复编码
        $data = array_map(function($item) {
            if (is_string($item)) {
                // 确保字符串是有效的 UTF-8
                return mb_convert_encoding($item, 'UTF-8', 'UTF-8');
            }
            return $item;
        }, $data);
        $json = json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    file_put_contents($dbFile, $json);
}

$action = $_GET['action'] ?? '';
$user = $_GET['user'] ?? 'root';

$data = loadData();

switch ($action) {
    case 'init_db':
        echo json_encode(['success' => true]);
        break;

    case 'get_types':
        $userTypes = array_filter($data['event_types'], fn($t) => $t['user'] === $user);
        $types = array_values($userTypes);
        // 按 created_at 倒序
        usort($types, fn($a, $b) => strcmp($b['created_at'], $a['created_at']));
        echo json_encode($types);
        break;

    case 'add_type':
        $name = $_GET['name'] ?? '';
        $color = $_GET['color'] ?? '#3498DB';
        if (empty($name)) {
            echo json_encode(['success' => false, 'error' => 'name is required']);
            break;
        }
        // 检查是否已存在
        foreach ($data['event_types'] as $t) {
            if ($t['user'] === $user && $t['name'] === $name) {
                echo json_encode(['success' => false, 'error' => 'event type already exists']);
                exit;
            }
        }
        $newId = empty($data['event_types']) ? 1 : max(array_column($data['event_types'], 'id')) + 1;
        $data['event_types'][] = [
            'id' => $newId,
            'user' => $user,
            'name' => $name,
            'color' => $color,
            'created_at' => date('Y-m-d H:i:s')
        ];
        saveData($data);
        echo json_encode(['success' => true, 'id' => $newId]);
        break;

    case 'edit_type':
        $id = intval($_GET['id'] ?? 0);
        $name = $_GET['name'] ?? '';
        $color = $_GET['color'] ?? '';
        if (empty($name)) {
            echo json_encode(['success' => false, 'error' => 'name is required']);
            break;
        }
        foreach ($data['event_types'] as &$t) {
            if ($t['id'] === $id && $t['user'] === $user) {
                $t['name'] = $name;
                $t['color'] = $color;
                saveData($data);
                echo json_encode(['success' => true]);
                exit;
            }
        }
        echo json_encode(['success' => false, 'error' => 'not found']);
        break;

    case 'del_type':
        $id = intval($_GET['id'] ?? 0);
        $before = count($data['event_types']);
        $data['event_types'] = array_values(array_filter($data['event_types'], fn($t) => $t['id'] !== $id || $t['user'] !== $user));
        // 同时删除关联的记录
        $data['event_records'] = array_values(array_filter($data['event_records'], fn($r) => $r['event_type_id'] !== $id || $r['user'] !== $user));
        saveData($data);
        echo json_encode(['success' => count($data['event_types']) < $before]);
        break;

    case 'get_records':
        $date = $_GET['date'] ?? date('Y-m-d');
        $typeMap = [];
        foreach ($data['event_types'] as $t) {
            $typeMap[$t['id']] = ['name' => $t['name'], 'color' => $t['color']];
        }
        $userRecords = array_filter($data['event_records'], fn($r) => $r['user'] === $user && $r['date'] === $date);
        $records = array_values($userRecords);
        foreach ($records as &$r) {
            $type = $typeMap[$r['event_type_id']] ?? null;
            $r['name'] = $type ? $type['name'] : '(已删除)';
            $r['color'] = $type ? $type['color'] : '#ccc';
        }
        // 按 created_at 倒序
        usort($records, fn($a, $b) => strcmp($b['created_at'], $a['created_at']));
        echo json_encode($records);
        break;

    case 'get_month_records':
        $year = intval($_GET['year'] ?? date('Y'));
        $month = intval($_GET['month'] ?? date('n'));
        $typeMap = [];
        foreach ($data['event_types'] as $t) {
            $typeMap[$t['id']] = ['name' => $t['name'], 'color' => $t['color']];
        }
        $prefix = "$year-" . sprintf("%02d", $month);
        $monthRecords = array_filter($data['event_records'], fn($r) => $r['user'] === $user && strpos($r['date'], $prefix) === 0);
        $records = array_values($monthRecords);
        foreach ($records as &$r) {
            $type = $typeMap[$r['event_type_id']] ?? null;
            $r['name'] = $type ? $type['name'] : '(已删除)';
            $r['color'] = $type ? $type['color'] : '#ccc';
        }
        echo json_encode($records);
        break;

    case 'add_record':
        $event_type_id = intval($_GET['event_type_id'] ?? 0);
        $date = $_GET['date'] ?? date('Y-m-d');
        // 检查是否已存在
        foreach ($data['event_records'] as $r) {
            if ($r['user'] === $user && $r['event_type_id'] === $event_type_id && $r['date'] === $date) {
                echo json_encode(['success' => false, 'error' => 'record already exists']);
                exit;
            }
        }
        $newId = empty($data['event_records']) ? 1 : max(array_column($data['event_records'], 'id')) + 1;
        $data['event_records'][] = [
            'id' => $newId,
            'user' => $user,
            'event_type_id' => $event_type_id,
            'date' => $date,
            'created_at' => date('Y-m-d H:i:s')
        ];
        saveData($data);
        echo json_encode(['success' => true, 'id' => $newId]);
        break;

    case 'del_record':
        $id = intval($_GET['id'] ?? 0);
        $before = count($data['event_records']);
        $data['event_records'] = array_values(array_filter($data['event_records'], fn($r) => $r['id'] !== $id || $r['user'] !== $user));
        saveData($data);
        echo json_encode(['success' => count($data['event_records']) < $before]);
        break;

    default:
        echo json_encode(['error' => 'unknown action']);
}
