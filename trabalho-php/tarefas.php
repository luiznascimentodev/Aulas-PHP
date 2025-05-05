<?php
session_start();

// Configurações gerais para sessões
$cookie_lifetime = 60 * 60 * 24 * 30; // 30 dias
$cookie_path = '/';
$cookie_domain = '';
$cookie_secure = false; // Altere para true em produção com HTTPS
$cookie_httponly = true;

// Definindo timeout da sessão (2 horas)
ini_set('session.gc_maxlifetime', 7200);
session_set_cookie_params(7200, $cookie_path, $cookie_domain, $cookie_secure, $cookie_httponly);

// Verifica timeout de inatividade (30 minutos)
$timeout_duration = 30 * 60;
if (isset($_SESSION['ultimo_acesso']) && (time() - $_SESSION['ultimo_acesso']) > $timeout_duration) {
    session_unset();
    session_destroy();
    header('Location: login.php?timeout=1');
    exit;
}

$_SESSION['ultimo_acesso'] = time();

if (!isset($_SESSION['ultima_regeneracao']) || (time() - $_SESSION['ultima_regeneracao']) > 1800) {
    session_regenerate_id(true);
    $_SESSION['ultima_regeneracao'] = time();
}

if (!isset($_SESSION['usuario_id'])) {
    if (isset($_COOKIE['remember_me_id'], $_COOKIE['remember_me_token'])) {
        $user_id = $_COOKIE['remember_me_id'];
        $token = $_COOKIE['remember_me_token'];
        $usuarios = carregarUsuarios();

        $autenticado = false;
        foreach ($usuarios as $usuario) {
            if (
                isset($usuario['id'], $usuario['remember_token']) &&
                $usuario['id'] == $user_id &&
                $usuario['remember_token'] === $token
            ) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['ultimo_acesso'] = time();

                session_regenerate_id(true);
                $_SESSION['ultima_regeneracao'] = time();

                $autenticado = true;
                break;
            }
        }

        if (!$autenticado) {
            setcookie('remember_me_id', '', time() - 3600, $cookie_path, $cookie_domain, $cookie_secure, $cookie_httponly);
            setcookie('remember_me_token', '', time() - 3600, $cookie_path, $cookie_domain, $cookie_secure, $cookie_httponly);
            header('Location: login.php');
            exit;
        }
    } else {
        header('Location: login.php');
        exit;
    }
}

// Definir o arquivo de tarefas
define('TAREFAS_FILE', 'tarefas.json');
define('COMENTARIOS_FILE', 'comentarios.json');
define('HISTORICO_FILE', 'historico.json');

// Funções para gerenciar tarefas
function carregarTarefas()
{
    if (file_exists(TAREFAS_FILE)) {
        $tarefasJson = file_get_contents(TAREFAS_FILE);
        $tarefas = json_decode($tarefasJson, true);
        return is_array($tarefas) ? $tarefas : [];
    }
    return [];
}

function salvarTarefas($tarefas)
{
    file_put_contents(TAREFAS_FILE, json_encode($tarefas, JSON_PRETTY_PRINT));
}

// Funções para gerenciar comentários
function carregarComentarios()
{
    if (file_exists(COMENTARIOS_FILE)) {
        $comentariosJson = file_get_contents(COMENTARIOS_FILE);
        $comentarios = json_decode($comentariosJson, true);
        return is_array($comentarios) ? $comentarios : [];
    }
    return [];
}

function salvarComentarios($comentarios)
{
    file_put_contents(COMENTARIOS_FILE, json_encode($comentarios, JSON_PRETTY_PRINT));
}

// Funções para gerenciar histórico
function carregarHistorico()
{
    if (file_exists(HISTORICO_FILE)) {
        $historicoJson = file_get_contents(HISTORICO_FILE);
        $historico = json_decode($historicoJson, true);
        return is_array($historico) ? $historico : [];
    }
    return [];
}

function salvarHistorico($historico)
{
    file_put_contents(HISTORICO_FILE, json_encode($historico, JSON_PRETTY_PRINT));
}

// Função para registrar uma alteração no histórico
function registrarAlteracao($tarefa_id, $tipo_alteracao, $valor_antigo, $valor_novo, $usuario_id, $usuario_nome)
{
    $historico = carregarHistorico();

    $alteracao = [
        'id' => uniqid(),
        'tarefa_id' => $tarefa_id,
        'tipo' => $tipo_alteracao,
        'valor_antigo' => $valor_antigo,
        'valor_novo' => $valor_novo,
        'usuario_id' => $usuario_id,
        'usuario_nome' => $usuario_nome,
        'data' => date('Y-m-d H:i:s')
    ];

    $historico[] = $alteracao;
    salvarHistorico($historico);

    return $alteracao;
}

// Carregar usuários do sistema
function carregarUsuarios()
{
    $usuariosJson = file_get_contents('usuarios.json');
    return json_decode($usuariosJson, true) ?: [];
}

$usuarios = carregarUsuarios();
$usuario_valido = false;

foreach ($usuarios as $usuario) {
    if ($usuario['id'] == $_SESSION['usuario_id']) {
        $usuario_valido = true;
        $_SESSION['usuario_nome'] = $usuario['nome'];
        break;
    }
}

if (!$usuario_valido) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Carregar todas as tarefas do arquivo
$todas_tarefas = carregarTarefas();

// Atualizar tarefas antigas para usar o novo formato de status
foreach ($todas_tarefas as $key => $tarefa) {
    if (!isset($tarefa['status'])) {
        // Converter do formato antigo para o novo
        $todas_tarefas[$key]['status'] = $tarefa['concluida'] ? 'concluida' : 'pendente';
    }
}
salvarTarefas($todas_tarefas); // Salvar as atualizações

// Processa o formulário de adição de tarefa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar_tarefa'])) {
    $titulo = trim($_POST['titulo']);
    $descricao = trim($_POST['descricao']);
    $prioridade = $_POST['prioridade'];
    $data_limite = !empty($_POST['data_limite']) ? $_POST['data_limite'] : null;
    $responsavel_id = isset($_POST['responsavel']) ? $_POST['responsavel'] : $_SESSION['usuario_id'];

    // Encontrar nome do responsável
    $responsavel_nome = '';
    foreach ($usuarios as $usuario) {
        if ($usuario['id'] == $responsavel_id) {
            $responsavel_nome = $usuario['nome'];
            break;
        }
    }

    if (!empty($titulo)) {
        // Cria nova tarefa com ID único
        $nova_tarefa = [
            'id' => uniqid(),
            'titulo' => $titulo,
            'descricao' => $descricao,
            'prioridade' => $prioridade,
            'data_criacao' => date('Y-m-d H:i:s'),
            'data_limite' => $data_limite,
            'responsavel_id' => $responsavel_id,
            'responsavel_nome' => $responsavel_nome,
            'criador_id' => $_SESSION['usuario_id'],
            'criador_nome' => $_SESSION['usuario_nome'],
            'status' => 'pendente',
            'concluida' => false // Mantém para compatibilidade
        ];

        // Adiciona a tarefa ao array
        $todas_tarefas[] = $nova_tarefa;

        // Salva no arquivo JSON
        salvarTarefas($todas_tarefas);

        // Mensagem de sucesso
        $mensagem_sucesso = "Tarefa adicionada com sucesso!";
    } else {
        $mensagem_erro = "Por favor, informe um título para a tarefa.";
    }
}

// Processar edição de tarefa
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar_tarefa'])) {
    $tarefa_id = $_POST['tarefa_id'];
    $novo_titulo = trim($_POST['titulo']);
    $nova_descricao = trim($_POST['descricao']);
    $nova_prioridade = $_POST['prioridade'];
    $nova_data_limite = !empty($_POST['data_limite']) ? $_POST['data_limite'] : null;
    $novo_responsavel_id = $_POST['responsavel'];

    // Encontrar nome do responsável
    $responsavel_nome = '';
    foreach ($usuarios as $usuario) {
        if ($usuario['id'] == $novo_responsavel_id) {
            $responsavel_nome = $usuario['nome'];
            break;
        }
    }

    $tarefa_alterada = false;

    foreach ($todas_tarefas as $key => $tarefa) {
        if ($tarefa['id'] === $tarefa_id) {
            // Verificar permissões (apenas criador ou responsável pode editar)
            if ($tarefa['criador_id'] == $_SESSION['usuario_id'] || $tarefa['responsavel_id'] == $_SESSION['usuario_id']) {

                // Registrar alterações no histórico
                if ($tarefa['titulo'] !== $novo_titulo) {
                    registrarAlteracao(
                        $tarefa_id,
                        'titulo',
                        $tarefa['titulo'],
                        $novo_titulo,
                        $_SESSION['usuario_id'],
                        $_SESSION['usuario_nome']
                    );
                }

                if ($tarefa['descricao'] !== $nova_descricao) {
                    registrarAlteracao(
                        $tarefa_id,
                        'descricao',
                        $tarefa['descricao'],
                        $nova_descricao,
                        $_SESSION['usuario_id'],
                        $_SESSION['usuario_nome']
                    );
                }

                if ($tarefa['prioridade'] !== $nova_prioridade) {
                    registrarAlteracao(
                        $tarefa_id,
                        'prioridade',
                        $tarefa['prioridade'],
                        $nova_prioridade,
                        $_SESSION['usuario_id'],
                        $_SESSION['usuario_nome']
                    );
                }

                if ($tarefa['data_limite'] !== $nova_data_limite) {
                    registrarAlteracao(
                        $tarefa_id,
                        'data_limite',
                        $tarefa['data_limite'],
                        $nova_data_limite,
                        $_SESSION['usuario_id'],
                        $_SESSION['usuario_nome']
                    );
                }

                if ($tarefa['responsavel_id'] !== $novo_responsavel_id) {
                    registrarAlteracao(
                        $tarefa_id,
                        'responsavel',
                        $tarefa['responsavel_nome'],
                        $responsavel_nome,
                        $_SESSION['usuario_id'],
                        $_SESSION['usuario_nome']
                    );
                }

                // Atualizar os valores da tarefa
                $todas_tarefas[$key]['titulo'] = $novo_titulo;
                $todas_tarefas[$key]['descricao'] = $nova_descricao;
                $todas_tarefas[$key]['prioridade'] = $nova_prioridade;
                $todas_tarefas[$key]['data_limite'] = $nova_data_limite;
                $todas_tarefas[$key]['responsavel_id'] = $novo_responsavel_id;
                $todas_tarefas[$key]['responsavel_nome'] = $responsavel_nome;

                $tarefa_alterada = true;
            } else {
                $mensagem_erro = "Você não tem permissão para editar esta tarefa.";
            }
            break;
        }
    }

    if ($tarefa_alterada) {
        // Salvar as alterações no arquivo JSON
        salvarTarefas($todas_tarefas);
        $mensagem_sucesso = "Tarefa atualizada com sucesso!";

        // Redirecionar para evitar reenvio do formulário
        header('Location: tarefas.php');
        exit;
    }
}

// Função para atualizar status da tarefa
if (isset($_POST['atualizar_status']) && !empty($_POST['tarefa_id']) && !empty($_POST['novo_status'])) {
    $tarefa_id = $_POST['tarefa_id'];
    $novo_status = $_POST['novo_status'];
    $tarefa_alterada = false;

    foreach ($todas_tarefas as $key => $tarefa) {
        if ($tarefa['id'] === $tarefa_id) {
            $status_antigo = $tarefa['status'];
            $todas_tarefas[$key]['status'] = $novo_status;

            // Atualiza o campo concluida para compatibilidade
            $todas_tarefas[$key]['concluida'] = ($novo_status === 'concluida');

            // Registrar data e usuário que alterou o status
            if ($novo_status === 'concluida') {
                $todas_tarefas[$key]['data_conclusao'] = date('Y-m-d H:i:s');
                $todas_tarefas[$key]['concluido_por'] = $_SESSION['usuario_id'];
            }

            // Registrar alteração no histórico
            registrarAlteracao(
                $tarefa_id,
                'status',
                $status_antigo,
                $novo_status,
                $_SESSION['usuario_id'],
                $_SESSION['usuario_nome']
            );

            $tarefa_alterada = true;
            break;
        }
    }

    if ($tarefa_alterada) {
        // Salva as alterações no arquivo JSON
        salvarTarefas($todas_tarefas);
    }

    // Redireciona para evitar reenvio do formulário
    header('Location: tarefas.php');
    exit;
}

// Função para marcar tarefa como concluída (compatibilidade)
if (isset($_GET['concluir']) && !empty($todas_tarefas)) {
    $tarefa_id = $_GET['concluir'];
    $tarefa_alterada = false;

    foreach ($todas_tarefas as $key => $tarefa) {
        if ($tarefa['id'] === $tarefa_id) {
            $status_antigo = $tarefa['status'];
            $novo_status = $status_antigo === 'concluida' ? 'pendente' : 'concluida';
            $todas_tarefas[$key]['status'] = $novo_status;
            $todas_tarefas[$key]['concluida'] = ($novo_status === 'concluida');

            if ($novo_status === 'concluida') {
                $todas_tarefas[$key]['data_conclusao'] = date('Y-m-d H:i:s');
                $todas_tarefas[$key]['concluido_por'] = $_SESSION['usuario_id'];
            }

            // Registrar alteração no histórico
            registrarAlteracao(
                $tarefa_id,
                'status',
                $status_antigo,
                $novo_status,
                $_SESSION['usuario_id'],
                $_SESSION['usuario_nome']
            );

            $tarefa_alterada = true;
            break;
        }
    }

    if ($tarefa_alterada) {
        // Salva as alterações no arquivo JSON
        salvarTarefas($todas_tarefas);
    }

    // Redireciona para evitar reenvio do formulário
    header('Location: tarefas.php');
    exit;
}

// Processar adição de comentários
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['adicionar_comentario'])) {
    $tarefa_id = $_POST['tarefa_id'];
    $comentario_texto = trim($_POST['comentario_texto']);

    if (!empty($comentario_texto)) {
        // Carregar comentários existentes
        $comentarios = carregarComentarios();

        // Criar novo comentário
        $novo_comentario = [
            'id' => uniqid(),
            'tarefa_id' => $tarefa_id,
            'usuario_id' => $_SESSION['usuario_id'],
            'usuario_nome' => $_SESSION['usuario_nome'],
            'texto' => $comentario_texto,
            'data_criacao' => date('Y-m-d H:i:s')
        ];

        // Adicionar ao array de comentários
        $comentarios[] = $novo_comentario;

        // Salvar no arquivo JSON
        salvarComentarios($comentarios);

        // Redirecionar para evitar reenvio do formulário
        header('Location: tarefas.php');
        exit;
    }
}

// Excluir comentário
if (isset($_GET['excluir_comentario'])) {
    $comentario_id = $_GET['excluir_comentario'];
    $comentario_excluido = false;

    // Carregar comentários
    $comentarios = carregarComentarios();

    foreach ($comentarios as $key => $comentario) {
        if ($comentario['id'] === $comentario_id) {
            // Verificar permissão (apenas o autor do comentário pode excluí-lo)
            if ($comentario['usuario_id'] == $_SESSION['usuario_id']) {
                unset($comentarios[$key]);
                $comentario_excluido = true;
            } else {
                $mensagem_erro = "Você não tem permissão para excluir este comentário.";
            }
            break;
        }
    }

    if ($comentario_excluido) {
        // Reindexar array e salvar
        $comentarios = array_values($comentarios);
        salvarComentarios($comentarios);
        $mensagem_sucesso = "Comentário excluído com sucesso!";
    }

    // Redirecionar para evitar problemas
    if (!isset($mensagem_erro)) {
        header('Location: tarefas.php');
        exit;
    }
}

// Carregar comentários para exibição
$comentarios = carregarComentarios();

// Carregar histórico para exibição
$historico = carregarHistorico();

// Filtrar tarefas
$tarefas_filtradas = $todas_tarefas;

// Aplicar filtros se existirem
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['filtrar'])) {
    $filtro_responsavel = isset($_GET['filtro_responsavel']) ? $_GET['filtro_responsavel'] : '';
    $filtro_status = isset($_GET['filtro_status']) ? $_GET['filtro_status'] : '';
    $filtro_data_limite = isset($_GET['filtro_data_limite']) ? $_GET['filtro_data_limite'] : '';

    if (!empty($filtro_responsavel) || !empty($filtro_status) || !empty($filtro_data_limite)) {
        $tarefas_filtradas = array_filter($todas_tarefas, function ($tarefa) use ($filtro_responsavel, $filtro_status, $filtro_data_limite) {
            $match = true;

            // Filtro por responsável
            if (!empty($filtro_responsavel) && $tarefa['responsavel_id'] != $filtro_responsavel) {
                $match = false;
            }

            // Filtro por status
            if (!empty($filtro_status)) {
                $status_tarefa = isset($tarefa['status']) ? $tarefa['status'] : ($tarefa['concluida'] ? 'concluida' : 'pendente');
                if ($status_tarefa !== $filtro_status) {
                    $match = false;
                }
            }

            // Filtro por data limite
            if (!empty($filtro_data_limite) && !empty($tarefa['data_limite'])) {
                if (strtotime($tarefa['data_limite']) > strtotime($filtro_data_limite)) {
                    $match = false;
                }
            }

            return $match;
        });
    }
}

// Excluir tarefa
if (isset($_GET['excluir'])) {
    $tarefa_id = $_GET['excluir'];
    $tarefa_excluida = false;

    foreach ($todas_tarefas as $key => $tarefa) {
        if ($tarefa['id'] === $tarefa_id) {
            // Verificar permissão (apenas criador ou responsável pode excluir)
            if ($tarefa['criador_id'] == $_SESSION['usuario_id'] || $tarefa['responsavel_id'] == $_SESSION['usuario_id']) {
                unset($todas_tarefas[$key]);
                $tarefa_excluida = true;
            } else {
                $mensagem_erro = "Você não tem permissão para excluir esta tarefa.";
            }
            break;
        }
    }

    if ($tarefa_excluida) {
        // Reindexar array e salvar
        $todas_tarefas = array_values($todas_tarefas);
        salvarTarefas($todas_tarefas);
        $mensagem_sucesso = "Tarefa excluída com sucesso!";
    }

    // Redireciona para evitar problemas
    if (!isset($mensagem_erro)) {
        header('Location: tarefas.php');
        exit;
    }
}
?>

<?php include 'partials/head.php'; ?>

<body>
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light bg-white rounded shadow-sm mb-4">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php">
                    <i class="fas fa-tasks me-2"></i>Sistema de Tarefas
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <span class="nav-link text-white">Olá, <?php echo htmlspecialchars($_SESSION['usuario_nome'] ?? 'Usuário'); ?>!</span>
                        </li>
                        <li class="nav-item">
                            <a href="index.php" class="nav-link btn btn-sm btn-outline-secondary ms-2">
                                <i class="fas fa-home me-1"></i>Início
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="login.php?logout=1" class="nav-link btn btn-sm btn-outline-danger ms-2">
                                <i class="fas fa-sign-out-alt me-1"></i>Sair
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="row">
            <div class="col-12">
                <header class="page-header mb-4">
                    <h1 class="display-5">Gerenciador de Tarefas</h1>
                </header>
            </div>
        </div>

        <?php if (isset($mensagem_sucesso)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo $mensagem_sucesso; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($mensagem_erro)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $mensagem_erro; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-plus-circle me-2"></i>Adicionar Nova Tarefa
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="titulo" class="form-label">Título</label>
                                <input type="text" class="form-control" id="titulo" name="titulo" required>
                                <div class="invalid-feedback">Por favor, informe um título.</div>
                            </div>

                            <div class="mb-3">
                                <label for="descricao" class="form-label">Descrição</label>
                                <textarea class="form-control" id="descricao" name="descricao" rows="3"></textarea>
                            </div>

                            <div class="mb-3">
                                <label for="prioridade" class="form-label">Prioridade</label>
                                <select class="form-select" id="prioridade" name="prioridade">
                                    <option value="baixa">Baixa</option>
                                    <option value="media" selected>Média</option>
                                    <option value="alta">Alta</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="data_limite" class="form-label">Data Limite</label>
                                <input type="date" class="form-control" id="data_limite" name="data_limite">
                            </div>

                            <div class="mb-3">
                                <label for="responsavel" class="form-label">Responsável</label>
                                <select class="form-select" id="responsavel" name="responsavel">
                                    <?php foreach ($usuarios as $usuario): ?>
                                        <option value="<?php echo $usuario['id']; ?>" <?php echo ($usuario['id'] == $_SESSION['usuario_id']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($usuario['nome']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="d-grid">
                                <button type="submit" name="adicionar_tarefa" class="btn btn-primary">
                                    <i class="fas fa-plus-circle me-2"></i>Adicionar Tarefa
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-filter me-2"></i>Filtrar Tarefas
                    </div>
                    <div class="card-body">
                        <form method="GET" action="" class="row g-3">
                            <div class="col-md-4">
                                <label for="filtro_responsavel" class="form-label">Responsável</label>
                                <select class="form-select" id="filtro_responsavel" name="filtro_responsavel">
                                    <option value="">Todos</option>
                                    <?php foreach ($usuarios as $usuario): ?>
                                        <option value="<?php echo $usuario['id']; ?>" <?php echo isset($_GET['filtro_responsavel']) && $_GET['filtro_responsavel'] == $usuario['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($usuario['nome']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="filtro_status" class="form-label">Status</label>
                                <select class="form-select" id="filtro_status" name="filtro_status">
                                    <option value="">Todos</option>
                                    <option value="pendente" <?php echo isset($_GET['filtro_status']) && $_GET['filtro_status'] == 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                                    <option value="andamento" <?php echo isset($_GET['filtro_status']) && $_GET['filtro_status'] == 'andamento' ? 'selected' : ''; ?>>Em Andamento</option>
                                    <option value="concluida" <?php echo isset($_GET['filtro_status']) && $_GET['filtro_status'] == 'concluida' ? 'selected' : ''; ?>>Concluída</option>
                                </select>
                            </div>

                            <div class="col-md-4">
                                <label for="filtro_data_limite" class="form-label">Data Limite até</label>
                                <input type="date" class="form-control" id="filtro_data_limite" name="filtro_data_limite" value="<?php echo isset($_GET['filtro_data_limite']) ? $_GET['filtro_data_limite'] : ''; ?>">
                            </div>

                            <div class="col-12">
                                <div class="d-flex justify-content-end">
                                    <button type="submit" name="filtrar" class="btn btn-primary me-2">
                                        <i class="fas fa-search me-2"></i>Filtrar
                                    </button>
                                    <a href="tarefas.php" class="btn btn-outline-secondary">
                                        <i class="fas fa-broom me-2"></i>Limpar Filtros
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div class="card shadow-sm mb-5">
            <div class="card-header bg-primary text-white">
                <i class="fas fa-list-check me-2"></i>Minhas Tarefas
            </div>
            <div class="card-body p-0">
                <?php if (empty($tarefas_filtradas)): ?>
                    <div class="p-4 text-center">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <p class="mb-0">Nenhuma tarefa encontrada com os filtros aplicados.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Descrição</th>
                                    <th>Prioridade</th>
                                    <th>Responsável</th>
                                    <th>Data Limite</th>
                                    <th>Status</th>
                                    <th width="280">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tarefas_filtradas as $tarefa):
                                    $status = isset($tarefa['status']) ? $tarefa['status'] : ($tarefa['concluida'] ? 'concluida' : 'pendente');
                                    $tarefa_id = $tarefa['id'];

                                    // Filtrar comentários para esta tarefa
                                    $comentarios_tarefa = array_filter($comentarios, function ($comentario) use ($tarefa_id) {
                                        return $comentario['tarefa_id'] === $tarefa_id;
                                    });

                                    // Ordenar comentários por data (mais recentes primeiro)
                                    usort($comentarios_tarefa, function ($a, $b) {
                                        return strtotime($b['data_criacao']) - strtotime($a['data_criacao']);
                                    });

                                    $num_comentarios = count($comentarios_tarefa);
                                ?>
                                    <tr class="<?php echo $status; ?>">
                                        <td><?php echo htmlspecialchars($tarefa['titulo']); ?></td>
                                        <td>
                                            <?php
                                            if (strlen($tarefa['descricao']) > 50) {
                                                echo htmlspecialchars(substr($tarefa['descricao'], 0, 50)) . '...';
                                            } else {
                                                echo htmlspecialchars($tarefa['descricao']);
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $prioridade_classes = [
                                                'baixa' => 'success',
                                                'media' => 'warning',
                                                'alta' => 'danger'
                                            ];
                                            $classe = $prioridade_classes[$tarefa['prioridade']] ?? 'secondary';
                                            ?>
                                            <span class="badge bg-<?php echo $classe; ?> text-white">
                                                <?php echo ucfirst($tarefa['prioridade']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info text-dark">
                                                <?php echo htmlspecialchars($tarefa['responsavel_nome'] ?? 'Não atribuído'); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($tarefa['data_limite'])): ?>
                                                <?php
                                                $data_limite = new DateTime($tarefa['data_limite']);
                                                $hoje = new DateTime();
                                                $classe = ($data_limite < $hoje && $status !== 'concluida') ? 'text-danger fw-bold' : '';
                                                echo '<span class="' . $classe . '">' . $data_limite->format('d/m/Y') . '</span>';
                                                ?>
                                            <?php else: ?>
                                                <span class="text-muted fst-italic">Sem data</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $status_badges = [
                                                'pendente' => 'warning',
                                                'andamento' => 'primary',
                                                'concluida' => 'success'
                                            ];
                                            $badge_class = $status_badges[$status] ?? 'secondary';

                                            $status_labels = [
                                                'pendente' => 'Pendente',
                                                'andamento' => 'Em Andamento',
                                                'concluida' => 'Concluída'
                                            ];
                                            $status_label = $status_labels[$status] ?? 'Desconhecido';
                                            ?>
                                            <span class="badge bg-<?php echo $badge_class; ?>">
                                                <?php echo $status_label; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <form method="POST" action="" class="me-1">
                                                    <input type="hidden" name="tarefa_id" value="<?php echo $tarefa['id']; ?>">
                                                    <select name="novo_status" class="form-select form-select-sm" onchange="this.form.submit()" style="max-width: 130px;">
                                                        <option value="pendente" <?php echo $status === 'pendente' ? 'selected' : ''; ?>>Pendente</option>
                                                        <option value="andamento" <?php echo $status === 'andamento' ? 'selected' : ''; ?>>Em Andamento</option>
                                                        <option value="concluida" <?php echo $status === 'concluida' ? 'selected' : ''; ?>>Concluída</option>
                                                    </select>
                                                    <input type="hidden" name="atualizar_status" value="1">
                                                </form>

                                                <button class="btn btn-sm btn-outline-info ms-1" onclick="toggleComentarios('<?php echo $tarefa['id']; ?>')">
                                                    <i class="fas fa-comments me-1"></i> <?php echo $num_comentarios; ?>
                                                </button>

                                                <button class="btn btn-sm btn-outline-secondary ms-1" onclick="toggleHistorico('<?php echo $tarefa['id']; ?>')">
                                                    <i class="fas fa-history"></i>
                                                </button>

                                                <?php if ($tarefa['criador_id'] == $_SESSION['usuario_id'] || $tarefa['responsavel_id'] == $_SESSION['usuario_id']): ?>
                                                    <a href="tarefas.php?excluir=<?php echo $tarefa['id']; ?>" class="btn btn-sm btn-outline-danger ms-1" onclick="return confirm('Tem certeza que deseja excluir esta tarefa?')">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                    <!-- Linha para comentários (oculta por padrão) -->
                                    <tr id="comentarios-<?php echo $tarefa['id']; ?>" class="linha-comentarios" style="display: none;">
                                        <td colspan="7">
                                            <div class="comentarios-container card mb-0">
                                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                                    <h5 class="card-title mb-0 fs-6">
                                                        <i class="fas fa-comments me-2"></i>Comentários
                                                    </h5>
                                                    <button type="button" class="btn-close" aria-label="Fechar" onclick="toggleComentarios('<?php echo $tarefa['id']; ?>')"></button>
                                                </div>
                                                <div class="card-body">
                                                    <!-- Lista de comentários existentes -->
                                                    <div class="lista-comentarios mb-3">
                                                        <?php if (empty($comentarios_tarefa)): ?>
                                                            <p class="text-muted fst-italic text-center py-3">Nenhum comentário para esta tarefa.</p>
                                                        <?php else: ?>
                                                            <?php foreach ($comentarios_tarefa as $comentario): ?>
                                                                <div class="comentario border-bottom pb-2 mb-3">
                                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                                        <div>
                                                                            <span class="fw-bold"><?php echo htmlspecialchars($comentario['usuario_nome']); ?></span>
                                                                            <small class="text-muted ms-2"><?php echo date('d/m/Y H:i', strtotime($comentario['data_criacao'])); ?></small>
                                                                        </div>
                                                                        <?php if ($comentario['usuario_id'] == $_SESSION['usuario_id']): ?>
                                                                            <a href="tarefas.php?excluir_comentario=<?php echo $comentario['id']; ?>"
                                                                                class="text-danger text-decoration-none small"
                                                                                onclick="return confirm('Excluir este comentário?')">
                                                                                <i class="fas fa-times me-1"></i>Excluir
                                                                            </a>
                                                                        <?php endif; ?>
                                                                    </div>
                                                                    <div class="comentario-texto">
                                                                        <?php echo nl2br(htmlspecialchars($comentario['texto'])); ?>
                                                                    </div>
                                                                </div>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </div>

                                                    <!-- Formulário para adicionar novo comentário -->
                                                    <form method="POST" action="" class="form-comentario">
                                                        <input type="hidden" name="tarefa_id" value="<?php echo $tarefa['id']; ?>">
                                                        <div class="mb-3">
                                                            <textarea name="comentario_texto" class="form-control" placeholder="Escreva seu comentário aqui..." rows="2" required></textarea>
                                                        </div>
                                                        <div class="text-end">
                                                            <button type="submit" name="adicionar_comentario" class="btn btn-primary btn-sm">
                                                                <i class="fas fa-paper-plane me-1"></i>Comentar
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Linha para histórico de alterações (oculta por padrão) -->
                                    <tr id="historico-<?php echo $tarefa['id']; ?>" class="linha-historico" style="display: none;">
                                        <td colspan="7">
                                            <div class="historico-container card mb-0">
                                                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                                                    <h5 class="card-title mb-0 fs-6">
                                                        <i class="fas fa-history me-2"></i>Histórico de Alterações
                                                    </h5>
                                                    <button type="button" class="btn-close" aria-label="Fechar" onclick="toggleHistorico('<?php echo $tarefa['id']; ?>')"></button>
                                                </div>
                                                <div class="card-body">
                                                    <!-- Lista de alterações realizadas -->
                                                    <div class="lista-historico">
                                                        <?php
                                                        // Filtrar alterações para esta tarefa
                                                        $alteracoes_tarefa = array_filter($historico, function ($alteracao) use ($tarefa_id) {
                                                            return $alteracao['tarefa_id'] === $tarefa_id;
                                                        });

                                                        // Ordenar alterações por data (mais recentes primeiro)
                                                        usort($alteracoes_tarefa, function ($a, $b) {
                                                            return strtotime($b['data']) - strtotime($a['data']);
                                                        });
                                                        ?>

                                                        <?php if (empty($alteracoes_tarefa)): ?>
                                                            <p class="text-muted fst-italic text-center py-3">Nenhuma alteração registrada para esta tarefa.</p>
                                                        <?php else: ?>
                                                            <div class="table-responsive">
                                                                <table class="table table-sm table-hover">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Data</th>
                                                                            <th>Usuário</th>
                                                                            <th>Campo</th>
                                                                            <th>De</th>
                                                                            <th>Para</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php foreach ($alteracoes_tarefa as $alteracao): ?>
                                                                            <?php
                                                                            $tipo = $alteracao['tipo'];
                                                                            $tipo_texto = '';

                                                                            switch ($tipo) {
                                                                                case 'titulo':
                                                                                    $tipo_texto = 'Título';
                                                                                    break;
                                                                                case 'descricao':
                                                                                    $tipo_texto = 'Descrição';
                                                                                    break;
                                                                                case 'prioridade':
                                                                                    $tipo_texto = 'Prioridade';
                                                                                    break;
                                                                                case 'data_limite':
                                                                                    $tipo_texto = 'Data Limite';
                                                                                    $alteracao['valor_antigo'] = !empty($alteracao['valor_antigo']) ? date('d/m/Y', strtotime($alteracao['valor_antigo'])) : 'Sem data';
                                                                                    $alteracao['valor_novo'] = !empty($alteracao['valor_novo']) ? date('d/m/Y', strtotime($alteracao['valor_novo'])) : 'Sem data';
                                                                                    break;
                                                                                case 'responsavel':
                                                                                    $tipo_texto = 'Responsável';
                                                                                    break;
                                                                                case 'status':
                                                                                    $tipo_texto = 'Status';

                                                                                    if ($alteracao['valor_antigo'] == 'pendente') $alteracao['valor_antigo'] = 'Pendente';
                                                                                    else if ($alteracao['valor_antigo'] == 'andamento') $alteracao['valor_antigo'] = 'Em Andamento';
                                                                                    else if ($alteracao['valor_antigo'] == 'concluida') $alteracao['valor_antigo'] = 'Concluída';

                                                                                    if ($alteracao['valor_novo'] == 'pendente') $alteracao['valor_novo'] = 'Pendente';
                                                                                    else if ($alteracao['valor_novo'] == 'andamento') $alteracao['valor_novo'] = 'Em Andamento';
                                                                                    else if ($alteracao['valor_novo'] == 'concluida') $alteracao['valor_novo'] = 'Concluída';
                                                                                    break;
                                                                                default:
                                                                                    $tipo_texto = ucfirst($tipo);
                                                                            }
                                                                            ?>
                                                                            <tr>
                                                                                <td><?php echo date('d/m/Y H:i', strtotime($alteracao['data'])); ?></td>
                                                                                <td><?php echo htmlspecialchars($alteracao['usuario_nome']); ?></td>
                                                                                <td><span class="fw-medium"><?php echo $tipo_texto; ?></span></td>
                                                                                <td><span class="text-danger"><?php echo htmlspecialchars($alteracao['valor_antigo'] ?? 'Não definido'); ?></span></td>
                                                                                <td><span class="text-success"><?php echo htmlspecialchars($alteracao['valor_novo'] ?? 'Não definido'); ?></span></td>
                                                                            </tr>
                                                                        <?php endforeach; ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php include 'partials/footer.php'; ?>
    </div>

    <!-- Bootstrap JS Bundle com Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>


</body>

</html>