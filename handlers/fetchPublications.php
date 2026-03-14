<?php
require_once "../autoload.php";

header('Content-Type: application/json');

try {
    $db = (new Database())->connect();
    if (!$db) {
        throw new Exception('Database connection failed');
    }

    $query = trim($_GET['q'] ?? '');
    $companyId = (int)($_GET['company_id'] ?? 0);
    $limit = max(1, min((int)($_GET['limit'] ?? 20), 50));
    $offset = max(0, (int)($_GET['offset'] ?? 0));

    $where = ['p.is_public = TRUE'];
    $params = [];

    if ($companyId > 0) {
        $where[] = 'p.company_id = :company_id';
        $params[':company_id'] = $companyId;
    }

    if ($query !== '') {
        $where[] = '(p.title ILIKE :search OR p.summary ILIKE :search OR p.keywords ILIKE :search OR c.name ILIKE :search)';
        $params[':search'] = '%' . $query . '%';
    }

    $whereSql = implode(' AND ', $where);

    $countStmt = $db->prepare(
        "SELECT COUNT(*) AS total
         FROM publications p
         LEFT JOIN companies c ON c.id = p.company_id
         WHERE $whereSql"
    );
    $countStmt->execute($params);
    $total = (int)($countStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

    $stmt = $db->prepare(
        "SELECT
            p.id,
            p.title,
            p.summary,
            p.keywords,
            p.original_file_name,
            p.file_size_bytes,
            p.downloads_count,
            p.published_at,
            u.username AS publisher_username,
            c.name AS company_name
         FROM publications p
         JOIN users u ON u.id = p.publisher_user_id
         LEFT JOIN companies c ON c.id = p.company_id
         WHERE $whereSql
         ORDER BY p.published_at DESC
         LIMIT :limit OFFSET :offset"
    );

    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value, PDO::PARAM_STR);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode([
        'success' => true,
        'total' => $total,
        'limit' => $limit,
        'offset' => $offset,
        'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
    ]);
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ]);
}
