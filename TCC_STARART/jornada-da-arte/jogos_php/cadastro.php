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
                // Erro que você estava tendo (nome duplicado)
                $erro = "🚫 O nome de usuário '{$nome}' já está cadastrado. Por favor, escolha outro.";
            } elseif ($usuario_existente['email'] === $email) {
                // Boa prática: Verificar também se o email é duplicado
                $erro = "🚫 O email '{$email}' já está em uso. Por favor, utilize outro.";
            }
        }
    }

    // --- 2. INSERÇÃO NO BANCO DE DADOS (Se não houver erros) ---
    if (empty($erro)) {
        $hash = password_hash($senha, PASSWORD_DEFAULT);

        $stmt_insert = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
        
        try {
            // A linha 13 original do seu erro seria a linha abaixo
            $stmt_insert->execute([$nome, $email, $hash]); 

            // Configura a sessão e redireciona
            $_SESSION['usuario_id'] = $pdo->lastInsertId();
            $_SESSION['usuario_nome'] = $nome;

            header('Location: index.php');
            exit;
        } catch (PDOException $e) {
            // Esta camada é para erros inesperados do banco (ex: conexão, sintaxe SQL)
            $erro = "❌ Erro inesperado ao cadastrar. Tente novamente mais tarde.";
            // Para debug: $erro .= " Detalhes: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Cadastro - StarArt</title>
<link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="container">
    <h2>Cadastro</h2>
    
    <?php if (!empty($erro)): ?>
        <p style="
            color: #721c24; 
            background-color: #f8d7da; 
            border: 1px solid #f5c6cb; 
            padding: 10px; 
            border-radius: 5px;
        ">
            <?php echo $erro; ?>
        </p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="nome" placeholder="Nome" value="<?php echo htmlspecialchars($nome); ?>" required>
        <input type="email" name="email" placeholder="Email" value="<?php echo htmlspecialchars($email); ?>" required>
        <input type="password" name="senha" placeholder="Senha" required>
        <button type="submit">Cadastrar</button>
    </form>
    <p>Já tem conta? <a href="login.php">Faça login</a></p>
</div>
</body>
</html>