<?php
$host = 'localhost';
$db   = 'poll_system';
$user = 'root'; // 필요시 계정에 맞게 변경
$pass = '';     // 필요시 비밀번호 설정
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'DB 연결 실패']);
    exit;
}
?>