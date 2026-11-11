<?php
// Só inicia a sessão se ainda não houver uma (o conexao.php pode já ter iniciado)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {   // ajuste para a chave do seu login
    http_response_code(401);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['ok' => false, 'erro' => 'Faça login para continuar.']);
    exit;
}
