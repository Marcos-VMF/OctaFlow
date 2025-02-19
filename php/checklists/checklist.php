<?php
include '../bd/conexao.php';
include $_SERVER['DOCUMENT_ROOT'] . '/OctaFlow/navbar.php';

// Buscar todas as empresas
$query_empresas = "SELECT * FROM empresas ORDER BY nome ASC";
$result_empresas = $conn->query($query_empresas);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checklist de Formatação</title>
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
        <h2>Checklist de T.I.</h2>
        <form action="salvar_checklist.php" method="POST">
            <div class="section">
                <label>Dados do Cliente:</label>
                <select name="empresa_id" id="empresa_id" onchange="buscarSistemas()" required>
                    <option value="">Selecione a empresa</option>
                    <?php while ($empresa = $result_empresas->fetch_assoc()) { ?>
                        <option value="<?= $empresa['id']; ?>"><?= htmlspecialchars($empresa['nome']); ?></option>
                    <?php } ?>
                </select>
                <input type="date" name="data" required>
                <input type="text" name="ticket" placeholder="Ticket">
            </div>

        <div class="section">
            <label>Nome Pré-formatação:</label>
            <input type="text" name="nome_pre_formatacao" placeholder="Equipamento">
            <label>Nome Pós-formatação:</label>
            <input type="text" name="nome_pos_formatacao"  placeholder="Equipamento">
        </div>
        <div class="section">
            <label>Usuário antigo:</label>
            <input type="text" name="usuario_antigo" placeholder="Usuário">
            <label>Usuário novo:</label>
            <input type="text" name="usuario_novo" placeholder="Usuário">
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
        <div class="section">
                <label>Instalações:</label>
                <div class="checkbox-group" id="instalacoes">
                    <p>Selecione uma empresa para ver os sistemas vinculados.</p>
                </div>
            </div>

            <button type="submit">Salvar Checklist</button>
        </form>
    </div>

<script>
    function buscarSistemas() {
        var empresa_id = document.getElementById("empresa_id").value;
        var instalacoesDiv = document.getElementById("instalacoes");

        if (empresa_id) {
            fetch('buscar_sistemas.php?empresa_id=' + empresa_id)
                .then(response => response.text())
                .then(data => {
                    instalacoesDiv.innerHTML = data;
                });
        } else {
            instalacoesDiv.innerHTML = "<p>Selecione uma empresa para ver os sistemas vinculados.</p>";
        }
    }

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
