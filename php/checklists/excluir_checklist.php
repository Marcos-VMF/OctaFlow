<?php
include '../bd/conexao.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Garante que é um número inteiro

    // Deleta o checklist
    $stmt = $conn->prepare("DELETE FROM checklists WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        // Redireciona com sucesso
        header("Location: listar_checklists.php");
    } else {
        // Erro ao excluir
        header("Location: listar_checklists.php");
    }
    
    $stmt->close();
}

$conn->close();
?>
