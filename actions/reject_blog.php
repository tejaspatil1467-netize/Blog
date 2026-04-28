<?php
// ============================================================
//  TechSync Blog — Reject Blog Action
//  actions/reject_blog.php
//
//  Handles POST from admin_dashboard.php
//  Changes blog status to 'rejected'.
// ============================================================

session_start();
require_once '../config/db.php';

// ── Admin Guard ───────────────────────────────────────────────
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../pages/login.php?error=Admin+access+required');
    exit;
}

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/admin_dashboard.php');
    exit;
}

// ── Validate Blog ID ──────────────────────────────────────────
$blog_id = intval($_POST['blog_id'] ?? 0);

if ($blog_id <= 0) {
    header('Location: ../pages/admin_dashboard.php?error=Invalid+blog+ID');
    exit;
}

// ── Update Status to Rejected ─────────────────────────────────
$stmt = mysqli_prepare($conn, "UPDATE blogs SET status = 'rejected' WHERE id = ?");
mysqli_stmt_bind_param($stmt, 'i', $blog_id);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header('Location: ../pages/admin_dashboard.php?success=Blog+rejected.');
} else {
    mysqli_stmt_close($stmt);
    header('Location: ../pages/admin_dashboard.php?error=Failed+to+reject+blog');
}
exit;
