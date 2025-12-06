<?php
// admin_projects_list.php - Simple list of all projects
session_start();
require_once 'adminAuth.php';
require_once 'db_config.php';

requireAdminLogin();

// Get all projects from database
function getAllProjects() {
    try {
        $pdo = getDatabaseConnection();
        $stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Database error: " . $e->getMessage());
        return [];
    }
}

$projects = getAllProjects();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <title>Manage Projects — Nixon Norman Media</title>
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
            max-width: 1200px;
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

        .projects-grid {
            display: grid;
            gap: 20px;
        }

        .project-card {
            background: white;
            border-radius: 8px;
            padding: 25px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }

        .project-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .project-info {
            flex: 1;
        }

        .project-info h3 {
            color: #333;
            margin: 0 0 10px 0;
            font-size: 1.3rem;
        }

        .project-category {
            display: inline-block;
            background: #325b78;
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .btn-edit {
            background: #325b78;
            color: white;
            padding: 12px 30px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: background 0.3s;
        }

        .btn-edit:hover {
            background: #1e4159;
        }

        .no-projects {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 8px;
        }

        .no-projects h2 {
            color: #666;
            margin-bottom: 10px;
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
            transition: background 0.3s;
        }

        .logout-btn:hover {
            background: #c0392b;
        }
    </style>
</head>
<body>
    <a href="adminLogout.php" class="logout-btn">Logout</a>

    <section class="admin-hero">
        <div class="admin-content">
            <h1>All Projects</h1>
            <p>Click a project to edit or manage images</p>
        </div>
    </section>

    <div class="container">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <a href="adminHome.php" class="back-link" style="margin: 0;">← Back to Dashboard</a>
            <div style="display: flex; gap: 10px;">
                <a href="admin_manage_tags.php" class="btn-edit" style="background: #7f8c8d;">Manage Tags</a>
                <a href="admin_add_project.php" class="btn-edit" style="background: #27ae60;">+ Add New Project</a>
            </div>
        </div>

        <div class="projects-grid">
            <?php if (!empty($projects)): ?>
                <?php foreach ($projects as $project): ?>
                    <div class="project-card">
                        <div class="project-info">
                            <h3><?php echo htmlspecialchars($project['title']); ?></h3>
                            <span class="project-category"><?php echo htmlspecialchars($project['category']); ?></span>
                        </div>
                        <a href="admin_edit_project.php?id=<?php echo $project['id']; ?>" class="btn-edit">Edit</a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-projects">
                    <h2>No projects yet</h2>
                    <p>Projects you create will appear here.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
