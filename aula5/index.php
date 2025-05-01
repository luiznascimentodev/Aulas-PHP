<?php

function listar($valor) : string{
    return "<li> $valor </li>";
}

$numeros = "1, 2, 3, 4, 5, 6, 7";
$vetorNumeros = explode(", ", $numeros);

$resultado = array_map("listar", $vetorNumeros);

echo "<ul>". implode($resultado) ."</ul>";

echo "<pre>";
$string = "    Olá, mundo!    ";

echo $string;
echo "<p> Tamanho da String " . strlen($string) . "<br>";
echo " Tamanho da String multibyte " . mb_strlen($string) . "</p>";

echo "<p> Dividindo a String " . substr($string, 7). "<br>";
echo " Dividindo a String MB" . mb_substr($string, 7). "</p>";

// (String, Posição de início, Quantidade de Caracteres)
echo "<p> Dividindo a String " . substr($string, 7, 5). "<br>";
echo " Dividindo a String MB" . mb_substr($string, 7, 5). "</p>";

echo "<p> Removendo os espaços antes e depois " . trim($string) . "</p>";

echo "<p> Transformando em caixa baixa " . strtolower($string) . "<br>";
echo "Transformando em caixa baixa MB " . mb_strtolower($string) . "</p>";

echo "<p> Transformando em caixa alta " . strtoupper($string) . "<br>";
echo " Transformando em caixa alta MB " . mb_strtoupper($string) . "</p>";

echo "<p> Substituindo parte do texto " . str_replace("mundo", "planeta", $string) . "</p>";

echo "<p> Retornando a primeira ocorrencia " . strpos($string, " ") . "<br>";
echo " Retornando a ultima ocorrencia " . strrpos($string, " ") . "</p>";

echo "<p> Retornando a primeira ocorrencia MB " . mb_strpos($string, " ") . "<br>";
echo " Retornando a ultima ocorrencia MB" . mb_strrpos($string, " ") . "</p>";

echo "<p> Verificando se contem a string \"olá\" " . str_contains($string, "olá") . "<br>";
echo " Verificando se contem a string \"olá\" " . str_contains(mb_strtolower($string), "olá") . "</p>";

echo "<p> Verificar se o texto começa com \"olá\" " . str_starts_with(trim(mb_strtolower($string)), "olá") . "<br>";
echo "<p> Verificar se o texto começa com \"olá\" " . str_ends_with(trim(mb_strtolower($string)), "!") . "<br>";


echo "<hr>";

$email = "j%! ão@teste.com";
echo filter_var($email, FILTER_VALIDATE_EMAIL)? "Email é válido": "Email é inválido";
$emailLimpo = filter_var($email, FILTER_SANITIZE_EMAIL);
echo "<p>Email limpo $emailLimpo </p>";

//$email ="<script>alert('teste')</script>";

echo $email;

$cpf = "123.456.789-10";

$cpf = str_replace([".", "-"], "", $cpf);
echo "<p>preenchendo String a direita " . str_pad(substr($cpf, 0,3), 11, "*", STR_PAD_RIGHT) . "</p>";
echo "<p>preenchendo String a esquerda " . str_pad(substr($cpf, 9,5), 11, "*", STR_PAD_LEFT) . "</p>";
echo "<p>preenchendo String a ambos os lados " . str_pad(substr($cpf, 5,4), 11, "*", STR_PAD_BOTH) . "</p>";

echo "embaralhar dados " . str_shuffle($cpf);

$cpfVetor = mb_str_split($cpf, 3);

echo "<br> CPF formatado " . $cpfVetor[0] . "." . $cpfVetor[1] . "." . $cpfVetor[2] . "-" . $cpfVetor[3];

echo vsprintf("<p>vsprintf() = %d.%d.%d-%d </p>", mb_str_split($cpf, 3));
[$cpf1, $cpf2, $cpf3, $cpf4] = mb_str_split($cpf, 3);

echo sprintf("<p>sprintf() = %d.%d.%d-%d</p>", $cpf1, $cpf2, $cpf3, $cpf4);

$float = 2999.99;

echo "R$ " . number_format($float, 2, ",", ".") . "<br>";

echo "</pre>";
?>

<br><br><br><br><br><br><br><br>