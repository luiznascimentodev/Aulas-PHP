<?php
$page = $_GET["page"] ?? "home";
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aula POST</title>
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>
<?php
    require_once "./header.php";
    require_once "./menu.php";
    echo "<main>";
    require_once (match($page){
        "home" => "./pages/home.php",
        "form" => "./pages/formulario.php",
        "tab" => "./pages/tabela.php",
        default => "./pages/404.php",
    });
    echo "</main>";
    include_once "./footer.php";
?>
</body>
</html>