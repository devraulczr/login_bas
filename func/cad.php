<?php
session_start();

if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Erro: Token CSRF inválido!');
}

$host = "127.0.0.1";
$user = "root";
$password = "";
$database = "login_bas";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}
$email = $_POST["email"] ?? "";
$pass = $_POST["password"] ?? "";
$username = $_POST["username"] ?? "";2
if (empty($email) || empty($pass) || empty($username)) {
    die("Erro: Todos os campos são obrigatórios!");
}

$passHash = password_hash($pass, PASSWORD_DEFAULT);

$sql = "SELECT * FROM usuarios WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    die("Erro: E-mail já cadastrado!");
} else {
    $sql = "INSERT INTO usuarios (username, email, password) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $email, $passHash);
    
    if ($stmt->execute()) {
        header("Location: ../public/index.php");
        exit;
    } else {
        die("Erro ao cadastrar usuário: " . $stmt->error);
    }
}

$stmt->close();
$conn->close();
?>
