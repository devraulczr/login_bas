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

$email = $_POST["email"] ?? "";
$pass = $_POST["password"] ?? "";
$remember = isset($_POST["remember"]);

$sql = "SELECT * FROM usuarios";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        if ($row["email"] == $email) {
            echo "Tem uma conta com esse e-mail";
            echo "<br>";
            if (password_verify($pass, $row["password"])) {
                echo "Logado com sucesso!";
                $_SESSION['user_id'] = $row["id"];
                $_SESSION['loged'] = true;
                if ($remember) {
                    setcookie('user_id', $email, time() + (86400 * 30), "/"); // 30 dias
                }
                header("Location: ../public/index.php");
                exit;
            } else {
                echo "Senha incorreta.";
            }

        } else {
        }
    }
} else {
    echo "Nenhum usuário encontrado.";
}
$error = "Email ou Senha Incorretos";
echo '<form id="redirectForm" action="../public/index.php" method="POST">
        <input type="hidden" name="error" value="' . htmlspecialchars($error) . '">
      </form>';

echo '<script>document.getElementById("redirectForm").submit();</script>';
exit();
?>