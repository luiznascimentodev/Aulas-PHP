<?php
    $titulo = "Revisão de formulário";
    $lang = "pt-br";
    $rodape = "
        <footer>
            <small>Rodapé</small>
        </footer>
    ";
?>
<!DOCTYPE html>
<html lang="<?= $lang ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $titulo ?></title>
</head>
<body>
    <nav>
        <a href="./index.html">Index</a> | 
        <a href="./contato.php">Contato</a>
    </nav>
    <main>
        <form>
            <label for="nome">nome</label>
            <input type="text" name="nome" id="nome" aria-label="nome"><br>
            <label for="email">e-mail</label>
            <input type="email" name="email" id="email"><br>
            <button type="submit">Enviar</button>
            <input type="submit" value="Enviar">
            <button type="reset">Limpar</button>
        </form>
    </main>
    <?= $rodape ?>
</body>
</html>