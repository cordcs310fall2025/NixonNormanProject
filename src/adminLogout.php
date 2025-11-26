<?php
/**
 * ============================================================
 * Admin Logout Page
 * ============================================================
 * This page logs out the current admin user and sends them
 * back to the main website.
 * 
 * How it works:
 * - Clears all login session data
 * - Destroys the session completely
 * - Redirects to home page
 * 
 * Usage: Just link to this page (e.g., <a href="adminLogout.php">Logout</a>)
 * ============================================================
 */

// Include our authentication functions
require_once 'adminAuth.php';

// Log out the current admin user
// This removes all session data and destroys the session
logoutAdmin();

// Redirect to the home page
// You could also redirect to the login page if you prefer
header('Location: homePage.php');
exit();
?>
