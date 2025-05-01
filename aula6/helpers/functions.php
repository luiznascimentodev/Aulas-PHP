<?php
function validaInput($dados): string {
    $dados = trim($dados);
    $dados = htmlspecialchars($dados);
    return $dados;
}
?>