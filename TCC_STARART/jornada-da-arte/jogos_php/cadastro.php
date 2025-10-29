<?php
session_start();
// O arquivo db.php deve conter a conexão PDO configurada
require 'db.php'; 

// Inicializa a variável para armazenar mensagens de erro ou sucesso
$erro = ''; 
$nome = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Limpa espaços em branco no início e fim
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    
    // --- 1. VALIDAÇÃO E VERIFICAÇÃO DE DUPLICIDADE ---

    // Verifica se os campos estão preenchidos (validação básica)
    if (empty($nome) || empty($email) || empty($senha)) {
        $erro = "🚫 Todos os campos são obrigatórios.";
    } 
    
    // Se não há erro de campos vazios, verifica duplicidade
    if (empty($erro)) {
        // Prepara a consulta para ver se o nome OU o email já existem
        $stmt_check = $pdo->prepare("SELECT nome, email FROM usuarios WHERE nome = :nome OR email = :email");
        $stmt_check->execute([':nome' => $nome, ':email' => $email]);
        $usuario_existente = $stmt_check->fetch(PDO::FETCH_ASSOC);

        if ($usuario_existente) {
            // Se a consulta retornou um resultado, o nome/email é duplicado
            if ($usuario_existente['nome'] === $nome) {
                $erro = "🚫 O nome de usuário '{$nome}' já está cadastrado. Por favor, escolha outro.";
            } elseif ($usuario_existente['email'] === $email) {
                $erro = "🚫 O email '{$email}' já está em uso. Por favor, utilize outro.";
            }
        }
    }

    // --- 2. INSERÇÃO NO BANCO DE DADOS (Se não houver erros) ---
    if (empty($erro)) {
        $hash = password_hash($senha, PASSWORD_DEFAULT);

        $stmt_insert = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
        
        try {
            $stmt_insert->execute([$nome, $email, $hash]); 

            // Configura a sessão e redireciona
            $_SESSION['usuario_id'] = $pdo->lastInsertId();
            $_SESSION['usuario_nome'] = $nome;

            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            $erro = "❌ Erro inesperado ao cadastrar. Tente novamente mais tarde.";
        }
    }
}

// Determina a classe do tema para inicialização do frontend
$theme_class = (isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'light-theme') ? 'light-theme' : '';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cadastro - StarArt</title>

<style>
    /* Fonte principal */
    @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&display=swap');

    /* --- VARIÁVEIS DE TEMA E CORES --- */
    :root {
        /* ESCURO (Padrão) */
        --bg-primary: #0A192F;      /* Fundo Azul Escuro */
        --bg-secondary: #112B50;    /* Fundo do Container */
        --color-text: #E0E0E0;      /* Texto Cinza Claro */
        --color-accent: #D4AF37;    /* Dourado Principal */
        --color-input-bg: #1e3a65;
        --color-shadow: rgba(0, 0, 0, 0.4);
    }

    body.light-theme {
        /* CLARO */
        --bg-primary: #dbeeff;      /* Azul Bebê Suave */
        --bg-secondary: #FFFFFF;    /* Fundo do Container */
        --color-text: #0A192F;      /* Texto Azul Escuro */
        --color-accent: #D4AF37;    /* Dourado Principal */
        --color-input-bg: #f0f8ff;
        --color-shadow: rgba(0, 0, 0, 0.15);
    }

    /* --- ESTILOS GERAIS --- */
    body {
        margin: 0;
        font-family: 'Playfair Display', serif;
        background-color: var(--bg-primary); 
        color: var(--color-text); 
        transition: background-color 0.5s ease, color 0.5s ease;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px;
    }

    a {
        color: var(--color-accent); 
        text-decoration: none;
        transition: color 0.3s ease;
    }

    a:hover {
        text-decoration: underline;
    }

    /* --- CONTAINER DE CADASTRO --- */
    .container {
        max-width: 450px;
        width: 100%;
        padding: 40px;
        background-color: var(--bg-secondary); 
        border-radius: 20px;
        box-shadow: 0 10px 25px var(--color-shadow);
        text-align: center;
        transition: background-color 0.5s ease;
        border: 2px solid var(--color-accent);
    }

    .container h2 {
        color: var(--color-accent); 
        font-size: 2.2em;
        margin-bottom: 30px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    /* --- ESTILOS DO FORMULÁRIO --- */
    form input {
        width: 100%;
        padding: 12px;
        margin-bottom: 20px;
        border: 1px solid var(--color-accent);
        border-radius: 50px; /* BEM ARREDONDADO */
        background-color: var(--color-input-bg);
        color: var(--color-text);
        font-size: 1rem;
        box-sizing: border-box;
        transition: border-color 0.3s, background-color 0.3s;
        text-align: center;
    }

    form input::placeholder {
        color: var(--color-text);
        opacity: 0.7;
    }

    form input:focus {
        border-color: #FFD700;
        box-shadow: 0 0 5px var(--color-accent);
        outline: none;
    }
    
    /* Botão de Cadastro */
    form button[type="submit"] {
        width: 100%;
        padding: 15px;
        font-size: 1.2rem;
        font-weight: 700;
        background-color: var(--color-accent); 
        color: var(--bg-primary); 
        border: none;
        border-radius: 50px; /* Redondinho */
        cursor: pointer;
        box-shadow: 0 4px 0 #b8942e;
        transition: all 0.2s ease;
        text-transform: uppercase;
        margin-top: 10px;
    }

    form button[type="submit"]:hover {
        background-color: #b8942e; 
        box-shadow: 0 2px 0 #9e7f22;
        transform: translateY(2px);
    }

    /* --- MENSAGEM DE ERRO/AVISO --- */
    .status-message {
        padding: 10px;
        margin-bottom: 20px;
        border-radius: 8px;
        font-weight: 700;
        text-align: center;
        border: 1px solid;
    }
    
    .status-error {
        /* Usando a paleta original para erro */
        background-color: #f8d7da;
        color: #721c24;
        border-color: #f5c6cb;
    }

    /* Link de Login */
    .container p {
        margin-top: 25px;
        font-size: 1.1em;
    }
    
    /* --- BOTÃO DE TEMA (Redondinho) --- */
    .theme-toggle-btn {
        position: fixed;
        top: 20px;
        right: 20px;
        background-color: var(--color-accent); 
        color: var(--bg-primary);
        border: none;
        border-radius: 50%;
        padding: 12px;
        font-size: 1rem;
        cursor: pointer;
        box-shadow: 0 4px 8px var(--color-shadow);
        z-index: 100;
        transition: all 0.3s;
    }
    
    .theme-toggle-btn:hover {
        background-color: #b8942e;
        transform: scale(1.1);
    }

</style>
</head>
<body class="<?= htmlspecialchars($theme_class) ?>">

<div class="container">
    <h2>Cadastro</h2>
    
    <?php if (!empty($erro)): ?>
        <div class="status-message status-error">
            <?php echo $erro; ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="nome" placeholder="Nome de Usuário" value="<?php echo htmlspecialchars($nome); ?>" required>
        <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required>
        <input type="password" name="senha" placeholder="Senha" required>
        <button type="submit">Cadastrar</button>
    </form>
    <p>Já tem conta? <a href="login.php">Faça login</a></p>
</div>

<button id="theme-toggle" class="theme-toggle-btn">
    <?php 
        echo (isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'light-theme') ? '🌙' : '☀️';
    ?>
</button>

<script>
    const toggleButton = document.getElementById('theme-toggle');
    const body = document.body;

    // Função para aplicar o tema e salvar no cookie
    function applyTheme(theme) {
        if (theme === 'light-theme') {
            body.classList.add('light-theme');
            toggleButton.textContent = '🌙';
            document.cookie = "theme=light-theme; path=/; max-age=31536000"; 
        } else {
            body.classList.remove('light-theme');
            toggleButton.textContent = '☀️';
            document.cookie = "theme=; path=/; max-age=0"; 
        }
    }

    // Listener para o botão de troca
    toggleButton.addEventListener('click', () => {
        const isLightTheme = body.classList.contains('light-theme');
        const newTheme = isLightTheme ? 'dark-theme' : 'light-theme';
        
        applyTheme(newTheme);
    });
</script>

</body>
</html>