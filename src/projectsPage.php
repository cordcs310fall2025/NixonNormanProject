<?php
// projectsPage.php - Projects Gallery

session_start();
require_once 'db_config.php';

$pageTitle = "Nixon Norman Media — Projects";
$currentPage = "projects";

// Get the selected category from URL (if any)
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : 'ALL';

// Function to get all projects or filter by category
function getProjects($category = 'ALL') {
    if (testDatabaseConnection()) {
        try {
            $pdo = getDatabaseConnection();
            
            if ($category === 'ALL') {
                $stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
            } else {
                $stmt = $pdo->prepare("SELECT * FROM projects WHERE category = :category ORDER BY created_at DESC");
                $stmt->bindValue(':category', $category, PDO::PARAM_STR);
                $stmt->execute();
            }
            
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Database error: " . $e->getMessage());
        }
    }
    return [];
}

// Get projects based on selected category
$projects = getProjects($selectedCategory);

// Get all unique categories for the filter buttons
function getCategories() {
    if (testDatabaseConnection()) {
        try {
            $pdo = getDatabaseConnection();
            $stmt = $pdo->query("SELECT DISTINCT category FROM projects WHERE category IS NOT NULL ORDER BY category");
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (Exception $e) {
            error_log("Database error: " . $e->getMessage());
        }
    }
    return [];
}

$categories = getCategories();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            list-style: none;
            text-decoration: none;
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            color: white;
            background-color: #325b78;
        }
        
        /* Hero Section */
        .hero {
            height: 100vh;
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)),
                        url('/images/photography.jpg') center/cover;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        
        .hero h1 {
            font-size: 8rem;
            margin-bottom: 1rem;
            font-weight: 700;
            letter-spacing: 0.1em;
        }

        
        .hero-subtitle {
            font-size: 1rem;
            letter-spacing: 0.3em;
            color: rgba(255, 255, 255, 0.9);
            margin-top: 0.5rem;
        }
        
        /* Filter Navigation Section */
        .filter-section {
            background: #325b78;
            padding: 2rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .filters {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 3rem;
            max-width: 1400px;
            margin: 0 auto;
        }

        .filter-link {
            color: #888;
            text-decoration: none;
            font-size: 0.9rem;
            font-weight: 500;
            letter-spacing: 0.15em;
            padding: 1rem 0;
            position: relative;
            transition: color 0.3s ease;
        }

        .filter-link:hover {
            color: #fff;
        }

        .filter-link.active {
            color: #fff;
        }

        .filter-link.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: #ffffff;
        }
        
        /* Gallery Section */
        .gallery {
            background:#325b78;
            padding: 5rem 2rem;
            min-height: 100vh;
        }

        .gallery-title {
            text-align: center;
            font-size: 3rem;
            color: #333;
            margin-bottom: 4rem;
            font-weight: 700;
        }
        
        .grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .photo-card {
            position: relative;
            overflow: hidden;
            cursor: pointer;
        }
        
        .photo-card img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .photo-card:hover img {
            transform: scale(1.1);
        }
        
        .overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.9), transparent);
            padding: 2rem;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .overlay p {
            font-size: 0.75rem;
            letter-spacing: 0.2em;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 0.5rem;
        }

        .overlay h3 {
            font-size: 1.5rem;
            color: white;
            font-weight: 700;
        }
        
        .photo-card:hover .overlay {
            opacity: 1;
        }
        
        /* Responsive Design */
        @media (max-width: 1024px) {
            .grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .hero h1 {
                font-size: 5rem;
            }

            .hero-logo {
                max-width: 200px;
            }

            .gallery-title {
                font-size: 2.5rem;
            }

            .filters {
                gap: 2rem;
            }
        }

        @media (max-width: 768px) {
            .grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .hero h1 {
                font-size: 3rem;
            }

            .hero-logo {
                max-width: 150px;
            }

            .gallery {
                padding: 3rem 1rem;
            }

            .gallery-title {
                font-size: 2rem;
                margin-bottom: 2rem;
            }

            .filters {
                gap: 1.5rem;
                flex-wrap: wrap;
                padding: 0 1rem;
            }

            .filter-link {
                font-size: 0.8rem;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <div class="header">
        <div class="inner_header">
            <div class="logo_container">
                <img src="../images/NNM-white.png" alt="Nixon Norman Media Logo" width="80" height="80">
            </div>
            <nav class="navigation">
                <ul>
                    <li><a href="homePage.php">HOME</a></li>
                    <li><a href="aboutPage.php">ABOUT</a></li>
                    <li><a href="contactPage.php">CONTACT</a></li>
                    <li><a href="gearPage.php">GEAR</a></li>
                    <li><a href="projectsPage.php">PROJECTS</a></li>
                    <li><a href="adminHome.php">ADMIN</a></li>
                </ul>
            </nav>
        </div>
    </div>
    
    <!-- Hero Section -->
    <section class="hero">
        <h1>GALLERY</h1>
        <p class="hero-subtitle">BROWSE MY WORK</p>
    </section>

    <!-- Filter Navigation -->
    <section class="filter-section">
        <div class="filters">
            <a href="projectsPage.php?category=ALL" class="filter-link <?php echo ($selectedCategory === 'ALL') ? 'active' : ''; ?>">
                ALL PHOTOS
            </a>
            <?php foreach ($categories as $cat): ?>
                <a href="projectsPage.php?category=<?php echo urlencode($cat); ?>" 
                   class="filter-link <?php echo ($selectedCategory === $cat) ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($cat); ?>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="gallery">
        <div class="grid">
            <?php if (!empty($projects)): ?>
                <?php foreach ($projects as $project): ?>
                    <div class="photo-card">
                        <img src="<?php echo htmlspecialchars($project['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($project['title']); ?>">
                        <div class="overlay">
                            <p><?php echo htmlspecialchars($project['category']); ?></p>
                            <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: white; text-align: center; width: 100%;">No projects found in this category.</p>
            <?php endif; ?>
        </div>
    </section>

    <footer class="footer">
        <div class="footer-content">
            <h3>Get in Touch</h3><br>
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