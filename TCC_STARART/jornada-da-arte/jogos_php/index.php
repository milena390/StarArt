<?php
session_start();
if(!isset($_SESSION['usuario_id'])){
    header('Location: login.php');
    exit;
}
$nome = $_SESSION['usuario_nome'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Menu de Jogos - StarArt</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<header>
    <nav>
        <a href="../index/index.html">Início</a>
        <a href="../autor/autores.html">Autores</a>
        <a href="../index/historia.html">História</a>
        <a href="../index/movimentos.html">Movimentos</a>
        <a href="../contatostar/contatos.html">Autores</a>
        <span>Olá, <?= htmlspecialchars($nome) ?></span>
        <a href="logout.php">Sair</a>
    </nav>
</header>

<div class="container">
    <h2>Escolha um jogo</h2>
    <a class="botao-jogo" href="memoria.php">Jogo da Memória</a>
    <a class="botao-jogo" href="quiz.php?nivel=facil">Quiz de Arte</a>
    <a class="botao-jogo" href="ranking_memoria.php">Ranking da Memoria</a>
    <a class="botao-jogo" href="ranking_quiz.php">Ranking do quiz</a>
</div>
</body>
</html>
