<?php
header('Content-Type: application/json');
require_once 'db.php';

$adminPassword = '20224361';
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

$pdo->prepare("DELETE FROM votes WHERE poll_id = ?")->execute([$pollId]);
$pdo->prepare("DELETE FROM polls WHERE id = ?")->execute([$pollId]);

echo json_encode(['success' => true]);
