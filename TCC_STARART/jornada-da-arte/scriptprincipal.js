// Função para alternar o tema
function toggleTheme() {
  const body = document.body;
  body.classList.toggle('light-theme');

  // Salva a preferência no localStorage
  if (body.classList.contains('light-theme')) {
    localStorage.setItem('theme', 'light');
  } else {
    localStorage.setItem('theme', 'dark');
  }
}

// Aplica o tema salvo no carregamento da página
function applySavedTheme() {
  const savedTheme = localStorage.getItem('theme');
  if (savedTheme === 'light') {
    document.body.classList.add('light-theme');
  }
}

// Configura o evento do botão
document.addEventListener('DOMContentLoaded', () => {
  applySavedTheme();

  const btn = document.querySelector('.theme-toggle');
  btn.addEventListener('click', toggleTheme);
});
