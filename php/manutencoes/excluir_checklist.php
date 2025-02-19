<?php
include '../bd/conexao.php';

$id = $_GET['id'];

// Delete the checklist
$query = "DELETE FROM checklists_manutencao WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();

// Redirect back to the list
header("Location: listar_checklists.php");
exit;
?>