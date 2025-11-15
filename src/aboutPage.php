<?php
// aboutPage.php - Dynamic About Page for Nixon Norman Media

// Start session for potential admin functionality
session_start();

// Include database configuration
require_once 'db_config.php';

// Set page-specific variables
$pageTitle = "Nixon Norman Media — About";
$currentPage = "about";

// Function to get about content from database
function getAboutContent() {
    // Try to get content from database first
    if (testDatabaseConnection()) {
        try {
            $pdo = getDatabaseConnection();
            $stmt = $pdo->query("SELECT * FROM about_content ORDER BY id DESC LIMIT 1");
            $dbContent = $stmt->fetch();
            
            if ($dbContent) {
                return [
                    'headline' => $dbContent['headline'],
                    'intro' => $dbContent['intro'],
                    'bio' => $dbContent['bio'],
                    'mission' => $dbContent['mission'],
                    'services' => json_decode($dbContent['services'], true),
                    'experience_years' => $dbContent['experience_years'],
                    'projects_completed' => $dbContent['projects_completed'],
                    'happy_clients' => $dbContent['happy_clients']
                ];
            }
        } catch (Exception $e) {
            // Fall back to default content if database fails
            error_log("Database error in aboutPage.php: " . $e->getMessage());
        }
    }
    
    // Default content (fallback if database is not available)
    return [
        'headline' => 'About Nixon Norman Media',
        'intro' => 'Capturing moments that matter through the lens of creativity and passion.',
        'bio' => 'Nixon Norman Media specializes in professional photography and videography services. With years of experience in automotive, commercial, and event photography, we bring your vision to life with stunning visual storytelling.',
        'mission' => 'Our mission is to deliver exceptional visual content that exceeds expectations and creates lasting impressions.',
        'services' => [
            'Commercial Photography',
            'Event Photography', 
            'Automotive Photography',
            'Video Production',
            'Social Media Content'
        ],
        'experience_years' => '5+',
        'projects_completed' => '150+',
        'happy_clients' => '50+'
    ];
}

// Get the about content
$aboutContent = getAboutContent();

// Check if running from src/ subdirectory for image paths
$imageBasePath = (strpos($_SERVER['REQUEST_URI'], '/src/') !== false) ? '../images/' : '/images/';

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
        /* Additional styles for enhanced about page */
        .about-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .stat-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #325b78;
            margin-bottom: 10px;
        }
        
        .stat-label {
            color: #666;
            font-weight: 500;
        }
        
        .services-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }
        
        .service-item {
            background: linear-gradient(135deg, #325b78, #1e4159);
            color: white;
            padding: 25px;
            border-radius: 12px;
            text-align: center;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .service-item:hover {
            background: linear-gradient(135deg, #1e4159, #325b78);
            transform: scale(1.05);
        }
        
        .bio-section {
            max-width: 800px;
            margin: 60px auto;
            padding: 0 20px;
            text-align: center;
        }
        
        .bio-text {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #333;
            margin-bottom: 30px;
        }
        
        .mission-text {
            font-style: italic;
            font-size: 1.2rem;
            color: #325b78;
            border-left: 4px solid #325b78;
            padding-left: 20px;
            margin: 30px 0;
        }
        
        .about-image-container {
            text-align: center;
            margin: 40px 0;
        }
        
        .about-image-container img {
            max-width: 100%;
            height: auto;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.15);
        }
        
        @media (max-width: 768px) {
            .about-stats {
                grid-template-columns: 1fr;
            }
            
            .services-grid {
                grid-template-columns: 1fr;
            }
            
            .stat-number {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!--Navigation-->
    <?php include '../includes/header.php'; ?>

    <section class="welcometext">
        <h2><?php echo htmlspecialchars($aboutContent['headline']); ?></h2>
        <p style="color:white; padding-top:20px; font-size: 1.2rem;">
            <?php echo htmlspecialchars($aboutContent['intro']); ?>
        </p>
    </section>

    <!-- About Stats Section -->
    <section class="about-stats">
        <div class="stat-card">
            <div class="stat-number"><?php echo $aboutContent['experience_years']; ?></div>
            <div class="stat-label">Years Experience</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $aboutContent['projects_completed']; ?></div>
            <div class="stat-label">Projects Completed</div>
        </div>
        <div class="stat-card">
            <div class="stat-number"><?php echo $aboutContent['happy_clients']; ?></div>
            <div class="stat-label">Happy Clients</div>
        </div>
    </section>

    <!-- Bio Section -->
    <section class="bio-section">
        <p class="bio-text"><?php echo htmlspecialchars($aboutContent['bio']); ?></p>
        
        <blockquote class="mission-text">
            <?php echo htmlspecialchars($aboutContent['mission']); ?>
        </blockquote>
    </section>

    <!-- About Image -->
    <div class="about-image-container">  
        <img src="<?php echo $imageBasePath; ?>nixonaboutpicture.png" alt="Picture of Nixon" width="500px" height="auto">
    </div>

    <!-- Services Section -->
    <section style="background-color: #f5f5f5; padding: 60px 0;">
        <div style="text-align: center; margin-bottom: 40px;">
            <h2 style="color: #325b78; font-size: 2.5rem;">Our Services</h2>
        </div>
        <div class="services-grid">
            <?php foreach ($aboutContent['services'] as $service): ?>
                <div class="service-item">
                    <?php echo htmlspecialchars($service); ?>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Contact CTA Section -->
    <section style="background-color: #325b78; color: white; padding: 60px 0; text-align: center;">
        <h2 style="margin-bottom: 20px;">Ready to Work Together?</h2>
        <p style="font-size: 1.2rem; margin-bottom: 30px;">Let's create something amazing together.</p>
        <a href="contactPage.php" style="display: inline-block; background-color: white; color: #325b78; padding: 15px 30px; border-radius: 8px; text-decoration: none; font-weight: 600; transition: all 0.3s ease;" 
           onmouseover="this.style.backgroundColor='#f0f0f0'" 
           onmouseout="this.style.backgroundColor='white'">
            Get In Touch
        </a>
    </section>

   
    <script>
        // Add some interactivity with JavaScript
        document.addEventListener('DOMContentLoaded', function() {
            // Animate stats on scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, observerOptions);

            // Observe stat cards
            const statCards = document.querySelectorAll('.stat-card');
            statCards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(30px)';
                card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
                observer.observe(card);
            });

            // Observe service items
            const serviceItems = document.querySelectorAll('.service-item');
            serviceItems.forEach((item, index) => {
                item.style.opacity = '0';
                item.style.transform = 'translateY(30px)';
                item.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
                observer.observe(item);
            });
        });
    </script>
</body>
</html>
