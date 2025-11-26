<?php
/**
 * ============================================================
 * Admin Authentication Helper Functions
 * ============================================================
 * This file contains all the security functions needed to:
 * - Check if someone is logged in as an admin
 * - Verify login credentials
 * - Log admin users in and out
 * - Protect admin pages from unauthorized access
 * ============================================================
 */

// Make sure database connection is available
require_once 'db_config.php';

/**
 * Checks if the current user is logged in as an admin
 * 
 * How it works:
 * - Looks for a special flag in the session (like a backstage pass)
 * - Returns true if they have the pass, false if they don't
 * 
 * @return bool True if user is logged in, false otherwise
 */
function isAdminLoggedIn() {
    // Start or resume the session (this is like opening a locker)
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Check if the 'admin_logged_in' flag exists and is set to true
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Requires admin login to view a page
 * 
 * How it works:
 * - Checks if user is logged in
 * - If not, sends them to the login page
 * - Use this at the top of any admin page to protect it
 * 
 * @return void
 */
function requireAdminLogin() {
    // Check if they're logged in
    if (!isAdminLoggedIn()) {
        // Not logged in? Send them to the login page
        header('Location: adminLogin.php');
        exit(); // Stop the page from loading
    }
}

/**
 * Attempts to log in an admin user
 * 
 * How it works:
 * - Takes a username and password
 * - Looks up the username in the database
 * - Checks if the password matches (using secure comparison)
 * - Creates a login session if everything checks out
 * 
 * @param string $username The username entered by the user
 * @param string $password The password entered by the user
 * @return bool True if login successful, false if failed
 */
function loginAdmin($username, $password) {
    // Start or resume the session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Make sure we can connect to the database
    if (!testDatabaseConnection()) {
        return false;
    }
    
    try {
        // Get database connection
        $pdo = getDatabaseConnection();
        
        // Look up the admin user by username
        // Using prepared statements to prevent SQL injection attacks
        $stmt = $pdo->prepare("SELECT id, username, password_hash FROM admin_users WHERE username = :username");
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        
        // Get the user record
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        // If user doesn't exist, login fails
        if (!$user) {
            return false;
        }
        
        // Verify the password using PHP's secure password verification
        // This compares the entered password with the stored hash
        if (password_verify($password, $user['password_hash'])) {
            // Password is correct! Set up the login session
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $user['id'];
            $_SESSION['admin_username'] = $user['username'];
            
            // Update the last login time in the database
            $updateStmt = $pdo->prepare("UPDATE admin_users SET last_login = NOW() WHERE id = :id");
            $updateStmt->bindValue(':id', $user['id'], PDO::PARAM_INT);
            $updateStmt->execute();
            
            return true;
        }
        
        // Password didn't match
        return false;
        
    } catch (Exception $e) {
        // Something went wrong with the database
        error_log("Login error: " . $e->getMessage());
        return false;
    }
}

/**
 * Logs out the current admin user
 * 
 * How it works:
 * - Removes all session data (clears the backstage pass)
 * - Destroys the session completely
 * - User will need to log in again to access admin pages
 * 
 * @return void
 */
function logoutAdmin() {
    // Start or resume the session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Clear all session variables
    $_SESSION = array();
    
    // Destroy the session cookie if it exists
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time() - 3600, '/');
    }
    
    // Completely destroy the session
    session_destroy();
}

/**
 * Gets the currently logged in admin's username
 * 
 * How it works:
 * - Returns the username from the session
 * - Returns null if no one is logged in
 * 
 * @return string|null The admin username or null
 */
function getAdminUsername() {
    // Start or resume the session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    // Return the username if it exists, otherwise null
    return isset($_SESSION['admin_username']) ? $_SESSION['admin_username'] : null;
}

/**
 * Creates a new admin user account
 * 
 * How it works:
 * - Takes a username and password
 * - Creates a secure hash of the password
 * - Saves the new admin account to the database
 * 
 * Note: Only use this function in a separate admin setup page
 * Don't expose this to the public!
 * 
 * @param string $username The desired username
 * @param string $password The desired password (will be hashed)
 * @return bool True if account created, false if failed
 */
function createAdminUser($username, $password) {
    // Make sure we can connect to the database
    if (!testDatabaseConnection()) {
        return false;
    }
    
    try {
        // Get database connection
        $pdo = getDatabaseConnection();
        
        // Create a secure hash of the password
        // Never store passwords as plain text!
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert the new admin user into the database
        $stmt = $pdo->prepare("INSERT INTO admin_users (username, password_hash) VALUES (:username, :password_hash)");
        $stmt->bindValue(':username', $username, PDO::PARAM_STR);
        $stmt->bindValue(':password_hash', $passwordHash, PDO::PARAM_STR);
        $stmt->execute();
        
        return true;
        
    } catch (Exception $e) {
        // Failed - might be a duplicate username or database error
        error_log("Create admin user error: " . $e->getMessage());
        return false;
    }
}
?>
