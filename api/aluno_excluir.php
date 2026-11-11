<?php
require '_autenticado.php';
require '../conexao.php';
$pdo = $conn;

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'erro' => 'Método não permitido.']);
    exit;
}

// lê o corpo JSON enviado pelo fetch
$entrada = json_decode(file_get_contents('php://input'), true);
$id = filter_var($entrada['id'] ?? null, FILTER_VALIDATE_INT);

if (!$id) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'erro' => 'ID inválido.']);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM alunos WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['ok' => true, 'removidos' => $stmt->rowCount()]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'erro' => 'Não foi possível excluir.']);
}
