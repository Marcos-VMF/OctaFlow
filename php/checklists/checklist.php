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
    <link href='//fonts.googleapis.com/css?family=Montserrat:thin,extra-light,light,100,200,300,400,500,600,700,800' rel='stylesheet' type='text/css'>
    <style>
        :root {
            --bg-primary: #1a1a1a;
            --bg-secondary: #242424;
            --bg-tertiary: #3a3a3a;
            --text-primary: #ffffff;
            --text-secondary: #a0a0a0;
            --accent-primary: #00c6ff;
            --accent-secondary: #0072ff;
            --border-color: #333333;
            --danger: #dc3545;
            --success: #28a745;
            --warning: #ffc107;
            --info: #17a2b8;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: var(--text-primary);
            margin-bottom: 30px;
            font-weight: 600;
            font-size: 1.5rem;
        }

        .section {
            margin-bottom: 20px;
            padding: 20px;
            border-radius: 10px;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-secondary);
            font-weight: 500;
            font-size: 0.9rem;
        }

        input[type="text"],
        input[type="date"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            background: var(--bg-tertiary);
            color: var(--text-primary);
            font-family: 'Montserrat', sans-serif;
        }

        input[type="text"]:focus,
        input[type="date"]:focus,
        select:focus {
            outline: none;
            border-color: var(--accent-primary);
        }

        .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 10px;
        }

        .checkbox-group label {
            display: flex;
            align-items: center;
            margin: 0;
            cursor: pointer;
            color: var(--text-primary);
            font-size: 0.9rem;
        }

        input[type="checkbox"],
        input[type="radio"] {
            margin-right: 8px;
            cursor: pointer;
        }

        input[type="radio"] {
            accent-color: var(--accent-primary);
        }

        button[type="submit"] {
            background: linear-gradient(45deg, var(--accent-secondary), var(--accent-primary));
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            font-size: 1rem;
            width: 100%;
            margin-top: 20px;
            font-family: 'Montserrat', sans-serif;
            transition: all 0.2s;
        }

        button[type="submit"]:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        #outroInput,
        #backupLocal {
            margin-top: 10px;
        }

        #outroInput[disabled],
        #backupLocal[disabled] {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .checkbox-group ul {
            list-style: none;
            padding: 0;
            margin: 0;
            width: 100%;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 10px;
        }

        .checkbox-group ul li {
            position: relative;
            padding: 8px 8px 8px 25px;
            margin-bottom: 0;
            background: var(--bg-tertiary);
            border-radius: 5px;
            border: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            min-height: 40px;
        }

        .checkbox-group ul li:before {
            content: "•";
            position: absolute;
            left: 10px;
            color: var(--accent-primary);
        }

        .checkbox-group ul li label {
            display: flex;
            align-items: center;
            margin: 0;
            cursor: pointer;
            width: 100%;
            font-size: 0.9rem;
            line-height: 1.2;
        }

        .checkbox-group ul li input[type="checkbox"] {
            margin-right: 8px;
            flex-shrink: 0;
        }

        @media (max-width: 768px) {
            .container {
                margin: 20px auto;
                padding: 15px;
            }

            .section {
                padding: 15px;
            }

            .checkbox-group {
                flex-direction: column;
            }

            .checkbox-group ul {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Checklist de T.I.</h2>
        <form action="salvar_checklist.php" method="POST">
            <div class="section">
                <label>Dados do Cliente</label>
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
                <label>Nome Pré-formatação</label>
                <input type="text" name="nome_pre_formatacao" placeholder="Equipamento">
                <label>Nome Pós-formatação</label>
                <input type="text" name="nome_pos_formatacao" placeholder="Equipamento">
            </div>

            <div class="section">
                <label>Usuário antigo</label>
                <input type="text" name="usuario_antigo" placeholder="Usuário">
                <label>Usuário novo</label>
                <input type="text" name="usuario_novo" placeholder="Usuário">
            </div>

            <div class="section">
                <label>Procedimento inicial</label>
                <div class="checkbox-group">
                    <label><input type="checkbox" name="procedimento_inicial[]" value="Etiquetar"> Etiquetar</label>
                    <label><input type="checkbox" name="procedimento_inicial[]" value="Limpeza interna"> Limpeza interna</label>
                    <label><input type="checkbox" name="procedimento_inicial[]" value="Limpeza externa"> Limpeza externa</label>
                </div>
            </div>

            <div class="section">
                <label>Sistema Operacional</label>
                <div class="checkbox-group">
                    <label><input type="radio" name="sistema_operacional" value="Windows 7" onclick="toggleOutroInput()"> Windows 7</label>
                    <label><input type="radio" name="sistema_operacional" value="Windows 10" onclick="toggleOutroInput()"> Windows 10</label>
                    <label><input type="radio" name="sistema_operacional" value="Windows 11" onclick="toggleOutroInput()"> Windows 11</label>
                    <label><input type="radio" name="sistema_operacional" value="Outro" id="outroCheckbox" onclick="toggleOutroInput()"> Outro:</label>
                    <input type="text" name="outro_sistema" id="outroInput" disabled>
                </div>
            </div>

            <div class="section">
                <label>Backup</label>
                <div class="checkbox-group">
                    <label><input type="radio" name="backup" value="Nao Realizado" onclick="toggleBackupInput()"> Não Realizado</label>
                    <label><input type="radio" name="backup" value="Feito e Restaurado" onclick="toggleBackupInput()"> Feito e Restaurado</label>
                    <label><input type="radio" name="backup" value="Feito" id="backupFeito" onclick="toggleBackupInput()"> Feito</label>
                    <input type="text" name="local_salvamento" id="backupLocal" placeholder="Local de Salvamento" disabled>
                </div>
            </div>

            <div class="section">
                <label>Instalações</label>
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
                        if (data.trim()) {
                            const labels = data.match(/<label>.*?<\/label>/g) || [];
                            if (labels.length > 0) {
                                // Sort labels alphabetically
                                labels.sort((a, b) => {
                                    const textA = a.match(/>([^<]+)<\/label>/)[1].toLowerCase();
                                    const textB = b.match(/>([^<]+)<\/label>/)[1].toLowerCase();
                                    return textA.localeCompare(textB);
                                });
                                const html = '<ul>' + labels.map(label => `<li>${label}</li>`).join('') + '</ul>';
                                instalacoesDiv.innerHTML = html;
                            } else {
                                instalacoesDiv.innerHTML = "<p>Nenhum sistema encontrado para esta empresa.</p>";
                            }
                        } else {
                            instalacoesDiv.innerHTML = "<p>Nenhum sistema encontrado para esta empresa.</p>";
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        instalacoesDiv.innerHTML = "<p>Erro ao carregar os sistemas.</p>";
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
                outroInput.value = "";
            }
        }

        function toggleBackupInput() {
            var backupFeito = document.getElementById("backupFeito");
            var backupLocal = document.getElementById("backupLocal");

            if (backupFeito.checked) {
                backupLocal.disabled = false;
            } else {
                backupLocal.disabled = true;
                backupLocal.value = "";
            }
        }
    </script>
</body>
</html>