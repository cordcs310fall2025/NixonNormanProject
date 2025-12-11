<?php
// admin_add_project.php - Add new project
session_start();
require_once 'adminAuth.php';
require_once 'db_config.php';

requireAdminLogin();

$message = '';
$messageType = '';

// Get all available categories for suggestions
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
    $title = trim($_POST['title']);
    $category = trim($_POST['category']);

    if (empty($title) || empty($category)) {
        $message = 'Project name and category are required';
        $messageType = 'error';
    } else {
        try {
            $pdo = getDatabaseConnection();
            $stmt = $pdo->prepare("
                INSERT INTO projects (title, category, created_at)
                VALUES (:title, :category, NOW())
            ");
            $stmt->execute([
                ':title' => $title,
                ':category' => $category
            ]);

            $newProjectId = $pdo->lastInsertId();

            // Redirect to manage images for the new project
            header("Location: manage_project_images.php?project_id=" . $newProjectId);
            exit;

        } catch (Exception $e) {
            $message = 'Error creating project: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
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
    <title>Add New Project — Nixon Norman Media</title>
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

        .admin-hero p {
            font-size: 1.1rem;
            opacity: 0.9;
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

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 6px;
            font-size: 1rem;
            font-family: 'Montserrat', sans-serif;
        }

        .form-group input:focus {
            outline: none;
            border-color: #325b78;
        }

        .form-help {
            color: #666;
            font-size: 0.9rem;
            margin-top: 5px;
        }

        .btn-create {
            background: #27ae60;
            color: white;
            padding: 14px 40px;
            border: none;
            border-radius: 6px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            width: 100%;
        }

        .btn-create:hover {
            background: #229954;
            transform: translateY(-2px);
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
        <h1>Add New Project</h1>
        <p>Create a new project and upload images</p>
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
                    <label for="title">Project Name *</label>
                    <input type="text" id="title" name="title" required placeholder="e.g., McLaren Wide Shot">
                    <p class="form-help">Give your project a descriptive name</p>
                </div>

                <div class="form-group">
                    <label for="category">Category / Tag *</label>
                    <input type="text" id="category" name="category" required placeholder="e.g., Automotive, Aerial, Agriculture" list="categories">
                    <datalist id="categories">
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo htmlspecialchars($cat); ?>">
                        <?php endforeach; ?>
                    </datalist>
                    <p class="form-help">Choose an existing category or create a new one</p>
                </div>

                <button type="submit" class="btn-create">Create Project & Add Images</button>
            </form>
        </div>
    </div>
</body>
</html>
