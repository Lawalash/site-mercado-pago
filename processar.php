<?php
require 'vendor/autoload.php';

use Aws\CognitoIdentityProvider\CognitoIdentityProviderClient;
use Aws\Exception\AwsException;

// Configuração da região e pool de usuários do Cognito
$region = 'us-east-2';
$pool_id = 'us-east-2_BIq4QDmmt';
$client_id = '2issf3j3chrjf7fn4to4bp1j13';

// Cria um cliente do Cognito
$client = new CognitoIdentityProviderClient([
    'version' => 'latest',
    'region' => $region,
]);

// Verifica se o formulário foi submetido
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Recupera os dados do usuário
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Autentica o usuário
    try {
        $result = $client->initiateAuth([
            'AuthFlow' => 'USER_PASSWORD_AUTH',
            'ClientId' => $client_id,
            'AuthParameters' => [
                'USERNAME' => $username,
                'PASSWORD' => $password,
            ],
        ]);
    } catch (AwsException $e) {
        // Ocorreu um erro ao autenticar o usuário
        echo 'Erro ao autenticar o usuário: ' . $e->getMessage();
        exit;
    }

    // Se chegou aqui, o usuário foi autenticado com sucesso
    // Recupera o token de acesso e o ID do usuário
    $access_token = $result['AuthenticationResult']['AccessToken'];
    $id_token = $result['AuthenticationResult']['IdToken'];
    $user_id = $result['AuthenticationResult']['IdTokenPayload']['sub'];

    // Cria uma sessão para armazenar as informações do usuário
    session_start();
    $_SESSION['access_token'] = $access_token;
    $_SESSION['id_token'] = $id_token;
    $_SESSION['user_id'] = $user_id;

    // Redireciona o usuário para a página restrita
    header('Location:restrita.php');
    exit;
}

// Se o formulário não foi submetido, exibe o formulário de login
?>
<form method="post">
    <label for="username">Usuário:</label>
    <input type="text" id="username" name="username"><br>

    <label for="password">Senha:</label>
    <input type="password" id="password" name="password"><br>

    <button type="submit">Entrar</button>
</form>
