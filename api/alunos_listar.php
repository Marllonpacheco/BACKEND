<?php
require '_autenticado.php';
require '../conexao.php';
$pdo = $conn;   // o conexao.php cria $conn

header('Content-Type: application/json; charset=utf-8');

const PASTA_FOTOS_URL   = 'uploads/alunos/';              // relativa ao index.php
const PASTA_FOTOS_DISCO = __DIR__ . '/../uploads/alunos/';

try {
    $busca     = trim($_GET['busca'] ?? '');
    $turma     = trim($_GET['turma'] ?? '');
    $porPagina = 10;
    $pagina    = max(1, (int) ($_GET['pagina'] ?? 1));

    $condicoes  = [];
    $parametros = [];

    if ($busca !== '') {
        $condicoes[]  = "(nome LIKE ? OR email LIKE ?)";
        $parametros[] = "%$busca%";
        $parametros[] = "%$busca%";
    }
    if ($turma !== '') {
        $condicoes[]  = "turma = ?";
        $parametros[] = $turma;
    }
    $where = $condicoes ? " WHERE " . implode(" AND ", $condicoes) : "";

    // total de resultados com os filtros aplicados
    $stmtTotal = $pdo->prepare("SELECT COUNT(*) FROM alunos" . $where);
    $stmtTotal->execute($parametros);
    $total        = (int) $stmtTotal->fetchColumn();
    $totalPaginas = (int) ceil($total / $porPagina);

    $pagina = min($pagina, max(1, $totalPaginas));
    $offset = ($pagina - 1) * $porPagina;

    // só as colunas necessárias (nada de SELECT *)
    $stmt = $pdo->prepare(
        "SELECT id, nome, email, turma, foto FROM alunos"
        . $where . " ORDER BY nome LIMIT ? OFFSET ?"
    );
    foreach ($parametros as $i => $valor) {
        $stmt->bindValue($i + 1, $valor, PDO::PARAM_STR);
    }
    $stmt->bindValue(count($parametros) + 1, $porPagina, PDO::PARAM_INT);
    $stmt->bindValue(count($parametros) + 2, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // troca o nome do arquivo por uma URL (ou null se não existir)
    foreach ($alunos as &$a) {
        $foto = basename((string) ($a['foto'] ?? ''));
        $a['foto'] = ($foto !== '' && is_file(PASTA_FOTOS_DISCO . $foto))
            ? PASTA_FOTOS_URL . rawurlencode($foto)
            : null;
    }
    unset($a);

    echo json_encode([
        'ok'           => true,
        'total'        => $total,
        'pagina'       => $pagina,
        'totalPaginas' => $totalPaginas,
        'alunos'       => $alunos,
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'erro' => 'Erro ao consultar os alunos.']);
}
