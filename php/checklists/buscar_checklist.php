<?php
include '../bd/conexao.php';

header('Content-Type: application/json');

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo json_encode(['error' => 'ID inválido']);
    exit;
}

$query = "SELECT c.*, e.nome AS empresa,
          GROUP_CONCAT(DISTINCT s.nome) as sistemas_instalados
          FROM checklists c 
          JOIN empresas e ON c.empresa_id = e.id 
          LEFT JOIN checklist_sistemas cs ON c.id = cs.checklist_id
          LEFT JOIN sistemas s ON cs.sistema_id = s.id
          WHERE c.id = ?
          GROUP BY c.id";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$checklist = $result->fetch_assoc();

if (!$checklist) {
    echo json_encode(['error' => 'Checklist não encontrado']);
    exit;
}

// Convert procedimento_inicial from JSON if stored that way
if (isset($checklist['procedimento_inicial'])) {
    $procedimentos = json_decode($checklist['procedimento_inicial']);
    $checklist['procedimento_inicial'] = is_array($procedimentos) ? implode(", ", $procedimentos) : $checklist['procedimento_inicial'];
}

echo json_encode($checklist);
?>
