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
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #555;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 14px;
        }
        .form-group textarea {
            resize: vertical;
            height: 100px;
        }
        .form-group button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
        }
        .form-group button:hover {
            background-color: #0056b3;
        }
        .form-group input[type="checkbox"] {
            width: auto;
        }
    </style>
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
</head>
<body>
    <div class="container">
        <h2>Editar Checklist</h2>
        <form method="POST">
            <div class="form-group">
                <label for="data">Data:</label>
                <input type="date" id="data" name="data" value="<?= htmlspecialchars($checklist['data']); ?>" required>
            </div>
            <div class="form-group">
                <label for="ticket">Ticket:</label>
                <input type="text" id="ticket" name="ticket" value="<?= htmlspecialchars($checklist['ticket']); ?>" required>
            </div>
            <div class="form-group">
                <label for="equipamento">Equipamento:</label>
                <select id="equipamento" name="equipamento" required onchange="toggleChargerField()">
                    <option value="computador" <?= $checklist['equipamento'] === 'computador' ? 'selected' : ''; ?>>Computador</option>
                    <option value="notebook" <?= $checklist['equipamento'] === 'notebook' ? 'selected' : ''; ?>>Notebook</option>
                    <option value="servidor" <?= $checklist['equipamento'] === 'servidor' ? 'selected' : ''; ?>>Servidor</option>
                </select>
            </div>
            <div class="form-group">
                <label for="modelo">Modelo:</label>
                <input type="text" id="modelo" name="modelo" value="<?= htmlspecialchars($checklist['modelo']); ?>">
            </div>
            <div class="form-group">
                <label for="acompanha_carregador">Acompanha Carregador:</label>
                <input type="checkbox" id="acompanha_carregador" name="acompanha_carregador" <?= $checklist['acompanha_carregador'] ? 'checked' : ''; ?>>
            </div>
            <div class="form-group">
                <label for="nome_maquina">Nome da Máquina:</label>
                <input type="text" id="nome_maquina" name="nome_maquina" value="<?= htmlspecialchars($checklist['nome_maquina']); ?>">
            </div>
            <div class="form-group">
                <label for="processador">Processador:</label>
                <input type="text" id="processador" name="processador" value="<?= htmlspecialchars($checklist['processador']); ?>">
            </div>
            <div class="form-group">
                <label for="memoria_ram">Memória RAM (GB):</label>
                <input type="number" id="memoria_ram" name="memoria_ram" value="<?= htmlspecialchars($checklist['memoria_ram']); ?>">
            </div>
            <div class="form-group">
                <label for="armazenamento_tipo">Tipo de Armazenamento:</label>
                <select id="armazenamento_tipo" name="armazenamento_tipo" required>
                    <option value="hd" <?= $checklist['armazenamento_tipo'] === 'hd' ? 'selected' : ''; ?>>HD</option>
                    <option value="ssd" <?= $checklist['armazenamento_tipo'] === 'ssd' ? 'selected' : ''; ?>>SSD</option>
                    <option value="nenhum" <?= $checklist['armazenamento_tipo'] === 'nenhum' ? 'selected' : ''; ?>>Nenhum</option>
                </select>
            </div>
            <div class="form-group">
                <label for="capacidade_armazenamento">Capacidade de Armazenamento (GB):</label>
                <input type="number" id="capacidade_armazenamento" name="capacidade_armazenamento" value="<?= htmlspecialchars($checklist['capacidade_armazenamento']); ?>">
            </div>
            <div class="form-group">
                <label for="defeitos">Defeitos:</label>
                <textarea id="defeitos" name="defeitos"><?= htmlspecialchars($checklist['defeitos']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="servicos_realizados">Serviços Realizados:</label>
                <textarea id="servicos_realizados" name="servicos_realizados"><?= htmlspecialchars($checklist['servicos_realizados']); ?></textarea>
            </div>
            <div class="form-group">
                <label for="observacoes">Observações:</label>
                <textarea id="observacoes" name="observacoes"><?= htmlspecialchars($checklist['observacoes']); ?></textarea>
            </div>
            <div class="form-group">
                <button type="submit">Salvar</button>
            </div>
        </form>
    </div>
</body>
</html>