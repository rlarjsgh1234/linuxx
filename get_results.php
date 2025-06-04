<?php
header('Content-Type: application/json');
require_once 'db.php';

$pollId = isset($_GET['pollId']) ? (int)$_GET['pollId'] : 0;

if ($pollId === 0) {
    echo json_encode(['success' => false, 'message' => '유효하지 않은 투표 ID']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT vote_option, COUNT(*) as count FROM votes WHERE poll_id = ? GROUP BY vote_option");
    $stmt->execute([$pollId]);

    $results = ['yes' => 0, 'no' => 0];
    while ($row = $stmt->fetch()) {
        $results[$row['vote_option']] = (int)$row['count'];
    }

    echo json_encode(['success' => true, 'results' => $results]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '결과 불러오기 실패']);
}
?>