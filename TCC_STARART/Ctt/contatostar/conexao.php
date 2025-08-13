<?php
$host = "localhost"; // Nome do host (geralmente localhost)
$dbname = "db_contato"; // Nome do banco de dados
$username = "root"; // Usuário do banco de dados
$password = "&tec77@info!"; // Senha do banco de dados

// Criando a conexão com o banco
$conn = new mysqli($host, $username, $password, $dbname);

// Verificando a conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}
?>
