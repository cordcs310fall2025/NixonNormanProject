<?php
/**
 * ============================================================
 * Admin Dashboard Home Page
 * ============================================================
 * This is the main admin control panel where logged-in admins
 * can access all management tools for the website.
 * 
 * Features:
 * - Requires admin login (unauthorized users are redirected)
 * - Shows quick links to manage projects, media, gear, etc.
 * - Displays admin username and logout option
 * - Protected from public access
 * ============================================================
 */

// Start the session
session_start();

// Include authentication functions
require_once 'adminAuth.php';

// SECURITY CHECK: Make sure user is logged in as admin
// If not logged in, they'll be sent to the login page
requireAdminLogin();

// Get the logged-in admin's username for display
$adminUsername = getAdminUsername();

// Set page variables
$pageTitle = "Admin Dashboard — Nixon Norman Media";
$currentPage = "admin";

// Set base path for images
$imagePath = '/NixonNormanProject/images/';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <style>
        /* Additional styles for the admin dashboard */
        
        /* Hero section at the top */
        .admin-hero {
            background: linear-gradient(135deg, #325b78, #1e4159);
            color: white;
            padding: 80px 20px;
            text-align: center;
        }
        
        .admin-content h1 {
            font-size: 3rem;
            margin-bottom: 10px;
        }
        
        .admin-content p {
            font-size: 1.2rem;
            opacity: 0.9;
        }
        
        /* Welcome message with username */
        .admin-welcome {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: white;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            z-index: 1000;
        }
        
        .admin-welcome p {
            color: #333;
            font-size: 0.9rem;
            margin: 0;
        }
        
        .admin-welcome a {
            color: white;
            background: #e74c3c;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            transition: background 0.3s ease;
        }
        
        .admin-welcome a:hover {
            background: #c0392b;
        }
        
        /* Main dashboard section */
        .admin-dashboard {
            padding: 60px 20px;
            background: #f5f5f5;
        }
        
        /* Grid of dashboard cards */
        .dashboard-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto 60px;
        }
        
        /* Individual dashboard card */
        .dashboard-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            text-decoration: none;
            position: relative;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .dashboard-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        }
        
        /* Colored overlay on each card */
        .card-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            opacity: 0.05;
        }
        
        /* Different colors for different cards */
        .card-projects .card-overlay { background: #325b78; }
        .card-media .card-overlay { background: #e74c3c; }
        .card-gear .card-overlay { background: #27ae60; }
        .card-analytics .card-overlay { background: #f39c12; }
        
        /* Content inside each card */
        .card-content {
            padding: 40px 30px;
            position: relative;
            z-index: 1;
        }
        
        /* Icon at top of card */
        .card-icon {
            margin-bottom: 20px;
        }
        
        .card-icon svg {
            stroke: #325b78;
        }
        
        /* Card title */
        .card-content h3 {
            color: #333;
            font-size: 1.5rem;
            margin-bottom: 10px;
        }
        
        /* Large number displayed on card */
        .card-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #325b78;
            margin: 15px 0;
        }
        
        /* Link text at bottom of card */
        .card-link {
            color: #666;
            font-size: 0.9rem;
            font-weight: 600;
        }
        
        /* Recent activity / quick actions section */
        .recent-activity {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .recent-activity h2 {
            color: #325b78;
            margin-bottom: 30px;
            font-size: 2rem;
        }
        
        /* Container for action buttons */
        .action-buttons {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        
        /* Individual action button */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 15px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            justify-content: center;
        }
        
        /* Primary button (blue) */
        .btn-primary {
            background: #325b78;
            color: white;
        }
        
        .btn-primary:hover {
            background: #1e4159;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(50, 91, 120, 0.3);
        }
        
        /* Secondary button (gray) */
        .btn-secondary {
            background: #7f8c8d;
            color: white;
        }
        
        .btn-secondary:hover {
            background: #6c7a7b;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(127, 140, 141, 0.3);
        }
        
        /* Accent button (green) */
        .btn-accent {
            background: #27ae60;
            color: white;
        }
        
        .btn-accent:hover {
            background: #229954;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(39, 174, 96, 0.3);
        }
        
        /* Responsive design for smaller screens */
        @media (max-width: 768px) {
            .admin-content h1 {
                font-size: 2rem;
            }
            
            .dashboard-container {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                grid-template-columns: 1fr;
            }
            
            .recent-activity {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Main navigation header -->
    <div class="header">
        <div class="inner_header">
            <div class="logo_container">
                <img src="<?php echo $imagePath; ?>NNM-white.png" alt="Nixon Norman Media Logo" width="80" height="80">
            </div>
            <nav class="navigation">
                <ul>
                    <li><a href="homePage.php">Home</a></li>
                    <li><a href="aboutPage.php">About</a></li>
                    <li><a href="contactPage.php">Contact</a></li>
                    <li><a href="projectsPage.php">Projects</a></li>
                    <li><a href="adminHome.php" class="active">Admin</a></li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Logout button in bottom right corner -->
    <div class="admin-welcome">
        <p>
            <a href="adminLogout.php">Logout</a>
        </p>
    </div>

    <!-- Hero section -->
    <section class="admin-hero">
        <div class="admin-content">
            <h1>Admin Dashboard</h1>
            <p>Manage your projects</p>
        </div>
    </section>

    <!-- Main dashboard section -->
    <section class="admin-dashboard">
        <!-- Quick actions section -->
        <div class="recent-activity">
            <h2>Manage Projects</h2>
            <div class="action-buttons">
                <!-- View all projects button -->
                <a href="admin_projects_list.php" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                        <circle cx="8.5" cy="8.5" r="1.5"></circle>
                        <polyline points="21 15 16 10 5 21"></polyline>
                    </svg>
                    View All Projects
                </a>

                <!-- Manage tags button -->
                <a href="admin_manage_tags.php" class="btn btn-secondary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path>
                        <line x1="7" y1="7" x2="7.01" y2="7"></line>
                    </svg>
                    Manage Tags
                </a>
            </div>
        </div>

        <!-- Client Management Section -->
        <div class="recent-activity" style="margin-top: 40px;">
            <h2>Manage Clients</h2>
            <div class="action-buttons">
                <!-- View all clients button -->
                <a href="admin_clients_list.php" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                    View All Clients
                </a>

                <!-- Add new client button -->
                <a href="admin_add_client.php" class="btn btn-accent">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="8.5" cy="7" r="4"></circle>
                        <line x1="20" y1="8" x2="20" y2="14"></line>
                        <line x1="23" y1="11" x2="17" y2="11"></line>
                    </svg>
                    Add New Client
                </a>
            </div>
        </div>
    </section>

</body>
</html>
