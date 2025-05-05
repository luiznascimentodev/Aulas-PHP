<?php

/*
|--------------------------------------------------------------------------
| Informações sobre os Autores
|--------------------------------------------------------------------------
| Este projeto foi desenvolvido com dedicação por três autores:
|
| - Luiz Felippe Luna do Nascimento - RGM: 40338207
| - Willian Cordeiro - RGM: 40333337
| - Nathan Henrique - RGM: 39879763
|
| Agradecemos por utilizar nosso sistema de gerenciamento de tarefas!
|--------------------------------------------------------------------------
*/

session_start();

// Configurações gerais para cookies e sessões
$cookie_lifetime = 60 * 60 * 24 * 30; // 30 dias
$cookie_path = '/';
$cookie_domain = '';
$cookie_secure = false; // Altere para true em produção com HTTPS
$cookie_httponly = true;

// Variáveis para controle de estado da página
$usuario_logado = false;
$nome_usuario = '';

// Função para carregar usuários
function carregarUsuarios()
{
    $usersFile = 'usuarios.json';
    if (file_exists($usersFile)) {
        $users = json_decode(file_get_contents($usersFile), true);
        if (!is_array($users)) {
            $users = [];
        }
        return $users;
    }
    return [];
}

// Verifica se o usuário está logado pela sessão
if (isset($_SESSION['usuario_id'])) {
    $usuario_logado = true;
    $nome_usuario = $_SESSION['usuario_nome'] ?? 'Usuário';
}
// Verifica se existe cookie "lembrar-me"
else if (isset($_COOKIE['remember_me_id']) && isset($_COOKIE['remember_me_token'])) {
    $user_id = $_COOKIE['remember_me_id'];
    $token = $_COOKIE['remember_me_token'];
    $usuarios = carregarUsuarios();

    foreach ($usuarios as $usuario) {
        if (
            isset($usuario['id']) && $usuario['id'] == $user_id &&
            isset($usuario['remember_token']) && $usuario['remember_token'] === $token
        ) {
            // Token válido, iniciar sessão
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_nome'] = $usuario['nome'];
            $_SESSION['usuario_email'] = $usuario['email'];
            $_SESSION['ultimo_acesso'] = time();

            // Regenerar ID de sessão por segurança
            session_regenerate_id(true);

            $usuario_logado = true;
            $nome_usuario = $usuario['nome'];
            break;
        }
    }

    // Se chegou aqui e não autenticou, o token é inválido - limpar cookies
    if (!$usuario_logado) {
        setcookie('remember_me_id', '', time() - 3600, $cookie_path, $cookie_domain, $cookie_secure, $cookie_httponly);
        setcookie('remember_me_token', '', time() - 3600, $cookie_path, $cookie_domain, $cookie_secure, $cookie_httponly);
    }
}
?>
<?php include 'partials/head.php'; ?>

<body>
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light bg-white rounded shadow-sm mb-4">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">Sistema de Tarefas</a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <?php if ($usuario_logado): ?>
                            <li class="nav-item">
                                <span class="nav-link text-uppercase">Olá, <?= htmlspecialchars($nome_usuario); ?>!</span>
                            </li>
                            <li class="nav-item">
                                <a href="tarefas.php" class="nav-link btn btn-sm btn-primary text-white ms-2">Minhas Tarefas</a>
                            </li>
                            <li class="nav-item">
                                <a href="logout.php" class="nav-link btn btn-sm btn-outline-secondary ms-2">Sair</a>
                            </li>
                        <?php else: ?>
                            <li class="nav-item">
                                <a href="login.php" class="nav-link btn btn-sm btn-primary text-white ms-2">Entrar</a>
                            </li>
                            <li class="nav-item">
                                <a href="registro.php" class="nav-link btn btn-sm btn-outline-secondary ms-2">Registrar</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="row">
            <div class="col-12">
                <header class="text-center mb-4">
                    <h1 class="display-4">Sistema de Gerenciamento de Tarefas</h1>
                </header>
            </div>
        </div>

        <?php if ($usuario_logado): ?>
            <section class="welcome-back p-4 rounded shadow-sm mb-4">
                <h2 class="mb-3">Bem-vindo de volta, <?php echo htmlspecialchars($nome_usuario); ?>!</h2>
                <p class="mb-4">Clique no botão abaixo para acessar suas tarefas e continuar de onde parou.</p>
                <a href="tarefas.php" class="btn btn-success px-4 py-2">
                    <i class="fas fa-tasks me-2"></i>Ir para Minhas Tarefas
                </a>
            </section>
        <?php else: ?>
            <section class="hero-section p-5 rounded shadow-sm mb-5">
                <h2 class="mb-3">Organize suas tarefas com facilidade</h2>
                <p class="lead mb-4">Um sistema simples e eficiente para gerenciar suas tarefas diárias, projetos e colaborações.</p>
                <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                    <a href="login.php" class="btn btn-primary btn-lg px-4 me-md-2">
                        <i class="fas fa-sign-in-alt me-2"></i>Acessar o Sistema
                    </a>
                    <a href="registro.php" class="btn btn-outline-secondary btn-lg px-4">
                        <i class="fas fa-user-plus me-2"></i>Criar uma Conta
                    </a>
                </div>
            </section>

            <div class="row row-cols-1 row-cols-md-3 g-4 mb-5">
                <div class="col">
                    <div class="card h-100 feature-card">
                        <div class="card-body text-center">
                            <div class="feature-icon">
                                <i class="fas fa-tasks"></i>
                            </div>
                            <h3 class="card-title h5 mb-3">Organize Tarefas</h3>
                            <p class="card-text">Crie, gerencie e organize suas tarefas facilmente. Defina prioridades e prazos para manter-se no caminho certo.</p>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card h-100 feature-card">
                        <div class="card-body text-center">
                            <div class="feature-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <h3 class="card-title h5 mb-3">Colaboração</h3>
                            <p class="card-text">Trabalhe em equipe atribuindo tarefas a outros usuários e acompanhando o progresso em tempo real.</p>
                        </div>
                    </div>
                </div>

                <div class="col">
                    <div class="card h-100 feature-card">
                        <div class="card-body text-center">
                            <div class="feature-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <h3 class="card-title h5 mb-3">Acompanhamento</h3>
                            <p class="card-text">Histórico completo de alterações e sistema de comentários para facilitar a comunicação sobre cada tarefa.</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php include 'partials/footer.php'; ?>
    </div>
</body>

</html>