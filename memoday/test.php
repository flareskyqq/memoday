<?php
header('Content-Type: text/plain; charset=utf-8');
$name = $_GET['name'] ?? '';
$color = $_GET['color'] ?? '#3498DB';
file_put_contents('/disk1/www/fs/memoday/test_output.txt', "name=$name,color=$color,bytes=" . bin2hex($name));
echo json_encode(['name' => $name, 'color' => $color]);
?>
