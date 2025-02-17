<?php
include '../bd/conexao.php';

if (isset($_GET['empresa_id'])) {
    $empresa_id = $_GET['empresa_id'];

    // Buscar sistemas vinculados a essa empresa
    $query = "
        SELECT s.id, s.nome 
        FROM sistemas s
        JOIN empresa_sistemas es ON s.id = es.sistema_id
        WHERE es.empresa_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $empresa_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    // Gerar checkboxes
    while ($sistema = $result->fetch_assoc()) {
        echo "<label><input type='checkbox' name='sistemas[]' value='{$sistema['id']}'> " . htmlspecialchars($sistema['nome']) . "</label>";
    }
}
?>
