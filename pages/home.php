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
$host = "127.0.0.1";
$user = "root";
$password = "";
$database = "login_bas";
$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION["user_id"]);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();

    $email = $usuario["email"];
    $pass = $usuario["password"];
    $saldo = $usuario["saldo"];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Olá, <?=$usuario["username"];?></title>
    <link rel="stylesheet" href="../css/nav.css"> <!-- Incluindo o CSS da Navbar -->
    <link rel="stylesheet" href="../css/home.css">
    <link rel="shortcut icon" href="../src/images/favicon.ico" type="image/x-icon">
</head>
<body>
    <!-- Incluindo a navbar -->
    <?php include('nav.php'); ?>

    <div class="container">
        <h2>Informações: </h2>
        <div class="info">
            <p id="username_id">Username: <?=$usuario["username"];?></p>
            <p>
            <pre id="saldo_id">Saldo: <?=$usuario["saldo"];?> <button onclick="hideShow()" id="hideshow_btn" class='hideshow_btn'><img src="../src/images/hide.png" alt=""></button></pre>
            
        </p>
        <a href="../pages/transfer.php"><button>Transferencia</button></a>
        </div>
        <div class="options">

        </div>

    </div>

    <script>
    var stat = false;
    const texto = document.getElementById("username_id");
    // Definindo o saldo PHP como variável JS
    var saldoOriginal = "<?=$usuario['saldo']?>"; // Salva o saldo original

    // Função para realizar o logout
    function logout() {
        window.location.href = "../func/logout.php";
    }

    function hideShow() {
        var saldo_id = document.getElementById("saldo_id");
        var btn = document.getElementById("hideshow_btn");
        if (!stat) {
            // Oculta o saldo original
            stat = true;
            saldo_id.innerHTML = "Saldo: *** <button onclick='hideShow()' id='hideshow_btn' class='hideshow_btn'><img src='../src/images/show.png' alt=''></button>";
            btn.innerText  = "Show";
        } else {
            // Exibe o saldo original
            stat = false;
            saldo_id.innerHTML = "Saldo: " + saldoOriginal + " <button onclick='hideShow()' id='hideshow_btn' class='hideshow_btn'><img src='../src/images/hide.png' alt=''></button>";
        }
    }
    

    texto.addEventListener("mouseover", function() {
        texto.innerText = "User Id: <?=$usuario["id"];?>";
    });

    texto.addEventListener("mouseout", function() {
        texto.innerText = "Username: <?=$usuario["username"];?>";
    });
    </script>
</body>
</html>
