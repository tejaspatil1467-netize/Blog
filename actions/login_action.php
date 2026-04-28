<?php
// ============================================================
//  TechSync Blog — Login Action
//  actions/login_action.php
//
//  Handles POST from pages/login.php
//  Validates credentials and sets session.
// ============================================================

session_start();
require_once '../config/db.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/login.php');
    exit;
}

// ── Sanitize & Validate Input ─────────────────────────────────
$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($username) || empty($password)) {
    header('Location: ../pages/login.php?error=Please+fill+in+all+fields');
    exit;
}

// ── Fetch User from Database (Prepared Statement) ─────────────
$stmt = mysqli_prepare($conn, "SELECT id, username, password, role FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    // Verify password against stored hash
    if (password_verify($password, $row['password'])) {
        // ── Set Session Variables ─────────────────────────────
        $_SESSION['user_id']  = $row['id'];
        $_SESSION['username'] = $row['username'];
        $_SESSION['role']     = $row['role'];

        // ── Redirect Based on Role ────────────────────────────
        if ($row['role'] === 'admin') {
            header('Location: ../pages/admin_dashboard.php');
        } else {
            header('Location: ../pages/user_dashboard.php');
        }
        exit;
    }
}

// ── Invalid Credentials ────────────────────────────────────────
mysqli_stmt_close($stmt);
header('Location: ../pages/login.php?error=Invalid+username+or+password');
exit;
