<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
} else {
    echo "Sessão já iniciada!";
}

if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == null) {
    header("Location: ../public/index.php");
    die("Você não tem permissão");
}
// Gerar CSRF token
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = $_POST["error"] ?? "";
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
    $balance = $usuario["saldo"];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/addbal.css">
    <link rel="stylesheet" href="../css/nav.css">
    <?php include('nav.php'); ?>
</head>
<body>
    <div class="container">
    <h1>Transferir</h1>
    <a style="color: white"><?=$error?></a>
    <form action="../func/transfer.php" method="post">
        <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
        <label for="">Id Target</label>
        <input type="number" name="target_id" id="target_id">
        <label for="">Amount</label>
        <input type="number" name="amount" id="amount" step="0.01" mim="0">
        <input type="submit" value="Transferir">
        <a style="color: white">Balance: <?=$balance?></a>
    </form>
    </div>
</body>
</html>