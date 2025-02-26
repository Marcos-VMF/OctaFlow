<?php
include '../bd/conexao.php';
include $_SERVER['DOCUMENT_ROOT'] . '/OctaFlow/navbar.php';

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Collect form data
    $data = $_POST['data'];
    $ticket = $_POST['ticket'];
    $equipamento = $_POST['equipamento'];
    $modelo = $_POST['modelo'];
    $acompanha_carregador = isset($_POST['acompanha_carregador']) ? 1 : 0;
    $nome_maquina = $_POST['nome_maquina'];
    $processador = $_POST['processador'];
    $memoria_ram = $_POST['memoria_ram'];
    $armazenamento_tipo = $_POST['armazenamento_tipo'];
    $capacidade_armazenamento = $_POST['capacidade_armazenamento'];
    $defeitos = $_POST['defeitos'];
    $servicos_realizados = $_POST['servicos_realizados'];
    $observacoes = $_POST['observacoes'];

    $allowed_armazenamento_tipos = ['hd', 'ssd', 'nenhum'];
    if (!in_array($_POST['armazenamento_tipo'], $allowed_armazenamento_tipos)) {
        die("Valor inválido para o tipo de armazenamento.");
    }

    // Update the checklist
    $query = "UPDATE checklists_manutencao 
              SET data = ?, ticket = ?, equipamento = ?, modelo = ?, acompanha_carregador = ?, nome_maquina = ?, 
                  processador = ?, memoria_ram = ?, armazenamento_tipo = ?, capacidade_armazenamento = ?, 
                  defeitos = ?, servicos_realizados = ?, observacoes = ? 
              WHERE id = ?";
    $stmt = $conn->prepare($query);
// Certifique-se de que $id seja um inteiro válido

// Agora passe as variáveis diretamente para bind_param()
$stmt->bind_param("ssssissssssssi", 
    $data, 
    $ticket, 
    $equipamento, 
    $modelo, 
    $acompanha_carregador, 
    $nome_maquina, 
    $processador, 
    $memoria_ram, 
    $armazenamento_tipo, 
    $capacidade_armazenamento, 
    $defeitos, 
    $servicos_realizados, 
    $observacoes, 
    $id // Agora está corretamente referenciado
);

    $stmt->execute();

    // Redirect back to the list
    header("Location: listar_checklists.php");
    exit;
}

// Fetch the checklist data
$query = "SELECT * FROM checklists_manutencao WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$checklist = $result->fetch_assoc();

if (!$checklist) {
    die("Checklist não encontrado.");
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
            margin: 0;
            padding: 0;
            color: var(--text-primary);
            line-height: 1.6;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
        }

        .card {
            background: var(--bg-secondary);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            padding: 20px;
        }

        h2 {
            text-align: center;
            color: var(--text-primary);
            margin-bottom: 30px;
            font-weight: 600;
            font-size: 1.5rem;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-secondary);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            background: var(--bg-tertiary);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            color: var(--text-primary);
            font-family: 'Montserrat', sans-serif;
            font-size: 0.9rem;
            transition: border-color 0.2s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--accent-primary);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            margin: 0;
            accent-color: var(--accent-primary);
        }

        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 30px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            font-family: 'Montserrat', sans-serif;
            transition: all 0.2s;
            min-width: 120px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(45deg, var(--accent-secondary), var(--accent-primary));
            color: white;
        }

        .btn-secondary {
            background: var(--bg-tertiary);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .btn:active {
            transform: translateY(0);
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
                margin: 20px auto;
            }

            .actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2>Editar Checklist</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="data">Data</label>
                    <input type="date" id="data" name="data" value="<?= htmlspecialchars($checklist['data']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="ticket">Ticket</label>
                    <input type="text" id="ticket" name="ticket" value="<?= htmlspecialchars($checklist['ticket']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="equipamento">Equipamento</label>
                    <select id="equipamento" name="equipamento" required onchange="toggleChargerField()">
                        <option value="computador" <?= $checklist['equipamento'] === 'computador' ? 'selected' : ''; ?>>Computador</option>
                        <option value="notebook" <?= $checklist['equipamento'] === 'notebook' ? 'selected' : ''; ?>>Notebook</option>
                        <option value="servidor" <?= $checklist['equipamento'] === 'servidor' ? 'selected' : ''; ?>>Servidor</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="modelo">Modelo</label>
                    <input type="text" id="modelo" name="modelo" value="<?= htmlspecialchars($checklist['modelo']); ?>">
                </div>
                <div class="form-group">
                    <div class="checkbox-group">
                        <input type="checkbox" id="acompanha_carregador" name="acompanha_carregador" <?= $checklist['acompanha_carregador'] ? 'checked' : ''; ?>>
                        <label for="acompanha_carregador">Acompanha Carregador</label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="nome_maquina">Nome da Máquina</label>
                    <input type="text" id="nome_maquina" name="nome_maquina" value="<?= htmlspecialchars($checklist['nome_maquina']); ?>">
                </div>
                <div class="form-group">
                    <label for="processador">Processador</label>
                    <input type="text" id="processador" name="processador" value="<?= htmlspecialchars($checklist['processador']); ?>">
                </div>
                <div class="form-group">
                    <label for="memoria_ram">Memória RAM (GB)</label>
                    <input type="number" id="memoria_ram" name="memoria_ram" value="<?= htmlspecialchars($checklist['memoria_ram']); ?>">
                </div>
                <div class="form-group">
                    <label for="armazenamento_tipo">Tipo de Armazenamento</label>
                    <select id="armazenamento_tipo" name="armazenamento_tipo" required>
                        <option value="hd" <?= $checklist['armazenamento_tipo'] === 'hd' ? 'selected' : ''; ?>>HD</option>
                        <option value="ssd" <?= $checklist['armazenamento_tipo'] === 'ssd' ? 'selected' : ''; ?>>SSD</option>
                        <option value="nenhum" <?= $checklist['armazenamento_tipo'] === 'nenhum' ? 'selected' : ''; ?>>Nenhum</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="capacidade_armazenamento">Capacidade de Armazenamento (GB)</label>
                    <input type="number" id="capacidade_armazenamento" name="capacidade_armazenamento" value="<?= htmlspecialchars($checklist['capacidade_armazenamento']); ?>">
                </div>
                <div class="form-group">
                    <label for="defeitos">Defeitos</label>
                    <textarea id="defeitos" name="defeitos"><?= htmlspecialchars($checklist['defeitos']); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="servicos_realizados">Serviços Realizados</label>
                    <textarea id="servicos_realizados" name="servicos_realizados"><?= htmlspecialchars($checklist['servicos_realizados']); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="observacoes">Observações</label>
                    <textarea id="observacoes" name="observacoes"><?= htmlspecialchars($checklist['observacoes']); ?></textarea>
                </div>
                <div class="actions">
                    <a href="listar_checklists.php" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        function toggleChargerField() {
            var equipamento = document.getElementById("equipamento").value;
            var carregadorField = document.getElementById("acompanha_carregador");

            if (equipamento === "notebook") {
                carregadorField.disabled = false;
            } else {
                carregadorField.disabled = true;
                carregadorField.checked = false;
            }
        }

        window.onload = function() {
            toggleChargerField(); // Initialize the state on page load
        };
    </script>
</body>
</html>