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
    if ($admin == 1) {
    } else {
        header("Location: ../public/index.php");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Info</title>
    <link rel="stylesheet" href="../css/addbal.css">
    <link rel="stylesheet" href="../css/nav.css">
    <link rel="shortcut icon" href="../src/images/favicon.ico" type="image/x-icon">
</head>
<body>
<?php include('nav.php'); ?>
<div class="container">
    <h1>Alterar Informações</h1>
    <h3 style="color: red;"><?= htmlspecialchars($_POST["returned"] ?? "") ?></h3>

    <form action="../func/editInfo.php" method="post">
        <label for="id_num">ID</label>
        <input type="number" name="id_num" id="id_num" autocomplete="off" required>

        <label for="username">Username</label>
        <input type="text" name="username" id="username" autocomplete="off">

        <label for="email">Email</label>
        <input type="email" name="email" id="email" autocomplete="off">

        <label for="password">Password</label>
        <input type="password" name="password" id="password" autocomplete="off">

        <label for="saldo">Saldo</label>
        <input type="number" name="saldo_user" id="saldo" autocomplete="off" step="0.01" min="0">

        <input type="submit" value="Alterar">
    </form>
</div>

</body>
</html>