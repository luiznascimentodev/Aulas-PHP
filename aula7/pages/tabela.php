<?php
require_once "./helpers/funcoes.php";
if(isset($_POST["nome"])){
    $chave = ["nome", "email", "senha", "confSenha", "dataNasc", "hora", "genero"];
    [$nome, $email, $senha, $confSenha, $dataNasc, $horaNasc, $genero] = array_map(fn($key) => validaInput($_POST[$key]), $chave);

    if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
        $alerta["email"] = "
        <div class=\"error-message\">
            E-mail Inválido
        </div>";
        $email = "";
    }
}

?>
<h1>Tabela</h1>
<?php if(isset($alerta)) foreach($alerta as $a) echo $a ?>

<table>
    <thead>
        <tr>
            <th>Nome</th>
            <th>E-mail</th>
            <th>Senha</th>
            <th>Data de Nascimento</th>
            <th>Idade</th>
            <th>Período</th>
            <th>Gênero</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><?= $nome ?? "" ?></td>
            <td><?= $email ?? "" ?></td>
            <td><?= $senha ?? "" ?></td>
            <td><?= $dataNasc ?? "" ?></td>
            <td><?= " " ?></td>
            <td><?= " " ?></td>
            <td><?= $genero ?? "" ?></td>
        </tr>
    </tbody>
</table>