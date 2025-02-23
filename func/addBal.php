<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
} else {
}

$host = "127.0.0.1";
$user = "root";
$password = "";
$database = "login_bas";
$conn = new mysqli($host, $user, $password, $database);

if($conn->connect_error) {
    die("Falha Na Conexão: ".$conn->connect_error);
}
$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();
$result = $stmt->get_result();
if($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
    $admin = $usuario["admin"];
    $valor = $_POST["amount"] ?? "";
    $amount = floatval($valor);
    $valor2 = $_POST["valor2"] ?? "";
    $tipo = $_POST["tipo"] ?? "";
    if ($admin == 1) {
        if ($tipo === "email") {
            $sql = "UPDATE usuarios SET saldo = saldo + ? WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ds", $amount, $valor2);
            $stmt->execute();
            if($stmt->affected_rows > 0) {
                $message = "Saldo Atualizado para o E-mail!";
                echo '<form id="redirectForm" action="../pages/setBalance.php" method="POST">
                    <input type="hidden" name="returned" value="' . htmlspecialchars($message) . '">
                    <input type="hidden" name="status" value="success">
                </form>';
                echo '<script>document.getElementById("redirectForm").submit();</script>';
            } else {
                $error = "Erro ao atualizar saldo para o E-mail.";
                echo '<form id="redirectForm" action="../pages/setBalance.php" method="POST">
                    <input type="hidden" name="returned" value="' . htmlspecialchars($error) . '">
                    <input type="hidden" name="status" value="error">
                </form>';
                echo '<script>document.getElementById("redirectForm").submit();</script>';
            }
        } else if ($tipo == "id") {
            // Verifica se o ID foi preenchido corretamente
            if (is_numeric($valor2)) {
                $id = intval($valor2);
                $sql = "UPDATE usuarios SET saldo = saldo + ? WHERE id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ds", $amount, $id);
                $stmt->execute();
                if($stmt->affected_rows > 0) {
                    $message = "Saldo Atualizado para o Id!";
                    echo '<form id="redirectForm" action="../pages/setBalance.php" method="POST">
                        <input type="hidden" name="returned" value="' . htmlspecialchars($message) . '">
                        <input type="hidden" name="status" value="success">
                    </form>';
                    echo '<script>document.getElementById("redirectForm").submit();</script>';
                } else {
                    $error = "Erro ao atualizar saldo para o Id.";
                    echo '<form id="redirectForm" action="../pages/setBalance.php" method="POST">
                        <input type="hidden" name="returned" value="' . htmlspecialchars($error) . '">
                        <input type="hidden" name="status" value="error">
                    </form>';
                    echo '<script>document.getElementById("redirectForm").submit();</script>';
                }
            } else {
                echo "ID inválido.";
            }
    } else {
        header("Location: ../public/index.php");
    }
}
} else {
    echo "Usuario Não Encontrado";
}
?>