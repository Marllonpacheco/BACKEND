<?php
include "conexao.php"; // traz a variável $conn

// Faz o SELECT e já transforma em array
$sql    = "SELECT * FROM stockeasy ORDER BY id DESC";
$alunos = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>StockEasy</title>
</head>
<body>

<h1>Cadastro de Estoque</h1>
<a href="novo.php">Cadastrar item no estoque</a>
<hr>

<table border="1" cellpadding="8">
    <tr>
        <th>ID</th>
        <th>Nome</th>
        <th>Código</th>
        <th>Quantidade</th>
        <th>Estoque mínimo</th>
        <th>Categoria</th>
        <th>Ações</th>
    </tr>

    <?php foreach ($alunos as $aluno): ?>
    <tr>
        <td><?= $aluno['id'] ?></td>
        <td><?= htmlspecialchars($aluno['nome'])  ?></td>
        <td><?= htmlspecialchars($aluno['email']) ?></td>
        <td>
            <a href="editar.php?id=<?= $aluno['id'] ?>">Editar</a>
            |
            <a href="excluir.php?id=<?= $aluno['id'] ?>"
               onclick="return confirm('Deseja excluir?')">Excluir</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

</body>
</html>