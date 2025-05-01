<?php
session_start([
    'name' => "minha_sessao",
    'cookie_lifetime' => 60 * 60 * 24,
    'cookie_path' => '/',
    'cookie_secure' => false, //true habilita somente para HTTPS
    'cookie_httponly' => false,
    'cookie_samesite' => 'Strict'

]);

$_SESSION["usuario"] = 'Admin';

//setcookie('cookie', "valor do cookie", time() + 60 * 60);

setcookie('teste', "testando...", [
    'expires' => time() + 60 * 60,
    'path' => '/',
    'secure' => false,
    'httponly' => false,
    'samesite' => "Strict"
]);

if (isset($_GET['logout'])) {
    $_SESSION = [];
    session_destroy();

    foreach ($_COOKIE as $nome => $valor) {
        setcookie($nome, "",  [
            'expires' => time() - 60 * 60,
            'path' => '/',
            'secure' => false,
            'httponly' => false,
            'samesite' => "Strict"
        ]);
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Principal</title>
</head>
<body>
<main>
    <h1>Página Principal</h1>
    <p>Usuário Logado <strong><?= htmlspecialchars($_SESSION["usuario"])?></strong></p>
    <p>Time <?= time() ?></p>
    <div id="resultado"></div>
    <a href="?logout=1">Clique aqui para sair</a>
</main>
<script>
    const resultado = document.getElementById("resultado");

    if (document.cookie) {
        const cookies = document.cookie.split(';');
        let lista = "<ul>";
        cookies.forEach(c =>{
            lista += "<li>" + c + "</li>";
        });
        lista += "</ul>";
        resultado.innerHTML = lista;
    }else{
        resultado.innerText = "Nenhum cookie acessível via JS";
    }

</script>
</body>
</html>