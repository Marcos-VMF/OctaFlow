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
        header("Location: listar_checklists.php");
        exit();
    } else {
        echo "Erro ao atualizar: " . $stmt->error;
    }
}

$query_empresas = "SELECT * FROM empresas ORDER BY nome ASC";
$result_empresas = $conn->query($query_empresas);

// Buscar sistemas da empresa
$sistemas_selecionados = [];
if (isset($row['empresa_id'])) {
    $query_sistemas = "
        SELECT s.id, s.nome, CASE WHEN cs.sistema_id IS NOT NULL THEN 1 ELSE 0 END as selecionado
        FROM sistemas s
        JOIN empresa_sistemas es ON s.id = es.sistema_id
        LEFT JOIN checklist_sistemas cs ON s.id = cs.sistema_id AND cs.checklist_id = ?
        WHERE es.empresa_id = ?
        ORDER BY s.nome ASC";
    
    $stmt = $conn->prepare($query_sistemas);
    $stmt->bind_param("ii", $row['id'], $row['empresa_id']);
    $stmt->execute();
    $result_sistemas = $stmt->get_result();
    while ($sistema = $result_sistemas->fetch_assoc()) {
        $sistemas_selecionados[$sistema['id']] = $sistema['selecionado'];
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Checklist</title>
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
        <h2>Editar Checklist</h2>
        <form method="POST">
            <input type="hidden" name="id" value="<?= $row['id']; ?>">
            <div class="section">
                <label>Dados do Cliente</label>
                <select name="empresa_id" id="empresa_id" onchange="buscarSistemas()" required>
                    <option value="">Selecione a empresa</option>
                    <?php while ($empresa = $result_empresas->fetch_assoc()) { ?>
                        <option value="<?= $empresa['id']; ?>" <?= ($empresa['id'] == $row['empresa_id']) ? 'selected' : ''; ?>><?= htmlspecialchars($empresa['nome']); ?></option>
                    <?php } ?>
                </select>
                <input type="date" name="data" value="<?= $row['data']; ?>" required>
                <input type="text" name="ticket" value="<?= $row['ticket']; ?>" placeholder="Ticket">
            </div>

            <div class="section">
                <label>Nome Pré-formatação</label>
                <input type="text" name="nome_pre_formatacao" value="<?= $row['nome_pre_formatacao']; ?>" placeholder="Equipamento">
                <label>Nome Pós-formatação</label>
                <input type="text" name="nome_pos_formatacao" value="<?= $row['nome_pos_formatacao']; ?>" placeholder="Equipamento">
            </div>

            <div class="section">
                <label>Usuário antigo</label>
                <input type="text" name="usuario_antigo" value="<?= $row['usuario_antigo']; ?>" placeholder="Usuário">
                <label>Usuário novo</label>
                <input type="text" name="usuario_novo" value="<?= $row['usuario_novo']; ?>" placeholder="Usuário">
            </div>

            <div class="section">
                <label>Procedimento inicial</label>
                <div class="checkbox-group">
                    <?php
                    $procedimentos = explode(', ', $row['procedimento_inicial']);
                    ?>
                    <label>
                        <input type="checkbox" name="procedimento_inicial[]" value="Etiquetar" 
                            <?= in_array('Etiquetar', $procedimentos) ? 'checked' : ''; ?>> Etiquetar
                    </label>
                    <label>
                        <input type="checkbox" name="procedimento_inicial[]" value="Limpeza interna"
                            <?= in_array('Limpeza interna', $procedimentos) ? 'checked' : ''; ?>> Limpeza interna
                    </label>
                    <label>
                        <input type="checkbox" name="procedimento_inicial[]" value="Limpeza externa"
                            <?= in_array('Limpeza externa', $procedimentos) ? 'checked' : ''; ?>> Limpeza externa
                    </label>
                </div>
            </div>

            <div class="section">
                <label>Sistema Operacional</label>
                <div class="checkbox-group">
                    <label>
                        <input type="radio" name="sistema_operacional" value="Windows 7" 
                            <?= $row['sistema_operacional'] == 'Windows 7' ? 'checked' : ''; ?> onclick="toggleOutroInput()"> Windows 7
                    </label>
                    <label>
                        <input type="radio" name="sistema_operacional" value="Windows 10"
                            <?= $row['sistema_operacional'] == 'Windows 10' ? 'checked' : ''; ?> onclick="toggleOutroInput()"> Windows 10
                    </label>
                    <label>
                        <input type="radio" name="sistema_operacional" value="Windows 11"
                            <?= $row['sistema_operacional'] == 'Windows 11' ? 'checked' : ''; ?> onclick="toggleOutroInput()"> Windows 11
                    </label>
                    <label>
                        <input type="radio" name="sistema_operacional" value="Outro" id="outroCheckbox"
                            <?= !in_array($row['sistema_operacional'], ['Windows 7', 'Windows 10', 'Windows 11']) ? 'checked' : ''; ?> onclick="toggleOutroInput()"> Outro:
                    </label>
                    <input type="text" name="outro_sistema" id="outroInput" 
                        value="<?= !in_array($row['sistema_operacional'], ['Windows 7', 'Windows 10', 'Windows 11']) ? $row['sistema_operacional'] : ''; ?>"
                        <?= !in_array($row['sistema_operacional'], ['Windows 7', 'Windows 10', 'Windows 11']) ? '' : 'disabled'; ?>>
                </div>
            </div>

            <div class="section">
                <label>Backup</label>
                <div class="checkbox-group">
                    <label>
                        <input type="radio" name="backup" value="Nao Realizado"
                            <?= $row['backup'] == 'Nao Realizado' ? 'checked' : ''; ?> onclick="toggleBackupInput()"> Não Realizado
                    </label>
                    <label>
                        <input type="radio" name="backup" value="Feito e Restaurado"
                            <?= $row['backup'] == 'Feito e Restaurado' ? 'checked' : ''; ?> onclick="toggleBackupInput()"> Feito e Restaurado
                    </label>
                    <label>
                        <input type="radio" name="backup" value="Feito" id="backupFeito"
                            <?= $row['backup'] == 'Feito' ? 'checked' : ''; ?> onclick="toggleBackupInput()"> Feito
                    </label>
                    <input type="text" name="local_salvamento" id="backupLocal" 
                        value="<?= $row['local_salvamento']; ?>" placeholder="Local de Salvamento"
                        <?= $row['backup'] == 'Feito' ? '' : 'disabled'; ?>>
                </div>
            </div>

            <div class="section">
                <label>Instalações</label>
                <div class="checkbox-group" id="instalacoes">
                    <?php if (!empty($sistemas_selecionados)): ?>
                        <ul>
                        <?php 
                        $stmt->execute();
                        $result_sistemas = $stmt->get_result();
                        while ($sistema = $result_sistemas->fetch_assoc()): 
                        ?>
                            <li>
                                <label>
                                    <input type="checkbox" name="sistemas[]" value="<?= $sistema['id']; ?>"
                                        <?= $sistema['selecionado'] ? 'checked' : ''; ?>>
                                    <?= htmlspecialchars($sistema['nome']); ?>
                                </label>
                            </li>
                        <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <p>Selecione uma empresa para ver os sistemas vinculados.</p>
                    <?php endif; ?>
                </div>
            </div>

            <button type="submit">Atualizar Checklist</button>
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

        // Initialize the form with correct states
        window.onload = function() {
            toggleOutroInput();
            toggleBackupInput();
        };
    </script>
</body>
</html>
