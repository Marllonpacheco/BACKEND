<?php
require 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = $_POST['nome'];
    $email = $_POST['email'];
    $hash  = password_hash($_POST['senha'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare(
        "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)"
    );
    $stmt->execute([$nome, $email, $hash]);

    header("Location: login.php");
    exit;
}
?>