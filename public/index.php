<?php 
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
$error = $_POST["error"] ?? "";
if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != "") {
    header("Location: ../pages/home.php");
    exit; 
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
        <div class="login-box">
            <h2>Login</h2>
            <a style="color: red"><?=$error?></a>
            <form action="../func/pros.php" method="POST">
                <div class="input-group">
                    <label for="email" id="email">Email</label>
                    <input type="email" name="email" id="email" required>
                </div>
                <div class="input-group">
                    <label for="password" id="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <div class="options">
                    <a href="forgot_password.html">Forgot password</a>
                </div>
                <button type="submit" class="btn" id="submit">Login</button>
                <p><a href="register.php" class="create-account">Create Account</a></p>
            </form>
        </div>
    </div>

    <script>
        let email = document.getElementById("email");
        let password = document.getElementById("password");
        let submit = document.getElementById("submit");

        submit.addEventListener("mouseenter", function() {
            if (email.value === "" || password.value === "") {
                let offsetX = (Math.random() * 200) - 100;
                let offsetY = (Math.random() * 200) - 100;
                submit.style.transform = `translate(${offsetX}px, ${offsetY}px)`;
            }
        });
    </script>
</body>
</html>
