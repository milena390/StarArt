<?php
session_start();
require 'db.php';
// Redireciona se o usuário não estiver logado
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

    // Salva a pontuação no banco (Mantido o código de banco de dados)
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
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Quiz Finalizado</title>
        <style>
            @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap');
            /* Estilos para o Modal de Finalização */
            body {
                font-family: 'Montserrat', sans-serif;
                background: linear-gradient(135deg, #000033 0%, #00001a 100%);
                color: #e0e0e0;
                margin: 0;
                height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                animation: fadeIn 1s ease-in-out;
            }
            @keyframes fadeIn {
                from { opacity: 0; }
                to { opacity: 1; }
            }
            #modal {
                background: rgba(0,0,0,0.9);
                border-radius: 20px;
                padding: 30px 40px;
                text-align: center;
                max-width: 450px;
                width: 90%;
                box-shadow: 0 0 40px rgba(255, 215, 0, 0.7);
                border: 2px solid #FFD700;
            }
            #modal h2 {
                color: #FFD700;
                margin-bottom: 20px;
                font-size: 2.5em;
                text-shadow: 0 0 15px #FFD700;
                text-transform: uppercase;
                letter-spacing: 1px;
            }
            #modal p {
                margin-bottom: 30px;
                font-size: 1.3em;
            }
            #modal p strong {
                color: #007bff;
                font-weight: 700;
                font-size: 1.5em;
                display: block;
                margin-top: 5px;
            }
            .button-container {
                display: flex;
                flex-direction: row;
                gap: 15px;
                justify-content: center;
                flex-wrap: wrap;
            }
            button {
                background: linear-gradient(90deg, #007bff 0%, #FFD700 100%);
                border: none;
                padding: 12px 25px;
                border-radius: 50px;
                font-size: 1.1em;
                font-weight: 700;
                cursor: pointer;
                color: #00001a;
                box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
                transition: all 0.3s ease;
                min-width: 150px;
            }
            button:hover {
                background: linear-gradient(90deg, #FFD700 0%, #007bff 100%);
                color: #00001a;
                transform: translateY(-3px) scale(1.05);
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.7);
            }

            /* Responsividade */
            @media (max-width: 500px) {
                #modal {
                    padding: 20px;
                }
                #modal h2 {
                    font-size: 2em;
                }
                #modal p {
                    font-size: 1.1em;
                    margin-bottom: 20px;
                }
                .button-container {
                    flex-direction: column;
                    gap: 10px;
                }
                button {
                    width: 100%;
                }
            }
        </style>
    </head>
    <body>
        <div id="modal">
            <h2>🎉 Quiz Finalizado! 🏆</h2>
            <p>Sua pontuação: <strong style="color: #007bff;"><?= $pontos ?></strong></p>
            <p>Parabéns pela sua performance!</p>
            <div class="button-container">
                <button id="btnRanking">Ver Ranking</button>
                <button id="btnJogar">Jogar Novamente</button>
            </div>
        </div>

        <script>
            document.getElementById('btnRanking').addEventListener('click', function() {
                window.location.href = 'ranking_quiz.php';
            });
            document.getElementById('btnJogar').addEventListener('click', function() {
                // Redireciona para o mesmo quiz, reiniciando
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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quiz - Nível <?= htmlspecialchars($nivel) ?></title>
<style>
    /* Importação da Fonte */
    @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;600;700&display=swap');

    /* Estilos Gerais e Fundo */
    body {
        font-family: 'Montserrat', sans-serif;
        background: linear-gradient(135deg, #000033 0%, #00001a 100%);
        color: #e0e0e0;
        
        /* --- AJUSTES PARA CENTRALIZAÇÃO TOTAL (Sem Header) --- */
        display: flex;
        flex-direction: column;
        align-items: center; 
        justify-content: center; /* Centraliza o conteúdo principal verticalmente */
        min-height: 100vh; 
        margin: 0;
        padding: 15px; 
        box-sizing: border-box;
        overflow-x: hidden;
        animation: fadeIn 1s ease-in-out;
        position: relative; /* Necessário para posicionar o botão "Sair" */
    }

    /* --- ESTILO DO BOTÃO SAIR (NOVO) --- */
    #btnSair {
        position: absolute;
        top: 20px;
        right: 20px;
        background: #dc3545; /* Cor vermelha para indicar saída */
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 5px;
        font-size: 1em;
        font-weight: 600;
        cursor: pointer;
        transition: background-color 0.3s, transform 0.3s;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.4);
        text-decoration: none; /* Caso use <a> ao invés de <button> */
        display: inline-block;
    }
    #btnSair:hover {
        background-color: #c82333;
        transform: translateY(-1px);
    }
    /* --- FIM ESTILO DO BOTÃO SAIR --- */

    /* Animação de entrada (Fade-in) */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Estilo do Título/Pergunta */
    h2 {
        color: #FFD700;
        font-size: clamp(1.5em, 5vw, 2.5em);
        margin-bottom: 40px;
        font-weight: 700;
        text-shadow: 0 0 15px rgba(255, 215, 0, 0.6);
        max-width: 800px;
        text-align: center;
        line-height: 1.4;
    }

    /* Estilo do Formulário */
    form {
        display: flex;
        flex-direction: column;
        align-items: center;
        width: 100%;
        max-width: 650px;
    }

    /* Estilo das Opções de Resposta (Label) */
    label {
        background-color: rgba(255, 255, 255, 0.08);
        color: #e0e0e0;
        border: 1px solid rgba(0, 100, 255, 0.5);
        padding: 18px 25px;
        margin-bottom: 18px;
        border-radius: 15px;
        cursor: pointer;
        width: 100%;
        text-align: left;
        transition: all 0.3s ease, box-shadow 0.3s ease;
        font-weight: 600;
        display: flex;
        align-items: center;
        box-sizing: border-box;
        line-height: 1.3;
    }
    
    label:hover {
        background-color: rgba(255, 255, 255, 0.15);
        border-color: #FFD700;
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
    }

    /* Estilo dos Radio Buttons */
    input[type="radio"] {
        appearance: none;
        min-width: 24px;
        height: 24px;
        border: 3px solid #007bff;
        border-radius: 50%;
        margin-right: 20px;
        position: relative;
        cursor: pointer;
        flex-shrink: 0;
        transition: all 0.2s ease;
    }

    input[type="radio"]:checked {
        background-color: #FFD700;
        border-color: #FFD700;
        box-shadow: 0 0 10px rgba(255, 215, 0, 0.8);
    }

    input[type="radio"]:checked::before {
        content: '';
        display: block;
        width: 12px;
        height: 12px;
        background-color: #00001a;
        border-radius: 50%;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    /* Estilo do Botão de Responder */
    button[type="submit"] {
        display: inline-block;
        background: linear-gradient(90deg, #007bff 0%, #FFD700 100%);
        color: #00001a;
        border: none;
        padding: 18px 50px;
        border-radius: 50px;
        font-size: 1.2em;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 30px;
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.6);
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    button[type="submit"]:hover {
        background: linear-gradient(90deg, #FFD700 0%, #007bff 100%);
        transform: translateY(-3px) scale(1.03);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.8);
        color: #fff;
    }

    /* Media Query para Dispositivos Móveis */
    @media (max-width: 600px) {
        body {
            padding: 15px;
        }
        
        #btnSair {
            top: 10px;
            right: 10px;
            padding: 8px 15px;
            font-size: 0.9em;
        }

        h2 {
            font-size: 1.8em;
            margin-bottom: 25px;
            /* Garante que o quiz não fique escondido pelo botão no mobile */
            margin-top: 50px; 
        }

        label {
            padding: 15px 20px;
            font-size: 0.95em;
        }
        
        input[type="radio"] {
            min-width: 20px;
            height: 20px;
        }

        input[type="radio"]:checked::before {
            width: 10px;
            height: 10px;
        }

        button[type="submit"] {
            padding: 15px 40px;
            font-size: 1.1em;
            margin-top: 25px;
        }
    }
</style>
</head>
<body>

<a href="../jogos_php/index.php" id="btnSair">Sair</a>

<div style="color: #007bff; font-weight: 600; margin-bottom: 10px; font-size: 1.1em; text-transform: capitalize;">
    Nível: <?= htmlspecialchars($nivel) ?>
</div>
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