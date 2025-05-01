<h1>Formulário</h1>

<form method="post" action="?page=tab">
    <label for="nome">Nome:</label>
    <input type="text" name="nome" id="nome"><br><br>
    
    <label for="email">E-mail</label>
    <input type="email" name="email" id="email"><br><br>

    <label for="senha">Senha:</label>
    <input type="password" name="senha" id="senha"><br><br>

    <label for="confSenha">Confirmar Senha</label>
    <input type="password" name="confSenha" id="confSenha"><br><br>

    <label for="dataNascimento">Data de Nascimento</label>
    <input type="date" name="dataNasc" id="dataNascimento"><br><br>

    <label for="hora">Hora do Nascimento</label>
    <input type="time" name="hora" id="hora"><br><br>

    <label for="genero">Gênero</label>
    <select name="genero" id="genero">
        <option value="m">Masculino</option>
        <option value="f">Feminino</option>
    </select><br><br>
    <button type="submit">Enviar</button>
</form>