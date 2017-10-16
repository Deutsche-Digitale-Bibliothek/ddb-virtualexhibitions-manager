<?php
$threshold = 1 * 24 * 60 *60; // a day

$baseDir = dirname(__FILE__);
$downloadsDir = $baseDir . DIRECTORY_SEPARATOR
    . 'public'. DIRECTORY_SEPARATOR
    . 'downloads';
$files = scandir($downloadsDir);
$maxAge = time() - $threshold;
foreach ($files as $file) {
    $filePath = $downloadsDir . DIRECTORY_SEPARATOR . $file;
    if (is_file($filePath) && filemtime($filePath) < $maxAge) {
        unlink($filePath);
    }
}
?>