<?php
// Incluir a conexão com o banco de dados
include('conexao.php');

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitizar e validar os dados recebidos do formulário
    $nome = mysqli_real_escape_string($conn, trim($_POST['nome']));
    $email = mysqli_real_escape_string($conn, trim($_POST['email']));
    $telefone = isset($_POST['telefone']) ? mysqli_real_escape_string($conn, trim($_POST['telefone'])) : '';
    $mensagem = mysqli_real_escape_string($conn, trim($_POST['mensagem']));

    // Validação de dados
    if (empty($nome) || empty($email) || empty($mensagem)) {
        die("Nome, e-mail e mensagem são obrigatórios.");
    }

   

    // Inserir os dados no banco de dados
    $sql = "INSERT INTO contatos (nome, email, telefone, mensagem) VALUES ('$nome', '$email', '$telefone', '$mensagem')";

    if ($conn->query($sql) === TRUE) {
        echo "<p>Mensagem enviada com sucesso!</p>";
    } else {
        echo "Erro ao enviar a mensagem: " . $conn->error;
    }

    // Fechar a conexão com o banco de dados
    $conn->close();
} else {
    echo "Por favor, envie o formulário corretamente.";
}
?>
