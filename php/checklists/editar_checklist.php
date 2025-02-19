<?php
include '../bd/conexao.php';
include $_SERVER['DOCUMENT_ROOT'] . '/OctaFlow/navbar.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM checklists WHERE id = $id";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
}

// Processar atualização quando o formulário for enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $empresa_id = $_POST['empresa_id'];
    $data = $_POST['data'];
    $ticket = $_POST['ticket'];
    $nome_pre_formatacao = $_POST['nome_pre_formatacao'];
    $nome_pos_formatacao = $_POST['nome_pos_formatacao'];
    $usuario_antigo = $_POST['usuario_antigo'];
    $usuario_novo = $_POST['usuario_novo'];
    $procedimento_inicial = isset($_POST['procedimento_inicial']) ? implode(', ', $_POST['procedimento_inicial']) : '';
    $sistema_operacional = $_POST['sistema_operacional'];
    $backup = $_POST['backup'];
    $local_salvamento = $_POST['local_salvamento'];

    $sql_update = "UPDATE checklists SET empresa_id=?, data=?, ticket=?, nome_pre_formatacao=?, nome_pos_formatacao=?, usuario_antigo=?, usuario_novo=?, procedimento_inicial=?, sistema_operacional=?, backup=?, local_salvamento=? WHERE id=?";
    $stmt = $conn->prepare($sql_update);
    $stmt->bind_param("issssssssssi", $empresa_id, $data, $ticket, $nome_pre_formatacao, $nome_pos_formatacao, $usuario_antigo, $usuario_novo, $procedimento_inicial, $sistema_operacional, $backup, $local_salvamento, $id);
    
    if ($stmt->execute()) {
        header("Location: listar_checklists.php"); // Redireciona após a atualização
        exit();
    } else {
        echo "Erro ao atualizar: " . $stmt->error;
    }
}

$query_empresas = "SELECT * FROM empresas ORDER BY nome ASC";
$result_empresas = $conn->query($query_empresas);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Checklist</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #eef2f3;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
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
        input[type="text"], select, input[type="date"] {
            width: calc(100% - 10px);
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-top: 5px;
        }
        input[type="checkbox"] {
            margin-right: 5px;
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
        <h2>Editar Checklist</h2>
        <form method="POST">
            <input type="hidden" name="id" value="<?= $row['id']; ?>">
            <div class="section">
                <label>Dados do Cliente:</label>
                <select name="empresa_id" required>
                    <option value="">Selecione a empresa</option>
                    <?php while ($empresa = $result_empresas->fetch_assoc()) { ?>
                        <option value="<?= $empresa['id']; ?>" <?= ($empresa['id'] == $row['empresa_id']) ? 'selected' : ''; ?>><?= htmlspecialchars($empresa['nome']); ?></option>
                    <?php } ?>
                </select>
                <input type="date" name="data" value="<?= $row['data']; ?>" required>
                <input type="text" name="ticket" value="<?= $row['ticket']; ?>" placeholder="Ticket">
            </div>

            <div class="section">
                <label>Nome Pré-formatação:</label>
                <input type="text" name="nome_pre_formatacao" value="<?= $row['nome_pre_formatacao']; ?>" placeholder="Equipamento">
                <label>Nome Pós-formatação:</label>
                <input type="text" name="nome_pos_formatacao" value="<?= $row['nome_pos_formatacao']; ?>" placeholder="Equipamento">
            </div>

            <div class="section">
                <label>Usuário antigo:</label>
                <input type="text" name="usuario_antigo" value="<?= $row['usuario_antigo']; ?>" placeholder="Usuário">
                <label>Usuário novo:</label>
                <input type="text" name="usuario_novo" value="<?= $row['usuario_novo']; ?>" placeholder="Usuário">
            </div>

            <div class="section">
            <label>Procedimento inicial:</label>
                <div class="checkbox-group">
                    <label><input type="checkbox" name="procedimento_inicial[]" value="Etiquetar"> Etiquetar</label>
                    <label><input type="checkbox" name="procedimento_inicial[]" value="Limpeza interna"> Limpeza interna</label>
                    <label><input type="checkbox" name="procedimento_inicial[]" value="Limpeza externa"> Limpeza externa</label>
                </div>
            </div>

            <div class="section">
            <label>Sistema Operacional:</label>
                <div class="checkbox-group">
                    <label><input type="radio" name="sistema_operacional" value="Windows 7" onclick="toggleOutroInput()"> Windows 7</label>
                    <label><input type="radio" name="sistema_operacional" value="Windows 10" onclick="toggleOutroInput()"> Windows 10</label>
                    <label><input type="radio" name="sistema_operacional" value="Windows 11" onclick="toggleOutroInput()"> Windows 11</label>
                    <label><input type="radio" name="sistema_operacional" value="Outro" id="outroCheckbox" onclick="toggleOutroInput()"> Outro:</label>
                    <input type="text" name="outro_sistema" id="outroInput" disabled>
                </div>
            </div>

            <div class="section">
                <label>Backup:</label>
                <div class="checkbox-group">
                    <label><input type="radio" name="backup" value="Nao Realizado" onclick="toggleBackupInput()"> Não Realizado</label>
                    <label><input type="radio" name="backup" value="Feito e Restaurado" onclick="toggleBackupInput()"> Feito e Restaurado</label>
                    <label><input type="radio" name="backup" value="Feito" id="backupFeito" onclick="toggleBackupInput()"> Feito</label>
                    <input type="text" name="local_salvamento" id="backupLocal" placeholder="Local de Salvamento" disabled>
                </div>
            </div>

            <button type="submit">Atualizar</button>
        </form>
    </div>
    <script>
        function toggleOutroInput() {
        var outroCheckbox = document.getElementById("outroCheckbox");
        var outroInput = document.getElementById("outroInput");

        if (outroCheckbox.checked) {
            outroInput.disabled = false;
        } else {
            outroInput.disabled = true;
            outroInput.value = ""; // Limpa o campo caso o usuário mude de ideia
        }
    }

    function toggleBackupInput() {
        var backupFeito = document.getElementById("backupFeito");
        var backupLocal = document.getElementById("backupLocal");

        if (backupFeito.checked) {
            backupLocal.disabled = false;
        } else {
            backupLocal.disabled = true;
            backupLocal.value = ""; // Limpa o campo caso o usuário mude de ideia
        }
    }
    </script>
</body>
</html>
