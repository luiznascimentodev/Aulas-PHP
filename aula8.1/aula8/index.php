<?php 
    session_start();
    session_regenerate_id(true);
    
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aula Sessões em PHP - Login</title>
    <link rel="stylesheet" href="./assets/style.css">
</head>
<body>
    <header>
        <h1>Sessões em PHP</h1>
    </header>

    <main>
        <form action="./admin" method="post">
            <h2>Login</h2>
            <input type="hidden" name="csrf" value="<?= $_SESSION['csrf_token'] ?>">

            <div class="form-group">
                <label for="login">Login:</label>
                <input type="text" name="login" id="login" required>
            </div>

            <div class="form-group">
                <label for="senha">Senha:</label>
                <input type="password" name="senha" id="senha" required>
            </div>

            <button type="submit">Entrar</button>
        </form>
    </main>

    <footer>
        <p>Rodapé</p>
    </footer>
</body>
</html>