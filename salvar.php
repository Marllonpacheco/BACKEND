<?php
include "conexao.php";
$pdo = $conn;
$nome  = $_POST['nome'];
$email = $_POST['email'];

// novo.php — ao receber o POST do formulário
$turma = trim($_POST['turma'] ?? '');

$stmt = $pdo->prepare(
    "INSERT INTO alunos (nome, email, turma) VALUES (?, ?, ?)"
);
$stmt->execute([
    $nome,
    $email,
    $turma !== '' ? $turma : null   // vazio vira NULL no banco
]);
// Passa ?msg=criado para o index.php exibir o alert
header("Location: index.php?msg=criado");
exit;
?>