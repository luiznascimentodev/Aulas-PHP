<?php

echo "Olá mundo!\n<br>" . PHP_EOL;
echo "Olá mundo!\n<br>";

$variavel_em_php = "Não tem tipo?";
$variavel_com_numero = 10;

print "Teste com print<br>";
// print retorna um booleano
$var = print "teste";

//TypeCast
$inteiro = (int)10;
$float = (float)10;
$texto = (string)10 . ".5.5 texto";
$booleano = (bool)1;

@$soma = $inteiro + $texto;
echo "<br>";
print_r($soma);
echo "<br>";

var_dump($variavel_em_php);
echo "<hr>";
var_dump($variavel_com_numero);
echo "<hr>";
var_dump($inteiro);
echo "<hr>";
var_dump($float);
echo "<hr>";
var_dump($texto);
echo "<hr>";
var_dump($booleano);
echo "<hr>";

$total = 4 + 4 * 2 - 2 / 2;

echo "O Resultado é  $total <br>";

// Vetores

$vetor = [1, 2, 3, 4, 5, 6, 7];

echo "<h1>Vetores</h1>";

$array_associativo = [
    "teste" => "Teste",
    "outroTeste" => "Outro Teste"
];

// Em php todas as posições abaixo são iguais
$array = [
    1 => "Primeiro Valor",
    "1" => "Segundo Valor",
    1.5 => "Terceiro Valor"
];

print_r($vetor);
echo "<hr><pre>";
var_dump($vetor);
echo "</pre><hr>";

// Estrturas de controle

$page = "value";

if(is_null($page)) {
    echo "<p>Variável Nula</p>";
} elseif($page == "value") {
    echo "<p> $page </p>";
} else {
    echo "<p> Nenhum dos casos </p>";
}

if($inteiro === "10") {
    echo "<p> Inteiro é igual a 10 </p>";
}
echo "<h2>Switch</h2>";
switch($page) {
    case "value":
        echo "<p> $page </p>";
    break;   
    case "teste":
        echo "<p> $page </p>";
    break;   
    case "outro":
        echo "<p> $page </p>";
    break;   
    default:
        echo "<p> Nenhum dos casos </p>";
    break;
}

echo "<h2>Match</h2>";

echo "<hr>" . match($page) {
    "value" => $page,
    "teste" => $page,
    "outro" => $page,
    default => "Nenhum dos casos",
} . "<hr>";

