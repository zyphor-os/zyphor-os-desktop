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
$avatarDir = $configDir . '/uploads/avatars/'; // filesystem storage, NOT web-accessible directly
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

// $configDir lives outside the web root, so the browser can't fetch avatar
// files directly. process/avatar.php streams them back over HTTP instead.
function avatarUrl(string $filename): string
{
    return $filename === '' ? '' : 'process/avatar.php?file=' . rawurlencode($filename);
}

/**
 * Crops an image to a perfect square based on its shortest side,
 * keeping the crop centered, without scaling down its resolution.
 */
function cropToCenterSquare(string $filePath): bool
{
    $imageInfo = @getimagesize($filePath);
    if (!$imageInfo) {
        return false;
    }

    list($width, $height, $type) = $imageInfo;

    // If it is already a square, no cropping is needed
    if ($width === $height) {
        return true;
    }

    $squareSize = min($width, $height);

    // Calculate coordinates to center the crop
    $x = ($width > $height) ? (int)floor(($width - $squareSize) / 2) : 0;
    $y = ($height > $width) ? (int)floor(($height - $squareSize) / 2) : 0;

    // Load the image based on its type
    switch ($type) {
        case IMAGETYPE_JPEG:
            $srcImage = @imagecreatefromjpeg($filePath);
            break;
        case IMAGETYPE_PNG:
            $srcImage = @imagecreatefrompng($filePath);
            break;
        default:
            return false; 
    }

    if (!$srcImage) {
        return false;
    }

    // Create a new blank canvas for the square image
    $dstImage = imagecreatetruecolor($squareSize, $squareSize);

    // Preserve PNG transparency
    if ($type === IMAGETYPE_PNG) {
        imagealphablending($dstImage, false);
        imagesavealpha($dstImage, true);
        $transparent = imagecolorallocatealpha($dstImage, 255, 255, 255, 127);
        imagefilledrectangle($dstImage, 0, 0, $squareSize, $squareSize, $transparent);
    }

    // Crop and copy the centered square region
    imagecopyresampled(
        $dstImage, $srcImage,
        0, 0, $x, $y,
        $squareSize, $squareSize, $squareSize, $squareSize
    );

    // Save the cropped image back, replacing the original upload
    $saved = false;
    switch ($type) {
        case IMAGETYPE_JPEG:
            $saved = imagejpeg($dstImage, $filePath, 95); // 95% quality
            break;
        case IMAGETYPE_PNG:
            $saved = imagepng($dstImage, $filePath, 5); // compression level 5
            break;
    }

    imagedestroy($srcImage);
    imagedestroy($dstImage);

    return $saved;
}

// ======================================================================
// DESKTOP-ENVIRONMENT AWARE AVATAR REFRESH
// ======================================================================
//
// Writing ~/.face and ~/.face.icon plus notifying AccountsService works on
// every mainstream desktop (GNOME, KDE Plasma, XFCE, Cinnamon, MATE, LXQt,
// Budgie, Pantheon, COSMIC ...) and on LightDM / GDM / SDDM greeters, so
// that part is always done. Only the few desktops that cache the icon in a
// running process need an extra nudge; those live in the registry below.
//
// To support a new desktop in the future, add one line to
// desktopRefreshCommands() — nothing else needs to change.

/** True when $command is available on PATH. */
function commandExists(string $command): bool
{
    return trim((string) shell_exec('command -v ' . escapeshellarg($command) . ' 2>/dev/null')) !== '';
}

/**
 * Extra, desktop-specific refresh steps, keyed by normalised desktop name
 * (see detectDesktops()). Each entry is [required binary, command to run].
 * Desktops that are not listed here need nothing beyond the common steps.
 */
function desktopRefreshCommands(): array
{
    return [
        // XFCE: user widgets in the panel cache the icon, so restart the panel.
        'xfce' => ['xfce4-panel', 'xfce4-panel -r'],

        // GNOME, KDE Plasma, Cinnamon, MATE, LXQt, Budgie, Pantheon, COSMIC:
        // they read AccountsService / ~/.face(.icon) themselves, so nothing
        // extra is needed. Add an entry here if one of them ever does.
    ];
}

/**
 * Names of the desktop environment(s) the current session is running,
 * lower-cased and normalised (e.g. "xfce", "gnome", "kde", "cinnamon").
 * Returns [] when nothing can be detected — callers must cope with that.
 */
function detectDesktops(string $username): array
{
    // Spellings seen in XDG_CURRENT_DESKTOP / DESKTOP_SESSION -> one canonical name
    $aliases = [
        'xfce4'         => 'xfce',
        'plasma'        => 'kde',
        'plasmawayland' => 'kde',
        'plasmax11'     => 'kde',
        'kde-plasma'    => 'kde',
        'lxqt-session'  => 'lxqt',
    ];

    // 1) The session environment. XDG_CURRENT_DESKTOP can be a colon-separated
    //    list such as "ubuntu:GNOME" or "X-Cinnamon".
    $raw = getenv('XDG_CURRENT_DESKTOP')
        ?: getenv('XDG_SESSION_DESKTOP')
        ?: getenv('DESKTOP_SESSION')
        ?: '';

    $found = [];
    foreach (preg_split('/[:;,\s]+/', strtolower((string) $raw), -1, PREG_SPLIT_NO_EMPTY) as $token) {
        if (strncmp($token, 'x-', 2) === 0) {
            $token = substr($token, 2);            // "x-cinnamon" -> "cinnamon"
        }
        if (strncmp($token, 'gnome', 5) === 0) {
            $token = 'gnome';                      // gnome-classic, gnome-xorg, ...
        }
        $found[] = $aliases[$token] ?? $token;
    }

    if (!empty($found)) {
        return array_values(array_unique($found));
    }

    // 2) Fallback when the environment variables were not inherited: look for
    //    a characteristic process owned by this user.
    $processMarkers = [
        'xfce'     => 'xfce4-session',
        'gnome'    => 'gnome-shell',
        'kde'      => 'plasmashell',
        'cinnamon' => 'cinnamon',
        'mate'     => 'mate-session',
        'lxqt'     => 'lxqt-session',
        'budgie'   => 'budgie-panel',
    ];

    foreach ($processMarkers as $desktop => $process) {
        $pids = trim((string) shell_exec(
            'pgrep -u ' . escapeshellarg($username) . ' -x ' . escapeshellarg($process) . ' 2>/dev/null'
        ));
        if ($pids !== '') {
            $found[] = $desktop;
        }
    }

    return array_values(array_unique($found));
}

// Live-refreshes anything currently showing the old avatar. Writing
// ~/.face alone only takes effect the next time something re-reads it
// (e.g. the greeter at the next login) — this nudges the running desktop too.
// Fire-and-forget + backgrounded so a slow/missing dbus or panel never
// delays the HTTP response.
function refreshFaceIcon(string $username, string $facePath): void
{
    // Common step (every desktop): notify AccountsService over D-Bus. This is
    // what GDM, SDDM, LightDM greeters and most desktop shells watch,
    // separately from ~/.face.
    $uid = trim((string) shell_exec('id -u ' . escapeshellarg($username)));

    if ($uid !== '' && commandExists('gdbus')) {
        $objectPath = '/org/freedesktop/Accounts/User' . $uid;
        $cmd = 'gdbus call --system --dest org.freedesktop.Accounts '
             . '--object-path ' . escapeshellarg($objectPath) . ' '
             . '--method org.freedesktop.Accounts.User.SetIconFile '
             . escapeshellarg($facePath)
             . ' > /dev/null 2>&1 &';
        shell_exec($cmd);
    }

    // Desktop-specific extras: only run for the desktop(s) actually in use,
    // and only if the tool they need is installed.
    $extras = desktopRefreshCommands();

    foreach (detectDesktops($username) as $desktop) {
        if (!isset($extras[$desktop])) {
            continue;
        }

        [$binary, $command] = $extras[$desktop];

        if (commandExists($binary)) {
            shell_exec($command . ' > /dev/null 2>&1 &');
        }
    }
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

        // Cropping needs PHP's GD extension; fail with a clear message
        // instead of a fatal "undefined function" error.
        if (!extension_loaded('gd')) {
            respond(['message' => 'The PHP GD extension is not installed (install php-gd).'], 500);
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
        $uploadedPath = $avatarDir . $newFilename;

        // Auto-crop to a centered square based on the shortest side
        if (!cropToCenterSquare($uploadedPath)) {
            File::removeFile($uploadedPath);
            respond(['message' => 'Failed to process image cropping.'], 500);
        }

        // Remove the previous avatar file, if one exists
        $records = JSON::jsonRead($jsonFile);
        foreach ($records as $record) {
            if ($record['username'] === $username && !empty($record['avatar'])) {
                $oldPath = $avatarDir . $record['avatar'];
                if (file_exists($oldPath)) {
                    File::removeFile($oldPath);
                }
            }
        }

        // Store the bare filename — the URL is built on demand via avatarUrl()
        JSON::jsonUpdate($jsonFile, 'username', $username, 'avatar', $newFilename);

        // Mirror the new avatar to the "face" icon files that greeters
        // (LightDM, SDDM, GDM) and desktop user menus look for directly in
        // the user's home directory.
        $facePath     = rtrim($homeDir, '/') . '/.face';
        $faceIconPath = rtrim($homeDir, '/') . '/.face.icon';
        @copy($uploadedPath, $facePath);
        @copy($uploadedPath, $faceIconPath);
        @chmod($facePath, 0644);
        @chmod($faceIconPath, 0644);

        // Nudge the running desktop to pick up the new icon now, instead of
        // waiting for next login (AccountsService + any desktop-specific step)
        refreshFaceIcon($username, $facePath);

        respond(['avatar' => avatarUrl($newFilename), 'message' => 'Avatar updated.']);
        break;

    // ---------- remove avatar ----------
    case 'avatar_remove':
        $records = JSON::jsonRead($jsonFile);

        foreach ($records as $record) {
            if ($record['username'] === $username && !empty($record['avatar'])) {
                $oldPath = $avatarDir . $record['avatar'];
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