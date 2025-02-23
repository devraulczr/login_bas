<?php
require __DIR__ . '/vendor/autoload.php';
use SendinBlue\Client\Api\TransactionalEmailsApi;
use SendinBlue\Client\Configuration;
use SendinBlue\Client\Model\SendSmtpEmail;
use SendinBlue\Client\Model\SendSmtpEmailTo;

header('Content-Type: application/json');

$host     = '127.0.0.1';
$dbname   = 'login_bas';
$user     = 'root';
$pass     = '';
$charset  = 'utf8mb4';
$dsn      = "mysql:host=$host;dbname=$dbname;charset=$charset";
$options  = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Erro na conexão com o banco de dados"]);
    exit;
}

function generateCode($length = 6) {
    return substr(str_shuffle("0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);
}

function sendResetEmail($toEmail, $code) {
    $apiKey = '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';
    $config = Configuration::getDefaultConfiguration()->setApiKey('api-key', $apiKey);
    $apiInstance = new TransactionalEmailsApi(new \GuzzleHttp\Client(), $config);
    $email = new SendSmtpEmail();
    $email->setSender(['email' => 'raulcrezar@gmail.com', 'name' => 'devraulczr']);
    $email->setTo([new SendSmtpEmailTo(['email' => $toEmail])]);
    $email->setSubject('Código para redefinição de senha');
    $email->setHtmlContent("Seu código para redefinição de senha é: <b>$code</b>");
    $email->setTextContent("Seu código para redefinição de senha é: $code");
    try {
        return true;
    } catch (Exception $e) {
        return "Erro ao enviar o e-mail: " . $e->getMessage();
    }
}

$action = $_GET['action'] ?? '';

if ($action === 'forgot_password' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $email = $_GET['email'] ?? '';
    
    if (empty($email)) {
        http_response_code(400);
        echo json_encode(["error" => "Email é obrigatório"]);
        exit;
    }

    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    if (!$email) {
        http_response_code(400);
        echo json_encode(["error" => "Email inválido"]);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();
    if (!$usuario) {
        http_response_code(400);
        echo json_encode(["error" => "Usuário não encontrado"]);
        exit;
    }

    $code = generateCode();
    
    $expireAt = date('Y-m-d H:i:s', strtotime('+30 minutes'));
    $date     = date('Y-m-d H:i:s', strtotime('+30 minutes'));
    echo json_encode(["error" => $date]);
    $stmt = $pdo->prepare("UPDATE usuarios SET reset_code = ?, code_expires_at = ? WHERE email = ?");
    $stmt->execute([$code, $date, $email]);

    // Envia o e-mail
    $result = sendResetEmail($email, $code);
    if ($result !== true) {
        http_response_code(500);
        echo json_encode(["error" => $result]);
        exit;
    }

    echo json_encode(["message" => "Código gerado e enviado para $email"]);
    exit;
} elseif ($action === 'reset_password' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $email = $_GET['email'] ?? '';
    $code = $_GET['code']   ?? '';
    $newPassword = $_GET['new_password'] ?? '';

    if (empty($email) || empty($code) || empty($newPassword)) {
        http_response_code(400);
        echo json_encode(["error" => "Email, código e nova senha são obrigatórios"]);
        exit;
    }

    $email = filter_var($email, FILTER_VALIDATE_EMAIL);
    if (!$email) {
        http_response_code(400);
        echo json_encode(["error" => "Email inválido"]);
        exit;
    }

    $stmt = $pdo->prepare("SELECT reset_code, code_expires_at FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();
    if (!$usuario) {
        http_response_code(404);
        echo json_encode(["error" => "Usuário não encontrado"]);
        exit;
    }
    if ($usuario['reset_code'] !== $code && $date < date('Y-m-d H:i:s')  ){
        http_response_code(400);
        echo json_encode(["error" => "Código inválido ou expirado"]);
        exit;
    }

    $newPasswordHash = password_hash($newPassword, PASSWORD_BCRYPT);
    $stmt = $pdo->prepare("UPDATE usuarios SET password = ?, reset_code = NULL, code_expires_at = NULL WHERE email = ?");
    $stmt->execute([$newPasswordHash, $email]);

    echo json_encode(["message" => "Senha redefinida com sucesso"]);
    exit;
} elseif ($action === 'cadastrar_usuario' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    $email = $_GET['email']       ?? '';
    $username = $_GET['username'] ?? '';
    $password = $_GET['password'] ?? '';
    $csrf = $_GET["csrf_token"]   ?? '';
    if (isset($_SESSION['csrf_token']) && !empty($_SESSION['csrf_token'])) {
        if ($csrf === $_SESSION['csrf_token']) {
        } else {
            http_response_code(400);
            echo json_encode(["error" => "Token inválido"]);
            exit;
        }
    } else {
        http_response_code(400);
        echo json_encode(["error" => "Token não encontrado"]);
        exit;
    }
    
    

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();
    
    if ($usuario) {
        http_response_code(400);
        echo json_encode(["error" => "Email já cadastrado no banco de dados"]);
    } else {
        $newPasswordHash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO usuarios (username, email, password) VALUES (?, ?, ?)");
        $result = $stmt->execute([$username, $email, $newPasswordHash]);
    
        if ($result) {
            http_response_code(200);
            echo json_encode(["message" => "Conta criada com sucesso!"]);
        } else {
            http_response_code(400);
            echo json_encode(["error"  => "Erro ao criar a conta"]);
        }
    }
}  elseif ($action === 'transferir' && $_SERVER["REQUEST_METHOD"] === 'GET') {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == null) {
        http_response_code(400);
        echo json_encode(["error" => "Usuário não logado"]);
        exit;
    }

    http_response_code(200);
    echo json_encode(["message" => "Usuário está logado"]);

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $usuario = $stmt->fetch();

    if (!$usuario) {
        http_response_code(404);
        echo json_encode(["error" => "Usuário não encontrado"]);
        exit;
    }

    $dest_id = isset($_GET['dest_id']) ? (int) $_GET['dest_id'] : 0;
    $amount = isset($_GET['amount']) ? (float) $_GET['amount'] : 0;

    if ($dest_id === $_SESSION['user_id']) {
        http_response_code(400);
        echo json_encode(["error" => "Você não pode transferir para si mesmo"]);
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$dest_id]);
    $destinatario = $stmt->fetch();

    if (!$destinatario) {
        http_response_code(404);
        echo json_encode(["error" => "Destinatário não encontrado"]);
        exit;
    }

    if ($usuario["saldo"] < $amount) {
        http_response_code(400);
        echo json_encode(["error" => "Saldo insuficiente"]);
        exit;
    }

    $stmt = $pdo->prepare("UPDATE usuarios SET saldo = saldo + ? WHERE id = ?");
    $stmt->execute([$amount, $dest_id]);

    $stmt = $pdo->prepare("UPDATE usuarios SET saldo = saldo - ? WHERE id = ?");
    $stmt->execute([$amount, $_SESSION["user_id"]]);

    echo json_encode(["message" => "Transferência realizada com sucesso"]);
    exit;
} else {
    http_response_code(404);
    echo json_encode(["error" => "Endpoint não encontrado ou método não permitido"]);
    exit;
}
?>