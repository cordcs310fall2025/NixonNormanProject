<?php
// delete_project.php - Handle project deletion
session_start();
require_once 'adminAuth.php';
require_once 'db_config.php';

requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: admin_projects_list.php');
    exit;
}

$projectId = isset($_POST['project_id']) ? intval($_POST['project_id']) : 0;

if ($projectId <= 0) {
    header('Location: admin_projects_list.php');
    exit;
}

try {
    $pdo = getDatabaseConnection();

    // Get all images for this project to delete physical files
    $stmt = $pdo->prepare("SELECT filename FROM project_images WHERE project_id = :project_id");
    $stmt->execute([':project_id' => $projectId]);
    $images = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Delete physical image files
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/NixonNormanProject/uploads/projects/';
    foreach ($images as $filename) {
        $filePath = $uploadDir . $filename;
        if (file_exists($filePath)) {
            unlink($filePath);
        }
    }

    // Delete from database (images will be auto-deleted due to CASCADE)
    $stmt = $pdo->prepare("DELETE FROM projects WHERE id = :id");
    $stmt->execute([':id' => $projectId]);

    // Redirect back to list with success message
    header('Location: admin_projects_list.php?deleted=1');
    exit;

} catch (Exception $e) {
    error_log("Error deleting project: " . $e->getMessage());
    header('Location: admin_projects_list.php?error=1');
    exit;
}
?>
