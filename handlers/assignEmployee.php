<?php
require_once "../autoload.php";

header('Content-Type: application/json');

try {
    SessionManager::requireAuth();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $companyId = (int)($_POST['company_id'] ?? 0);
    $userId = (int)($_POST['user_id'] ?? 0);
    $positionTitle = trim($_POST['position_title'] ?? 'Employee');

    if ($companyId <= 0 || $userId <= 0) {
        throw new Exception('Company and user are required');
    }

    $db = (new Database())->connect();
    if (!$db) {
        throw new Exception('Database connection failed');
    }

    $permissionStmt = $db->prepare(
        'SELECT 1
         FROM company_employees
         WHERE company_id = :company_id AND user_id = :user_id AND is_company_admin = TRUE'
    );
    $permissionStmt->execute([
        ':company_id' => $companyId,
        ':user_id' => SessionManager::getUserId(),
    ]);

    if (!$permissionStmt->fetch()) {
        throw new Exception('Only company admins can assign employees');
    }

    $stmt = $db->prepare(
        'INSERT INTO company_employees (company_id, user_id, position_title, employment_status, is_company_admin, joined_at)
         VALUES (:company_id, :user_id, :position_title, :employment_status, :is_company_admin, NOW())
         ON CONFLICT (company_id, user_id)
         DO UPDATE SET position_title = EXCLUDED.position_title, employment_status = EXCLUDED.employment_status'
    );

    $stmt->execute([
        ':company_id' => $companyId,
        ':user_id' => $userId,
        ':position_title' => mb_substr($positionTitle, 0, 120),
        ':employment_status' => 'active',
        ':is_company_admin' => false,
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Employee assigned successfully',
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ]);
}
