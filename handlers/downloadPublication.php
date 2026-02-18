<?php
require_once "../autoload.php";

try {
    $publicationId = (int)($_GET['id'] ?? 0);
    if ($publicationId <= 0) {
        throw new Exception('Invalid publication id');
    }

    $db = (new Database())->connect();
    if (!$db) {
        throw new Exception('Database connection failed');
    }

    $stmt = $db->prepare(
        'SELECT id, original_file_name, file_path, is_public
         FROM publications
         WHERE id = :id'
    );
    $stmt->execute([':id' => $publicationId]);
    $publication = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$publication || !$publication['is_public']) {
        http_response_code(404);
        echo 'File not found';
        exit;
    }

    $relativePath = preg_replace('#^\.\./#', '', $publication['file_path']);
    $absolutePath = realpath(__DIR__ . '/../' . $relativePath);

    if (!$absolutePath || !file_exists($absolutePath)) {
        http_response_code(404);
        echo 'File not found';
        exit;
    }

    $updateStmt = $db->prepare('UPDATE publications SET downloads_count = downloads_count + 1 WHERE id = :id');
    $updateStmt->execute([':id' => $publicationId]);

    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . basename($publication['original_file_name']) . '"');
    header('Content-Length: ' . filesize($absolutePath));
    header('Cache-Control: private, max-age=86400');
    readfile($absolutePath);
    exit;
} catch (Exception $e) {
    http_response_code(400);
    echo htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
}
