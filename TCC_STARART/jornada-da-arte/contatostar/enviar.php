<?php
include('conexao.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = mysqli_real_escape_string($conn, trim($_POST['nome']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $mensagem = mysqli_real_escape_string($conn, trim($_POST['mensagem']));

    if (empty($nome) || empty($email) || empty($mensagem)) {
        echo "Preencha todos os campos obrigatórios.";
        exit;
    }

    $sql = "INSERT INTO tbl_contato (nome, email, mensagem) VALUES ('$nome', '$email', '$mensagem')";

    if ($conn->query($sql) === TRUE) {
        echo "Mensagem enviada com sucesso!";
    } else {
        echo "Erro ao enviar a mensagem: " . $conn->error;
    }

    $conn->close();
} else {
    echo "Requisição inválida.";
}
