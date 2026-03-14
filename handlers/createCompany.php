<?php
require_once "../autoload.php";

header('Content-Type: application/json');

try {
    SessionManager::requireAuth();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $websiteUrl = trim($_POST['website_url'] ?? '');

    if ($name === '' || strlen($name) > 150) {
        throw new Exception('Company name is required and must be less than 150 characters');
    }

    if ($websiteUrl !== '' && !filter_var($websiteUrl, FILTER_VALIDATE_URL)) {
        throw new Exception('Website URL is invalid');
    }

    $db = (new Database())->connect();
    if (!$db) {
        throw new Exception('Database connection failed');
    }

    $stmt = $db->prepare(
        'INSERT INTO companies (name, description, website_url, created_by, created_at, updated_at)
         VALUES (:name, :description, :website_url, :created_by, NOW(), NOW())
         RETURNING id'
    );

    $stmt->execute([
        ':name' => $name,
        ':description' => $description ?: null,
        ':website_url' => $websiteUrl ?: null,
        ':created_by' => SessionManager::getUserId(),
    ]);

    $company = $stmt->fetch(PDO::FETCH_ASSOC);

    // Add creator as company admin employee
    $employeeStmt = $db->prepare(
        'INSERT INTO company_employees (company_id, user_id, position_title, employment_status, is_company_admin, joined_at)
         VALUES (:company_id, :user_id, :position_title, :employment_status, :is_company_admin, NOW())
         ON CONFLICT (company_id, user_id) DO NOTHING'
    );

    $employeeStmt->execute([
        ':company_id' => $company['id'],
        ':user_id' => SessionManager::getUserId(),
        ':position_title' => 'Founder',
        ':employment_status' => 'active',
        ':is_company_admin' => true,
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'Company created successfully',
        'company_id' => $company['id'] ?? null,
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ]);
}
