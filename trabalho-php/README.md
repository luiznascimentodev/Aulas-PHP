# Gerenciador de Tarefas Colaborativo

## Autores

- Luiz Felippe Luna do Nascimento - RGM: 40338207
- Willian Cordeiro - RGM: 40333337
- Nathan Henrique - RGM: 39879763

## Descrição do Projeto

Este projeto é um sistema de gerenciamento de tarefas colaborativo desenvolvido em PHP. Ele permite que diferentes usuários gerenciem tarefas de forma eficiente, com funcionalidades como cadastro de usuários, login, gerenciamento de tarefas, comentários e histórico de alterações. O sistema foi desenvolvido para atender aos critérios estabelecidos no trabalho acadêmico.

## Funcionalidades Implementadas

1. **Cadastro de Usuários**:

   - Permite que novos usuários se cadastrem com nome, email e senha.
   - Os dados dos usuários são armazenados no arquivo `usuarios.json`.

2. **Login e Logout**:

   - Sistema de autenticação que permite o acesso às funcionalidades apenas após o login.
   - Logout disponível para encerrar a sessão do usuário.

3. **Adicionar Tarefa**:

   - Usuários podem criar tarefas com título, descrição, data limite e atribuí-las a si mesmos ou a outros membros da equipe.
   - As tarefas são armazenadas no arquivo `tarefas.json`.

4. **Listar Tarefas**:

   - Exibe todas as tarefas cadastradas no sistema.
   - Filtros disponíveis para exibir tarefas por responsável, status ou data limite.

5. **Atualizar Status**:

   - Permite que os usuários atualizem o status de suas tarefas (pendente, em andamento, concluída).
   - Restrições: Apenas o criador ou o responsável pela tarefa pode alterar seu status.

6. **Comentários**:

   - Usuários podem adicionar comentários às tarefas para discutir detalhes ou andamento.
   - Os comentários são armazenados no arquivo `comentarios.json`.

7. **Histórico de Alterações**:

   - Registra todas as alterações feitas nas tarefas, incluindo mudanças de status, edições e quem realizou a alteração.
   - O histórico é armazenado no arquivo `historico.json`.

8. **Cookies e Sessões**:

   - Sessões são usadas para autenticação e gerenciamento de usuários logados.
   - Cookies são utilizados para a funcionalidade "Lembre-me".

9. **Design**:

   - Layout visualmente agradável utilizando CSS no arquivo `assets/css/style.css`.

10. **HTML Semântico**:

    - Estrutura clara e organizada utilizando classes semânticas no HTML.

11. **Sem Uso de Bibliotecas Externas**:
    - Todo o sistema foi desenvolvido utilizando apenas HTML, CSS e PHP nativo.

## Estrutura do Projeto

```
comentarios.json          # Armazena os comentários das tarefas
historico.json            # Armazena o histórico de alterações das tarefas
index.php                 # Página inicial do sistema
login.php                 # Página de login
logout.php                # Script para logout
registro.php              # Página de registro de novos usuários
tarefas.json              # Armazena as tarefas criadas
tarefas.php               # Página principal para gerenciar tarefas
usuarios.json             # Armazena os dados dos usuários
assets/                   # Arquivos estáticos (CSS e JS)
  css/
    style.css             # Estilos personalizados do sistema
  js/
    scripts.js            # Scripts JavaScript personalizados
partials/                 # Componentes reutilizáveis
  footer.php              # Rodapé do sistema
  head.php                # Cabeçalho do sistema
```

## Requisitos do Sistema

- **Servidor Web**: Apache ou Nginx.
- **PHP**: Versão 7.4 ou superior.
- **Extensão JSON**: Habilitada no PHP.

## Configuração do Ambiente

1. Clone este repositório para o seu ambiente local:

   ```bash
   git clone <URL_DO_REPOSITORIO>
   ```

2. Certifique-se de que o servidor web está configurado para servir os arquivos deste projeto.

3. Verifique se o PHP está configurado corretamente no servidor.

4. Ajuste as permissões dos arquivos `tarefas.json`, `comentarios.json`, `historico.json` e `usuarios.json` para que o servidor web possa lê-los e gravá-los:

   ```bash
   chmod 666 tarefas.json comentarios.json historico.json usuarios.json
   ```

5. Acesse o sistema no navegador através do endereço configurado no servidor (exemplo: `http://localhost/trabalho-php`).

## Uso do Sistema

1. **Registro e Login**:

   - Acesse a página de registro (`registro.php`) para criar uma nova conta.
   - Faça login com as credenciais criadas.

2. **Gerenciamento de Tarefas**:

   - Na página principal (`tarefas.php`), adicione novas tarefas preenchendo o formulário.
   - Edite ou exclua tarefas existentes utilizando os botões disponíveis.
   - Atualize o status das tarefas diretamente na tabela.

3. **Comentários e Histórico**:

   - Adicione comentários às tarefas clicando no botão de comentários.
   - Visualize o histórico de alterações clicando no botão de histórico.

4. **Filtros**:
   - Utilize os filtros disponíveis para encontrar tarefas específicas.

## Conclusão

Este sistema foi desenvolvido para atender aos critérios do trabalho acadêmico, implementando todas as funcionalidades solicitadas de forma eficiente e organizada. Caso tenha dúvidas ou sugestões, estamos à disposição.
