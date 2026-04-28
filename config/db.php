<?php
// ============================================================
//  TechSync Blog — Database Configuration
//  config/db.php
//
//  Adjust the constants below to match your local setup.
//  XAMPP defaults: host=localhost, user=root, password=''
// ============================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');              // Empty for default XAMPP
define('DB_NAME', 'techsync_blog');

// Create MySQLi connection
$conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if (!$conn) {
    // In production, never expose raw error messages.
    // For a college project, this is acceptable.
    die('<h3 style="font-family:sans-serif;color:#e11d48;padding:24px;">
            ❌ Database connection failed: ' . mysqli_connect_error() . '
            <br><small>Check your credentials in config/db.php and make sure MySQL is running.</small>
         </h3>');
}

// Set character set to UTF-8 for proper text handling
mysqli_set_charset($conn, 'utf8mb4');
