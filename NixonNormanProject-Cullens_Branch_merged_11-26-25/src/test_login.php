<?php
/**
 * Debug Login Test
 * This will help us see what's going wrong
 */

session_start();
require_once 'adminAuth.php';
require_once 'db_config.php';

echo "<h2>Login Debug Test</h2>";

// Test 1: Database connection
echo "<h3>Test 1: Database Connection</h3>";
if (testDatabaseConnection()) {
    echo "✓ Database connected successfully<br>";
} else {
    echo "✗ Database connection FAILED<br>";
    exit;
}

// Test 2: Check if admin user exists
echo "<h3>Test 2: Check Admin User</h3>";
try {
    $pdo = getDatabaseConnection();
    $stmt = $pdo->query("SELECT id, username, password_hash FROM admin_users WHERE username = 'admin'");
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        echo "✓ Admin user found<br>";
        echo "ID: " . $user['id'] . "<br>";
        echo "Username: " . $user['username'] . "<br>";
        echo "Password Hash: " . substr($user['password_hash'], 0, 30) . "...<br>";
    } else {
        echo "✗ Admin user NOT found in database<br>";
        exit;
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "<br>";
    exit;
}

// Test 3: Test password verification
echo "<h3>Test 3: Password Verification</h3>";
$testPassword = 'admin123';
echo "Testing password: " . $testPassword . "<br>";

if (password_verify($testPassword, $user['password_hash'])) {
    echo "✓ Password verification SUCCESSFUL<br>";
} else {
    echo "✗ Password verification FAILED<br>";
    echo "The hash in the database doesn't match the password 'admin123'<br>";
    echo "<br><strong>Solution:</strong> Run this SQL in phpMyAdmin:<br>";
    $newHash = password_hash($testPassword, PASSWORD_DEFAULT);
    echo "<code>UPDATE admin_users SET password_hash = '" . $newHash . "' WHERE username = 'admin';</code><br>";
}

// Test 4: Try the actual login function
echo "<h3>Test 4: Login Function Test</h3>";
if (loginAdmin('admin', 'admin123')) {
    echo "✓ Login function worked!<br>";
    echo "You should now be logged in. <a href='adminHome.php'>Go to Admin Dashboard</a><br>";
} else {
    echo "✗ Login function failed<br>";
}
?>
