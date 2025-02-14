<?php
include '../bd/conexao.php';

if (isset($_GET['id'])) {
    $sistema_id = $_GET['id'];

    // Buscar as empresas vinculadas ao sistema
    $stmt = $conn->prepare("
        SELECT e.nome FROM empresas e
        JOIN empresa_sistemas es ON e.id = es.empresa_id
        WHERE es.sistema_id = ?");
    $stmt->bind_param("i", $sistema_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $empresas_vinculadas = [];

    while ($row = $result->fetch_assoc()) {
        $empresas_vinculadas[] = $row['nome'];
    }
    $stmt->close();

    if (!empty($empresas_vinculadas) && !isset($_GET['confirm'])) {
        // Criar lista das empresas vinculadas
        $empresas_lista = implode(", ", $empresas_vinculadas);
        echo "<script>
            var confirmDelete = confirm('Esse programa está vinculado às empresas: $empresas_lista. Deseja realmente excluir?');
            if (confirmDelete) {
                window.location.href = 'excluir_sistema.php?id=$sistema_id&confirm=true';
            } else {
                window.location.href = 'listar_sistemas.php';
            }
        </script>";
        exit();
    }

    // Se a confirmação foi dada ou o sistema não tem vínculos, excluir
    if (isset($_GET['confirm']) && $_GET['confirm'] == "true") {
        // Remover os vínculos antes de excluir o sistema
        $stmt = $conn->prepare("DELETE FROM empresa_sistemas WHERE sistema_id = ?");
        $stmt->bind_param("i", $sistema_id);
        $stmt->execute();
        $stmt->close();

        // Excluir o sistema do banco de dados
        $stmt = $conn->prepare("DELETE FROM sistemas WHERE id = ?");
        $stmt->bind_param("i", $sistema_id);
        $stmt->execute();
        $stmt->close();

        header("Location: listar_sistemas.php");
        exit();
    } else {
        // Se não houver vínculos, excluir diretamente
        $stmt = $conn->prepare("DELETE FROM sistemas WHERE id = ?");
        $stmt->bind_param("i", $sistema_id);
        $stmt->execute();
        $stmt->close();

        header("Location: listar_sistemas.php");
        exit();
    }
}
?>
