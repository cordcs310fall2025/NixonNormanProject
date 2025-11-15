<?php
/**
 * projectsPage.php
 * Projects Gallery - Displays portfolio with category filtering
 */

// initialize and load dependencies
session_start();
require_once 'db_config.php';
require_once '../includes/projects_functions.php';

// page config
$pageTitle = "Nixon Norman Media — Projects";
$currentPage = "projects";

// get cat. filter from URL param
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : 'ALL';

// get the data from DB
$projects = getProjects($selectedCategory);
$categories = getCategories();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../css/projects.css">

    <title><?php echo htmlspecialchars($pageTitle); ?></title>
    
</head>
<body>
    <!-- Navigation -->

    <?php include '../includes/header.php'; ?>

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

    <!--FOOTER -->
    <?php include '../includes/footer.php'; ?>

</body>
</html>