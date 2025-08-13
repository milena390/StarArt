document.addEventListener("DOMContentLoaded", () => {
  const gameBoard = document.querySelector('.memory-game');
  const restartBtn = document.getElementById('restart-btn');
  const startBtn = document.getElementById('start-btn');
  const moveCount = document.getElementById('move-count');
  const difficultySelect = document.getElementById('difficulty');
  const winMessage = document.getElementById('win-message');
  const playAgainBtn = document.getElementById('play-again-btn');
  const winSound = document.getElementById('win-sound');
  const flipSound = document.getElementById('flip-sound');
  const wrongSound = document.getElementById('wrong-sound');
  const bestScoreDisplay = document.getElementById('best-score');
  const rankingPopup = document.getElementById('ranking-popup');
  const rankingList = document.getElementById('ranking-list');
  const viewRankingBtn = document.getElementById('view-ranking-btn');
  const closeRankingBtn = document.getElementById('close-ranking-btn');

  const imagePaths = [
    'memoria/vangogh.png', 'memoria/tarsila.png', 'memoria/noite.png',
    'memoria/harmonia.png', 'memoria/cuca.png', 'memoria/claude.png',
    'memoria/cafe.png', 'img/vaca.png', 'img/porco.png', 'img/galinha.png',
    'img/polvo.png', 'img/leao.png', 'img/tartaruga.png', 'img/cavalo.png',
    'img/pinguim.png', 'img/unicornio.png'
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
    rankingPopup.classList.add('hidden');

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
      winMessage.classList.add('visible');
      winSound.play();

      const level = difficultySelect.value;
      const bestKey = `best-score-${level}`;
      const historyKey = `history-${level}`;
      const rankKey = `ranking-${level}`;

      const currentBest = parseInt(localStorage.getItem(bestKey)) || Infinity;
      if (moves < currentBest) {
        localStorage.setItem(bestKey, moves);
        bestScoreDisplay.textContent = `Recorde: ${moves}`;
      }

      let history = JSON.parse(localStorage.getItem(historyKey)) || [];
      history.unshift(moves);
      history = history.slice(0, 5);
      localStorage.setItem(historyKey, JSON.stringify(history));

      let ranking = JSON.parse(localStorage.getItem(rankKey)) || [];
      ranking.push(moves);
      ranking.sort((a, b) => a - b);
      ranking = ranking.slice(0, 3);
      localStorage.setItem(rankKey, JSON.stringify(ranking));
    }
  }

  function updateBestScoreDisplay() {
    const best = localStorage.getItem('best-score-' + difficultySelect.value);
    bestScoreDisplay.textContent = best ? `Recorde: ${best}` : 'Recorde: --';
  }

  startBtn.addEventListener('click', () => {
    if (jogoAtivo) return;
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

  restartBtn.addEventListener('click', () => {
    initGame();
    updateBestScoreDisplay();
  });

  playAgainBtn.addEventListener('click', () => {
    winMessage.classList.remove('visible');
    winMessage.classList.add('hidden');
    initGame();
    updateBestScoreDisplay();
  });

  difficultySelect.addEventListener('change', () => {
    initGame();
    updateBestScoreDisplay();
  });

  viewRankingBtn.addEventListener('click', () => {
    const level = difficultySelect.value;
    const ranking = JSON.parse(localStorage.getItem(`ranking-${level}`)) || [];
    const history = JSON.parse(localStorage.getItem(`history-${level}`)) || [];

    let html = `<strong>Top 3 Recordes:</strong><ul>`;
    ranking.forEach((score, i) => {
      html += `<li>${i + 1}º lugar: ${score} jogadas</li>`;
    });
    html += `</ul><strong>Últimas partidas:</strong><ul>`;
    history.forEach((score, i) => {
      html += `<li>${i + 1}ª: ${score} jogadas</li>`;
    });
    html += `</ul>`;
    rankingList.innerHTML = html;
    rankingPopup.classList.remove('hidden');
  });

  if (closeRankingBtn) {
    closeRankingBtn.addEventListener('click', () => {
      rankingPopup.classList.add('hidden');
    });
  }

  // Inicializa ao carregar
  initGame();
  updateBestScoreDisplay();
});
