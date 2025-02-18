<?php
include '../bd/conexao.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $query = "SELECT cm.*, e.nome AS empresa_nome FROM checklists_manutencao cm
              JOIN empresas e ON cm.empresa_id = e.id
              WHERE cm.id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $checklist = $result->fetch_assoc();
    } else {
        echo "<p>Checklist não encontrada.</p>";
        exit;
    }
} else {
    echo "<p>ID não fornecido.</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visualizar Checklist</title>
    <style>
        .modal {
            display: flex;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            width: 600px;
            max-width: 90%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .close-btn {
            background: red;
            color: white;
            border: none;
            padding: 10px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="modal">
        <div class="modal-content">
            <h2>Checklist de Manutenção</h2>
            <p><strong>Empresa:</strong> <?= htmlspecialchars($checklist['empresa_nome']); ?></p>
            <p><strong>Data:</strong> <?= htmlspecialchars($checklist['data']); ?></p>
            <p><strong>Ticket:</strong> <?= htmlspecialchars($checklist['ticket']); ?></p>
            <p><strong>Equipamento:</strong> <?= htmlspecialchars($checklist['equipamento']); ?></p>
            <p><strong>Modelo:</strong> <?= htmlspecialchars($checklist['modelo'] ?: 'N/A'); ?></p>
            <p><strong>Acompanha Carregador:</strong> <?= $checklist['acompanha_carregador'] ? 'Sim' : 'Não'; ?></p>
            <p><strong>Nome da Máquina:</strong> <?= htmlspecialchars($checklist['nome_maquina'] ?: 'N/A'); ?></p>
            <p><strong>Sem Nome:</strong> <?= $checklist['nao_tem_nome'] ? 'Sim' : 'Não'; ?></p>
            <p><strong>Processador:</strong> <?= htmlspecialchars($checklist['processador'] ?: 'N/A'); ?></p>
            <p><strong>Memória RAM:</strong> <?= htmlspecialchars($checklist['memoria_ram'] ? $checklist['memoria_ram'] . ' GB' : 'N/A'); ?></p>
            <p><strong>Armazenamento:</strong> <?= htmlspecialchars($checklist['armazenamento_tipo'] ?: 'N/A'); ?></p>
            <p><strong>Capacidade:</strong> <?= htmlspecialchars($checklist['capacidade_armazenamento'] ? $checklist['capacidade_armazenamento'] . ' GB' : 'N/A'); ?></p>
            <p><strong>Defeitos:</strong> <?= nl2br(htmlspecialchars($checklist['defeitos'] ?: 'N/A')); ?></p>
            <p><strong>Serviços Realizados:</strong> <?= nl2br(htmlspecialchars($checklist['servicos_realizados'] ?: 'N/A')); ?></p>
            <p><strong>Observações:</strong> <?= nl2br(htmlspecialchars($checklist['observacoes'] ?: 'N/A')); ?></p>
            <button class="close-btn" onclick="window.history.back();">Fechar</button>
        </div>
    </div>
</body>
</html>
