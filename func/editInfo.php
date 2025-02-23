<?php
session_start();

$host = "127.0.0.1";
$user = "root";
$password = "";
$database = "login_bas";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$id_num = $_POST["id_num"] ?? "";
$email = $_POST["email"] ?? "";
$pass = $_POST["password"] ?? "";
$username = $_POST["username"] ?? "";
$saldo = $_POST["saldo_user"] ?? null;

$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $id_num);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "Id Existe<br>";

    if (!empty($email)) {
        $sql = "UPDATE usuarios SET email = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $id_num);
        $stmt->execute();
        echo "Email atualizado!<br>";
    }

    if (!empty($pass)) {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET password = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $hash, $id_num);
        $stmt->execute();
        echo "Senha atualizada!<br>";
    }

    if (!empty($username)) {
        $sql = "UPDATE usuarios SET username = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $username, $id_num);
        $stmt->execute();
        echo "Username atualizado!<br>";
    }

    if (isset($_POST["saldo_user"])) { 
        $sql = "UPDATE usuarios SET saldo = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ds", $saldo, $id_num);
        $stmt->execute();
        echo "Saldo atualizado!<br>";
    }

    $message = "Informações Atualizadas!";
    echo '<form id="redirectForm" action="../pages/editInfo.php" method="POST">
        <input type="hidden" name="returned" value="' . htmlspecialchars($message) . '">
        <input type="hidden" name="status" value="success">
    </form>';
    echo '<script>document.getElementById("redirectForm").submit();</script>';
} else {
    echo "ID não encontrado!";
}

$stmt->close();
$conn->close();
?>
