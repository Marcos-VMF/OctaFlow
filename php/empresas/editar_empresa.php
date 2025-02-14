<?php
include '../bd/conexao.php';

if (isset($_GET['id'])) {
    $empresa_id = $_GET['id'];

    // Buscar empresa pelo ID
    $stmt = $conn->prepare("SELECT nome FROM empresas WHERE id = ?");
    $stmt->bind_param("i", $empresa_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $empresa = $result->fetch_assoc();
    $stmt->close();

    // Buscar sistemas já vinculados à empresa
    $query_sistemas_vinculados = "
        SELECT s.id, s.nome, s.link_download 
        FROM sistemas s 
        JOIN empresa_sistemas es ON s.id = es.sistema_id 
        WHERE es.empresa_id = ?";
    $stmt = $conn->prepare($query_sistemas_vinculados);
    $stmt->bind_param("i", $empresa_id);
    $stmt->execute();
    $sistemas_vinculados = $stmt->get_result();
    $stmt->close();

    // Buscar sistemas disponíveis para vincular
    $query_sistemas_disponiveis = "
        SELECT * FROM sistemas 
        WHERE id NOT IN (SELECT sistema_id FROM empresa_sistemas WHERE empresa_id = ?)";
    $stmt = $conn->prepare($query_sistemas_disponiveis);
    $stmt->bind_param("i", $empresa_id);
    $stmt->execute();
    $sistemas_disponiveis = $stmt->get_result();
    $stmt->close();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $novo_nome = $_POST['nome_empresa'];
        $sistemas_removidos = $_POST['remover_sistemas'] ?? [];
        $sistemas_adicionados = $_POST['adicionar_sistemas'] ?? [];
        $novos_sistemas = $_POST['novo_sistema'] ?? [];
        $links_sistemas = $_POST['link_sistema'] ?? [];

        // Atualizar nome da empresa
        $stmt = $conn->prepare("UPDATE empresas SET nome = ? WHERE id = ?");
        $stmt->bind_param("si", $novo_nome, $empresa_id);
        $stmt->execute();
        $stmt->close();

        // Remover vínculos com sistemas selecionados
        foreach ($sistemas_removidos as $sistema_id) {
            $stmt = $conn->prepare("DELETE FROM empresa_sistemas WHERE empresa_id = ? AND sistema_id = ?");
            $stmt->bind_param("ii", $empresa_id, $sistema_id);
            $stmt->execute();
            $stmt->close();
        }

        // Adicionar novos vínculos
        foreach ($sistemas_adicionados as $sistema_id) {
            $stmt = $conn->prepare("INSERT INTO empresa_sistemas (empresa_id, sistema_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $empresa_id, $sistema_id);
            $stmt->execute();
            $stmt->close();
        }

        // Adicionar novos sistemas ao banco e vincular à empresa
        foreach ($novos_sistemas as $index => $nome_sistema) {
            if (!empty($nome_sistema)) {
                $link = $links_sistemas[$index] ?? null;
                $stmt = $conn->prepare("INSERT INTO sistemas (nome, link_download) VALUES (?, ?)");
                $stmt->bind_param("ss", $nome_sistema, $link);
                $stmt->execute();
                $sistema_id = $stmt->insert_id;
                $stmt->close();

                // Vincular o novo sistema à empresa
                $stmt = $conn->prepare("INSERT INTO empresa_sistemas (empresa_id, sistema_id) VALUES (?, ?)");
                $stmt->bind_param("ii", $empresa_id, $sistema_id);
                $stmt->execute();
                $stmt->close();
            }
        }

        header("Location: listar_empresas.php");
        exit();
    }
} else {
    header("Location: listar_empresas.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Empresa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #eef2f3;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .section {
            margin-bottom: 15px;
            padding: 15px;
            border-radius: 5px;
            background: #f9f9f9;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="url"] {
            width: calc(100% - 10px);
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-top: 5px;
        }
        .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Editar Empresa</h2>
        <form method="POST">
            <div class="section">
                <label>Nome da Empresa:</label>
                <input type="text" name="nome_empresa" value="<?= htmlspecialchars($empresa['nome']); ?>" required>
            </div>

            <div class="section">
                <label>Remover Sistemas:</label>
                <div class="checkbox-group">
                    <?php while ($sistema = $sistemas_vinculados->fetch_assoc()) { ?>
                        <label>
                            <input type="checkbox" name="remover_sistemas[]" value="<?= $sistema['id']; ?>">
                            <?= htmlspecialchars($sistema['nome']); ?>
                        </label>
                    <?php } ?>
                </div>
            </div>

            <div class="section">
                <label>Adicionar Sistemas:</label>
                <div class="checkbox-group">
                    <?php while ($sistema = $sistemas_disponiveis->fetch_assoc()) { ?>
                        <label>
                            <input type="checkbox" name="adicionar_sistemas[]" value="<?= $sistema['id']; ?>">
                            <?= htmlspecialchars($sistema['nome']); ?>
                        </label>
                    <?php } ?>
                </div>
            </div>

            <div class="section" id="novos-sistemas">
                <label>Adicionar Novo Sistema:</label>
                <div class="sistema-item">
                    <input type="text" name="novo_sistema[]" placeholder="Nome do Sistema">
                    <input type="url" name="link_sistema[]" placeholder="Link de Download">
                </div>
            </div>
            <button type="button" onclick="adicionarSistema()">Adicionar Outro Sistema</button>

            <button type="submit">Salvar Alterações</button>
        </form>
    </div>

<script>
    function adicionarSistema() {
        var container = document.getElementById('novos-sistemas');
        var div = document.createElement('div');
        div.classList.add('sistema-item');
        div.innerHTML = '<input type="text" name="novo_sistema[]" placeholder="Nome do Sistema">' +
                        '<input type="url" name="link_sistema[]" placeholder="Link de Download">';
        container.appendChild(div);
    }
</script>

</body>
</html>
