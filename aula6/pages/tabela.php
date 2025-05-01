<?php
require_once "./helpers/functions.php";

if (isset($_GET["nome"])) {
    $chaves = ["nome", "email", "idade", "cidade", "estado"];
    [$nome, $email, $idade, $cidade, $estado] = array_map(
        fn($key) => validaInput($_GET[$key]), $chaves);
    
}


?>
<h1>Tabela</h1>
<table>
    <tr>
        <th>Nome</th>
        <th>E-mail</th>
        <th>Idade</th>
        <th>Cidade</th>
        <th>Estado</th>
    </tr>
    <tr>
        <td><?= isset($nome)? $nome: "" ?></td>
        <td><?= isset($email)? $email: "" ?></td>
        <td><?= isset($idade)? $idade: "" ?></td>
        <td><?= isset($cidade)? $cidade: "" ?></td>
        <td><?= isset($estado)? $estado: "" ?></td>
    </tr>
    <tr>
        <td><?= $nome ?? "" ?></td>
        <td><?= $email ?? "" ?></td>
        <td><?= $idade ?? "" ?></td>
        <td><?= $cidade ?? "" ?></td>
        <td><?= $estado ?? "" ?></td>
    </tr>
</table>