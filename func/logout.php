<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
} else {
    echo "Sessão já iniciada!";
}

session_unset();

session_destroy();
header("Location: ../public/index.php");
exit();
?>
