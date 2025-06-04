<?php
header('Content-Type: application/json');
require_once 'db.php';

try {
    $stmt = $pdo->query("SELECT id, question FROM polls ORDER BY created_at DESC");
    $polls = $stmt->fetchAll();
    echo json_encode($polls);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '투표 목록을 불러오는 데 실패했습니다.']);
}
?>