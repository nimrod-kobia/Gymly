<?php
require_once "../autoload.php";

header('Content-Type: application/json');

try {
    SessionManager::requireAuth();

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    if (!isset($_FILES['pdf_file']) || $_FILES['pdf_file']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('PDF file is required');
    }

    $title = trim($_POST['title'] ?? '');
    $summary = trim($_POST['summary'] ?? '');
    $keywords = trim($_POST['keywords'] ?? '');
    $companyId = (int)($_POST['company_id'] ?? 0);
    $isPublic = isset($_POST['is_public']) ? (bool)$_POST['is_public'] : true;

    if ($title === '' || strlen($title) > 200) {
        throw new Exception('Title is required and must be less than 200 characters');
    }

    $file = $_FILES['pdf_file'];
    $maxBytes = 25 * 1024 * 1024;
    if ($file['size'] > $maxBytes) {
        throw new Exception('PDF exceeds max file size (25MB)');
    }

    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if ($extension !== 'pdf') {
        throw new Exception('Only PDF files are allowed');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    if (!in_array($mime, ['application/pdf', 'application/octet-stream'], true)) {
        throw new Exception('Uploaded file is not a valid PDF');
    }

    $db = (new Database())->connect();
    if (!$db) {
        throw new Exception('Database connection failed');
    }

    if ($companyId > 0) {
        $accessStmt = $db->prepare(
            'SELECT 1 FROM company_employees WHERE company_id = :company_id AND user_id = :user_id AND employment_status = :status'
        );
        $accessStmt->execute([
            ':company_id' => $companyId,
            ':user_id' => SessionManager::getUserId(),
            ':status' => 'active',
        ]);

        if (!$accessStmt->fetch()) {
            throw new Exception('You are not an active employee of that company');
        }
    } else {
        $companyId = null;
    }

    $uploadDir = realpath(__DIR__ . '/../assets') . '/uploads/publications';
    if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true) && !is_dir($uploadDir)) {
        throw new Exception('Failed to create upload directory');
    }

    $safeBase = preg_replace('/[^a-zA-Z0-9_-]/', '_', pathinfo($file['name'], PATHINFO_FILENAME));
    $fileName = date('YmdHis') . '_' . $safeBase . '_' . bin2hex(random_bytes(4)) . '.pdf';
    $targetPath = $uploadDir . '/' . $fileName;

    if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
        throw new Exception('Failed to move uploaded file');
    }

    $publicPath = '../assets/uploads/publications/' . $fileName;

    $stmt = $db->prepare(
        'INSERT INTO publications (
            publisher_user_id, company_id, title, summary, keywords, file_path,
            original_file_name, file_size_bytes, downloads_count, is_public,
            published_at, created_at, updated_at
        ) VALUES (
            :publisher_user_id, :company_id, :title, :summary, :keywords, :file_path,
            :original_file_name, :file_size_bytes, 0, :is_public,
            NOW(), NOW(), NOW()
        )'
    );

    $stmt->execute([
        ':publisher_user_id' => SessionManager::getUserId(),
        ':company_id' => $companyId,
        ':title' => $title,
        ':summary' => $summary ?: null,
        ':keywords' => $keywords ?: null,
        ':file_path' => $publicPath,
        ':original_file_name' => $file['name'],
        ':file_size_bytes' => (int)$file['size'],
        ':is_public' => $isPublic,
    ]);

    echo json_encode([
        'success' => true,
        'message' => 'PDF published successfully',
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ]);
}
