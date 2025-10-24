<?php
require_once "../autoload.php";

header('Content-Type: application/json');

try {
    $db = (new Database())->connect();
    if (!$db) {
        echo json_encode(["data" => []]);
        exit;
    }

    // Fetch raw timestamp columns from DB; format in PHP below.
    $stmt = $db->query("
        SELECT id, full_name, username, email, created_at, updated_at, is_verified
        FROM users
        ORDER BY id ASC
    ");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Format created_at / updated_at for DataTables display (d-m-Y H:i)
    if (!empty($users)) {
        foreach ($users as &$user) {
            // Null-safe formatting; keep empty string if no timestamp present
            $user['created_at'] = !empty($user['created_at'])
                ? date('d-m-Y H:i', strtotime($user['created_at']))
                : '';
            $user['updated_at'] = !empty($user['updated_at'])
                ? date('d-m-Y H:i', strtotime($user['updated_at']))
                : '';
        }
        unset($user);
    }

    echo json_encode(["data" => $users]);
} catch (PDOException $e) {
    // Log the error for backend debugging, but do not expose full error to clients.
    error_log("fetchUsers.php PDOException: " . $e->getMessage());
    echo json_encode(["data" => [], "error" => "Server error fetching users"]);
}
?>
