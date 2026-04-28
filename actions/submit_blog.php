<?php
// ============================================================
//  TechSync Blog — Submit Blog Action
//  actions/submit_blog.php
//
//  Handles POST from pages/write_blog.php
//  Saves the blog with status = 'pending'.
// ============================================================

session_start();
require_once '../config/db.php';

// ── Authentication Guard ─────────────────────────────────────
// Only logged-in users (not admins) can submit blogs
if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/login.php?error=Please+login+to+write+a+blog');
    exit;
}

if ($_SESSION['role'] === 'admin') {
    header('Location: ../pages/admin_dashboard.php');
    exit;
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/write_blog.php');
    exit;
}

// ── Sanitize & Validate Input ─────────────────────────────────
$title   = trim($_POST['title'] ?? '');
$content = trim($_POST['content'] ?? '');

if (empty($title) || empty($content)) {
    header('Location: ../pages/write_blog.php?error=Title+and+content+are+required');
    exit;
}

if (strlen($title) > 255) {
    header('Location: ../pages/write_blog.php?error=Title+is+too+long+(max+255+chars)');
    exit;
}

// ── Insert Blog into Database ─────────────────────────────────
$user_id = $_SESSION['user_id'];

$stmt = mysqli_prepare(
    $conn,
    "INSERT INTO blogs (title, content, status, user_id) VALUES (?, ?, 'pending', ?)"
);
mysqli_stmt_bind_param($stmt, 'ssi', $title, $content, $user_id);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    // Redirect with success message
    header('Location: ../pages/user_dashboard.php?success=Blog+submitted+for+admin+approval!');
    exit;
} else {
    mysqli_stmt_close($stmt);
    header('Location: ../pages/write_blog.php?error=Failed+to+submit+blog.+Please+try+again.');
    exit;
}
