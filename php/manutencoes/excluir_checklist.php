<?php
include '../bd/conexao.php';

header('Content-Type: application/json');

try {
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception('ID não fornecido');
    }

    $id = intval($_GET['id']);

    // Delete the checklist
    $query = "DELETE FROM checklists_manutencao WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Checklist excluído com sucesso']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Checklist não encontrado']);
        }
    } else {
        throw new Exception('Erro ao excluir o checklist');
    }

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>