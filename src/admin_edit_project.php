<?php
// admin_edit_project.php - Edit project details
session_start();
require_once 'adminAuth.php';
require_once 'db_config.php';

requireAdminLogin();

$projectId = isset($_GET['id']) ? intval($_GET['id']) : 0;
$message = '';
$messageType = '';

if ($projectId <= 0) {
    header('Location: admin_projects_list.php');
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

// Get all available categories
function getCategories() {
    try {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->query("SELECT DISTINCT category FROM projects WHERE category IS NOT NULL ORDER BY category");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (Exception $e) {
        return [];
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newTitle = trim($_POST['title']);
    $newCategory = trim($_POST['category']);

    if (empty($newTitle) || empty($newCategory)) {
        $message = 'Title and category are required';
        $messageType = 'error';
    } else {
        try {
            $pdo = getDatabaseConnection();
            $stmt = $pdo->prepare("UPDATE projects SET title = :title, category = :category WHERE id = :id");
            $stmt->execute([
                ':title' => $newTitle,
                ':category' => $newCategory,
                ':id' => $projectId
            ]);
            $message = 'Project updated successfully!';
            $messageType = 'success';
        } catch (Exception $e) {
            $message = 'Error updating project: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

$project = getProject($projectId);

if (!$project) {
    header('Location: admin_projects_list.php');
    exit;
}

$categories = getCategories();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <title>Edit Project — Nixon Norman Media</title>
    <style>
        body {
            background: #f5f5f5;
            min-height: 100vh;
        }

        .admin-hero {
            background: linear-gradient(135deg, #325b78, #1e4159);
            color: white;
            padding: 60px 20px;
            text-align: center;
        }

        .admin-hero h1 {
            font-size: 2.5rem;
            margin: 0 0 10px 0;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .back-link {
            display: inline-block;
            color: #325b78;
            text-decoration: none;
            margin-bottom: 30px;
            font-weight: 600;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        .form-card {
            background: white;
            border-radius: 8px;
            padding: 40px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            color: #333;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            font-family: 'Montserrat', sans-serif;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #325b78;
        }

        .btn-group {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        .btn {
            padding: 14px 30px;
            border: none;
            border-radius: 6px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn-save {
            background: #27ae60;
            color: white;
            flex: 1;
        }

        .btn-save:hover {
            background: #229954;
        }

        .btn-manage {
            background: #325b78;
            color: white;
            flex: 1;
        }

        .btn-manage:hover {
            background: #1e4159;
        }

        .btn-delete {
            background: #e74c3c;
            color: white;
        }

        .btn-delete:hover {
            background: #c0392b;
        }

        .message {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-weight: 600;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .logout-btn {
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #e74c3c;
            color: white;
            padding: 12px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .logout-btn:hover {
            background: #c0392b;
        }
    </style>
</head>
<body>
    <a href="adminLogout.php" class="logout-btn">Logout</a>

    <section class="admin-hero">
        <h1>Edit Project</h1>
    </section>

    <div class="container">
        <a href="admin_projects_list.php" class="back-link">← Back to Projects</a>

        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="form-card">
            <form method="POST">
                <div class="form-group">
                    <label for="title">Project Name</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($project['title']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="category">Category / Tag</label>
                    <input type="text" id="category" name="category" value="<?php echo htmlspecialchars($project['category']); ?>" required list="categories">
                    <datalist id="categories">
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>">
                        <?php endforeach; ?>
                    </datalist>
                </div>

                <div class="btn-group">
                    <button type="submit" class="btn btn-save">Save Changes</button>
                    <a href="manage_project_images.php?project_id=<?php echo $projectId; ?>" class="btn btn-manage">Manage Images</a>
                </div>
            </form>
        </div>

        <div class="form-card">
            <h3 style="color: #e74c3c; margin-top: 0;">Danger Zone</h3>
            <p style="color: #666; margin-bottom: 20px;">Deleting this project will also delete all associated images. This cannot be undone.</p>
            <form method="POST" action="delete_project.php" onsubmit="return confirm('Are you sure you want to delete this project? This will also delete all images and cannot be undone!');">
                <input type="hidden" name="project_id" value="<?php echo $projectId; ?>">
                <button type="submit" class="btn btn-delete">Delete Project</button>
            </form>
        </div>
    </div>
</body>
</html>
