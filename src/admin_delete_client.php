<?php
// admin_delete_client.php - Delete a client
session_start();
require_once 'adminAuth.php';
require_once 'db_config.php';

requireAdminLogin();

$clientId = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($clientId <= 0) {
    header('Location: admin_clients_list.php');
    exit;
}

try {
    $pdo = getDatabaseConnection();
    
    // Delete the client
    $stmt = $pdo->prepare("DELETE FROM clients WHERE id = :id");
    $stmt->execute([':id' => $clientId]);
    
    // Redirect back to clients list
    header('Location: admin_clients_list.php');
    exit;
    
} catch (Exception $e) {
    error_log("Error deleting client: " . $e->getMessage());
    header('Location: admin_clients_list.php');
    exit;
}
?>
