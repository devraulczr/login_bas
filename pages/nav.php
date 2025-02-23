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

if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
    $admin = $usuario["admin"];
}
?>
<nav>
    <div class="logo"><a href="../public/index.php"><img src="../src/images/logo.png" alt=""></a></div>
    <ul>
        <li><a href="home.php">Início</a></li>
        <li><a href="transfer.php">Transferir</a></li>

        <?php if ($admin == 1): ?>
            <li class="dropdown">
                <a href="javascript:void(0)" class="dropbtn">Admin</a>
                <div class="dropdown-content">
                    <a href="setBalance.php">Set Balance</a>
                    <a href="editInfo.php">Edit Info</a>
                </div>
            </li>
        <?php endif; ?>
        <li><a href="../func/logout.php">Logout</a></li>
    </ul>
    
</nav>

<style>
    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropbtn {
        background-color: rgb(50, 136, 175);
        color: white;
        padding: 10px 16px;
        font-size: 16px;
        border: none;
        cursor: pointer;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #f9f9f9;
        min-width: 160px;
        box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
        z-index: 1;
    }

    .dropdown-content a {
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
    }

    .dropdown-content a:hover {
        background-color: #f1f1f1;
    }

    .dropdown:hover .dropdown-content {
        display: block;
    }

    .dropdown:hover .dropbtn {
        background-color: #3e8e41;
    }
    img {
        width: 40%;
        transform: translate(-25%, 0);
    }
</style>
