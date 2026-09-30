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
          <h5 class="mb-0">➕ Novo Aluno</h5>
        </div>
        <div class="card-body">

          <form action="salvar.php" method="POST">

            <div class="mb-3">
              <label class="form-label">Nome completo</label>
              <input type="text" name="nome"
                     class="form-control"
                     placeholder="Ex: Ana Silva" required>
            </div>

            <div class="mb-3">
              <label class="form-label">E-mail</label>
              <input type="email" name="email"
                     class="form-control"
                     placeholder="Ex: ana@email.com" required>
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