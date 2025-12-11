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

     <!-- Navigation -->
     <?php include '../includes/header.php'; ?>
    
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

    <!--FOOTER-->
    <?php include '../includes/footer.php'; ?>  
</body>
</html>