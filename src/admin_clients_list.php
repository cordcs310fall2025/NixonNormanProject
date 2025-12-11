<?php
// admin_clients_list.php - Manage clients
session_start();
require_once 'adminAuth.php';
require_once 'db_config.php';

requireAdminLogin();

// Get filter status if provided
$filterStatus = isset($_GET['status']) ? $_GET['status'] : 'all';

// Get all clients or filter by status
function getClients($status = 'all') {
    try {
        $pdo = getDatabaseConnection();
        
        if ($status === 'all') {
            $stmt = $pdo->query("SELECT * FROM clients ORDER BY created_at DESC");
        } else {
            $stmt = $pdo->prepare("SELECT * FROM clients WHERE status = :status ORDER BY created_at DESC");
            $stmt->execute([':status' => $status]);
        }
        
        return $stmt->fetchAll();
    } catch (Exception $e) {
        error_log("Database error: " . $e->getMessage());
        return [];
    }
}

$clients = getClients($filterStatus);
$imagePath = '/NixonNormanProject/images/';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <title>Manage Clients — Nixon Norman Media</title>
    <style>
        body {
            background: #f5f5f5;
            min-height: 100vh;
        }

        .admin-hero {
            background: linear-gradient(135deg, #325b78, #1e4159);
            color: white;
            padding: 100px 20px 40px;
            text-align: center;
        }

        .admin-hero h1 {
            font-size: 2.5rem;
            margin: 0 0 10px 0;
        }

        .filter-tabs {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }

        .filter-tab {
            padding: 10px 20px;
            background: white;
            border: 2px solid #325b78;
            border-radius: 6px;
            text-decoration: none;
            color: #325b78;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .filter-tab:hover,
        .filter-tab.active {
            background: #325b78;
            color: white;
        }

        .clients-container {
            max-width: 1400px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .action-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .clients-table {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #325b78;
            color: white;
            padding: 15px;
            text-align: left;
            font-weight: 600;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .status-badge {
            display: inline-block;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .status-potential {
            background: #fff3cd;
            color: #856404;
        }

        .status-current {
            background: #d4edda;
            color: #155724;
        }

        .status-past {
            background: #f8d7da;
            color: #721c24;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #325b78;
            color: white;
        }

        .btn-primary:hover {
            background: #1e4159;
        }

        .btn-small {
            padding: 6px 12px;
            font-size: 0.85rem;
        }

        .btn-edit {
            background: #28a745;
            color: white;
        }

        .btn-edit:hover {
            background: #218838;
        }

        .btn-delete {
            background: #dc3545;
            color: white;
        }

        .btn-delete:hover {
            background: #c82333;
        }

        .no-clients {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        @media (max-width: 768px) {
            .clients-table {
                overflow-x: auto;
            }

            table {
                min-width: 800px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <div class="header">
        <div class="inner_header">
            <div class="logo_container">
                <img src="<?php echo $imagePath; ?>NNM-white.png" alt="Nixon Norman Media Logo" width="80" height="80">
            </div>
            <nav class="navigation">
                <ul>
                    <li><a href="homePage.php">Home</a></li>
                    <li><a href="aboutPage.php">About</a></li>
                    <li><a href="contactPage.php">Contact</a></li>
                    <li><a href="projectsPage.php">Projects</a></li>
                    <li><a href="adminHome.php">Admin</a></li>
                </ul>
            </nav>
        </div>
    </div>

    <!-- Hero Section -->
    <section class="admin-hero">
        <h1>Client Management</h1>
        <p>View and manage your clients</p>
    </section>

    <!-- Main Content -->
    <div class="clients-container">
        <!-- Filter Tabs -->
        <div class="filter-tabs">
            <a href="?status=all" class="filter-tab <?php echo $filterStatus === 'all' ? 'active' : ''; ?>">All Clients</a>
            <a href="?status=potential" class="filter-tab <?php echo $filterStatus === 'potential' ? 'active' : ''; ?>">Potential</a>
            <a href="?status=current" class="filter-tab <?php echo $filterStatus === 'current' ? 'active' : ''; ?>">Current</a>
            <a href="?status=past" class="filter-tab <?php echo $filterStatus === 'past' ? 'active' : ''; ?>">Past</a>
        </div>

        <!-- Action Bar -->
        <div class="action-bar">
            <h2 style="color: #325b78; margin: 0;">
                <?php 
                if ($filterStatus === 'all') {
                    echo 'All Clients (' . count($clients) . ')';
                } else {
                    echo ucfirst($filterStatus) . ' Clients (' . count($clients) . ')';
                }
                ?>
            </h2>
            <a href="admin_add_client.php" class="btn btn-primary">+ Add New Client</a>
        </div>

        <!-- Clients Table -->
        <?php if (!empty($clients)): ?>
            <div class="clients-table">
                <table>
                    <thead>
                        <tr>
                            <th>Client Name</th>
                            <th>Company</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Status</th>
                            <th>Services</th>
                            <th>Total Paid</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clients as $client): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($client['client_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($client['company_name'] ?? '—'); ?></td>
                                <td><?php echo htmlspecialchars($client['email']); ?></td>
                                <td><?php echo htmlspecialchars($client['phone'] ?? '—'); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $client['status']; ?>">
                                        <?php echo ucfirst($client['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($client['services_provided'] ?? '—'); ?></td>
                                <td>$<?php echo number_format($client['total_paid'], 2); ?></td>
                                <td>
                                    <a href="admin_edit_client.php?id=<?php echo $client['id']; ?>" class="btn btn-small btn-edit">Edit</a>
                                    <a href="admin_delete_client.php?id=<?php echo $client['id']; ?>" 
                                       class="btn btn-small btn-delete" 
                                       onclick="return confirm('Are you sure you want to delete this client?')">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="no-clients">
                <h3>No clients found</h3>
                <p>Start by adding your first client!</p>
                <a href="admin_add_client.php" class="btn btn-primary" style="margin-top: 20px;">+ Add New Client</a>
            </div>
        <?php endif; ?>

        <!-- Back Button -->
        <div style="margin-top: 30px; text-align: center;">
            <a href="adminHome.php" class="btn btn-secondary">← Back to Dashboard</a>
        </div>
    </div>
</body>
</html>
