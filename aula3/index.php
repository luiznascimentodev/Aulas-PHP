<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estruturas de Repetição</title>
</head>
<body>
    <h2>Estruturas de Repetição</h2>
    <?php
    /**
     * @deprecated 
     */

    $i = 0;
    while ($i <= 10) {
        echo "$i | ";
        $i++;
    }
    echo "<br>";
    do {
        $i--;
        echo "$i | ";
    } while ($i > 0);

    echo "<hr>";
    
    for (var_dump($i), $j=0; $i < 10 && $j > -20; ) { 
        echo "i = $i | j = $j | ";
        $i++;
        $j--;
    }
    echo "<hr>";

    $vetor = ["Abacaxi", "Banana", "Laranja", "Goiaba"];

    echo "<pre>";
    var_dump($vetor);
    echo "</pre>";

    echo $vetor[2] . "<br>";

    foreach($vetor as $valor){
        echo "O valor é $valor.<br>";
    }

    $arrayAssoc = array("nome" => "João", "idade" => "30", "ddd" => "41");
    $arrayAssoc["telefone"] = "3333-3333";

    foreach($arrayAssoc as $arr){
        echo "Valor: $arr ";
    }
    echo "<br>";
    echo $arrayAssoc["nome"] . "<br>";

    $clientes = array(
        1 => array(
            "nome" => "Pedro",
            "idade" => "25",
            "ddd" => "41",
            "telefone" => "3333-22222"
        ),
        2 => array(
            "nome" => "José",
            "idade" => "22",
            "ddd" => "42",
            "telefone" => "2222-22222"
        ),
    );

    echo "<hr>";
        foreach($clientes as $cliente){
            foreach($cliente as $chave => $dados){
            ?>
            <p>
                <strong><?= $chave ?> </strong> => <em><?= $dados ?></em>
            </p>
            <?php
            }
        }
    echo "<hr>";

    foreach($clientes as $key => $cli){
    ?>
        <h3>Cliente <?=$key?></h3>
        <strong>Nome:</strong> <?= $cli["nome"]?> <br>
        <strong>Idade:</strong> <?= $cli["idade"]?> <br>
        <strong>DDD:</strong> <?= $cli["ddd"]?> <br>
        <strong>Telefone:</strong> <?= $cli["telefone"]?> <br>
        <hr>
    <?php
    }

for($i = 0; $i < 10; print ++$i . " ");

    ?>
</body>
</html>