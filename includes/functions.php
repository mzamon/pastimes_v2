<?php
/**
 * includes/functions.php
 * Core utilities: session bootstrap, auth guards, helpers
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// BASE_URL auto-detection
$_scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '/');
define('BASE_URL', strpos($_scriptName, '/pastimes/') === 0 ? '/pastimes/' : '/');
define('UPLOAD_DIR', __DIR__ . '/../assets/images/uploads/');
define('IMAGE_BASE', BASE_URL . 'assets/images/');

require_once __DIR__ . '/TextScanner.php';

// ── Input & output helpers ────────────────────────────────────
function sanitize($data) {
    if (is_array($data)) return array_map('sanitize', $data);
    return trim(stripslashes((string)$data));
}

function h($str) {
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}

function getProductImage($image) {
    if (empty($image)) return IMAGE_BASE . 'placeholder/no-image.jpg';
    if (file_exists(__DIR__ . '/../assets/images/' . $image)) return IMAGE_BASE . $image;
    return IMAGE_BASE . 'placeholder/no-image.jpg';
}

function redirect($url) {
    header('Location: ' . $url);
    exit();
}

// ── Auth helpers ──────────────────────────────────────────────
function isLoggedIn()  { return isset($_SESSION['user_id']); }

function isSeller() {
    return isset($_SESSION['role'])
        && $_SESSION['role'] === 'seller'
        && isset($_SESSION['is_verified'])
        && (int)$_SESSION['is_verified'] === 1;
}

function isAdmin()     { return isset($_SESSION['role']) && $_SESSION['role'] === 'admin'; }
function isBuyer()     { return isset($_SESSION['role']) && $_SESSION['role'] === 'buyer'; }
function isVerified()  { return isset($_SESSION['is_verified']) && (int)$_SESSION['is_verified'] === 1; }
function isSellerRequestPending() {
    return isset($_SESSION['seller_request']) && $_SESSION['seller_request'] === 'pending';
}

// ── Auth guards ───────────────────────────────────────────────
function requireLogin() {
    if (!isLoggedIn()) redirect(BASE_URL . 'auth/login.php');
}

function requireVerified() {
    requireLogin();
    if (!isVerified()) redirect(BASE_URL . 'auth/login.php?pending=1');
}

function requireSeller() {
    requireLogin();
    if (!isSeller()) redirect(BASE_URL . 'index.php');
}

function requireAdmin() {
    if (!isLoggedIn()) redirect(BASE_URL . 'auth/admin_login.php');
    if (!isAdmin())    redirect(BASE_URL . 'index.php');
}

function requireSellerOrAdmin() {
    requireLogin();
    if (!isSeller() && !isAdmin()) redirect(BASE_URL . 'index.php');
}

// ── Cart helpers ──────────────────────────────────────────────
function getCartCount() {
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        return array_sum(array_column($_SESSION['cart'], 'quantity'));
    }
    return 0;
}

// ── Alert helpers ─────────────────────────────────────────────
function displayError($msg) {
    return '<div class="alert alert-error">' . h($msg) . '</div>';
}

function displaySuccess($msg) {
    return '<div class="alert alert-success">' . h($msg) . '</div>';
}

// ── Badge helpers ─────────────────────────────────────────────
function statusBadge($status) {
    $map = [
        'Pending'    => 'status-pending',
        'Packed'     => 'status-packed',
        'In Transit' => 'status-transit',
        'Delivered'  => 'status-delivered',
    ];
    $class = $map[$status] ?? 'status-pending';
    return '<span class="status-badge ' . $class . '">' . h($status) . '</span>';
}

function verificationBadge($isVerified) {
    $class = $isVerified ? 'status-delivered' : 'status-pending';
    $label = $isVerified ? 'Verified' : 'Pending';
    return '<span class="status-badge ' . $class . '">' . h($label) . '</span>';
}

function sellerRequestBadge($status) {
    $map = [
        'none'     => 'status-pending',
        'pending'  => 'status-packed',
        'approved' => 'status-delivered',
        'rejected' => 'status-transit',
    ];
    $class = $map[$status] ?? 'status-pending';
    return '<span class="status-badge ' . $class . '">' . h(ucfirst($status ?? 'none')) . '</span>';
}
?>
