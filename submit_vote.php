<?php
header('Content-Type: application/json');
require_once 'db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['pollId']) || !isset($data['voteOption'])) {
    echo json_encode(['success' => false, 'message' => '입력값이 부족합니다.']);
    exit;
}

$pollId = (int)$data['pollId'];
$voteOption = $data['voteOption'];

if (!in_array($voteOption, ['yes', 'no'])) {
    echo json_encode(['success' => false, 'message' => '잘못된 투표 선택지입니다.']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO votes (poll_id, vote_option) VALUES (?, ?)");
    $stmt->execute([$pollId, $voteOption]);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '투표 저장 실패']);
}
?>