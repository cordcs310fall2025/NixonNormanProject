<?php
// admin_add_client.php - Add new client
session_start();
require_once 'adminAuth.php';
require_once 'db_config.php';

requireAdminLogin();

$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clientName = trim($_POST['client_name']);
    $companyName = trim($_POST['company_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $status = $_POST['status'];
    $servicesProvided = trim($_POST['services_provided']);
    $totalPaid = floatval($_POST['total_paid']);
    $notes = trim($_POST['notes']);
    $firstContactDate = $_POST['first_contact_date'] ?: null;
    $lastProjectDate = $_POST['last_project_date'] ?: null;

    if (empty($clientName) || empty($email)) {
        $message = 'Client name and email are required';
        $messageType = 'error';
    } else {
        try {
            $pdo = getDatabaseConnection();
            $stmt = $pdo->prepare("
                INSERT INTO clients (client_name, company_name, email, phone, status, services_provided, total_paid, notes, first_contact_date, last_project_date)
                VALUES (:client_name, :company_name, :email, :phone, :status, :services_provided, :total_paid, :notes, :first_contact_date, :last_project_date)
            ");
            
            $stmt->execute([
                ':client_name' => $clientName,
                ':company_name' => $companyName ?: null,
                ':email' => $email,
                ':phone' => $phone ?: null,
                ':status' => $status,
                ':services_provided' => $servicesProvided ?: null,
                ':total_paid' => $totalPaid,
                ':notes' => $notes ?: null,
                ':first_contact_date' => $firstContactDate,
                ':last_project_date' => $lastProjectDate
            ]);

            header("Location: admin_clients_list.php");
            exit;

        } catch (Exception $e) {
            $message = 'Error creating client: ' . $e->getMessage();
            $messageType = 'error';
        }
    }
}

$imagePath = '/NixonNormanProject/images/';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    <title>Add Client — Nixon Norman Media</title>
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
            margin: 0;
        }

        .form-container {
            max-width: 800px;
            margin: 40px auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #325b78;
            font-weight: 600;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0e0e0;
            border-radius: 6px;
            font-family: 'Montserrat', sans-serif;
            transition: border-color 0.3s ease;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #325b78;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .message {
            padding: 15px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: #325b78;
            color: white;
        }

        .btn-primary:hover {
            background: #1e4159;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }

        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .form-container {
                padding: 20px;
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
        <h1>Add New Client</h1>
    </section>

    <!-- Form Container -->
    <div class="form-container">
        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="form-row">
                <div class="form-group">
                    <label for="client_name">Client Name *</label>
                    <input type="text" id="client_name" name="client_name" required>
                </div>

                <div class="form-group">
                    <label for="company_name">Company Name</label>
                    <input type="text" id="company_name" name="company_name">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email *</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="tel" id="phone" name="phone">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="status">Status *</label>
                    <select id="status" name="status" required>
                        <option value="potential">Potential</option>
                        <option value="current">Current</option>
                        <option value="past">Past</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="total_paid">Total Paid ($)</label>
                    <input type="number" id="total_paid" name="total_paid" step="0.01" value="0.00" min="0">
                </div>
            </div>

            <div class="form-group">
                <label for="services_provided">Services Provided</label>
                <input type="text" id="services_provided" name="services_provided" 
                       placeholder="e.g., Commercial Photography, Video Production">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="first_contact_date">First Contact Date</label>
                    <input type="date" id="first_contact_date" name="first_contact_date">
                </div>

                <div class="form-group">
                    <label for="last_project_date">Last Project Date</label>
                    <input type="date" id="last_project_date" name="last_project_date">
                </div>
            </div>

            <div class="form-group">
                <label for="notes">Notes</label>
                <textarea id="notes" name="notes" rows="4" 
                          placeholder="Any additional notes about this client..."></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Add Client</button>
                <a href="admin_clients_list.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</body>
</html>
