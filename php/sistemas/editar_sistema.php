<?php
include '../bd/conexao.php';
include $_SERVER['DOCUMENT_ROOT'] . '/OctaFlow/navbar.php';

if (isset($_GET['id'])) {
    $sistema_id = $_GET['id'];

    // Buscar sistema pelo ID
    $stmt = $conn->prepare("SELECT nome, link_download FROM sistemas WHERE id = ?");
    $stmt->bind_param("i", $sistema_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $sistema = $result->fetch_assoc();
    $stmt->close();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $novo_nome = $_POST['nome_sistema'];
        $novo_link = $_POST['link_download'];

        // Atualizar sistema
        $stmt = $conn->prepare("UPDATE sistemas SET nome = ?, link_download = ? WHERE id = ?");
        $stmt->bind_param("ssi", $novo_nome, $novo_link, $sistema_id);
        $stmt->execute();
        $stmt->close();

        header("Location: listar_sistemas.php");
        exit();
    }
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
    <title>Editar Sistema</title>
</head>
<body>
    <h2>Editar Sistema</h2>
    <form method="POST">
        <label>Nome do Sistema:</label>
        <input type="text" name="nome_sistema" value="<?= htmlspecialchars($sistema['nome']); ?>" required>
        
        <label>Link de Download:</label>
        <input type="url" name="link_download" value="<?= htmlspecialchars($sistema['link_download']); ?>" required>

        <button type="submit">Salvar Alterações</button>
    </form>
</body>
</html>
