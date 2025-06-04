<?php
header('Content-Type: application/json');
require_once 'db.php';

// 관리자 비밀번호 (간단 예시로 코드에 고정, 실제 운영은 환경변수 등 권장)
$ADMIN_PASSWORD = 'admin123';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['password']) || !isset($data['question'])) {
    echo json_encode(['success' => false, 'message' => '필수 데이터 누락']);
    exit;
}

if ($data['password'] !== $ADMIN_PASSWORD) {
    echo json_encode(['success' => false, 'message' => '비밀번호가 틀렸습니다']);
    exit;
}

try {
    $stmt = $pdo->prepare("INSERT INTO polls (question) VALUES (?)");
    $stmt->execute([$data['question']]);
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => '투표 생성 실패']);
}
?>