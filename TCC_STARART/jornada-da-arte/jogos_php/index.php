<?php
session_start();
// Se a sessão não estiver definida, redireciona para o login
if(!isset($_SESSION['usuario_id'])){
    header('Location: login.php');
    exit;
}
$nome = $_SESSION['usuario_nome'];

// Verifica se o tema 'light-theme' está no cookie, para aplicar a classe no body
$theme_class = (isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'light-theme') ? 'light-theme' : '';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Menu de Jogos - StarArt</title>
<link rel="icon" type="image/svg+xml" href="/favicon.svg">

<style>
    /* Fonte principal */
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&display=swap');

    /* --- VARIÁVEIS DE TEMA E CORES --- */
    :root {
        /* ESCURO (Padrão) */
        --bg-primary: #0A192F;      /* Fundo Azul Escuro */
        --bg-secondary: #112B50;    /* Fundo do Container (Escuro) */
        --color-text: #E0E0E0;      /* Texto Cinza Claro */
        --color-accent: #D4AF37;    /* Dourado Principal */
        --color-header-bg: #0A192F;
        --color-header-text: #E0E0E0;
    }

    body.light-theme {
        /* CLARO */
        --bg-primary: #dbeeff;      /* Azul Bebê Suave */
        --bg-secondary: #FFFFFF;    /* Fundo do Container (Claro) */
        --color-text: #0A192F;      /* Texto Azul Escuro */
        --color-accent: #D4AF37;    /* Dourado Principal */
        --color-header-bg: #dbeeff;
        --color-header-text: #0A192F;
    }

    /* --- ESTILOS GERAIS --- */
    body {
        margin: 0;
        font-family: 'Playfair Display', serif;
        background-color: var(--bg-primary); 
        color: var(--color-text); 
        transition: background-color 0.5s ease, color 0.5s ease;
        min-height: 100vh;
    }

    a {
        text-decoration: none;
        color: var(--color-accent); /* Links em Dourado */
        transition: color 0.3s ease;
    }

    a:hover {
        text-decoration: underline;
    }

    /* --- CABEÇALHO E NAVEGAÇÃO --- */
    header {
        background-color: var(--color-header-bg);
        padding: 1rem 2rem;
        border-bottom: 2px solid var(--color-accent);
        box-shadow: 0 4px 10px var(--color-shadow, rgba(0,0,0,0.5));
        transition: background-color 0.5s ease, border-color 0.5s ease;
    }

    nav {
        display: flex;
        justify-content: center;
        gap: 25px;
        align-items: center;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 10px;
        flex-wrap: wrap;
    }

    nav a, nav span {
        color: var(--color-header-text);
        font-weight: 400;
        padding: 5px 10px;
        border-radius: 5px; 
        transition: all 0.3s ease;
        font-size: 1rem;
        text-transform: uppercase;
    }
    
    /* Links da Navegação */
    nav a {
        color: var(--color-text); /* Cor do texto no header */
    }

    nav a:hover {
        color: var(--color-accent);
        border-bottom: 2px solid var(--color-accent);
        text-decoration: none;
        background-color: transparent;
    }
    
    /* Estiliza o "Olá, [Nome]" */
    nav span {
        color: var(--color-accent); 
        font-style: italic;
        font-weight: 700;
    }

    /* Estiliza o botão Sair */
    nav a[href="logout.php"] {
        background-color: #C82333; 
        color: #FFFFFF;
        padding: 8px 15px;
        border-radius: 50px; /* BEM ARREDONDADO */
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.4);
    }

    nav a[href="logout.php"]:hover {
        background-color: #a71d2a;
        transform: translateY(-1px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.5);
    }

    /* Botão de alternar tema (Integrado na navegação) */
    .theme-toggle-btn {
        background-color: var(--color-accent); 
        color: var(--bg-primary); /* Texto em azul escuro no botão dourado */
        border: none;
        border-radius: 50px;
        padding: 8px 15px;
        font-size: 1rem;
        cursor: pointer;
        font-weight: bold;
        box-shadow: 0 3px 6px var(--color-shadow);
        transition: all 0.3s ease;
    }

    .theme-toggle-btn:hover {
        background-color: #b8942e; /* dourado escuro */
        transform: scale(1.05);
    }

    /* --- CONTEÚDO PRINCIPAL (Container) --- */
    .container {
        max-width: 700px;
        margin: 60px auto;
        padding: 40px;
        background-color: var(--bg-secondary); 
        border-radius: 15px;
        box-shadow: 0 10px 20px var(--color-shadow);
        text-align: center;
        transition: background-color 0.5s ease;
    }

    .container h2 {
        color: var(--color-accent); /* Título em Dourado */
        font-size: 2.5em;
        margin-bottom: 40px;
        border-bottom: 3px solid var(--color-accent); 
        display: inline-block;
        padding-bottom: 5px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* --- BOTÕES DE JOGO ("frufru" - pequenos, arredondados) --- */
    .botao-jogo {
        display: inline-block; /* Alinha na mesma linha */
        margin: 10px;
        padding: 12px 25px;
        font-size: 1.1em;
        font-weight: 700;
        text-transform: capitalize;
        
        /* Estilo Botão Padrão */
        background-color: var(--color-accent); 
        color: var(--bg-primary); /* Texto em Azul Escuro */
        
        border: none;
        border-radius: 50px; /* BEM ARREDONDADO */
        cursor: pointer;
        box-shadow: 0 4px 0 #b8942e; /* Sombra dourada escura */
        transition: all 0.2s ease;
    }

    .botao-jogo:hover {
        background-color: #b8942e; 
        box-shadow: 0 2px 0 #9e7f22;
        transform: translateY(2px); /* Efeito de 'clique' */
    }

    /* Estilo para Ranking (Diferenciação visual) */
    .botao-jogo[href*="ranking"] {
        background-color: var(--bg-primary); 
        color: var(--color-accent);
        border: 2px solid var(--color-accent);
        box-shadow: 0 4px 0 var(--color-accent);
    }

    .botao-jogo[href*="ranking"]:hover {
        background-color: var(--bg-primary);
        color: #b8942e;
        border-color: #b8942e;
        box-shadow: 0 2px 0 #9e7f22;
        transform: translateY(2px);
    }
</style>
</head>
<body class="<?= htmlspecialchars($theme_class) ?>">
<header>
    <nav>
        <a href="../index/index.html">Início</a>
        <a href="../autor/autores.html">Autores</a>
        <a href="../index/historia.html">História</a>
        <a href="../index/movimentos.html">Movimentos</a>
        <a href="../contatostar/contatos.html">Contatos</a> 
        <span>Olá, <?= htmlspecialchars($nome) ?></span>
        <a href="logout.php">Sair</a>
        <button id="theme-toggle" class="theme-toggle-btn">
            <?php 
                echo (isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'light-theme') ? '🌙 Modo Escuro' : '☀️ Modo Claro';
            ?>
        </button>
    </nav>
</header>

<div class="container">
    <h2>Escolha um Jogo</h2>
    <a class="botao-jogo" href="memoria.php">Jogo da Memória</a>
    <a class="botao-jogo" href="quiz.php?nivel=facil">Quiz de Arte</a>
    <a class="botao-jogo" href="ranking_memoria.php">Ranking da Memória</a>
    <a class="botao-jogo" href="ranking_quiz.php">Ranking do Quiz</a>
</div>

<script>
    const toggleButton = document.getElementById('theme-toggle');
    const body = document.body;

    // Função para aplicar o tema e salvar no cookie
    function applyTheme(theme) {
        if (theme === 'light-theme') {
            body.classList.add('light-theme');
            toggleButton.textContent = '🌙 Modo Escuro';
            document.cookie = "theme=light-theme; path=/; max-age=31536000"; // Salva por 1 ano
        } else {
            body.classList.remove('light-theme');
            toggleButton.textContent = '☀️ Modo Claro';
            document.cookie = "theme=; path=/; max-age=0"; // Remove o cookie (Volta ao modo escuro padrão)
        }
    }

    // Listener para o botão de troca
    toggleButton.addEventListener('click', () => {
        // Verifica se a classe light-theme está presente
        const isLightTheme = body.classList.contains('light-theme');
        const newTheme = isLightTheme ? 'dark-theme' : 'light-theme';
        
        applyTheme(newTheme);
    });

    // O PHP já lida com a inicialização do tema ao carregar a página
    // (A classe <?= htmlspecialchars($theme_class) ?> já está no <body>)
</script>

</body>
</html>