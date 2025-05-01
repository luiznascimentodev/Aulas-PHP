<?php
function hello(){
    echo "<h1>Hello, World!</h1>";
}
hello();

meuNome("Pedro");
function meuNome($nome){
    echo"<p>Meu nome é <q>$nome</q></p>";
}

function soma($num1, $num2) : float{
    return $num1 + $num2;
}
$res = soma(10, 3);
echo "<pre>";
    var_dump($res);
echo"</pre>";

function apresentar($nome = "indefinido"){
    return "<p>Meu nome é $nome</p>";
}

echo apresentar();
echo apresentar("Pedro");

function teste() {
    $teste = 10;
    echo "<p>Teste existe aqui valor = $teste</p>";
}
//$teste = 1;
echo "<p>mas não existe aqui". (isset($teste)? 
" Variável Existe": " Variável NÃO Existe") ."</p>";

function fatorial(int $fat) : int{
    if ($fat > 1) {
        echo "Fatorial $fat <br>";
        return $fat * fatorial($fat - 1);
    }
    return 1;
}

echo "<p> Fatorial de 5 = " . fatorial(5) . "</p>";

function retornaVetor(){
    return [1,2,3];
}

[$um, $dois, $tres] = retornaVetor();
echo "<p> Vetor recebido $um, $dois, $tres</p>";

$funcao = function($valor){
    return "<p>Função Anonima valor = $valor</p>";
};

//$funcao = "";
echo is_callable($funcao)? $funcao("Teste"): "<p>Não é uma função</p>";

//Constantes
$pi = 3.5;
define("N_PI", 3.14);
const _NPI = 3;

function constantes(){
    global $pi;
    echo "<p>Variável comum $pi<br>";
    echo "Variável define " . N_PI . "<br>";
    echo "Variável const " . _NPI . "</p>";
    define("NOVA", "Nova constante");
}

constantes();
echo NOVA . "<br>";


?>