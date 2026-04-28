<?php
// ============================================================
//  TechSync Blog — Register Action
//  actions/register_action.php
//
//  Handles POST from pages/register.php
//  Creates a new user with hashed password.
// ============================================================

session_start();
require_once '../config/db.php';

// Only accept POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/register.php');
    exit;
}

// ── Sanitize & Validate Input ─────────────────────────────────
$username         = trim($_POST['username'] ?? '');
$password         = $_POST['password'] ?? '';
$confirm_password = $_POST['confirm_password'] ?? '';

if (empty($username) || empty($password) || empty($confirm_password)) {
    header('Location: ../pages/register.php?error=Please+fill+in+all+fields');
    exit;
}

if (strlen($username) < 3 || strlen($username) > 50) {
    header('Location: ../pages/register.php?error=Username+must+be+3-50+characters');
    exit;
}

if (strlen($password) < 6) {
    header('Location: ../pages/register.php?error=Password+must+be+at+least+6+characters');
    exit;
}

if ($password !== $confirm_password) {
    header('Location: ../pages/register.php?error=Passwords+do+not+match');
    exit;
}

// ── Check if Username Already Exists ─────────────────────────
$stmt = mysqli_prepare($conn, "SELECT id FROM users WHERE username = ?");
mysqli_stmt_bind_param($stmt, 's', $username);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);

if (mysqli_stmt_num_rows($stmt) > 0) {
    mysqli_stmt_close($stmt);
    header('Location: ../pages/register.php?error=Username+already+taken.+Choose+another.');
    exit;
}
mysqli_stmt_close($stmt);

// ── Hash Password & Insert User ───────────────────────────────
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = mysqli_prepare($conn, "INSERT INTO users (username, password, role) VALUES (?, ?, 'user')");
mysqli_stmt_bind_param($stmt, 'ss', $username, $hashed_password);

if (mysqli_stmt_execute($stmt)) {
    mysqli_stmt_close($stmt);
    header('Location: ../pages/login.php?success=Account+created!+Please+sign+in.');
    exit;
} else {
    mysqli_stmt_close($stmt);
    header('Location: ../pages/register.php?error=Registration+failed.+Please+try+again.');
    exit;
}
