<?php
// Incluir o arquivo de verificação de acesso
$pagina_permitida_para = 'aluno'; // Definindo que apenas o aluno pode acessar esta página
include('../verifica_acesso.php'); // Verifica o acesso do usuário

// Conectar ao banco de dados
$mysqli = new mysqli('localhost', 'root', '', 'autismo_plataforma');

// Verificar a conexão
if ($mysqli->connect_error) {
    die("Falha na conexão: " . $mysqli->connect_error);
}

// Configurar timezone
date_default_timezone_set('America/Sao_Paulo');

// Inicializar as variáveis
$upcoming_events = [];
$past_events = [];
$error = null;
$today = new DateTime();

// Consultar eventos associados ao aluno
$user_id = $_SESSION['user_id'];
$sql = "SELECT e.id, e.titulo, e.descricao, e.data_evento, u.nome as professor 
        FROM eventos e
        INNER JOIN aluno_eventos ae ON e.id = ae.evento_id
        INNER JOIN usuarios u ON e.usuario_id = u.id
        WHERE ae.aluno_id = ?
        ORDER BY e.data_evento ASC";

// Usando prepared statement para maior segurança
$stmt = $mysqli->prepare($sql);
if ($stmt) {
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $event_date = new DateTime($row['data_evento']);
        if ($event_date >= $today) {
            $upcoming_events[] = $row;
        } else {
            $past_events[] = $row;
        }
    }
    $stmt->close();
} else {
    $error = "Erro ao preparar a consulta: " . $mysqli->error;
}

// Preparar dados para o calendário
$events_js = [];
$all_events = array_merge($upcoming_events, $past_events);
foreach ($all_events as $event) {
    $event_date = new DateTime($event['data_evento']);
    $is_past = $event_date < $today;
    
    $events_js[] = [
        'id' => $event['id'],
        'title' => $event['titulo'],
        'start' => $event['data_evento'],
        'description' => $event['descricao'],
        'professor' => $event['professor'],
        'allDay' => true,
        'color' => $is_past ? '#6c757d' : '#3498db', // Cinza para eventos passados, azul para futuros
        'textColor' => '#ffffff'
    ];
}
$events_json = json_encode($events_js, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos - NeuroDev</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --past-event-color: #6c757d;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
        }
        
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
            background-color: #2980b9;
        }
        
        .card-custom {
            border: none;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            margin-bottom: 20px;
            background-color: white;
        }
        
        .card-custom:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        
        .card-custom .card-body {
            padding: 20px;
        }
        
        #calendar {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .fc-event {
            cursor: pointer;
            border: none;
            font-size: 0.9rem;
            padding: 3px 5px;
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
        
        .no-events {
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .past-event {
            opacity: 0.7;
            border-left: 4px solid var(--past-event-color);
        }
        
        .upcoming-event {
            border-left: 4px solid var(--primary-color);
        }
        
        .section-title {
            color: var(--secondary-color);
            padding-bottom: 10px;
            border-bottom: 2px solid #eee;
            margin-top: 30px;
            margin-bottom: 20px;
        }
        
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
            }
            
            .sidebar.show {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <button class="toggle-btn d-lg-none" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <div class="d-flex">
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
                    <a class="nav-link" href="./tarefas.php"><i class="fas fa-tasks"></i> <span>Tarefas</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../aluno/JOGODAVELHA/index.html"><i class="fas fa-gamepad"></i> <span>Jogos</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="./eventos.php"><i class="fas fa-calendar"></i> <span>Eventos</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="./perfil.php"><i class="fas fa-user"></i> <span>Perfil</span></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../../logout.php"><i class="fas fa-sign-out-alt"></i> <span>Sair</span></a>
                </li>
            </ul>
        </div>

        <div class="main-content" id="main-content">
            <div class="container-fluid">
                <h1 class="my-4"><i class="fas fa-calendar-alt me-2"></i>Meus Eventos</h1>
                
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <div id="calendar"></div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12">
                        <h3 class="section-title"><i class="fas fa-list me-2"></i>Próximos Eventos</h3>
                        
                        <?php if (count($upcoming_events) > 0): ?>
                            <div class="row">
                                <?php foreach ($upcoming_events as $event): ?>
                                    <div class="col-md-4 mb-4">
                                        <div class="card card-custom h-100 upcoming-event">
                                            <div class="card-body">
                                                <h5 class="card-title text-primary"><?php echo htmlspecialchars($event['titulo']); ?></h5>
                                                <p class="card-text">
                                                    <small class="text-muted">
                                                        <i class="far fa-calendar-alt me-1"></i>
                                                        <?php echo date('d/m/Y H:i', strtotime($event['data_evento'])); ?>
                                                    </small>
                                                </p>
                                                <p class="card-text">
                                                    <i class="fas fa-user-tie me-1"></i>
                                                    <strong>Professor:</strong> <?php echo htmlspecialchars($event['professor']); ?>
                                                </p>
                                                <p class="card-text">
                                                    <i class="fas fa-align-left me-1"></i>
                                                    <strong>Descrição:</strong> <?php echo htmlspecialchars($event['descricao']); ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="no-events">
                                <i class="far fa-calendar-times fa-3x mb-3 text-muted"></i>
                                <h4 class="text-muted">Nenhum evento futuro encontrado</h4>
                                <p class="text-muted">Você não tem eventos agendados no momento.</p>
                            </div>
                        <?php endif; ?>
                        
                        <h3 class="section-title"><i class="fas fa-history me-2"></i>Eventos Passados</h3>
                        
                        <?php if (count($past_events) > 0): ?>
                            <div class="row">
                                <?php foreach ($past_events as $event): ?>
                                    <div class="col-md-4 mb-4">
                                        <div class="card card-custom h-100 past-event">
                                            <div class="card-body">
                                                <h5 class="card-title"><?php echo htmlspecialchars($event['titulo']); ?></h5>
                                                <p class="card-text">
                                                    <small class="text-muted">
                                                        <i class="far fa-calendar-alt me-1"></i>
                                                        <?php echo date('d/m/Y H:i', strtotime($event['data_evento'])); ?>
                                                    </small>
                                                </p>
                                                <p class="card-text">
                                                    <i class="fas fa-user-tie me-1"></i>
                                                    <strong>Professor:</strong> <?php echo htmlspecialchars($event['professor']); ?>
                                                </p>
                                                <p class="card-text">
                                                    <i class="fas fa-align-left me-1"></i>
                                                    <strong>Descrição:</strong> <?php echo htmlspecialchars($event['descricao']); ?>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="no-events">
                                <i class="far fa-calendar-times fa-3x mb-3 text-muted"></i>
                                <h4 class="text-muted">Nenhum evento passado encontrado</h4>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <a href="index.php" class="btn btn-secondary mt-3">
                    <i class="fas fa-arrow-left me-1"></i> Voltar para Início
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/pt-br.js"></script>
    <script>
        // Dados dos eventos
        const eventos = <?php echo $events_json; ?>;
        
        // Inicializar o calendário
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'pt-br',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay listMonth'
                },
                buttonText: {
                    today: 'Hoje',
                    month: 'Mês',
                    week: 'Semana',
                    day: 'Dia',
                    list: 'Lista'
                },
                events: eventos,
                eventClick: function(info) {
                    const event = info.event;
                    const startDate = event.start ? event.start.toLocaleString('pt-BR', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    }) : 'Data não especificada';
                    
                    const description = event.extendedProps.description || 'Sem descrição';
                    const professor = event.extendedProps.professor || 'Professor não especificado';
                    
                    alert(
                        `Evento: ${event.title}\n\n` +
                        `Professor: ${professor}\n\n` +
                        `Data: ${startDate}\n\n` +
                        `Descrição: ${description}`
                    );
                    
                    info.jsEvent.preventDefault();
                },
                eventContent: function(arg) {
                    return {
                        html: `<div class="fc-event-title">${arg.event.title}</div>`
                    };
                },
                height: 'auto',
                eventDisplay: 'block'
            });
            
            calendar.render();
        });
        
        // Toggle sidebar
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('main-content');
            
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('collapsed');
            
            if (window.innerWidth < 992) {
                sidebar.classList.toggle('show');
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
        handleResize(); // Executar no carregamento inicial
    </script>
</body>
</html>
<?php
$mysqli->close();
?>