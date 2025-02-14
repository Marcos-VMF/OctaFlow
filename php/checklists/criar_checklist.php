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
        <div class="section">
            <label>Dados do Cliente:</label>
            <select>
                <option value="">Selecione a empresa</option>
                <option value="Empresa A">Empresa A</option>
                <option value="Empresa B">Empresa B</option>
                <option value="Empresa C">Empresa C</option>
            </select>
            <input type="date" placeholder="Data">
            <input type="text" placeholder="Ticket">
        </div>
        <div class="section">
            <label>Nome Pré-formatação:</label>
            <input type="text" placeholder="Equipamento">
            <label>Nome Pós-formatação:</label>
            <input type="text" placeholder="Equipamento">
        </div>
        <div class="section">
            <label>Usuário antigo:</label>
            <input type="text" placeholder="Equipamento">
            <label>Usuário novo:</label>
            <input type="text" placeholder="Equipamento">
        </div>
        <div class="section">
            <label>Procedimento inicial:</label>
            <div class="checkbox-group">
                <label><input type="checkbox"> Etiquetar</label>
                <label><input type="checkbox"> Limpeza interna</label>
                <label><input type="checkbox"> Limpeza externa</label>
            </div>
        </div>
        <div class="section">
            <label>Sistema Operacional:</label>
            <div class="checkbox-group">
                <label><input type="checkbox"> Windows 7</label>
                <label><input type="checkbox"> Windows 10</label>
                <label><input type="checkbox"> Windows 11</label>
                <label><input type="checkbox" id="outroCheckbox" onclick="toggleOutroInput()"> Outro:</label>
            <input type="text" id="outroInput" disabled>
            </div>
        </div>
        <div class="section">
            <label>Backup:</label>
            <div class="checkbox-group">
                <label><input type="checkbox"> Não Realizado</label>
                <label><input type="checkbox"> Feito e restaurado</label>
                <label><input type="checkbox"> Feito</label>
                <label>Local de Salvamento:</label>
            <br>
            <input type="text">
            </div>
        </div>
        <div class="section">
            <label>Instalações:</label>
            <div class="checkbox-group">
                <label><input type="checkbox"> Sistema Operacional</label>
                <label><input type="checkbox"> Milvus</label>
                <label><input type="checkbox"> TeamViewer</label>
                <label><input type="checkbox"> Adobe Reader</label>
                <label><input type="checkbox"> Java</label>
                <label><input type="checkbox"> 7-zip</label>
                <label><input type="checkbox"> PDF Creator</label>
            </div>
        </div>
    </div>
<script>
        function toggleOutroInput() {
            var checkbox = document.getElementById('outroCheckbox');
            var input = document.getElementById('outroInput');
            input.disabled = !checkbox.checked;
        }
    </script>
</body>
</html>
