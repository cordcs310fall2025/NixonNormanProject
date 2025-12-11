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

// Function to get the featured image for a project
// NOTE: This function is no longer needed since image_url is stored directly in projects table
/*
function getFeaturedImage($projectId) {
    try {
        $pdo = getDatabaseConnection();

        // First, try to get a featured image
        $stmt = $pdo->prepare("
            SELECT file_path
            FROM project_images
            WHERE project_id = :project_id AND is_featured = 1
            LIMIT 1
        ");
        $stmt->execute([':project_id' => $projectId]);
        $image = $stmt->fetchColumn();

        // If no featured image, get the first image by display order
        if (!$image) {
            $stmt = $pdo->prepare("
                SELECT file_path
                FROM project_images
                WHERE project_id = :project_id
                ORDER BY display_order ASC, uploaded_at ASC
                LIMIT 1
            ");
            $stmt->execute([':project_id' => $projectId]);
            $image = $stmt->fetchColumn();
        }

        return $image;
    } catch (Exception $e) {
        error_log("Error getting featured image: " . $e->getMessage());
        return null;
    }
}
*/

// Get projects based on selected category
$projects = getProjects($selectedCategory);

// Set base path for images
$imagePath = '/NixonNormanProject/images/';

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
    <link rel="stylesheet" href="projectsPage_styles.css">
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
                    <?php
                    // Get the image URL directly from the projects table
                    $imageUrl = $project['image_url'];

                    if ($imageUrl): ?>
                        <div class="photo-card">
                            <img src="<?php echo htmlspecialchars($imageUrl); ?>"
                                 alt="<?php echo htmlspecialchars($project['title']); ?>">
                            <div class="overlay">
                                <p><?php echo htmlspecialchars($project['category']); ?></p>
                                <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: white; text-align: center; width: 100%;">No projects found in this category.</p>
            <?php endif; ?>
        </div>
    </section>

    <!--FOOTER-->
    <?php include '../includes/footer.php'; ?>

</body>
</html>