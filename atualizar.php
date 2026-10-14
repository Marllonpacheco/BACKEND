<?php
include "conexao.php";
$id    = $_POST['id'];
$nome  = $_POST['nome'];
$email = $_POST['email'];



$turma = trim($_POST['turma'] ?? '');

$stmt = $conn->prepare("INSERT INTO alunos (nome, email, turma) VALUES (?, ?, ?)");
$stmt->execute([$nome,$email,$turma !== '' ? $turma : null   // vazio vira NULL no banco
]);
header("Location: index.php?msg=atualizado"); // ← mensagem
exit;
?>