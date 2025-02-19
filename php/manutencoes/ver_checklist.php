<?php
include '../bd/conexao.php';
include $_SERVER['DOCUMENT_ROOT'] . '/OctaFlow/navbar.php';

$id = $_GET['id'];

$query = "SELECT cm.*, e.nome AS empresa 
          FROM checklists_manutencao cm 
          JOIN empresas e ON cm.empresa_id = e.id 
          WHERE cm.id = ?";
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
    <title>Ver Checklist</title>
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
        .details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        .details p {
            margin: 0;
            padding: 10px;
            background: #f9f9f9;
            border-radius: 8px;
            border: 1px solid #eee;
        }
        .details p strong {
            color: #555;
        }
        .back-button {
            display: block;
            width: 100%;
            text-align: center;
            margin-top: 20px;
        }
        .back-button button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .back-button button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Detalhes do Checklist</h2>
        <div class="details">
            <p><strong>Data:</strong> <?= htmlspecialchars($checklist['data']); ?></p>
            <p><strong>Ticket:</strong> <?= htmlspecialchars($checklist['ticket']); ?></p>
            <p><strong>Empresa:</strong> <?= htmlspecialchars($checklist['empresa']); ?></p>
            <p><strong>Equipamento:</strong> <?= htmlspecialchars($checklist['equipamento']); ?></p>
            <p><strong>Modelo:</strong> <?= htmlspecialchars($checklist['modelo']); ?></p>
            <p><strong>Acompanha Carregador:</strong> <?= $checklist['acompanha_carregador'] ? 'Sim' : 'Não'; ?></p>
            <p><strong>Nome da Máquina:</strong> <?= htmlspecialchars($checklist['nome_maquina']); ?></p>
            <p><strong>Processador:</strong> <?= htmlspecialchars($checklist['processador']); ?></p>
            <p><strong>Memória RAM:</strong> <?= htmlspecialchars($checklist['memoria_ram']); ?> GB</p>
            <p><strong>Tipo de Armazenamento:</strong> <?= htmlspecialchars($checklist['armazenamento_tipo']); ?></p>
            <p><strong>Capacidade de Armazenamento:</strong> <?= htmlspecialchars($checklist['capacidade_armazenamento']); ?> GB</p>
            <p><strong>Defeitos:</strong> <?= htmlspecialchars($checklist['defeitos']); ?></p>
            <p><strong>Serviços Realizados:</strong> <?= htmlspecialchars($checklist['servicos_realizados']); ?></p>
            <p><strong>Observações:</strong> <?= htmlspecialchars($checklist['observacoes']); ?></p>
        </div>
        <div class="back-button">
            <button onclick="window.location.href='listar_checklists.php'">Voltar</button>
        </div>
    </div>
</body>
</html>