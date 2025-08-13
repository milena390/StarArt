<?php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "starart";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) die("Erro: " . $conn->connect_error);

$nome = $_POST['nome'];
$pontuacao = $_POST['pontuacao'];

$stmt = $conn->prepare("INSERT INTO ranking (nome, pontuacao) VALUES (?, ?)");
$stmt->bind_param("si", $nome, $pontuacao);
$stmt->execute();
$stmt->close();
$conn->close();
?>
