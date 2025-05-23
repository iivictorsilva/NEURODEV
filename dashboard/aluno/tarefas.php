<?php
// Incluir o arquivo de verificação de acesso
$pagina_permitida_para = 'aluno';
include('../verifica_acesso.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Inicializar variáveis
$tarefas = [];
$erro = null;
$aluno_id = $_SESSION['user_id'];

try {
    // Conectar ao banco de dados
    $mysqli = new mysqli('localhost', 'root', '', 'autismo_plataforma');

    if ($mysqli->connect_error) {
        throw new Exception("Falha na conexão: " . $mysqli->connect_error);
    }

    // Consultar tarefas vinculadas ao aluno
    $sql = "SELECT t.* 
            FROM tarefas t
            INNER JOIN tarefas_alunos ta ON t.id = ta.tarefa_id
            WHERE ta.aluno_id = ?
            ORDER BY t.prazo ASC";
    
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        throw new Exception("Erro ao preparar a consulta: " . $mysqli->error);
    }

    $stmt->bind_param("i", $aluno_id);

    if (!$stmt->execute()) {
        throw new Exception("Erro ao executar a consulta: " . $stmt->error);
    }

    $result = $stmt->get_result();
    if (!$result) {
        throw new Exception("Erro ao obter resultados: " . $stmt->error);
    }

    while ($row = $result->fetch_assoc()) {
        $tarefas[] = $row;
    }

    $stmt->close();
    $mysqli->close();

} catch (Exception $e) {
    $erro = $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tarefas - NeuroDev</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
       :root {
        --primary-color: #3498db;
        --primary-hover: #2980b9;
        --secondary-color: #2c3e50;
        --light-color: #f8f9fa;
        --dark-color: #343a40;
        --success-color: #28a745;
        --warning-color: #ffc107;
        --danger-color: #dc3545;
        --text-color: #495057;
        --border-color: #dee2e6;
    }
    
    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background-color: #f5f5f5;
        color: var(--text-color);
        line-height: 1.6;
    }
    
    /* Sidebar Styles */
    .sidebar {
        min-height: 100vh;
        background-color: var(--secondary-color);
        padding-top: 20px;
        width: 250px;
        transition: all 0.3s ease;
        position: fixed;
        z-index: 1000;
    }
    
    .sidebar .nav-item {
        margin-bottom: 5px;
    }
    
    .sidebar .nav-link {
        color: rgba(255, 255, 255, 0.8);
        font-size: 16px;
        padding: 10px 15px;
        border-radius: 5px;
        transition: all 0.3s;
    }
    
    .sidebar .nav-link:hover {
        background-color: rgba(255, 255, 255, 0.1);
        color: white;
    }
    
    .sidebar .nav-link.active {
        background-color: var(--primary-color);
        color: white;
    }
    
    .sidebar .nav-link i {
        margin-right: 10px;
        width: 20px;
        text-align: center;
    }
    
    .main-content {
        margin-left: 250px;
        padding: 20px;
        transition: all 0.3s ease;
        min-height: 100vh;
    }
    
    .sidebar.collapsed {
        width: 70px;
    }
    
    .sidebar.collapsed .nav-link span {
        display: none;
    }
    
    .sidebar.collapsed .nav-link i {
        margin-right: 0;
        font-size: 1.2rem;
    }
    
    .sidebar.collapsed .nav-link {
        text-align: center;
        padding: 10px 5px;
    }
    
    .toggle-btn {
        position: fixed;
        top: 10px;
        left: 10px;
        z-index: 1100;
        background-color: var(--primary-color);
        color: white;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s;
    }
    
    .toggle-btn:hover {
        background-color: var(--primary-hover);
    }
    
    .site-title {
        color: white;
        text-align: center;
        padding: 15px 0;
        margin-bottom: 20px;
        font-weight: bold;
    }
    
    .site-title span {
        color: var(--primary-color);
    }
    
    @keyframes colorChange {
        0% { color: #3498db; }
        25% { color: #e74c3c; }
        50% { color: #9b59b6; }
        75% { color: #2ecc71; }
        100% { color: #3498db; }
    }
    
    .animated-text {
        animation: colorChange 8s infinite;
    }
    
    /* Main Content Styles */
    .page-header {
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 1px solid var(--border-color);
    }
    
    .page-header h1 {
        color: var(--secondary-color);
        font-weight: 600;
    }
    
    /* Card Styles with Enhanced Hover Effects */
    .task-card {
        border: none;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        margin-bottom: 25px;
        background-color: white;
        overflow: hidden;
        transform: translateY(0);
        position: relative;
    }
    
    .task-card:hover {
        transform: translateY(-5px) scale(1.02);
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.15);
        z-index: 1;
        background: linear-gradient(to bottom, rgba(255,255,255,0.95), rgba(255,255,255,1));
    }
    
    .task-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(52, 152, 219, 0.05);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .task-card:hover::before {
        opacity: 1;
    }
    
    .task-card .card-body {
        padding: 20px;
    }
    
    .task-title {
        color: var(--secondary-color);
        font-weight: 600;
        margin-bottom: 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .task-status {
        font-size: 0.85rem;
        font-weight: 500;
        padding: 5px 10px;
        border-radius: 20px;
    }
    
    .status-pendente {
        background-color: rgba(255, 193, 7, 0.2);
        color: #d39e00;
    }
    
    .status-concluida {
        background-color: rgba(40, 167, 69, 0.2);
        color: var(--success-color);
    }
    
    /* Estilo para descrição com limite de linhas */
    .task-description {
        color: var(--text-color);
        margin-bottom: 15px;
        display: -webkit-box;
        -webkit-line-clamp: 3; /* Limita a 3 linhas */
        -webkit-box-orient: vertical;
        overflow: hidden;
        text-overflow: ellipsis;
        line-height: 1.5;
        max-height: 4.5em; /* 3 linhas * 1.5 (line-height) */
    }
    
    .task-description.expanded {
        -webkit-line-clamp: unset;
        display: block;
        max-height: none;
    }
    
    .task-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 15px;
        font-size: 0.9rem;
    }
    
    .task-due {
        color: var(--dark-color);
        display: flex;
        align-items: center;
    }
    
    .task-due i {
        margin-right: 5px;
        color: var(--primary-color);
    }
    
    .task-actions .btn {
        padding: 5px 10px;
        font-size: 0.85rem;
        transition: all 0.3s ease;
    }
    
    .task-actions .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }
    
    /* Botão "Ver mais" */
    .read-more-btn {
        background: none;
        border: none;
        color: var(--primary-color);
        padding: 0;
        font-size: 0.9rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        margin-top: 5px;
        transition: all 0.3s ease;
    }
    
    .read-more-btn:hover {
        text-decoration: underline;
        color: var(--primary-hover);
    }
    
    .read-more-btn i {
        margin-left: 5px;
        font-size: 0.8rem;
        transition: transform 0.3s ease;
    }
    
    .read-more-btn.expanded i {
        transform: rotate(180deg);
    }
    
    /* No Tasks Styles */
    .no-tasks {
        background-color: white;
        padding: 40px 20px;
        border-radius: 10px;
        text-align: center;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        margin-top: 30px;
    }
    
    .no-tasks i {
        font-size: 3rem;
        color: var(--primary-color);
        margin-bottom: 15px;
        opacity: 0.7;
    }
    
    .no-tasks h3 {
        color: var(--secondary-color);
        margin-bottom: 10px;
    }
    
    .no-tasks p {
        color: #6c757d;
        margin-bottom: 20px;
    }
    
    /* Error Container */
    .error-container {
        background-color: #f8d7da;
        color: #721c24;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 30px;
        border: 1px solid #f5c6cb;
    }
    
    .error-container h4 {
        color: #721c24;
        margin-bottom: 10px;
    }
    
    /* Modal Styles */
    .modal-task-header {
        border-bottom: none;
        padding-bottom: 0;
    }
    
    .modal-task-title {
        color: var(--secondary-color);
        font-weight: 600;
    }
    
    .modal-task-body {
        padding: 20px;
    }
    
    .modal-section {
        margin-bottom: 20px;
    }
    
    .modal-section h6 {
        color: var(--secondary-color);
        font-weight: 600;
        margin-bottom: 10px;
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 5px;
    }
    
    .task-details {
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
    }
    
    .task-detail-item {
        display: flex;
        margin-bottom: 8px;
        align-items: center;
    }
    
    .task-detail-item i {
        width: 20px;
        text-align: center;
        margin-right: 10px;
        color: var(--primary-color);
    }
    
    .task-detail-label {
        font-weight: 500;
        min-width: 80px;
        color: var(--secondary-color);
    }
    
    /* Disable hover effects in modal */
    .modal .task-card:hover {
        transform: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        background: white;
    }
    
    .modal .task-card:hover::before {
        opacity: 0;
    }
    
    /* Responsive Styles */
    @media (max-width: 768px) {
        .sidebar {
            margin-left: -250px;
        }
        
        .sidebar.collapsed {
            margin-left: 0;
            width: 70px;
        }
        
        .main-content {
            margin-left: 0;
            padding: 15px;
        }
        
        .sidebar.show {
            margin-left: 0;
        }
        
        .task-meta {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .task-actions {
            margin-top: 10px;
            width: 100%;
        }
        
        .task-actions .btn {
            width: 100%;
        }
    }
    </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="site-title">
            <h4>Neuro<span class="animated-text">Dev</span></h4>
        </div>
        
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="index.php"><i class="fas fa-tachometer-alt"></i> <span>Início</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../../video_chamada/video_chamada.php"><i class="fas fa-video"></i> <span>Video Chamada</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="./tarefas.php"><i class="fas fa-tasks"></i> <span>Tarefas</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../aluno/JOGODAVELHA/index.html"><i class="fas fa-gamepad"></i> <span>Jogos</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="./eventos.php"><i class="fas fa-calendar"></i> <span>Eventos</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="./perfil.php"><i class="fas fa-user"></i> <span>Perfil</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="../../logout.php"><i class="fas fa-sign-out-alt"></i> <span>Sair</span></a>
            </li>
        </ul>
    </div>

    <button class="toggle-btn d-lg-none" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <div class="main-content" id="main-content">
        <div class="container-fluid">
            <h1 class="my-4"><i class="fas fa-tasks me-2"></i>Minhas Tarefas</h1>

            <?php if ($erro): ?>
                <div class="error-container">
                    <h4><i class="fas fa-exclamation-triangle me-2"></i>Erro ao carregar tarefas</h4>
                    <p><?php echo htmlspecialchars($erro); ?></p>
                    <a href="tarefas.php" class="btn btn-sm btn-outline-danger">Tentar novamente</a>
                </div>
            <?php elseif (!empty($tarefas)): ?>
                <div class="row row-cols-1 row-cols-md-2 g-4">
                    <?php foreach ($tarefas as $tarefa): ?>
                        <div class="col">
                            <div class="card task-card h-100">
                                <div class="card-body">
                                    <h3 class="task-title">
                                        <?php echo htmlspecialchars($tarefa['titulo']); ?>
                                        <?php if ($tarefa['status'] == 'concluida'): ?>
                                            <span class="badge bg-success badge-custom float-end">Concluída</span>
                                        <?php else: ?>
                                            <span class="badge bg-warning text-dark badge-custom float-end">Pendente</span>
                                        <?php endif; ?>
                                    </h3>
                                    
                                    <div class="task-description" id="descricao-<?php echo $tarefa['id']; ?>">
                                        <?php echo nl2br(htmlspecialchars($tarefa['descricao'])); ?>
                                    </div>
                                    
                                    <?php 
                                    // Mostrar botão "Ver mais" apenas se a descrição for longa
                                    $descricao = $tarefa['descricao'];
                                    $linhas = substr_count($descricao, "\n") + 1;
                                    $caracteres = strlen($descricao);
                                    if ($linhas > 3 || $caracteres > 150): ?>
                                        <button class="read-more-btn" onclick="toggleDescription(<?php echo $tarefa['id']; ?>)">
                                            Ver mais <i class="fas fa-chevron-down"></i>
                                        </button>
                                    <?php endif; ?>
                                    
                                    <div class="task-meta mt-3">
                                        <span class="task-due">
                                            <i class="far fa-calendar-alt me-1"></i>
                                            Prazo: <?php echo date('d/m/Y H:i', strtotime($tarefa['prazo'])); ?>
                                        </span>
                                        
                                        <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#taskModal-<?php echo $tarefa['id']; ?>">
                                            <i class="fas fa-expand"></i> Detalhes
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Modal -->
                        <div class="modal fade" id="taskModal-<?php echo $tarefa['id']; ?>" tabindex="-1" aria-labelledby="taskModalLabel-<?php echo $tarefa['id']; ?>" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="taskModalLabel-<?php echo $tarefa['id']; ?>">
                                            <i class="fas fa-tasks me-2"></i>
                                            <?php echo htmlspecialchars($tarefa['titulo']); ?>
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <h6>Descrição:</h6>
                                            <p><?php echo nl2br(htmlspecialchars($tarefa['descricao'])); ?></p>
                                        </div>
                                        
                                        <div class="modal-task-details">
                                            <p><i class="far fa-calendar-alt"></i> <strong>Prazo:</strong> 
                                                <?php echo date('d/m/Y H:i', strtotime($tarefa['prazo'])); ?>
                                            </p>
                                            <p><i class="fas fa-info-circle"></i> <strong>Status:</strong> 
                                                <?php if ($tarefa['status'] == 'pendente'): ?>
                                                    <span class="status-pendente">Pendente</span>
                                                <?php else: ?>
                                                    <span class="status-concluida">Concluída</span>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                        <?php if ($tarefa['status'] == 'pendente'): ?>
                                            <button type="button" class="btn btn-success" onclick="markAsCompleted(<?php echo $tarefa['id']; ?>)">
                                                <i class="fas fa-check"></i> Marcar como concluída
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-tasks text-center py-5">
                    <i class="fas fa-tasks fa-4x mb-3 text-muted"></i>
                    <h3 class="text-muted">Nenhuma tarefa encontrada</h3>
                    <p class="text-muted">Você não tem tarefas atribuídas no momento.</p>
                    <a href="index.php" class="btn btn-primary mt-3">
                        <i class="fas fa-home me-1"></i> Voltar ao Início
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle sidebar function
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('collapsed');
            
            if (window.innerWidth < 992) {
                sidebar.classList.toggle('show');
            }
        }
        
        // Alternar descrição expandida/recolhida
        function toggleDescription(taskId) {
            const description = document.getElementById(`descricao-${taskId}`);
            const button = document.querySelector(`#descricao-${taskId} + .read-more-btn`);
            
            description.classList.toggle('expanded');
            button.classList.toggle('expanded');
            
            if (description.classList.contains('expanded')) {
                button.innerHTML = 'Ver menos <i class="fas fa-chevron-up"></i>';
            } else {
                button.innerHTML = 'Ver mais <i class="fas fa-chevron-down"></i>';
            }
        }
        
        // Marcar tarefa como concluída
        function markAsCompleted(taskId) {
            if (confirm('Deseja realmente marcar esta tarefa como concluída?')) {
                fetch(`marcar_concluida.php?id=${taskId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Erro na requisição');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        alert('Tarefa marcada como concluída com sucesso!');
                        location.reload();
                    } else {
                        alert('Erro: ' + (data.message || 'Erro desconhecido'));
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Erro ao processar a requisição: ' + error.message);
                });
            }
        }
        
        // Ajustar sidebar em telas pequenas
        function handleResize() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            
            if (window.innerWidth < 992) {
                sidebar.classList.add('collapsed');
                sidebar.classList.remove('show');
                mainContent.classList.add('collapsed');
            } else {
                sidebar.classList.remove('collapsed');
                mainContent.classList.remove('collapsed');
            }
        }
        
        window.addEventListener('resize', handleResize);
        handleResize();
    </script>
</body>
</html>