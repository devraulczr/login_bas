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
    <title>Set Balance</title>
    <link rel="stylesheet" href="../css/addbal.css">
    <link rel="stylesheet" href="../css/nav.css">
    <link rel="shortcut icon" href="../src/images/favicon.ico" type="image/x-icon">
</head>
<body>
    <?php include('nav.php'); ?>

    <div class="container">
    <h3  style="color: white;"><?=$_POST["returned"]??"Add Balance"?></h3>
        <div class="buttonss">
        <button onclick="email()" id="email_btn" class="tipo">Email</button>
        <button onclick="iddiv()" id="id_btn" class="tipo">Id</button>
        </div>

    <div class="email" id="email" style="display: block;">
        <form action="../func/addBal.php" method="post">
            <input type="hidden" name="tipo" value="email">
            <label for="emailInput">Email</label> <br>
            <input type="email" name="valor2" id="emailInput"> <br>
            <label for="amount">Amount</label> <br>
            <input type="number" name="amount" id="amount" step="0.01" min="0"> <br>
            <input type="submit" value="Enviar">
        </form>
    </div>

    <div class="id" id="idform" style="display: none;">
        <form action="../func/addBal.php" method="post">
            <input type="hidden" name="tipo" value="id">
            <label for="idInput">Id</label> <br>
            <input type="number" name="valor2" id="idInput"> <br>
            <label for="amount">Amount</label> <br>
            <input type="number" name="amount" id="amount" step="0.01" min="0"> <br>
            <input type="submit" value="Enviar">
        </form>
    </div>
    </div>

    <script>
        var emailForm = document.getElementById("email");
        var idForm = document.getElementById("idform");
        var email_btn = document.getElementById("email_btn");
        var id_btn = document.getElementById("id_btn");

        function email() {
            emailForm.style.display = "block";
            idForm.style.display = "none";
            email_btn.style.background = "gray";
            email_btn.style.color = "white";
            id_btn.style.color = "black";
            id_btn.style.background = "white";
        }

        function iddiv() {
            emailForm.style.display = "none";
            idForm.style.display = "block";
            id_btn.style.background = "gray";
            id_btn.style.color = "white";
            email_btn.style.color = "black";
            email_btn.style.background = "white";
        }
    </script>
</body>


</html>