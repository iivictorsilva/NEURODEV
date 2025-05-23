<?php
// Conectar ao banco de dados
$host = "localhost";
$user = "root";
$password = "";
$dbname = "autismo_plataforma";
$conn = new mysqli($host, $user, $password, $dbname);

$sql = "SELECT id, titulo, descricao, data_inicio, data_fim FROM eventos";
$result = $conn->query($sql);

$events = array();
while ($row = $result->fetch_assoc()) {
    $events[] = array(
        'title' => $row['titulo'],
        'start' => $row['data_inicio'],
        'end' => $row['data_fim']
    );
}

echo json_encode($events);

$conn->close();
?>
