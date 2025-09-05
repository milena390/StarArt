<?php
session_start();
$usuario_logado = $_SESSION['usuario_nome'] ?? null; // Nome do usuário logado
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>StarArt - Início</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap');

        body {
            margin: 0;
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(135deg, #000033 0%, #00001a 100%);
            color: #e0e0e0;
            padding-top: 80px; /* Espaço para o header fixo */
        }

        header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background-color: rgba(0,0,26,0.95);
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 5vw;
            box-shadow: 0 4px 15px rgba(0,0,0,0.5);
            z-index: 1000;
        }

        .logo {
            color: #FFD700;
            font-size: 1.8em;
            font-weight: 700;
            text-decoration: none;
        }

        nav a {
            color: #FFD700;
            text-decoration: none;
            margin-left: 25px;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        nav a:hover {
            color: #4CAF50;
        }

        .usuario {
            font-weight: 500;
            color: #e0e0e0;
        }

        @media (max-width: 600px) {
            nav a {
                margin-left: 15px;
                font-size: 0.9em;
            }
        }

        .conteudo {
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
            text-align: center;
        }

        h1 {
            color: #FFD700;
            font-size: clamp(2em, 6vw, 3em);
            text-shadow: 0 0 10px rgba(255,215,0,0.6);
        }

        p {
            color: #e0e0e0;
            font-size: 1.1em;
            line-height: 1.6em;
        }

        .btn-login {
            display: inline-block;
            margin-top: 20px;
            padding: 15px 35px;
            border-radius: 50px;
            background: linear-gradient(90deg, #007bff 0%, #FFD700 100%);
            color: #fff;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .btn-login:hover {
            background: linear-gradient(90deg, #FFD700 0%, #007bff 100%);
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
    <header>
        <a href="index.php" class="logo">StarArt</a>
        <nav>
            <a href="index.html">Início</a>
            <a href="autores.html">Autores</a>
            <a href="historia.html">História</a>
            <a href="movimentos.html">Movimentos</a>
            <a href="movimentos.html">Movimentos</a>
            <?php if($usuario_logado): ?>
                <span class="usuario">Olá, <?= htmlspecialchars($usuario_logado) ?></span>
            <?php else: ?>
                <a href="../jogos_php/login.php">Login / Cadastro</a>
            <?php endif; ?>
        </nav>
    </header>

    <div class="conteudo">
        <h1>Bem-vindo à StarArt</h1>
        <p>Explore o mundo da arte, conheça os autores, os movimentos e jogue nossos quizzes para testar seus conhecimentos.</p>
        <?php if(!$usuario_logado): ?>
            <a href="../jogos_php/login.php" class="btn-login">Login / Cadastro</a>
        <?php else: ?>
            <a href="../jogos_php/index.php" class="btn-login">Ir para os Jogos</a>
        <?php endif; ?>
    </div>
</body>
</html>
