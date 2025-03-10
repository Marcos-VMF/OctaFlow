<?php include $_SERVER['DOCUMENT_ROOT'] . '/OctaFlow/navbar.php'; ?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checklist de Manutenção</title>
    <link href='//fonts.googleapis.com/css?family=Montserrat:thin,extra-light,light,100,200,300,400,500,600,700,800' rel='stylesheet' type='text/css'>
    <style>
        :root {
            --bg-primary: #1a1a1a;
            --bg-secondary: #242424;
            --bg-tertiary: #2a2a2a;
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
            background: var(--bg-secondary);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: var(--text-primary);
            font-weight: 600;
            font-size: 1.5rem;
        }

        .section {
            margin-bottom: 15px;
            padding: 15px;
            border-radius: 5px;
            background: var(--bg-tertiary);
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
            color: var(--text-primary);
        }

        input, textarea, select {
            width: 100%;
            padding: 8px;
            border: 1px solid var(--border-color);
            border-radius: 5px;
            margin-top: 5px;
            box-sizing: border-box;
            background: var(--bg-primary);
            color: var(--text-primary);
        }

        textarea {
            height: 80px;
        }

        button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: var(--accent-secondary);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background-color: var(--accent-primary);
        }

        .inline-group {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        input[type="checkbox"] {
            appearance: none;
            -webkit-appearance: none;
            width: 20px;
            height: 20px;
            border: 2px solid var(--border-color);
            border-radius: 4px;
            background: var(--bg-primary);
            cursor: pointer;
            position: relative;
            transition: all 0.2s;
        }

        input[type="checkbox"]:checked {
            background: var(--accent-primary);
            border-color: var(--accent-primary);
        }

        input[type="checkbox"]:checked::after {
            content: '✓';
            position: absolute;
            color: white;
            font-size: 14px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        input[type="checkbox"]:focus {
            outline: none;
            border-color: var(--accent-primary);
        }
    </style>
    <script>
        function toggleModelo() {
            var tipoEquipamento = document.querySelector('input[name="equipamento"]:checked').value;
            document.getElementById('modelo').disabled = !(tipoEquipamento === 'notebook' || tipoEquipamento === 'servidor');
            document.getElementById('carregador_section').style.display = (tipoEquipamento === 'notebook') ? 'inline-block' : 'none';
        }

        function toggleCapacidade() {
            var armazenamento = document.getElementById('armazenamento_tipo').value;
            var capacidade = document.getElementById('capacidade_armazenamento');
            capacidade.disabled = (armazenamento !== 'hd' && armazenamento !== 'ssd');
            if (capacidade.disabled) {
                capacidade.value = "";
            }
        }

        function toggleNomeMaquina() {
            var checkbox = document.getElementById('nao_tem_nome');
            var nomeMaquina = document.getElementById('nome_maquina');
            nomeMaquina.disabled = checkbox.checked;
            if (checkbox.checked) {
                nomeMaquina.value = "";
            }
        }
    </script>
</head>
<body>
    <div class="container">
        <h2>Checklist de Manutenção</h2>
        <form action="salvar_checklist_manutencao.php" method="POST">
            <div class="section">
                <label>Empresa:</label>
                <select name="empresa_id" required>
                    <option value="">Selecione a empresa</option>
                    <?php 
                    include '../bd/conexao.php';
                    $query_empresas = "SELECT * FROM empresas ORDER BY nome ASC";
                    $result_empresas = $conn->query($query_empresas);
                    while ($empresa = $result_empresas->fetch_assoc()) { 
                    ?>
                        <option value="<?= $empresa['id']; ?>"><?= htmlspecialchars($empresa['nome']); ?></option>
                    <?php } ?>
                </select>
                <label>Data:</label>
                <input type="date" name="data" required>
                <label>Ticket:</label>
                <input type="text" name="ticket" required>
            </div>
            <div class="section">
                <label>Equipamento e Nome da Máquina:</label>
                <div class="inline-group">
                    <label><input type="radio" name="equipamento" value="computador" onclick="toggleModelo()" required> Computador</label>
                    <label><input type="radio" name="equipamento" value="notebook" onclick="toggleModelo()"> Notebook</label>
                    <label><input type="radio" name="equipamento" value="servidor" onclick="toggleModelo()"> Servidor</label>
                    <input type="text" name="modelo" id="modelo" placeholder="Modelo" disabled>
                    <label id="carregador_section" style="display: none;"><input type="checkbox" name="acompanha_carregador"> Acompanha Carregador</label>
                </div>
                <input type="text" name="nome_maquina" id="nome_maquina" placeholder="Nome da Máquina">
                <label><input type="checkbox" id="nao_tem_nome" name="nao_tem_nome" onclick="toggleNomeMaquina()"> Não possui nome (computador não liga)</label>
            </div>
            <div class="section">
                <label>Especificações:</label>
                <label>Processador:</label>
                <input type="text" name="processador">
                <label>Memória RAM (GB):</label>
                <input type="text" name="memoria_ram">
                <label>Armazenamento:</label>
                <select name="armazenamento_tipo" id="armazenamento_tipo" onchange="toggleCapacidade()" required>
                    <option value="nenhum">Selecione</option> <!-- Default to 'nenhum' -->
                    <option value="hd">HD</option>
                    <option value="ssd">SSD</option>
                    <option value="nenhum">Nenhum</option>
                </select>
                <label>Capacidade (GB):</label>
                <input type="text" name="capacidade_armazenamento" id="capacidade_armazenamento" disabled>
            </div>
            <div class="section">
                <label>Defeitos Apresentados:</label>
                <textarea name="defeitos"></textarea>
                <label>Serviços Realizados:</label>
                <textarea name="servicos_realizados"></textarea>
            </div>
            <div class="section">
                <label>Observações:</label>
                <textarea name="observacoes"></textarea>
            </div>
            <button type="submit">Salvar Checklist</button>
        </form>
    </div>
</body>
</html>