<?php
/**
 * projects_functions.php
 * for managing and displaying projects
 */

/**
 * Get projects from DB, can be filtered by category
 * @param string $category Category to filter by, or 'ALL' for all projects
 * @return array Array of project records
 */

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
            error_log("Database error fetching projects: " . $e->getMessage());
        }
    }
    return [];
}

/**
 * Get all unique project categories from database
 * @return array Array of category names
 */

function getCategories() {
    if (testDatabaseConnection()) {
        try {
            $pdo = getDatabaseConnection();
            $stmt = $pdo->query("SELECT DISTINCT category FROM projects WHERE category IS NOT NULL ORDER BY category");
            return $stmt->fetchAll(PDO::FETCH_COLUMN);
        } catch (Exception $e) {
            error_log("Database error fetching categories: " . $e->getMessage());
        }
    }
    return [];
}
?>