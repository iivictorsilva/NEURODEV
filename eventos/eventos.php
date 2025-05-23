<?php
session_start();
require_once('../includes/conexao.php');

// Verificar autenticação e tipo de usuário
if (!isset($_SESSION['user_id']) || $_SESSION['tipo_usuario'] !== 'professor') {
    header("Location: ../login/login.php");
    exit();
}

// Processar criação de evento
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['criar_evento'])) {
        try {
            // Validar e sanitizar inputs
            $titulo = $conn->real_escape_string(trim($_POST['titulo']));
            $descricao = $conn->real_escape_string(trim($_POST['descricao']));
            $data_evento = $conn->real_escape_string($_POST['data_evento']);
            $usuario_id = $_SESSION['user_id'];

            if (empty($titulo) || empty($descricao) || empty($data_evento)) {
                throw new Exception("Todos os campos obrigatórios devem ser preenchidos");
            }

            // Inserir evento principal
            $sql = "INSERT INTO eventos (titulo, descricao, data_evento, usuario_id) 
                    VALUES ('$titulo', '$descricao', '$data_evento', '$usuario_id')";
            
            if (!$conn->query($sql)) {
                throw new Exception("Erro ao criar evento: " . $conn->error);
            }
            
            $evento_id = $conn->insert_id;
            
            // Associar evento aos alunos selecionados
            if (isset($_POST['alunos']) && is_array($_POST['alunos']) && count($_POST['alunos']) > 0) {
                foreach ($_POST['alunos'] as $aluno_id) {
                    $aluno_id = $conn->real_escape_string($aluno_id);
                    $sql = "INSERT INTO aluno_eventos (aluno_id, evento_id) VALUES ('$aluno_id', '$evento_id')";
                    if (!$conn->query($sql)) {
                        throw new Exception("Erro ao associar aluno ao evento: " . $conn->error);
                    }
                }
                $_SESSION['mensagem'] = "Evento criado e associado com sucesso!";
            } else {
                $_SESSION['mensagem'] = "Evento criado com sucesso! (Nenhum aluno associado)";
            }
            
            header("Location: eventos.php");
            exit();
            
        } catch(Exception $e) {
            $_SESSION['erro'] = $e->getMessage();
        }
    }
    // Processar exclusão de evento
    elseif (isset($_POST['excluir_evento'])) {
        try {
            $evento_id = $conn->real_escape_string($_POST['evento_id']);
            $usuario_id = $_SESSION['user_id'];

            // Verificar se o evento pertence ao professor antes de excluir
            $sql = "SELECT id FROM eventos WHERE id = '$evento_id' AND usuario_id = '$usuario_id'";
            $result = $conn->query($sql);
            
            if ($result->num_rows == 0) {
                throw new Exception("Evento não encontrado ou você não tem permissão para excluí-lo");
            }

            // Primeiro excluir as associações com alunos
            $sql = "DELETE FROM aluno_eventos WHERE evento_id = '$evento_id'";
            if (!$conn->query($sql)) {
                throw new Exception("Erro ao remover associações de alunos: " . $conn->error);
            }

            // Depois excluir o evento principal
            $sql = "DELETE FROM eventos WHERE id = '$evento_id'";
            if (!$conn->query($sql)) {
                throw new Exception("Erro ao excluir evento: " . $conn->error);
            }

            $_SESSION['mensagem'] = "Evento excluído com sucesso!";
            header("Location: eventos.php");
            exit();

        } catch(Exception $e) {
            $_SESSION['erro'] = $e->getMessage();
            header("Location: eventos.php");
            exit();
        }
    }
}

// Buscar todos os alunos para o select
$alunos = [];
$sql = "SELECT id, nome FROM usuarios WHERE tipo_usuario = 'aluno' ORDER BY nome ASC";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $alunos[] = $row;
    }
    $result->free();
} else {
    $_SESSION['erro'] = "Erro ao carregar alunos: " . $conn->error;
}

// Buscar eventos do professor
$eventos = [];
$sql = "SELECT e.id, e.titulo, e.descricao, e.data_evento, 
               GROUP_CONCAT(u.nome SEPARATOR ', ') as alunos_associados
        FROM eventos e
        LEFT JOIN aluno_eventos ae ON e.id = ae.evento_id
        LEFT JOIN usuarios u ON ae.aluno_id = u.id
        WHERE e.usuario_id = '" . $_SESSION['user_id'] . "'
        GROUP BY e.id
        ORDER BY e.data_evento ASC";
$result = $conn->query($sql);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $eventos[] = $row;
    }
    $result->free();
} else {
    $_SESSION['erro'] = "Erro ao carregar eventos: " . $conn->error;
}

// Formatar para o FullCalendar
$eventosCalendar = [];
foreach ($eventos as $evento) {
    $eventosCalendar[] = [
        'id' => $evento['id'],
        'title' => $evento['titulo'],
        'start' => $evento['data_evento'],
        'description' => $evento['descricao'] . "\n\nAlunos: " . ($evento['alunos_associados'] ?: 'Nenhum aluno associado'),
        'allDay' => true,
        'color' => '#3498db'
    ];
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Eventos</title>
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --light-color: #f5f7fa;
            --dark-color: #333;
            --success-color: #28a745;
            --danger-color: #dc3545;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: var(--light-color);
            color: var(--dark-color);
        }
        
        .container {
            display: flex;
            min-height: 100vh;
            gap: 20px;
        }
        
        .sidebar {
            width: 300px;
            padding: 20px;
            background-color: var(--secondary-color);
            color: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .main-content {
            flex: 1;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        
        input, textarea, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        textarea {
            min-height: 100px;
            resize: vertical;
        }
        
        .btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 16px;
            transition: background-color 0.3s;
        }
        
        .btn-danger {
            background-color: var(--danger-color);
        }
        
        .btn:hover {
            opacity: 0.9;
        }
        
        .btn-danger:hover {
            background-color: #c82333;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .select2-container--default .select2-selection--multiple {
            border: 1px solid #ddd;
            border-radius: 4px;
            min-height: 42px;
            padding: 5px;
        }
        
        #calendar {
            margin-top: 20px;
        }
        
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: white;
            margin: 15% auto;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 500px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }
        
        @media (max-width: 768px) {
            .container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                margin-bottom: 20px;
            }
            
            .modal-content {
                width: 90%;
                margin: 20% auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="sidebar">
            <h2><i class="fas fa-calendar-alt"></i> Gerenciar Eventos</h2>
            <p>Crie e atribua eventos aos alunos.</p>
            <a href="../dashboard/professor/index.php" class="btn">
                <i class="fas fa-arrow-left"></i> Voltar ao Dashboard
            </a>
        </div>

        <div class="main-content">
            <?php if (isset($_SESSION['mensagem'])): ?>
                <div class="alert alert-success">
                    <?= htmlspecialchars($_SESSION['mensagem']) ?>
                    <?php unset($_SESSION['mensagem']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['erro'])): ?>
                <div class="alert alert-error">
                    <?= htmlspecialchars($_SESSION['erro']) ?>
                    <?php unset($_SESSION['erro']); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="eventos.php">
                <div class="form-group">
                    <label for="titulo">Título do Evento *</label>
                    <input type="text" id="titulo" name="titulo" required>
                </div>
                
                <div class="form-group">
                    <label for="descricao">Descrição *</label>
                    <textarea id="descricao" name="descricao" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="data_evento">Data do Evento *</label>
                    <input type="date" id="data_evento" name="data_evento" required>
                </div>
                
                <div class="form-group">
                    <label for="alunos">Alunos</label>
                    <select id="alunos" name="alunos[]" class="select2" multiple="multiple">
                        <?php foreach ($alunos as $aluno): ?>
                            <option value="<?= htmlspecialchars($aluno['id']) ?>">
                                <?= htmlspecialchars($aluno['nome']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <button type="submit" name="criar_evento" class="btn">
                        <i class="fas fa-calendar-plus"></i> Criar Evento
                    </button>
                </div>
            </form>

            <h3><i class="fas fa-calendar"></i> Meus Eventos</h3>
            <div id="calendar"></div>
        </div>
    </div>

    <!-- Modal de Confirmação -->
    <div id="confirmModal" class="modal">
        <div class="modal-content">
            <h3 id="modalTitle">Confirmar Exclusão</h3>
            <p id="modalMessage">Tem certeza que deseja excluir este evento?</p>
            <div class="modal-actions">
                <button id="cancelButton" class="btn">Cancelar</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    <input type="hidden" name="evento_id" id="modalEventId">
                    <button type="submit" name="excluir_evento" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Excluir
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/pt-br.js'></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inicializar Select2
            $('#alunos').select2({
                placeholder: "Selecione os alunos",
                allowClear: true,
                width: '100%'
            });
            
            // Inicializar FullCalendar
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'pt-br',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                events: <?php echo json_encode($eventosCalendar); ?>,
                eventClick: function(info) {
                    var description = info.event.extendedProps.description || 'Sem descrição';
                    var eventTitle = info.event.title;
                    var eventId = info.event.id;
                    
                    // Configurar modal
                    $('#modalTitle').text('Evento: ' + eventTitle);
                    $('#modalMessage').html(description.replace(/\n/g, '<br>'));
                    $('#modalEventId').val(eventId);
                    
                    // Mostrar modal
                    $('#confirmModal').fadeIn();
                },
                eventContent: function(arg) {
                    return {
                        html: `<div class="fc-event-title">${arg.event.title}</div>`
                    };
                }
            });
            calendar.render();
            
            // Fechar modal ao clicar no botão Cancelar
            $('#cancelButton').click(function() {
                $('#confirmModal').fadeOut();
            });
            
            // Fechar modal ao clicar fora do conteúdo
            $(window).click(function(event) {
                if ($(event.target).is('#confirmModal')) {
                    $('#confirmModal').fadeOut();
                }
            });
        });
    </script>
</body>
</html>
<?php 
$conn->close();
?>