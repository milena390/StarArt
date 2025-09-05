<?php
session_start();
require 'db.php';

$stmt = $pdo->query("
    SELECT u.nome, p.pontos
    FROM pontuacoes_memoria p
    JOIN usuarios u ON p.usuarios_id = u.id
    ORDER BY p.pontos ASC
    LIMIT 10
");
$ranking = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Ranking Memória</title>
<link rel="stylesheet" href="stylememoria.css" />
<style>
body {
    font-family: Arial, sans-serif;
    background-color: #0A192F;
    color: #F5F5F5;
    text-align: center;
    padding: 20px;
}

h1 {
    margin-bottom: 30px;
    color: #FFD700;
    text-shadow: 0 0 10px #FFD700;
}

.ranking-list {
    list-style: none;
    padding: 0;
    max-width: 400px;
    margin: 0 auto 30px;
}

.ranking-list li {
    background-color: #D4AF37;
    color: #0A192F;
    margin: 10px 0;
    padding: 15px;
    border-radius: 12px;
    font-size: 1.1rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: transform 0.3s;
}

.ranking-list li:hover {
    transform: scale(1.05);
}

.ranking-list li.top1 { background-color: #FFD700; font-weight: bold; }
.ranking-list li.top2 { background-color: #FFC300; font-weight: bold; }
.ranking-list li.top3 { background-color: #FFB000; font-weight: bold; }

.ranking-list li .posicao {
    font-weight: 700;
    margin-right: 15px;
    width: 25px;
    color: #0A192F;
    text-align: center;
    flex-shrink: 0;
}

.ranking-list li .nome {
    flex-grow: 1;
    text-align: left;
}

button {
    padding: 10px 20px;
    font-size: 16px;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    background-color: #D4AF37;
    color: #0A192F;
    transition: all 0.3s;
}

button:hover {
    background-color: #FFD700;
    transform: scale(1.05);
}
</style>
</head>
<body>
<h1>🏆 Ranking Memória 🏆</h1>

<ul class="ranking-list">
    <?php foreach($ranking as $index => $linha): ?>
        <?php
        $class = '';
        if($index === 0) $class = 'top1';
        else if($index === 1) $class = 'top2';
        else if($index === 2) $class = 'top3';
        ?>
        <li class="<?= $class ?>">
            <span class="posicao"><?= ($index + 1) ?></span>
            <span class="nome"><?= htmlspecialchars($linha['nome']) ?></span>
            <span><?= $linha['pontos'] ?> pontos</span>
        </li>
    <?php endforeach; ?>
</ul>

<a href="index.php"><button>⬅ Voltar ao Menu</button></a>
</body>
</html>
