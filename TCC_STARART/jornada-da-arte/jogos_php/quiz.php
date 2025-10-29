<?php
session_start();
require 'db.php';
if(!isset($_SESSION['usuario_id'])) header('Location: login.php');

$nivel = $_GET['nivel'] ?? 'facil';
$arquivo = "perguntas/$nivel.json";
$perguntas = json_decode(file_get_contents($arquivo), true);

if(!isset($_SESSION['quiz'])){
    $_SESSION['quiz'] = ['indice'=>0, 'pontuacao'=>0];
    shuffle($perguntas);
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $resposta = $_POST['resposta'] ?? '';
    $correta = $_POST['correta'] ?? '';
    if($resposta === $correta) $_SESSION['quiz']['pontuacao']++;
    $_SESSION['quiz']['indice']++;
}

$indice = $_SESSION['quiz']['indice'];

if($indice >= count($perguntas)){
    $pontos = $_SESSION['quiz']['pontuacao'];

    // Salva a pontuação no banco
    $usuario_id = $_SESSION['usuario_id'];
    $stmt = $pdo->prepare("SELECT pontos FROM pontuacoes_quiz WHERE usuarios_id=?");
    $stmt->execute([$usuario_id]);
    $existe = $stmt->fetch();

    if($existe){
        if($pontos > $existe['pontos']){
            $stmt = $pdo->prepare("UPDATE pontuacoes_quiz SET pontos=? WHERE usuarios_id=?");
            $stmt->execute([$pontos,$usuario_id]);
        }
    } else {
        $stmt = $pdo->prepare("INSERT INTO pontuacoes_quiz (pontos, usuarios_id) VALUES (?,?)");
        $stmt->execute([$pontos,$usuario_id]);
    }

    // NÃO destruir a sessão para manter o usuário logado

    ?>
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8" />
        <title>Quiz Finalizado</title>
        <style>
            body {
                font-family: 'Montserrat', sans-serif;
                background: linear-gradient(135deg, #000033 0%, #00001a 100%);
                color: #e0e0e0;
                margin: 0;
                height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
            }
            #modal {
                background: rgba(0,0,0,0.9);
                border-radius: 20px;
                padding: 30px 40px;
                text-align: center;
                max-width: 400px;
                box-shadow: 0 0 30px #FFD700;
            }
            #modal h2 {
                color: #FFD700;
                margin-bottom: 15px;
                font-size: 2em;
                text-shadow: 0 0 10px #FFD700;
            }
            #modal p {
                margin-bottom: 25px;
                font-size: 1.2em;
            }
            button {
                background: linear-gradient(90deg, #007bff 0%, #FFD700 100%);
                border: none;
                padding: 12px 30px;
                border-radius: 50px;
                font-size: 1.1em;
                font-weight: 700;
                cursor: pointer;
                color: #00001a;
                margin: 0 10px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
                transition: all 0.3s ease;
            }
            button:hover {
                background: linear-gradient(90deg, #FFD700 0%, #007bff 100%);
                color: #fff;
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.5);
            }
        </style>
    </head>
    <body>
        <div id="modal">
            <h2>Quiz finalizado!</h2>
            <p>Sua pontuação: <strong><?= $pontos ?></strong></p>
            <p>É a hora do chá!</p>
            <button id="btnEnviar">Enviar pontuação</button>
            <button id="btnJogar">Jogar novamente</button>
        </div>

        <script>
            document.getElementById('btnEnviar').addEventListener('click', function() {
                // Redireciona para ranking_quiz.php para mostrar ranking
                window.location.href = 'ranking_quiz.php';
            });
            document.getElementById('btnJogar').addEventListener('click', function() {
                // Recarrega o quiz para jogar novamente no mesmo nível
                window.location.href = '<?= $_SERVER['PHP_SELF'] . "?nivel=" . htmlspecialchars($nivel) ?>';
            });
        </script>
    </body>
    </html>
    <?php
    exit;
}

$pergunta = $perguntas[$indice];
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8" />
<title>Quiz - Nível <?= htmlspecialchars($nivel) ?></title>
<style>
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap');

    body {
        font-family: 'Montserrat', sans-serif;
        background: linear-gradient(135deg, #000033 0%, #00001a 100%);
        color: #e0e0e0;
        display: flex;
        flex-direction: column;
        align-items: center;
        min-height: 100vh;
        margin: 0;
        padding-top: 100px;
        box-sizing: border-box;
        overflow-x: hidden;
    }

    h2 {
        color: #FFD700;
        font-size: clamp(1.5em, 5vw, 2.2em);
        margin-bottom: 30px;
        font-weight: 700;
        text-shadow: 0 0 15px rgba(255, 215, 0, 0.6);
        max-width: 700px;
        text-align: center;
    }

    form {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 90%;
        max-width: 700px;
    }

    label {
        background-color: rgba(255, 255, 255, 0.1);
        color: #e0e0e0;
        border: 1px solid rgba(0, 100, 200, 0.4);
        padding: 15px 20px;
        margin-bottom: 15px;
        border-radius: 12px;
        cursor: pointer;
        width: 100%;
        max-width: 450px;
        text-align: left;
        transition: all 0.3s ease;
        font-weight: 400;
        display: flex;
        align-items: center;
        box-sizing: border-box;
        word-wrap: break-word;
        overflow-wrap: break-word;
        white-space: normal;
    }
    
    label:hover {
        background-color: rgba(255, 255, 255, 0.15);
        border-color: #FFD700;
    }

    input[type="radio"] {
        appearance: none;
        min-width: 20px;
        height: 20px;
        border: 2px solid #0056b3;
        border-radius: 50%;
        margin-right: 15px;
        position: relative;
        cursor: pointer;
        flex-shrink: 0;
    }

    input[type="radio"]:checked {
        background-color: #FFD700;
        border-color: #FFD700;
    }

    input[type="radio"]:checked::before {
        content: '';
        display: block;
        width: 10px;
        height: 10px;
        background-color: #00001a;
        border-radius: 50%;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    button {
        display: inline-block;
        background: linear-gradient(90deg, #007bff 0%, #FFD700 100%);
        color: #fff;
        border: none;
        padding: 15px 40px;
        border-radius: 50px;
        font-size: 1.1em;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 20px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.4);
    }

    button:hover {
        background: linear-gradient(90deg, #FFD700 0%, #007bff 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.5);
    }
</style>
</head>
<body>

<h2><?= htmlspecialchars($pergunta['pergunta']) ?></h2>
<form method="POST">
    <?php foreach($pergunta['opcoes'] as $opcao): ?>
        <label>
            <input type="radio" name="resposta" value="<?= htmlspecialchars($opcao) ?>" required>
            <?= htmlspecialchars($opcao) ?>
        </label>
    <?php endforeach; ?>
    <input type="hidden" name="correta" value="<?= htmlspecialchars($pergunta['resposta']) ?>">
    <button type="submit">Responder</button>
</form>

</body>
</html>
