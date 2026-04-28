<?php
// fix_admin.php
// Visit this file in your browser to reset the admin password.

require_once 'config/db.php';

// Generate a fresh hash for 'admin123'
$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

// Update or Insert the admin user
$query = "INSERT INTO users (username, password, role) 
          VALUES ('admin', '$hash', 'admin') 
          ON DUPLICATE KEY UPDATE password = '$hash', role = 'admin'";

if (mysqli_query($conn, $query)) {
    echo "<h1>✅ Admin password successfully reset to 'admin123'!</h1>";
    echo "<p>Please delete this file (fix_admin.php) and go back to the <a href='pages/login.php'>Login Page</a>.</p>";
} else {
    echo "<h1>❌ Error updating admin password:</h1>";
    echo "<p>" . mysqli_error($conn) . "</p>";
}
?>
