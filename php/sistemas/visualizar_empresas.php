<?php
include '../bd/conexao.php';

if (!isset($_GET['id'])) {
    if (isset($_GET['ajax'])) {
        echo '<div class="empty-state"><i class="fas fa-exclamation-circle"></i><p>Sistema não encontrado</p></div>';
        exit;
    }
    header("Location: listar_sistemas.php");
    exit();
}

$sistema_id = $_GET['id'];

// Buscar empresas vinculadas ao sistema
$query_empresas = "
    SELECT e.id, e.nome 
    FROM empresas e
    JOIN empresa_sistemas es ON e.id = es.empresa_id
    WHERE es.sistema_id = ?
    ORDER BY e.nome ASC";
$stmt = $conn->prepare($query_empresas);
$stmt->bind_param("i", $sistema_id);
$stmt->execute();
$empresas = $stmt->get_result();

// Se for uma requisição AJAX, retornar apenas a lista de empresas
if (isset($_GET['ajax'])) {
    if ($empresas->num_rows > 0) {
        echo '<ul class="company-list">';
        while ($empresa = $empresas->fetch_assoc()) {
            echo '<li class="company-item">';
            echo '<i class="fas fa-building company-icon"></i>';
            echo htmlspecialchars($empresa['nome']);
            echo '</li>';
        }
        echo '</ul>';
    } else {
        echo '<div class="empty-state">';
        echo '<i class="fas fa-building"></i>';
        echo '<p>Nenhuma empresa vinculada a este sistema</p>';
        echo '</div>';
    }
    exit;
}

// Se não for AJAX, redirecionar para a lista de sistemas
header("Location: listar_sistemas.php");
exit();
?>
