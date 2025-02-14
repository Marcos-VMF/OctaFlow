<?php
include '../bd/conexao.php';

if (isset($_GET['id'])) {
    $sistema_id = $_GET['id'];

    // Buscar nome do sistema
    $stmt = $conn->prepare("SELECT nome FROM sistemas WHERE id = ?");
    $stmt->bind_param("i", $sistema_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $sistema = $result->fetch_assoc();
    $stmt->close();

    // Buscar empresas vinculadas ao sistema
    $query_empresas = "
        SELECT e.id, e.nome 
        FROM empresas e
        JOIN empresa_sistemas es ON e.id = es.empresa_id
        WHERE es.sistema_id = ?";
    $stmt = $conn->prepare($query_empresas);
    $stmt->bind_param("i", $sistema_id);
    $stmt->execute();
    $empresas = $stmt->get_result();
    $stmt->close();
} else {
    header("Location: listar_sistemas.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Empresas Vinculadas</title>
</head>
<body>
    <h2>Empresas Vinculadas ao Sistema: <?= htmlspecialchars($sistema['nome']); ?></h2>
    <ul>
        <?php while ($empresa = $empresas->fetch_assoc()) { ?>
            <li><?= htmlspecialchars($empresa['nome']); ?></li>
        <?php } ?>
    </ul>
    <a href="listar_sistemas.php">Voltar</a>
</body>
</html>
