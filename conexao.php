<?php
$host    = "localhost";  // onde o MySQL está rodando
$banco   = "crud_aula";  // nome do banco que criamos
$usuario = "root";       // usuário padrão do XAMPP
$senha   = "";          // sem senha no XAMPP local

$conn = new PDO(
    "mysql:host=$host;dbname=$banco;charset=utf8",
    $usuario,
    $senha
);

// Faz o PDO lançar erros em vez de falhar silenciosamente
$conn->setAttribute(
    PDO::ATTR_ERRMODE,
    PDO::ERRMODE_EXCEPTION
);
?>