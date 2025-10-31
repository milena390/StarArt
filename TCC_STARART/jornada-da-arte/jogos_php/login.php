<?php 
session_start(); 
// O arquivo db.php deve conter a conexão PDO configurada
require 'db.php';   

// Inicializa a variável para armazenar mensagens de erro ou sucesso
$erro = '';  
$nome_ou_email = ''; // Campo para guardar o valor digitado (nome ou email)

// Se o usuário já estiver logado, redireciona para o menu principal
if (isset($_SESSION['usuario_id'])) { 
    header('Location: index.php'); 
    exit; 
} 

if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    // Limpa espaços em branco no início e fim
    $nome_ou_email = trim($_POST['nome_ou_email'] ?? ''); 
    $senha = $_POST['senha'] ?? ''; 

    // --- 1. VALIDAÇÃO DE CAMPOS --- 
    if (empty($nome_ou_email) || empty($senha)) { 
        $erro = "🚫 Por favor, preencha o usuário/email e a senha."; 
    } 

    // --- 2. AUTENTICAÇÃO --- 
    if (empty($erro)) { 
        // Prepara a consulta para buscar o usuário por nome OU email
        $stmt = $pdo->prepare("
            SELECT id, nome, senha 
            FROM usuarios 
            WHERE nome = :nome OR email = :email
        ");

        // Passa os parâmetros para o execute, mesmo que o valor seja o mesmo
        $stmt->execute([
            ':nome' => $nome_ou_email,
            ':email' => $nome_ou_email
        ]);

        // Recupera o usuário da base de dados
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC); 

        if ($usuario && password_verify($senha, $usuario['senha'])) { 
            // Sucesso no Login
            $_SESSION['usuario_id'] = $usuario['id']; 
            $_SESSION['usuario_nome'] = $usuario['nome']; 

            header('Location: index.php'); // Redireciona para o menu
            exit; 
        } else { 
            // Credenciais inválidas
            $erro = "❌ Usuário, email ou senha inválidos."; 
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
    <title>Login - StarArt</title>  

    <style>     
        /* Fonte principal */     
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&display=swap');  

        /* --- VARIÁVEIS DE TEMA E CORES --- */     
        :root {         
            --bg-primary: #0A192F; /* Fundo Azul Escuro */         
            --bg-secondary: #112B50; /* Fundo do Container */         
            --color-text: #E0E0E0; /* Texto Cinza Claro */         
            --color-accent: #D4AF37; /* Dourado Principal */         
            --color-input-bg: #1e3a65;         
            --color-shadow: rgba(0, 0, 0, 0.4);     
        }  

        body.light-theme {         
            --bg-primary: #dbeeff; /* Azul Bebê Suave */         
            --bg-secondary: #FFFFFF; /* Fundo do Container */         
            --color-text: #0A192F; /* Texto Azul Escuro */         
            --color-accent: #D4AF37; /* Dourado Principal */         
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

        /* --- CONTAINER DE LOGIN --- */     
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
            border-radius: 50px; /* BEM ARREDONDADO (Frufru) */         
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

        /* Botão de Login */     
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
            background-color: #f8d7da;         
            color: #721c24;         
            border-color: #f5c6cb;     
        }  

        /* Link de Cadastro */     
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
        <h2>Acesso de Usuário</h2>          

        <?php if (!empty($erro)): ?>         
            <div class="status-message status-error"><?= $erro ?></div>     
        <?php endif; ?>          

        <form action="login.php" method="POST">           
            <input type="text" name="nome_ou_email" placeholder="Nome de usuário ou E-mail" value="<?= htmlspecialchars($nome_ou_email) ?>" required>           
            <input type="password" name="senha" placeholder="Senha" required>           
            <button type="submit">Entrar</button>       
        </form>        

        <p>Não tem uma conta? <a href="cadastro.php">Cadastre-se</a></p>       
    </div>  

</body> 
</html>
