<?php
header('Content-Type: application/json');

$adminPassword = '1234'; // 관리자 비밀번호 (실제로는 .env나 DB에 넣는 것이 좋음)

// JSON 파싱
$input = json_decode(file_get_contents('php://input'), true);
$pollId = intval($input['pollId'] ?? 0);
$password = $input['password'] ?? '';

if ($password !== $adminPassword) {
    echo json_encode(['success' => false, 'message' => '비밀번호가 틀렸습니다.']);
    exit;
}

if (!$pollId) {
    echo json_encode(['success' => false, 'message' => '투표 ID가 없습니다.']);
    exit;
}

// DB 연결
$conn = new mysqli('localhost', 'root', '', 'vote_db');
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'DB 연결 실패']);
    exit;
}

// 투표와 관련된 vote 데이터도 함께 삭제
$conn->query("DELETE FROM votes WHERE poll_id = $pollId");
$conn->query("DELETE FROM polls WHERE id = $pollId");

echo json_encode(['success' => true]);
?>