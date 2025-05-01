<?php
//$page = (isset($_GET["p"]))? $_GET["p"] : "home";
$page = $_GET["p"] ?? "home";

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Aula 6</title>
</head>
<body>
<?php
    //Require da Fatal Error e Include Warning
    //require("./head.php");
    include_once("./header.php");
   /* echo "<pre>";
    var_dump($_GET);
    echo "</pre>";*/
?>
<main>
<?php
    require_once(
        match ($page) {
            'home' => "./pages/home.php",
            'tab' => "./pages/tabela.php",
            'form' => "./pages/formulario.php",
             default => "./pages/404.php",
        }
    );
?>
</main>
<?php
    //include("./footer.php");
    require_once("./footer.php");
?>
</body>
</html>