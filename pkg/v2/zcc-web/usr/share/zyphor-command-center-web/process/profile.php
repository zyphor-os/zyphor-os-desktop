<?php

require_once __DIR__ . '/Smark/JSON.php';
require_once __DIR__ . '/Smark/File.php';

use App\Smark\JSON;
use App\Smark\File;

session_start();
header('Content-Type: application/json');

// ---------- config ----------
$username  = trim(shell_exec('whoami')); // identifies the record in profile.json
$homeDir   = getenv('HOME') ?: trim(shell_exec('eval echo ~' . escapeshellarg($username)));
$configDir = rtrim($homeDir, '/') . '/.config/zyphor-command-center-web';

$jsonFile  = $configDir . '/profile.json';
$avatarDir = __DIR__ . '/../assets/uploads/avatars/';
$avatarUrl = 'assets/uploads/avatars/'; // relative to site root (profile.php)
$validExts = ['png', 'jpg', 'jpeg'];
$maxBytes  = 2 * 1024 * 1024; // 2MB

if (!is_dir($configDir)) {
    mkdir($configDir, 0755, true);
}
if (!file_exists($jsonFile)) {
    file_put_contents($jsonFile, '[]');
}
if (!is_dir($avatarDir)) {
    mkdir($avatarDir, 0755, true);
}

function respond($data, int $status = 200)
{
    http_response_code($status);
    echo json_encode($data);
    exit;
}

// Makes sure a record for the current system user exists before we try to
// update/read it, since a fresh ~/.config/zyphor-command-center-web/profile.json
// won't have one yet.
function ensureProfileRecord(string $jsonFile, string $username): void
{
    $records = JSON::jsonRead($jsonFile);

    foreach ($records as $record) {
        if ($record['username'] === $username) {
            return;
        }
    }

    JSON::jsonPush($jsonFile, [
        'username' => $username,
        'name'     => '',
        'email'    => '',
        'bio'      => '',
        'avatar'   => '',
    ]);
}

// ---------- CSRF check ----------
$sentToken  = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_POST['_token'] ?? '');
$validToken = isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $sentToken);

if (!$validToken) {
    respond(['message' => 'Invalid or expired session token. Please refresh the page.'], 419);
}

$action = $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(['message' => 'Method not allowed.'], 405);
}

ensureProfileRecord($jsonFile, $username);

switch ($action) {

    // ---------- update name / email / bio ----------
    case 'update':
        $name  = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $bio   = trim($_POST['bio'] ?? '');

        $errors = [];
        if ($name === '') {
            $errors['name'] = ['Display name is required.'];
        }
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = ['Enter a valid email address.'];
        }

        if (!empty($errors)) {
            respond(['errors' => $errors], 422);
        }

        // The JSON helper only updates one field per call, so we call it
        // once per field we want to persist.
        JSON::jsonUpdate($jsonFile, 'username', $username, 'name', $name);
        JSON::jsonUpdate($jsonFile, 'username', $username, 'email', $email);
        JSON::jsonUpdate($jsonFile, 'username', $username, 'bio', $bio);

        respond(['message' => 'Profile updated successfully.']);
        break;

    // ---------- upload avatar ----------
    case 'avatar_upload':
        if (empty($_FILES['avatar'])) {
            respond(['message' => 'No file was uploaded.'], 422);
        }

        $file = $_FILES['avatar'];

        if ($file['size'] > $maxBytes) {
            respond(['message' => 'Image must be under 2MB.'], 422);
        }

        $uploaded = File::_upload($file, $avatarDir, $validExts);

        if (!$uploaded) {
            respond(['message' => 'Upload failed. Only PNG or JPG images are allowed.'], 422);
        }

        // Note: File::_upload stores the generated filename in File::$filename
        $newFilename = File::$filename;

        // Remove the previous avatar file, if one exists
        $records = JSON::jsonRead($jsonFile);
        foreach ($records as $record) {
            if ($record['username'] === $username && !empty($record['avatar'])) {
                $oldPath = __DIR__ . '/../' . $record['avatar'];
                if (file_exists($oldPath)) {
                    File::removeFile($oldPath);
                }
            }
        }

        $newAvatarUrl = $avatarUrl . $newFilename;
        JSON::jsonUpdate($jsonFile, 'username', $username, 'avatar', $newAvatarUrl);

        respond(['avatar' => $newAvatarUrl, 'message' => 'Avatar updated.']);
        break;

    // ---------- remove avatar ----------
    case 'avatar_remove':
        $records = JSON::jsonRead($jsonFile);

        foreach ($records as $record) {
            if ($record['username'] === $username && !empty($record['avatar'])) {
                $oldPath = __DIR__ . '/../' . $record['avatar'];
                if (file_exists($oldPath)) {
                    File::removeFile($oldPath);
                }
            }
        }

        JSON::jsonUpdate($jsonFile, 'username', $username, 'avatar', '');

        respond(['message' => 'Avatar removed.']);
        break;

    default:
        respond(['message' => 'Unknown action.'], 404);
}
