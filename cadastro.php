<?php
session_start();
require 'conexao.php';

$erro  = null;
$nome  = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome      = trim($_POST['nome'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $senha     = $_POST['senha'] ?? '';
    $confirmar = $_POST['confirmar'] ?? '';

    if ($nome === '' || $email === '' || $senha === '') {
        $erro = "Preencha todos os campos.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "E-mail inválido.";
    } elseif (strlen($senha) < 6) {
        $erro = "A senha deve ter pelo menos 6 caracteres.";
    } elseif ($senha !== $confirmar) {
        $erro = "As senhas não conferem.";
    } else {
        // e-mail já cadastrado?
        $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $erro = "Este e-mail já está cadastrado.";
        } else {
            $hash = password_hash($senha, PASSWORD_DEFAULT);

            $stmt = $conn->prepare(
                "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)"
            );
            $stmt->execute([$nome, $email, $hash]);

            header("Location: login.php?cadastro=ok");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>CRUD Alunos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container" style="max-width:380px;margin-top:60px">
  <div class="card shadow-sm">
    <div class="card-header text-white text-center"
         style="background-color:#7952b3">
      📝 Criar Conta
    </div>
    <div class="card-body">

      <?php if ($erro): ?>
        <div class="alert alert-danger">
          <?= htmlspecialchars($erro) ?>
        </div>
      <?php endif; ?>

      <form method="POST">
        <label class="form-label">Nome</label>
        <input type="text" name="nome" class="form-control mb-3"
               value="<?= htmlspecialchars($nome) ?>" required>

        <label class="form-label">E-mail</label>
        <input type="email" name="email" class="form-control mb-3"
               value="<?= htmlspecialchars($email) ?>" required>

        <label class="form-label">Senha</label>
        <input type="password" name="senha" class="form-control mb-3"
               minlength="6" required>

        <label class="form-label">Confirmar senha</label>
        <input type="password" name="confirmar" class="form-control mb-3"
               minlength="6" required>

        <button class="btn w-100" style="background-color:#7952b3;color:#fff">
          Cadastrar
        </button>
      </form>

      <p class="text-center mt-3 mb-0">
        Já tem conta? <a href="login.php">Entrar</a>
      </p>

    </div>
  </div>
</div>