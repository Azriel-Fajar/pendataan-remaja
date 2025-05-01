<?php
require_once 'includes/config.php';
require_once 'includes/storage.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$response = ['success' => false, 'message' => 'Invalid action'];

try {
    switch ($action) {
        case 'delete':
            $id = $_GET['id'] ?? '';
            if ($id && deleteAttendance($id)) {
                $response = ['success' => true, 'message' => 'Record deleted successfully'];
            } else {
                $response = ['success' => false, 'message' => 'Invalid ID or deletion failed'];
            }
            break;
    }
} catch (Exception $e) {
    $response = ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
}

echo json_encode($response);
