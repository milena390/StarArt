<?php
session_start();
require 'db.php';

if(!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

// Quando o jogo envia a pontuação
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $pontos = intval($_POST['pontuacao']);
    $usuario_id = $_SESSION['usuario_id'];

    $stmt = $pdo->prepare("SELECT pontos FROM pontuacoes_memoria WHERE usuarios_id=?");
    $stmt->execute([$usuario_id]);
    $existe = $stmt->fetch();

    if($existe){
        if($pontos > $existe['pontos']){
            $stmt = $pdo->prepare("UPDATE pontuacoes_memoria SET pontos=? WHERE usuarios_id=?");
            $stmt->execute([$pontos,$usuario_id]);
        }
    } else {
        $stmt = $pdo->prepare("INSERT INTO pontuacoes_memoria (pontos, usuarios_id) VALUES (?,?)");
        $stmt->execute([$pontos,$usuario_id]);
    }

    header("Location: ranking_memoria.php"); // vai direto para o ranking
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<title>Jogo da Memória</title>
<link rel="stylesheet" href="stylememoria.css" />
</head>
<body>
<h1>Jogo da Memória</h1>

<div class="info-bar">
    <button id="start-btn">🟢 Começar</button>
    <button id="restart-btn">🔄 Reiniciar</button>
    <span id="move-count">Jogadas: 0</span>
    <span id="best-score">Recorde: --</span>
    <label for="difficulty">Nível:</label>
    <select id="difficulty">
        <option value="8">Fácil</option>
        <option value="12">Médio</option>
        <option value="16">Difícil</option>
    </select>
</div>

<section class="memory-game"></section>

<!-- POPUP DE VITÓRIA -->
<div id="win-message" class="hidden">
    <div class="teacup">🍵</div>
    <h2>Hora do Chá da Vitória! 🎉</h2>
    <form id="submit-score" method="POST">
        <input type="hidden" name="pontuacao" id="pontuacao">
        <button type="submit">📤 Enviar Pontuação</button>
    </form>
    <button id="play-again-btn">🔁 Jogar de Novo</button>
</div>

<audio id="win-sound" src="som/vitoria.mp3" preload="auto"></audio>
<audio id="flip-sound" src="som/flip.mp3" preload="auto"></audio>
<audio id="wrong-sound" src="som/erro.mp3" preload="auto"></audio>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const gameBoard = document.querySelector('.memory-game');
    const restartBtn = document.getElementById('restart-btn');
    const startBtn = document.getElementById('start-btn');
    const moveCount = document.getElementById('move-count');
    const bestScoreDisplay = document.getElementById('best-score');
    const difficultySelect = document.getElementById('difficulty');
    const winMessage = document.getElementById('win-message');
    const playAgainBtn = document.getElementById('play-again-btn');
    const pontuacaoInput = document.getElementById('pontuacao');

    const winSound = document.getElementById('win-sound');
    const flipSound = document.getElementById('flip-sound');
    const wrongSound = document.getElementById('wrong-sound');

    const imagePaths = [
        'memoria/abaporu.png','memoria/beijo.png','memoria/cachorro.png',
        'memoria/cafe.png','memoria/claude.png','memoria/cuca.png',
        'memoria/harmonia.png','memoria/mona.png','memoria/mulher.png','memoria/noite.png',
        'memoria/tarsila.png','memoria/tempo.png','memoria/vangogh.png','memoria/au.png',
        'memoria/grito.png'
    ];

    let cardsArray = [];
    let firstCard = null;
    let lockBoard = true;
    let jogoAtivo = false;
    let moves = 0;

    function initGame() {
        gameBoard.innerHTML = '';
        firstCard = null;
        lockBoard = true;
        jogoAtivo = false;
        moves = 0;
        moveCount.textContent = 'Jogadas: 0';
        winMessage.classList.remove('visible');
        winMessage.classList.add('hidden');

        const difficulty = parseInt(difficultySelect.value);
        const selectedImages = imagePaths.slice(0, difficulty);
        cardsArray = [...selectedImages, ...selectedImages].sort(() => 0.5 - Math.random());

        let cols = difficulty <= 8 ? 4 : 6;
        gameBoard.style.gridTemplateColumns = `repeat(${cols}, 1fr)`;

        cardsArray.forEach(src => {
            const card = document.createElement('div');
            card.classList.add('card', 'flipped');
            card.innerHTML = `
                <div class="card-inner">
                    <img class="card-front" src="${src}" alt="Frente" />
                    <img class="card-back" src="img/verso.png" alt="Verso" />
                </div>
            `;
            card.addEventListener('click', () => handleCardClick(card));
            gameBoard.appendChild(card);
        });
    }

    function handleCardClick(card) {
        if(lockBoard || !jogoAtivo || card.classList.contains('flipped')) return;

        card.classList.add('flipped');
        flipSound.play();

        if(!firstCard){
            firstCard = card;
            return;
        }

        moves++;
        moveCount.textContent = `Jogadas: ${moves}`;
        const secondCard = card;

        const img1 = firstCard.querySelector('.card-front').src;
        const img2 = secondCard.querySelector('.card-front').src;

        if(img1 === img2){
            firstCard = null;
            checkWin();
        } else {
            lockBoard = true;
            wrongSound.play();
            setTimeout(() => {
                firstCard.classList.remove('flipped');
                secondCard.classList.remove('flipped');
                firstCard = null;
                lockBoard = false;
            }, 800);
        }
    }

    function checkWin() {
        const allFlipped = [...document.querySelectorAll('.card')].every(card => card.classList.contains('flipped'));
        if(allFlipped){
            winMessage.classList.remove('hidden');
            winMessage.classList.add('visible');
            winSound.play();
            pontuacaoInput.value = moves; // passa pontuação para enviar ao PHP
        }
    }

    startBtn.addEventListener('click', () => {
        const cards = document.querySelectorAll('.card');
        lockBoard = true;
        jogoAtivo = false;

        let countdown = 3;
        const originalText = startBtn.textContent;
        const countdownInterval = setInterval(() => {
            startBtn.textContent = `⏳ ${countdown--}`;
            if(countdown < 0){
                clearInterval(countdownInterval);
                cards.forEach((card, index) => {
                    setTimeout(() => {
                        card.classList.remove('flipped');
                        if(index === cards.length - 1){
                            jogoAtivo = true;
                            lockBoard = false;
                            startBtn.textContent = originalText;
                        }
                    }, index*80);
                });
            }
        }, 1000);
    });

    restartBtn.addEventListener('click', initGame);
    playAgainBtn.addEventListener('click', initGame);
    difficultySelect.addEventListener('change', initGame);

    // Inicializa ao carregar
    initGame();
});
</script>
</body>
</html>
