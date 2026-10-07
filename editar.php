<?php
include "conexao.php";
require 'verifica_login.php'; // barra quem não logou

// Pega o ID da URL: editar.php?id=3
$id = $_GET['id'];

// Busca apenas o aluno com esse ID
$sql  = "SELECT * FROM alunos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$id]);
$aluno = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Novo Aluno</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<?php include "navbar.php"; ?>

<div class="container mt-4">
  <div class="row justify-content-center">
    <div class="col-12 col-md-6">

      <div class="card shadow-sm">
        <div class="card-header">
          <h5 class="mb-0"> Editar Aluno</h5>
        </div>
        <div class="card-body">

          <form action="atualizar.php" method="POST">
            <input type="hidden" name="id" value="<?= $aluno['id'] ?>">
            <div class="mb-3">
              <label class="form-label">Nome completo</label>
              <input type="text" name="nome"
                     value="<?= htmlspecialchars($aluno['nome']) ?>" required>
            </div>

            <div class="mb-3">
              <label class="form-label">E-mail</label>
              <input type="email" name="email"
                     value="<?= htmlspecialchars($aluno['email']) ?>" required>
            </div>

            <div class="d-flex gap-2">
              <button type="submit" class="btn btn-success">💾 Salvar</button>
              <a href="index.php" class="btn btn-outline-secondary">← Voltar</a>
            </div>

          </form>
        </div>
      </div>

    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


