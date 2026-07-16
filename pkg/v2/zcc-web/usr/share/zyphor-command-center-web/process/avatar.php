<?php

$username  = trim(shell_exec('whoami'));
$homeDir   = getenv('HOME') ?: trim(shell_exec('eval echo ~' . escapeshellarg($username)));
$configDir = rtrim($homeDir, '/') . '/.config/zyphor-command-center-web';
$avatarDir = $configDir . '/uploads/avatars/';

$requested = $_GET['file'] ?? '';

// basename() strips any directory components, so ../../etc/passwd style
// path traversal can't escape $avatarDir
$filename = basename($requested);
$path     = $avatarDir . $filename;

if ($filename === '' || !is_file($path)) {
    http_response_code(404);
    exit;
}

$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
$mimeTypes = [
    'png'  => 'image/png',
    'jpg'  => 'image/jpeg',
    'jpeg' => 'image/jpeg',
];

if (!isset($mimeTypes[$ext])) {
    http_response_code(404);
    exit;
}

header('Content-Type: ' . $mimeTypes[$ext]);
header('Content-Length: ' . filesize($path));
header('Cache-Control: private, max-age=3600');

readfile($path);
exit;
