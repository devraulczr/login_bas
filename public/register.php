<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != "") {
    header("Location: ../pages/home.php");
    exit; 
}
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION["csrf_token"];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="../css/index.css">
    <link rel="shortcut icon" href="../src/images/favicon.ico" type="image/x-icon">
    <style>
        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            height: 100vh;
        }
        .stars-container {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }
        .star {
            position: absolute;
            background-color: white;
            border-radius: 50%;
            animation: fall 9s linear infinite;
        }
        @keyframes fall {
            0% {
                top: -10px;
                opacity: 1;
            }
            100% {
                top: 100vh;
                opacity: 0;
            }
        }
    </style>
    <script>
        function createStar() {
            let star = document.createElement("div");
            star.classList.add("star");

            let size = Math.random() * 5 + 1; 
            let startX = Math.random() * window.innerWidth;

            star.style.width = size + "px";
            star.style.height = size + "px";
            star.style.left = startX + "px";
            document.querySelector(".stars-container").appendChild(star);
            setTimeout(() => {
                star.remove();
            }, 9000); 
        }
        setInterval(createStar, 300);
    </script>
</head>
<body>
    <div class="stars-container"></div>
    <div class="background"></div>
    <div class="container">
        <form id="forgotPasswordForm" method="POST">
            <h2>Register</h2>
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" required>
            </div>
            <div class="input-group">
                <label for="email">Email</label>
                <input type="email" name="email" id="email" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
            <button type="submit" class="btn" id="submit">Register</button>
            <p><a href="index.php" class="create-account">Connect Account</a></p>
            <p id="responseMessage"></p>
        </form>
    </div>
    <script>
    document.querySelector("#forgotPasswordForm").addEventListener("submit", function(event) {
        event.preventDefault();

        let email = document.getElementById("email").value;
        let username = document.getElementById("username").value;
        let password = document.getElementById("password").value;
        let csrf_token = <?= json_encode($csrf_token) ?>;
        
        if (!email) {
            alert("Por favor, insira um e-mail válido!");
            return;
        }

        fetch(`../api/api.php?action=cadastrar_usuario&email=${encodeURIComponent(email)}&username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}&csrf_token=${encodeURIComponent(csrf_token)}`)
            .then(response => {
                console.log('Resposta do servidor:', response); 
                return response.json();
            })
            .then(data => {
                let responseMessage = document.getElementById("responseMessage");
                
                if (data.error) {
                    responseMessage.style.color = "red";
                    responseMessage.innerText = data.error;
                } else if (data.message) {
                    responseMessage.style.color = "green";
                    responseMessage.innerText = data.message;
                }
            })
            .catch(error => {
                console.error('Erro:', error);
            });
    });
</script>

</body>
</html>
