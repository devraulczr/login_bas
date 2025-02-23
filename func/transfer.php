<?php 

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die('Erro: Token CSRF inválido!');
}
if (!isset($_SESSION["user_id"])) {
    die("Erro: Usuário não está logado.");
}

$host = "127.0.0.1";
$user = "root";
$password = "";
$database = "login_bas";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$target_id = $_POST["target_id"] ?? 1;
$amount = $_POST["amount"] ?? 0;
echo $amount;

$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
    $balance = $usuario["saldo"];
    if ($usuario["id"] == $target_id) {
        $error = "Você Não Pode Transferir à Si Mesmo";
echo '<form id="redirectForm" action="../pages/transfer.php" method="POST">
        <input type="hidden" name="error" value="' . htmlspecialchars($error) . '">
      </form>';

echo '<script>document.getElementById("redirectForm").submit();</script>';
    } else {
        
    $stmt->free_result();

    $sql = "SELECT * FROM usuarios WHERE id = ?";
    $stmt->prepare($sql);
    $stmt->bind_param("i", $target_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->free_result();
    if ($result->num_rows > 0) {
        $target = $result->fetch_assoc();
        $username = $target["username"];

        echo "Usuário alvo: " . json_encode($target, JSON_PRETTY_PRINT);
    } else {
        die("Erro: Usuário alvo não encontrado.");
    }

    echo "Saldo do usuário logado: " . $balance;
    if ($balance >= $amount) {
            $sql = "UPDATE usuarios SET saldo = saldo + ? WHERE id = ?";
            $stmt->prepare($sql);
            $stmt->bind_param("di", $amount, $target_id);
            $stmt->execute();

            $sql = "UPDATE usuarios SET saldo = saldo - ? WHERE id = ?";
            $stmt->prepare($sql);
            $stmt->bind_param("di", $amount, $usuario["id"]);
            $stmt->execute();

            $error = "Transferência de R$ {$amount} para {$target["username"]} concluída!";
            echo '<form id="redirectForm" action="../pages/transfer.php" method="POST">
                    <input type="hidden" name="error" value="' . htmlspecialchars($error) . '">
                  </form>';
            
            echo '<script>document.getElementById("redirectForm").submit();</script>';
    } else {
        $error = "Não Tem Money Suficiente";
        echo '<form id="redirectForm" action="../pages/transfer.php" method="POST">
                <input type="hidden" name="error" value="' . htmlspecialchars($error) . '">
              </form>';
        
        echo '<script>document.getElementById("redirectForm").submit();</script>';
    }
    }
} else {
    die("Erro: Usuário não encontrado.");
}

$stmt->close();
$conn->close();
?>
