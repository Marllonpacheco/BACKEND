<?php
require 'verifica_login.php';
require_once 'conexao.php';

// Mantém compatibilidade com seu arquivo conexao.php, que cria $conn.
$pdo = $conn;

// Mensagem de sucesso
$msg = $_GET['msg'] ?? '';

// Busca e filtro
$busca = trim($_GET['busca'] ?? '');
$turma = trim($_GET['turma'] ?? '');

// Paginação
$porPagina = 10;
$pagina = max(1, (int) ($_GET['pagina'] ?? 1));

// Carrega as turmas cadastradas no banco
$stmtTurmas = $pdo->query(
    "SELECT DISTINCT turma
     FROM alunos
     WHERE turma IS NOT NULL AND turma <> ''
     ORDER BY turma"
);
$turmas = $stmtTurmas->fetchAll(PDO::FETCH_COLUMN);

// Monta os filtros
$condicoes = [];
$parametros = [];

if ($busca !== '') {
    $condicoes[] = "(nome LIKE ? OR email LIKE ?)";
    $parametros[] = "%$busca%";
    $parametros[] = "%$busca%";
}

if ($turma !== '') {
    $condicoes[] = "turma = ?";
    $parametros[] = $turma;
}

$where = $condicoes
    ? " WHERE " . implode(" AND ", $condicoes)
    : "";

// Conta os resultados considerando os filtros
$sqlTotal = "SELECT COUNT(*) FROM alunos" . $where;
$stmtTotal = $pdo->prepare($sqlTotal);
$stmtTotal->execute($parametros);

$total = (int) $stmtTotal->fetchColumn();
$totalPaginas = (int) ceil($total / $porPagina);

// Mantém pelo menos uma página para calcular o offset
$totalPaginasCalculo = max(1, $totalPaginas);
$pagina = min($pagina, $totalPaginasCalculo);
$offset = ($pagina - 1) * $porPagina;

// Busca somente os alunos da página atual, mantendo os filtros
$sql = "SELECT * FROM alunos"
     . $where
     . " ORDER BY nome LIMIT ? OFFSET ?";

$stmt = $pdo->prepare($sql);

foreach ($parametros as $i => $valor) {
    $stmt->bindValue($i + 1, $valor, PDO::PARAM_STR);
}

$stmt->bindValue(count($parametros) + 1, $porPagina, PDO::PARAM_INT);
$stmt->bindValue(count($parametros) + 2, $offset, PDO::PARAM_INT);

$stmt->execute();
$alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);
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

    <!-- Formulário de busca e filtros -->
    <form method="GET" action="index.php" class="row g-2 mb-4">
        <div class="col-md-6">
            <input
                type="text"
                name="busca"
                class="form-control"
                placeholder="Buscar por nome ou e-mail..."
                value="<?= htmlspecialchars($busca, ENT_QUOTES, 'UTF-8') ?>"
            >
        </div>

        <div class="col-md-3">
            <select name="turma" class="form-select">
                <option value="">Todas as turmas</option>
                <?php foreach ($turmas as $t): ?>
                    <option
                        value="<?= htmlspecialchars($t, ENT_QUOTES, 'UTF-8') ?>"
                        <?= $turma === $t ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars($t, ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                🔎 Buscar
            </button>
            <a href="index.php" class="btn btn-outline-secondary">
                Limpar
            </a>
        </div>
    </form>

    <p class="text-muted">
        Resultados encontrados: <strong><?= $total ?></strong>
    </p>

    <!-- Alerta de sucesso -->
    <?php if ($msg === 'criado'): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            ✅ Aluno cadastrado com sucesso!
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
        </div>
    <?php endif; ?>

    <!-- Card contendo a tabela -->
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">👥 Lista de Alunos</h5>
            <a href="novo.php" class="btn btn-success btn-sm">＋ Novo Aluno</a>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Nome</th>
                            <th>Email</th>
                            <th>turma</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (count($alunos) > 0): ?>
                        <?php foreach ($alunos as $a): ?>
                            <tr>
                                <td class="text-muted"><?= (int) $a['id'] ?></td>
                                <td class="fw-semibold"><?= htmlspecialchars($a['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($a['email'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($a['turma'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
                                <td class="text-nowrap">
                                    <a href="editar.php?id=<?= (int) $a['id'] ?>"
                                       class="btn btn-outline-primary btn-sm">✏️ Editar</a>

                                    <button
                                        type="button"
                                        class="btn btn-outline-danger btn-sm"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalExcluir"
                                        data-id="<?= (int) $a['id'] ?>"
                                        data-nome="<?= htmlspecialchars($a['nome'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
                                    >
                                        🗑️ Excluir
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
                                Nenhum aluno encontrado.
                            </td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Paginação: preserva os filtros ao mudar de página -->
    <?php if ($total > 0 && $totalPaginas > 1): ?>
        <nav class="mt-4" aria-label="Paginação de alunos">
            <ul class="pagination justify-content-center">
                <li class="page-item <?= $pagina <= 1 ? 'disabled' : '' ?>">
                    <a class="page-link"
                       href="?<?= htmlspecialchars(http_build_query([
                           'busca' => $busca,
                           'turma' => $turma,
                           'pagina' => max(1, $pagina - 1)
                       ]), ENT_QUOTES, 'UTF-8') ?>">
                        Anterior
                    </a>
                </li>

                <?php for ($p = 1; $p <= $totalPaginas; $p++): ?>
                    <li class="page-item <?= $p === $pagina ? 'active' : '' ?>">
                        <a class="page-link"
                           href="?<?= htmlspecialchars(http_build_query([
                               'busca' => $busca,
                               'turma' => $turma,
                               'pagina' => $p
                           ]), ENT_QUOTES, 'UTF-8') ?>">
                            <?= $p ?>
                        </a>
                    </li>
                <?php endfor; ?>

                <li class="page-item <?= $pagina >= $totalPaginas ? 'disabled' : '' ?>">
                    <a class="page-link"
                       href="?<?= htmlspecialchars(http_build_query([
                           'busca' => $busca,
                           'turma' => $turma,
                           'pagina' => min($totalPaginas, $pagina + 1)
                       ]), ENT_QUOTES, 'UTF-8') ?>">
                        Próxima
                    </a>
                </li>
            </ul>
        </nav>
    <?php endif; ?>

</div>

<!-- Modal de confirmação de exclusão -->
<?php include "modal_excluir.php"; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>