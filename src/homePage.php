<?php
// homePage.php - Dynamic Home Page for Nixon Norman Media

// Start session
session_start();

// Include database configuration
require_once 'db_config.php';

// Set page-specific variables
$pageTitle = "Nixon Norman Media — Home";
$currentPage = "home";

// Function to get home page content from database
function getHomeContent() {
    if (testDatabaseConnection()) {
        try {
            $pdo = getDatabaseConnection();
            $stmt = $pdo->query("SELECT * FROM home_content ORDER BY id DESC LIMIT 1");
            $dbContent = $stmt->fetch();
            
            if ($dbContent) {
                return [
                    'hero_title' => $dbContent['hero_title'],
                    'hero_subtitle' => $dbContent['hero_subtitle'],
                    'hero_description' => $dbContent['hero_description'],
                    'cta_button_text' => $dbContent['cta_button_text'],
                    'cta_button_link' => $dbContent['cta_button_link']
                ];
            }
        } catch (Exception $e) {
            error_log("Database error in homePage.php: " . $e->getMessage());
        }
    }
    
    // Default content (fallback)
    return [
        'hero_title' => 'Nixon Norman Media',
        'hero_subtitle' => 'Creative Visual Storytelling',
        'hero_description' => 'Professional photography and videography services.',
        'cta_button_text' => 'View Our Work',
        'cta_button_link' => 'projectsPage.php'
    ];
}

// Get all content
$homeContent = getHomeContent();

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
</head>
<body>
    <div class="header">
        <div class="inner_header">
            <div class="logo_container">
                    <img src="<?php echo $imagePath; ?>NNM-white.png" alt="Nixon Norman Media Logo" width="80" height="80">
            </div>
            <nav class="navigation">
                <ul>
                    <li><a href="homePage.php" <?php echo ($currentPage == 'home') ? 'class="active"' : ''; ?>>Home</a></li>
                    <li><a href="aboutPage.php" <?php echo ($currentPage == 'about') ? 'class="active"' : ''; ?>>About</a></li>
                    <li><a href="contactPage.php">Contact</a></li>
                    <li><a href="gearPage.php">Gear</a></li>
                    <li><a href="projectsPage.php">Projects</a></li>
                    <li><a href="adminHome.php">Admin</a></li>
                </ul>
            </nav>
        </div>
    </div>
    
    <div class="heroimage">  
        <img src="<?php echo $imagePath; ?>mclarenQuality.png" alt="Hero Image" width="100%" height="auto">
    </div>
    
    <section class="welcometext">
        <h2><?php echo htmlspecialchars($homeContent['hero_title']); ?></h2>
    </section>
 <section class="featuredWork">
        <h2>Featured Work</h2>
        <div class="grid">
            <div class="grid-item">
                <img src="<?php echo $imagePath; ?>tractorPhoto.png" alt="Tractor in field">
            </div>
            <div class="grid-item">
                <img src="<?php echo $imagePath; ?>supraPhoto.png" alt="Sports car">
            </div>
            <div class="grid-item">
                <img src="<?php echo $imagePath; ?>gymPhoto.png" alt="Gym workout">
            </div>
            <div class="grid-item">
                <img src="<?php echo $imagePath; ?>littleCar.png" alt="Classic car">
            </div>
            <div class="grid-item">
                <img src="<?php echo $imagePath; ?>f1Car.png" alt="Formula 1 car">
            </div>
        </div>
    </section>

    <section class="clients-section">
        <h1>Who I Have Worked With</h1>
        
        <div class="clients-grid">
            <div class="client-logo">
                <img src="<?php echo $imagePath; ?>espn-logo.png" alt="ESPN">
            </div>
            <div class="client-logo">
                <img src="<?php echo $imagePath; ?>marvinwindows-logo.png" alt="Marvin">
            </div>
            <div class="client-logo">
                <img src="<?php echo $imagePath; ?>reedRealty-logo.png" alt="Reed Realty">
            </div>
            <div class="client-logo client-logo-large">
                <img src="<?php echo $imagePath; ?>minnesotaWild-logo.png" alt="Minnesota Wild">
            </div>
            <div class="client-logo client-logo-large">
                <img src="<?php echo $imagePath; ?>sothebysInternationRealty-logo.png" alt="Sotheby's International Realty">
            </div>
        </div>
    </section>

<footer class="footer">
    <div class="footer-content">
        <p class="footer-email">NixonNormanMedia@gmail.com</p>
        <div class="social-icons">
            <a href="https://www.instagram.com/nixon_norman_media/" target="_blank" aria-label="Instagram">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                    <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                    <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                </svg>
            </a>
            <a href="https://www.youtube.com/@nixonnorman" target="_blank" aria-label="YouTube">
                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"></path>
                    <polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"></polygon>
                </svg>
            </a>
           <a href="https://linkedin.com/in/yourprofile" target="_blank" aria-label="LinkedIn">
            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path>
            <rect x="2" y="9" width="4" height="12"></rect>
            <circle cx="4" cy="4" r="2"></circle>
            </svg>
        </a>
        </div>
    </div>
</footer>    
</body>
</html>