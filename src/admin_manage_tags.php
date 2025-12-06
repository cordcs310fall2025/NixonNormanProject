<?php
// admin_manage_tags.php - Manage project categories/tags
session_start();
require_once 'adminAuth.php';
require_once 'db_config.php';

requireAdminLogin();

$message = '';
$messageType = '';

// Get all categories with project counts
function getCategoriesWithCounts() {
    try {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->query("
            SELECT category, COUNT(*) as project_count
            FROM projects
            WHERE category IS NOT NULL
            GROUP BY category
            ORDER BY category
        ");
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Database error: " . $e->getMessage());
        return [];
    }
}

// Handle tag renaming
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'rename') {
    $oldTag = trim($_POST['old_tag']);
    $newTag = trim($_POST['new_tag']);

    if (empty($oldTag) || empty($newTag)) {
        $message = 'Both old and new tag names are required';
        $messageType = 'error';
    } elseif ($oldTag === $newTag) {
        $message = 'New tag name must be different';
        $messageType = 'error';
    } else {
        try {
            $pdo = getDatabaseConnection();
            $stmt = $pdo->prepare("UPDATE projects SET category = :new_tag WHERE category = :old_tag");
            $stmt->execute([
                ':old_tag' => $oldTag,
                ':new_tag' => $newTag
            ]);
            $count = $stmt->rowCount();
            $message = "Successfully renamed tag from '{$oldTag}' to '{$newTag}' ({$count} projects updated)";
            $messageType = 'success';
        } catch (Exception $e) {
            $message = 'Error renaming tag: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

// Handle adding new tag
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    $newTag = trim($_POST['new_tag']);

    if (empty($newTag)) {
        $message = 'Tag name is required';
        $messageType = 'error';
    } else {
        // Check if tag already exists
        try {
            $pdo = getDatabaseConnection();
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM projects WHERE category = :tag");
            $stmt->execute([':tag' => $newTag]);
            $exists = $stmt->fetchColumn() > 0;

            if ($exists) {
                $message = "Tag '{$newTag}' already exists";
                $messageType = 'error';
            } else {
                $message = "Tag '{$newTag}' is ready to use. Create a project with this tag to activate it.";
                $messageType = 'success';
            }
        } catch (Exception $e) {
            $message = 'Error: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

$categories = getCategoriesWithCounts();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <title>Manage Tags — Nixon Norman Media</title>
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
            max-width: 1000px;
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

        .section-card {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .section-card h2 {
            color: #325b78;
            margin: 0 0 20px 0;
        }

        .tags-grid {
            display: grid;
            gap: 15px;
        }

        .tag-item {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .tag-info {
            flex: 1;
        }

        .tag-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
        }

        .tag-count {
            color: #666;
            font-size: 0.9rem;
        }

        .tag-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-rename {
            background: #325b78;
            color: white;
        }

        .btn-rename:hover {
            background: #1e4159;
        }

        .form-group {
            margin-bottom: 20px;
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

        .btn-add {
            background: #27ae60;
            color: white;
        }

        .btn-add:hover {
            background: #229954;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 8px;
            padding: 30px;
            max-width: 500px;
            width: 90%;
        }

        .modal-header {
            margin-bottom: 20px;
        }

        .modal-header h3 {
            margin: 0;
            color: #325b78;
        }

        .btn-cancel {
            background: #6c757d;
            color: white;
        }

        .btn-cancel:hover {
            background: #5a6268;
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

        .no-tags {
            text-align: center;
            padding: 40px;
            color: #666;
        }
    </style>
</head>
<body>
    <a href="adminLogout.php" class="logout-btn">Logout</a>

    <section class="admin-hero">
        <h1>Manage Tags</h1>
        <p>Organize your project categories</p>
    </section>

    <div class="container">
        <a href="admin_projects_list.php" class="back-link">← Back to Projects</a>

        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <!-- Add New Tag Section -->
        <div class="section-card">
            <h2>Add New Tag</h2>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label for="new_tag_add">Tag Name</label>
                    <input type="text" id="new_tag_add" name="new_tag" required placeholder="e.g., Automotive, Aerial, Agriculture">
                </div>
                <button type="submit" class="btn btn-add">Add Tag</button>
            </form>
        </div>

        <!-- Existing Tags Section -->
        <div class="section-card">
            <h2>Existing Tags</h2>

            <?php if (empty($categories)): ?>
                <div class="no-tags">
                    <p>No tags yet. Create a project to add your first tag!</p>
                </div>
            <?php else: ?>
                <div class="tags-grid">
                    <?php foreach ($categories as $category): ?>
                        <div class="tag-item">
                            <div class="tag-info">
                                <div class="tag-name"><?php echo htmlspecialchars($category['category']); ?></div>
                                <div class="tag-count">
                                    <?php echo $category['project_count']; ?>
                                    <?php echo $category['project_count'] == 1 ? 'project' : 'projects'; ?>
                                </div>
                            </div>
                            <div class="tag-actions">
                                <button class="btn btn-rename" onclick="openRenameModal('<?php echo htmlspecialchars($category['category'], ENT_QUOTES); ?>')">
                                    Rename
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Rename Modal -->
    <div class="modal" id="renameModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Rename Tag</h3>
            </div>
            <form method="POST">
                <input type="hidden" name="action" value="rename">
                <input type="hidden" name="old_tag" id="old_tag">
                <div class="form-group">
                    <label for="new_tag_rename">New Tag Name</label>
                    <input type="text" id="new_tag_rename" name="new_tag" required>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="submit" class="btn btn-rename" style="flex: 1;">Save</button>
                    <button type="button" class="btn btn-cancel" onclick="closeRenameModal()" style="flex: 1;">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openRenameModal(tagName) {
            document.getElementById('old_tag').value = tagName;
            document.getElementById('new_tag_rename').value = tagName;
            document.getElementById('renameModal').classList.add('active');
        }

        function closeRenameModal() {
            document.getElementById('renameModal').classList.remove('active');
        }

        // Close modal when clicking outside
        document.getElementById('renameModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeRenameModal();
            }
        });
    </script>
</body>
</html>
