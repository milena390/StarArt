document.addEventListener("DOMContentLoaded", () => {
  const startBtn = document.getElementById('start-btn');
  const gameBoard = document.querySelector('.memory-game');
  const moveCount = document.getElementById('move-count');
  const difficultySelect = document.getElementById('difficulty');

  const winMessage = document.getElementById('win-message');
  const playAgainBtn = document.getElementById('play-again-btn');

  const winSound = document.getElementById('win-sound');
  const flipSound = document.getElementById('flip-sound');
  const wrongSound = document.getElementById('wrong-sound');

  let cardsArray = [];
  let firstCard = null;
  let lockBoard = true;
  let jogoAtivo = false;
  let moves = 0;

  const imagePaths = [
    'memoria/tarsila.png', 'memoria/noite.png',
    'memoria/harmonia.png', 'memoria/cuca.png',
    'memoria/claude.png', 'memoria/cafe.png',
    'img/vaca.png', 'img/porco.png', 'img/galinha.png',
    'img/polvo.png', 'img/leao.png', 'img/tartaruga.png',
    'img/cavalo.png', 'img/pinguim.png', 'img/unicornio.png'
  ];

  function initGame() {
    gameBoard.innerHTML = '';
    firstCard = null;
    lockBoard = true;
    jogoAtivo = false;
    moves = 0;
    moveCount.textContent = 'Jogadas: 0';
    winMessage.classList.add('hidden');

    const difficulty = parseInt(difficultySelect.value);
    const selectedImages = imagePaths.slice(0, difficulty);
    cardsArray = [...selectedImages, ...selectedImages].sort(() => 0.5 - Math.random());

    const cols = difficulty <= 8 ? 4 : 6;
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
    if (lockBoard || !jogoAtivo || card.classList.contains('flipped')) return;

    card.classList.add('flipped');
    flipSound.play();

    if (!firstCard) {
      firstCard = card;
      return;
    }

    moves++;
    moveCount.textContent = `Jogadas: ${moves}`;
    const secondCard = card;

    const img1 = firstCard.querySelector('.card-front').src;
    const img2 = secondCard.querySelector('.card-front').src;

    if (img1 === img2) {
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
    const allFlipped = [...document.querySelectorAll('.card')].every(card =>
      card.classList.contains('flipped')
    );
    if (allFlipped) {
      winMessage.classList.remove('hidden');
      winSound.play();
      jogoAtivo = false;

      // Envia pontuação para ranking_memoria.php
      sendScore(moves);
    }
  }

  function sendScore(score) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'ranking_memoria.php';

    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = 'pontuacao';
    input.value = score;
    form.appendChild(input);

    document.body.appendChild(form);
    form.submit();
  }

  startBtn.addEventListener('click', () => {
    const cards = document.querySelectorAll('.card');
    lockBoard = true;
    jogoAtivo = false;

    let countdown = 3;
    const originalText = startBtn.textContent;
    const countdownInterval = setInterval(() => {
      startBtn.textContent = `⏳ ${countdown--}`;
      if (countdown < 0) {
        clearInterval(countdownInterval);
        cards.forEach((card, index) => {
          setTimeout(() => {
            card.classList.remove('flipped');
            if (index === cards.length - 1) {
              jogoAtivo = true;
              lockBoard = false;
              startBtn.textContent = originalText;
            }
          }, index * 80);
        });
      }
    }, 1000);
  });

  playAgainBtn.addEventListener('click', () => {
    winMessage.classList.add('hidden');
    initGame();
  });

  difficultySelect.addEventListener('change', () => {
    initGame();
  });

  initGame();
});
