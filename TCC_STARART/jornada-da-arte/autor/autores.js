// Seleciona o botão pelo ID
const toggleButton = document.getElementById('theme-toggle');

// Função para aplicar o tema salvo no carregamento
window.onload = () => {
  const tema = localStorage.getItem('tema');
  if (tema === 'claro') {
    document.body.classList.add('light-theme');
    toggleButton.textContent = '🌙'; // Ícone lua para tema claro
  } else {
    toggleButton.textContent = '🌞'; // Ícone sol para tema escuro (padrão)
  }
};

// Evento de clique no botão para alternar tema
toggleButton.addEventListener('click', () => {
  document.body.classList.toggle('light-theme');
  
  if(document.body.classList.contains('light-theme')) {
    toggleButton.textContent = '🌙'; // Lua
    localStorage.setItem('tema', 'claro');
  } else {
    toggleButton.textContent = '🌞'; // Sol
    localStorage.setItem('tema', 'escuro');
  }
});

