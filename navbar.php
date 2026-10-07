<!-- navbar.php — inclua em todas as páginas -->
<nav class="navbar navbar-expand-md navbar-dark bg-purple"
     style="background-color:#7952b3">
  <div class="container">

    <!-- Logo / nome do sistema -->
    <a class="navbar-brand fw-bold" href="index.php">🎓 CRUD Alunos</a>

    <!-- Botão hambúrguer (aparece só no celular) -->
    <button class="navbar-toggler" type="button"
            data-bs-toggle="collapse"
            data-bs-target="#navMenu">
      <span class="navbar-toggler-icon"></span>
    </button>

    <!-- Links do menu -->
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="nav-link" href="index.php">Início</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="novo.php">＋ Novo Aluno</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="">Olá, <?= $_SESSION['usuario_nome'] ?></a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="logout.php">Sair</a>
        </li>
      </ul>
    </div>
  </div>
</nav>