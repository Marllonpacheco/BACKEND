<?php
include "conexao.php";

// ID vem da URL: excluir.php?id=3
$id = $_GET['id'];

$sql  = "DELETE FROM alunos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$id]);

header("Location: index.php");
exit;
?>