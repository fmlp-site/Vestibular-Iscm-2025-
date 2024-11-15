<?php
session_start();

// URL da API RestDB.io
$api_url = "https://<seu-db>.restdb.io/rest/candidatos";
$api_key = "<sua-api-key>"; // Substitua pela sua API Key

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action']; // 'cadastro' ou 'login'
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    if ($action === 'cadastro') {
        // Cadastro do candidato
        $data = [
            "email" => $email,
            "senha" => $senha,
        ];

        $options = [
            'http' => [
                'header' => "Content-type: application/json\r\nx-apikey: $api_key\r\n",
                'method' => 'POST',
                'content' => json_encode($data),
            ],
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($api_url, false, $context);

        if ($result) {
            echo "<script>alert('Cadastro realizado com sucesso!');</script>";
        } else {
            echo "<script>alert('Erro ao cadastrar!');</script>";
        }
    } elseif ($action === 'login') {
        // Login do candidato
        $options = [
            'http' => [
                'header' => "x-apikey: $api_key\r\n",
                'method' => 'GET',
            ],
        ];

        $context = stream_context_create($options);
        $result = file_get_contents($api_url . "?q=" . urlencode(json_encode(["email" => $email])), false, $context);
        $data = json_decode($result, true);

        if (!empty($data) && $data[0]['senha'] === $senha) {
            $_SESSION['candidato'] = $data[0];
            header("Location: centraldocandidato.php");
            exit;
        } else {
            echo "<script>alert('Login inválido!');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Acesso Candidato</title>
</head>
<body>
    <h1>Cadastro/Login - Candidato</h1>
    <form method="POST">
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="senha" placeholder="Senha" required>
        <button type="submit" name="action" value="cadastro">Cadastrar</button>
        <button type="submit" name="action" value="login">Login</button>
    </form>
</body>
</html>
