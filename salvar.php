<?php
include "conexao.php";

$nome  = $_POST['nome'];
$email = $_POST['email'];

$stmt = $conn->prepare("INSERT INTO alunos (nome, email) VALUES (?, ?)");
$stmt->execute([$nome, $email]);

// Passa ?msg=criado para o index.php exibir o alert
header("Location: index.php?msg=criado");
exit;
?>