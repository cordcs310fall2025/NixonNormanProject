<?php
/**
 * Password Hash Generator
 * Run this file once to generate a proper password hash for admin123
 */

$password = 'admin123';
$hash = password_hash($password, PASSWORD_DEFAULT);

echo "Password: " . $password . "<br>";
echo "Hash: " . $hash . "<br><br>";
echo "Copy this SQL and run it in phpMyAdmin:<br><br>";
echo "UPDATE admin_users SET password_hash = '" . $hash . "' WHERE username = 'admin';";
?>
