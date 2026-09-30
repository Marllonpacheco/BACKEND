<?php
include "conexao.php";
$id    = $_POST['id'];
$nome  = $_POST['nome'];
$email = $_POST['email'];
$stmt  = $conn->prepare("UPDATE alunos SET nome = ?, email = ? WHERE id = ?");
$stmt->execute([$nome, $email, $id]);
header("Location: index.php?msg=atualizado"); // ← mensagem
exit;
?>