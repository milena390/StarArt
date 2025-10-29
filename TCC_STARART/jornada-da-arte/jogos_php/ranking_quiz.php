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
        // Atualiza apenas se a nova pontuação for MAIOR (DESC)
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
// ORDER BY p.pontos DESC é importante aqui, pois no Quiz mais pontos é melhor.
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Ranking Quiz</title>
    <style>
        /* Estilo base copiado do Ranking Memória */
        body {
            font-family: Arial, sans-serif;
            background-color: #0A192F; /* Fundo Azul Escuro */
            color: #F5F5F5;
            text-align: center;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        h1 {
            margin-top: 30px;
            margin-bottom: 40px;
            color: #FFD700; /* Dourado */
            text-shadow: 0 0 15px rgba(255, 215, 0, 0.8);
            font-size: 2.5em;
        }
        
        /* Lista de Ranking - Alterado de OL para UL para manter a consistência visual */
        .ranking-list {
            list-style: none;
            padding: 0;
            max-width: 500px; /* Um pouco mais largo que o de memória */
            width: 90%;
            margin: 0 auto 40px;
        }

        .ranking-list li {
            background-color: #D4AF37; /* Dourado/Bronze */
            color: #0A192F; /* Texto em Azul Escuro */
            margin: 12px 0;
            padding: 18px 25px;
            border-radius: 12px;
            font-size: 1.2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.4);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .ranking-list li:hover {
            transform: scale(1.02);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.6);
        }

        /* Destaques Top 3 */
        /* Cores levemente ajustadas para manter a paleta e dar um toque mais Quiz/Ouro */
        .ranking-list li.top1 { background-color: #FFD700; font-weight: bold; border: 3px solid #0A192F; } /* Ouro */
        .ranking-list li.top2 { background-color: #C0C0C0; font-weight: bold; } /* Prata */
        .ranking-list li.top3 { background-color: #CD7F32; font-weight: bold; } /* Bronze */

        .ranking-list li .posicao {
            font-weight: 900;
            margin-right: 20px;
            width: 30px;
            color: #0A192F;
            text-align: center;
            flex-shrink: 0;
        }

        .ranking-list li .nome {
            flex-grow: 1;
            text-align: left;
        }

        /* Botão de Voltar - Estilo Dourado/Azul */
        .back-button {
            display: inline-block;
            padding: 12px 25px;
            font-size: 1.1em;
            border-radius: 50px; /* BEM ARREDONDADO */
            border: none;
            cursor: pointer;
            background-color: #D4AF37; /* Dourado */
            color: #0A192F; /* Azul Escuro */
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 4px 0 #b8942e;
            transition: all 0.3s;
        }

        .back-button:hover {
            background-color: #FFD700;
            transform: translateY(2px);
            box-shadow: 0 2px 0 #b8942e;
        }
    </style>
</head>
<body>

<h1>🏆 Ranking Quiz 🏆</h1>

<ul class="ranking-list">
    <?php foreach($ranking as $index => $linha): ?>
        <?php
        // Lógica para aplicar classes Top 1, 2, 3
        $class = '';
        if($index === 0) $class = 'top1';
        else if($index === 1) $class = 'top2';
        else if($index === 2) $class = 'top3';
        ?>
        <li class="<?= $class ?>">
            <span class="posicao"><?= ($index + 1) ?>º</span>
            <span class="nome"><?= htmlspecialchars($linha['nome']) ?></span>
            <span><?= $linha['pontos'] ?> pontos</span>
        </li>
    <?php endforeach; ?>
</ul>

<a href="index.php" class="back-button">⬅ Voltar ao Menu</a>

</body>
</html>