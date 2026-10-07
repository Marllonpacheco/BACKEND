<?php
require 'verifica_login.php'; // barra quem não logou
require 'conexao.php';
include "conexao.php";
$alunos = $conn->query("SELECT * FROM alunos ORDER BY id DESC")
                  ->fetchAll(PDO::FETCH_ASSOC);

// Captura mensagem de sucesso da URL (?msg=criado)
$msg = $_GET['msg'] ?? '';
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

<?php include "navbar.php"; ?>

<div class="container mt-4">

  <!-- Alerta de sucesso (aparece só se vier ?msg=...) -->
  <?php if ($msg === 'criado'): ?>
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    ✅ Aluno cadastrado com sucesso!
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
  <?php endif; ?>

  <!-- Card contendo a tabela -->
  <div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">👥 Lista de Alunos</h5>
      <a href="novo.php" class="btn btn-success btn-sm">＋ Novo Aluno</a>
    </div>
    <div class="card-body p-0">
      <table class="table table-hover table-striped align-middle mb-0">
        <thead class="table-light">
          <tr>
            <th>#</th><th>Nome</th><th>Email</th><th>Ações</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($alunos as $a): ?>
          <tr>
            <td class="text-muted"><?= $a['id'] ?></td>
            <td class="fw-semibold"><?= htmlspecialchars($a['nome']) ?></td>
            <td><?= htmlspecialchars($a['email']) ?></td>
            <td>
              <a href="editar.php?id=<?= $a['id'] ?>"
                 class="btn btn-outline-primary btn-sm">✏️ Editar</a>
              <!-- Botão que abre o Modal (em vez de confirm()) -->
              <button class="btn btn-outline-danger btn-sm"
                      data-bs-toggle="modal"
                      data-bs-target="#modalExcluir"
                      data-id="<?= $a['id'] ?>"
                      data-nome="<?= htmlspecialchars($a['nome']) ?>">
                🗑️ Excluir
              </button>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal de confirmação de exclusão (próxima seção) -->
<?php include "modal_excluir.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>