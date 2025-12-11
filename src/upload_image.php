<?php
// upload_image.php - Handle image uploads for projects
session_start();
require_once 'db_config.php';

// Check if user is logged in as admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

header('Content-Type: application/json');

// Configuration
$uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/NixonNormanProject/uploads/projects/';
$maxFileSize = 10 * 1024 * 1024; // 10MB
$allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

// Check if this is an upload request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['image'])) {
    $projectId = isset($_POST['project_id']) ? intval($_POST['project_id']) : 0;
    $caption = isset($_POST['caption']) ? trim($_POST['caption']) : '';

    if ($projectId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid project ID']);
        exit;
    }

    $file = $_FILES['image'];

    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errorMessages = [
            UPLOAD_ERR_INI_SIZE => 'File exceeds upload_max_filesize',
            UPLOAD_ERR_FORM_SIZE => 'File exceeds MAX_FILE_SIZE',
            UPLOAD_ERR_PARTIAL => 'File was only partially uploaded',
            UPLOAD_ERR_NO_FILE => 'No file was uploaded',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary folder',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
            UPLOAD_ERR_EXTENSION => 'Upload stopped by extension'
        ];
        $message = $errorMessages[$file['error']] ?? 'Unknown upload error';
        echo json_encode(['success' => false, 'message' => $message]);
        exit;
    }

    // Validate file size
    if ($file['size'] > $maxFileSize) {
        echo json_encode(['success' => false, 'message' => 'File size exceeds 10MB limit']);
        exit;
    }

    // Validate MIME type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mimeType, $allowedTypes)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file type. Only images allowed.']);
        exit;
    }

    // Validate file extension
    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileExtension, $allowedExtensions)) {
        echo json_encode(['success' => false, 'message' => 'Invalid file extension']);
        exit;
    }

    // Generate unique filename
    $originalFilename = $file['name'];
    $filename = uniqid('project_' . $projectId . '_', true) . '.' . $fileExtension;
    $filePath = $uploadDir . $filename;

    // Create upload directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    // Move uploaded file
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file']);
        exit;
    }

    // Get the highest display order for this project
    try {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->prepare("SELECT COALESCE(MAX(display_order), -1) + 1 as next_order FROM project_images WHERE project_id = :project_id");
        $stmt->execute([':project_id' => $projectId]);
        $nextOrder = $stmt->fetchColumn();

        // Insert into database
        $stmt = $pdo->prepare("
            INSERT INTO project_images
            (project_id, filename, original_filename, file_path, file_size, mime_type, display_order, caption)
            VALUES
            (:project_id, :filename, :original_filename, :file_path, :file_size, :mime_type, :display_order, :caption)
        ");

        $stmt->execute([
            ':project_id' => $projectId,
            ':filename' => $filename,
            ':original_filename' => $originalFilename,
            ':file_path' => '/NixonNormanProject/uploads/projects/' . $filename,
            ':file_size' => $file['size'],
            ':mime_type' => $mimeType,
            ':display_order' => $nextOrder,
            ':caption' => $caption
        ]);

        $imageId = $pdo->lastInsertId();

        echo json_encode([
            'success' => true,
            'message' => 'Image uploaded successfully',
            'image' => [
                'id' => $imageId,
                'filename' => $filename,
                'file_path' => '/NixonNormanProject/uploads/projects/' . $filename,
                'file_size' => $file['size'],
                'display_order' => $nextOrder
            ]
        ]);

    } catch (Exception $e) {
        // Delete the uploaded file if database insert fails
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
    }

    exit;
}

// Handle image deletion
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' || ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_method']) && $_POST['_method'] === 'DELETE')) {
    $input = json_decode(file_get_contents('php://input'), true);
    $imageId = isset($input['image_id']) ? intval($input['image_id']) : 0;

    if ($imageId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid image ID']);
        exit;
    }

    try {
        $pdo = getDatabaseConnection();

        // Get image info before deleting
        $stmt = $pdo->prepare("SELECT filename FROM project_images WHERE id = :id");
        $stmt->execute([':id' => $imageId]);
        $image = $stmt->fetch();

        if (!$image) {
            echo json_encode(['success' => false, 'message' => 'Image not found']);
            exit;
        }

        // Delete from database
        $stmt = $pdo->prepare("DELETE FROM project_images WHERE id = :id");
        $stmt->execute([':id' => $imageId]);

        // Delete physical file
        $filePath = $uploadDir . $image['filename'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        echo json_encode(['success' => true, 'message' => 'Image deleted successfully']);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error deleting image: ' . $e->getMessage()]);
    }

    exit;
}

// Handle getting images for a project
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['project_id'])) {
    $projectId = intval($_GET['project_id']);

    try {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->prepare("
            SELECT id, filename, original_filename, file_path, file_size, display_order, caption, is_featured, uploaded_at
            FROM project_images
            WHERE project_id = :project_id
            ORDER BY display_order ASC, uploaded_at DESC
        ");
        $stmt->execute([':project_id' => $projectId]);
        $images = $stmt->fetchAll();

        echo json_encode(['success' => true, 'images' => $images]);

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error fetching images: ' . $e->getMessage()]);
    }

    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>
