<?php
// manage_project_images.php - Admin interface for managing project images
session_start();
require_once 'db_config.php';

// Check if user is logged in as admin
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: adminLogin.php');
    exit;
}

// Get project ID from URL
$projectId = isset($_GET['project_id']) ? intval($_GET['project_id']) : 0;

if ($projectId <= 0) {
    header('Location: adminItemList.html');
    exit;
}

// Get project details
function getProject($id) {
    try {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    } catch (Exception $e) {
        return null;
    }
}

$project = getProject($projectId);

if (!$project) {
    header('Location: adminItemList.html');
    exit;
}

// Get all images for this project
function getProjectImages($projectId) {
    try {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->prepare("
            SELECT * FROM project_images
            WHERE project_id = :project_id
            ORDER BY display_order ASC, uploaded_at DESC
        ");
        $stmt->execute([':project_id' => $projectId]);
        return $stmt->fetchAll();
    } catch (Exception $e) {
        return [];
    }
}

$images = getProjectImages($projectId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <title>Nixon Norman Media — Manage Project Images</title>
    <style>
        .image-manager {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }

        .project-info {
            background: #2a2a2a;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .project-info h2 {
            color: #fff;
            margin: 0 0 10px 0;
        }

        .project-info p {
            color: #888;
            margin: 5px 0;
        }

        .upload-section {
            background: #2a2a2a;
            padding: 30px;
            border-radius: 8px;
            margin-bottom: 30px;
        }

        .upload-area {
            border: 2px dashed #555;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: #1a1a1a;
        }

        .upload-area:hover,
        .upload-area.drag-over {
            border-color: #4CAF50;
            background: #252525;
        }

        .upload-area.drag-over {
            transform: scale(1.02);
        }

        .upload-icon {
            font-size: 48px;
            color: #555;
            margin-bottom: 20px;
        }

        .upload-area h3 {
            color: #fff;
            margin: 0 0 10px 0;
        }

        .upload-area p {
            color: #888;
            margin: 5px 0;
        }

        #fileInput {
            display: none;
        }

        .btn-upload {
            background: #4CAF50;
            color: white;
            padding: 10px 30px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 15px;
        }

        .btn-upload:hover {
            background: #45a049;
        }

        .images-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .image-card {
            background: #2a2a2a;
            border-radius: 8px;
            overflow: hidden;
            position: relative;
            transition: transform 0.3s;
        }

        .image-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
        }

        .image-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            display: block;
        }

        .image-info {
            padding: 15px;
        }

        .image-info p {
            color: #888;
            font-size: 12px;
            margin: 5px 0;
        }

        .image-actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }

        .btn-delete {
            background: #f44336;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            flex: 1;
        }

        .btn-delete:hover {
            background: #da190b;
        }

        .no-images {
            text-align: center;
            padding: 60px 20px;
            color: #888;
        }

        .upload-progress {
            display: none;
            margin-top: 20px;
        }

        .progress-bar {
            width: 100%;
            height: 30px;
            background: #1a1a1a;
            border-radius: 15px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #4CAF50, #45a049);
            width: 0%;
            transition: width 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 12px;
        }

        .message {
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            display: none;
        }

        .message.success {
            background: #4CAF50;
            color: white;
        }

        .message.error {
            background: #f44336;
            color: white;
        }

        .back-link {
            display: inline-block;
            color: #4CAF50;
            text-decoration: none;
            margin-bottom: 20px;
        }

        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <?php include '../includes/header.php'; ?>

    <div class="image-manager">
        <a href="adminItemList.html" class="back-link">← Back to Projects List</a>

        <div class="project-info">
            <h2><?php echo htmlspecialchars($project['title']); ?></h2>
            <p>Project ID: #<?php echo $project['id']; ?></p>
            <p>Category: <?php echo htmlspecialchars($project['category']); ?></p>
        </div>

        <div class="upload-section">
            <h2 style="color: white; margin-top: 0;">Upload New Images</h2>

            <div class="upload-area" id="uploadArea">
                <div class="upload-icon">📁</div>
                <h3>Drag & Drop Images Here</h3>
                <p>or</p>
                <button type="button" class="btn-upload" onclick="document.getElementById('fileInput').click()">
                    Choose Files
                </button>
                <p style="margin-top: 15px; font-size: 12px;">Supported formats: JPG, PNG, GIF, WebP (Max 10MB)</p>
            </div>

            <input type="file" id="fileInput" accept="image/*" multiple>

            <div class="upload-progress" id="uploadProgress">
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill">0%</div>
                </div>
            </div>

            <div class="message" id="message"></div>
        </div>

        <div style="background: #2a2a2a; padding: 30px; border-radius: 8px;">
            <h2 style="color: white; margin-top: 0;">Project Images (<?php echo count($images); ?>)</h2>

            <?php if (empty($images)): ?>
                <div class="no-images">
                    <p>No images uploaded yet. Upload some images to get started!</p>
                </div>
            <?php else: ?>
                <div class="images-grid" id="imagesGrid">
                    <?php foreach ($images as $image): ?>
                        <div class="image-card" data-image-id="<?php echo $image['id']; ?>">
                            <img src="<?php echo htmlspecialchars($image['file_path']); ?>"
                                 alt="<?php echo htmlspecialchars($image['original_filename']); ?>">
                            <div class="image-info">
                                <p><strong><?php echo htmlspecialchars($image['original_filename']); ?></strong></p>
                                <p>Size: <?php echo number_format($image['file_size'] / 1024, 2); ?> KB</p>
                                <p>Uploaded: <?php echo date('M j, Y', strtotime($image['uploaded_at'])); ?></p>
                                <div class="image-actions">
                                    <button class="btn-delete" onclick="deleteImage(<?php echo $image['id']; ?>)">
                                        Delete
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const projectId = <?php echo $projectId; ?>;
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('fileInput');
        const uploadProgress = document.getElementById('uploadProgress');
        const progressFill = document.getElementById('progressFill');
        const messageDiv = document.getElementById('message');
        const imagesGrid = document.getElementById('imagesGrid');

        // Prevent default drag behaviors
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
            document.body.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        // Highlight drop area when dragging over
        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, () => {
                uploadArea.classList.add('drag-over');
            }, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, () => {
                uploadArea.classList.remove('drag-over');
            }, false);
        });

        // Handle dropped files
        uploadArea.addEventListener('drop', (e) => {
            const files = e.dataTransfer.files;
            handleFiles(files);
        }, false);

        // Handle file input change
        fileInput.addEventListener('change', (e) => {
            handleFiles(e.target.files);
        });

        function handleFiles(files) {
            if (files.length === 0) return;

            showMessage('Uploading ' + files.length + ' file(s)...', 'success');
            uploadProgress.style.display = 'block';

            let uploadedCount = 0;
            const totalFiles = files.length;

            Array.from(files).forEach((file, index) => {
                uploadFile(file, () => {
                    uploadedCount++;
                    const progress = Math.round((uploadedCount / totalFiles) * 100);
                    progressFill.style.width = progress + '%';
                    progressFill.textContent = progress + '%';

                    if (uploadedCount === totalFiles) {
                        setTimeout(() => {
                            showMessage('All files uploaded successfully!', 'success');
                            uploadProgress.style.display = 'none';
                            progressFill.style.width = '0%';
                            fileInput.value = '';

                            // Reload page to show new images
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        }, 500);
                    }
                });
            });
        }

        function uploadFile(file, callback) {
            const formData = new FormData();
            formData.append('image', file);
            formData.append('project_id', projectId);

            fetch('upload_image.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    callback();
                } else {
                    showMessage('Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                showMessage('Upload failed: ' + error, 'error');
            });
        }

        function deleteImage(imageId) {
            if (!confirm('Are you sure you want to delete this image? This cannot be undone.')) {
                return;
            }

            fetch('upload_image.php', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ image_id: imageId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showMessage('Image deleted successfully', 'success');

                    // Remove image card from DOM
                    const imageCard = document.querySelector(`[data-image-id="${imageId}"]`);
                    if (imageCard) {
                        imageCard.remove();
                    }

                    // Reload page after a short delay
                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    showMessage('Error: ' + data.message, 'error');
                }
            })
            .catch(error => {
                showMessage('Delete failed: ' + error, 'error');
            });
        }

        function showMessage(text, type) {
            messageDiv.textContent = text;
            messageDiv.className = 'message ' + type;
            messageDiv.style.display = 'block';

            setTimeout(() => {
                messageDiv.style.display = 'none';
            }, 5000);
        }
    </script>
</body>
</html>
