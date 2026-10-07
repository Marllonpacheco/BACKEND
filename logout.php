<?php
session_start();
$_SESSION = [];       // limpa as variáveis
session_destroy();  // destrói a sessão no servidor
header("Location: login.php");
exit;
?>