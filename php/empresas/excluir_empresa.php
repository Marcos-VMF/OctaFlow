<?php
include '../bd/conexao.php';

if (isset($_GET['id'])) {
    $empresa_id = $_GET['id'];

    // Excluir vínculos da empresa com sistemas
    $stmt = $conn->prepare("DELETE FROM empresa_sistemas WHERE empresa_id = ?");
    $stmt->bind_param("i", $empresa_id);
    $stmt->execute();
    $stmt->close();

    // Excluir empresa
    $stmt = $conn->prepare("DELETE FROM empresas WHERE id = ?");
    $stmt->bind_param("i", $empresa_id);
    $stmt->execute();
    $stmt->close();

    header("Location: listar_empresas.php");
    exit();
}
?>
