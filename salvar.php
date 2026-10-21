<?php
require_once 'verifica_login.php';
require_once 'conexao.php';
require_once 'upload.php';

// conexao.php cria $conn; os demais arquivos usam $pdo
$pdo = $conn;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: novo.php');
    exit;
}

$nome  = trim($_POST['nome'] ?? '');
$email = trim($_POST['email'] ?? '');
$turma = trim($_POST['turma'] ?? '');

if ($nome === '' || $email === '') {
    exit('Nome e e-mail são obrigatórios.');
}

// Salva a foto, caso tenha sido enviada
$resultado = salvarFotoAluno($_FILES['foto'] ?? []);

if (!$resultado['ok']) {
    exit('Erro na foto: ' . htmlspecialchars(
        $resultado['erro'],
        ENT_QUOTES,
        'UTF-8'
    ));
}

$foto = $resultado['arquivo'];

try {
    $stmt = $pdo->prepare(
        "INSERT INTO alunos (nome, email, turma, foto)
         VALUES (?, ?, ?, ?)"
    );

    $stmt->execute([
        $nome,
        $email,
        $turma !== '' ? $turma : null,
        $foto
    ]);

    header('Location: index.php?msg=criado');
    exit;

} catch (PDOException $e) {
    // Evita deixar uma imagem órfã se o cadastro falhar
    if ($foto !== null) {
        $caminho = __DIR__ . '/uploads/alunos/' . $foto;

        if (is_file($caminho)) {
            unlink($caminho);
        }
    }

    error_log($e->getMessage());
    http_response_code(500);
    exit('Não foi possível cadastrar o aluno.');
}