<?php
session_start();
require 'db.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: index.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$usuario_nome = $_SESSION['usuario_nome'] ?? '';

// Recebe pontuação via POST (quando o quiz termina)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pontuacao'])) {
    $pontuacao = intval($_POST['pontuacao']);

    // Verifica se já existe pontuação
    $stmt = $pdo->prepare("SELECT pontos FROM pontuacoes_quiz WHERE usuarios_id=?");
    $stmt->execute([$usuario_id]);
    $existe = $stmt->fetch(PDO::FETCH_ASSOC);

    if($existe){
        if($pontuacao > $existe['pontos']){
            $stmt = $pdo->prepare("UPDATE pontuacoes_quiz SET pontos=? WHERE usuarios_id=?");
            $stmt->execute([$pontuacao, $usuario_id]);
        }
    } else {
        $stmt = $pdo->prepare("INSERT INTO pontuacoes_quiz (pontos, usuarios_id) VALUES (?,?)");
        $stmt->execute([$pontuacao, $usuario_id]);
    }
}

// Busca ranking completo
$stmt = $pdo->query("
    SELECT u.nome, p.pontos 
    FROM pontuacoes_quiz p
    JOIN usuarios u ON u.id = p.usuarios_id
    ORDER BY p.pontos DESC, u.nome ASC
    LIMIT 10
");
$ranking = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Ranking Quiz</title>
    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(135deg, #000033 0%, #00001a 100%);
            color: #e0e0e0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding-top: 130px;
        }

        h1 {
            color: #FFD700;
            font-size: 2.5em;
            margin-bottom: 30px;
            text-shadow: 0 0 15px rgba(255, 215, 0, 0.6);
        }

        ol {
            background-color: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 20px;
            max-width: 600px;
            width: 90%;
            list-style-type: none;
            counter-reset: rank-counter;
        }

        li {
            counter-increment: rank-counter;
            font-size: 1.2em;
            padding: 15px 20px;
            margin-bottom: 15px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
        }

        li::before {
            content: counter(rank-counter) ".";
            color: #4CAF50;
            font-weight: 700;
            margin-right: 20px;
            min-width: 30px;
            text-align: right;
        }

        .back-button {
            display: inline-block;
            margin-top: 40px;
            background: linear-gradient(90deg, #007bff 0%, #FFD700 100%);
            color: #fff;
            padding: 15px 35px;
            border-radius: 50px;
            font-size: 1.1em;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .back-button:hover {
            background: linear-gradient(90deg, #FFD700 0%, #007bff 100%);
        }
    </style>
</head>
<body>

<h1>Ranking Quiz</h1>
<ol>
    <?php foreach($ranking as $linha): ?>
        <li><?= htmlspecialchars($linha['nome']) ?> - <?= $linha['pontos'] ?> pontos</li>
    <?php endforeach; ?>
</ol>

<a href="index.php" class="back-button">Voltar ao Início</a>

</body>
</html>
