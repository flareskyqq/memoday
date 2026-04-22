<?php
$dataFile = '/disk1/www/fs/memoday/data.json';
echo "File exists: " . (file_exists($dataFile) ? 'yes' : 'no') . "\n";
echo "Is writable: " . (is_writable($dataFile) ? 'yes' : 'no') . "\n";
if (file_exists($dataFile)) {
    echo "Current content: " . file_get_contents($dataFile) . "\n";
}
$result = file_put_contents($dataFile, 'TEST_WRITE_' . date('Y-m-d H:i:s'));
echo "Write result: " . ($result === false ? 'false' : $result) . "\n";
echo "After write, content: " . file_get_contents($dataFile) . "\n";
?>
