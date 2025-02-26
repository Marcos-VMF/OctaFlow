<?php
include '../bd/conexao.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo '<p class="error">ID inválido</p>';
    exit;
}

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
    echo '<p class="error">Checklist não encontrado</p>';
    exit;
}

?>

<div class="detail-grid">
    <div class="detail-item">
        <div class="detail-label">Data</div>
        <div class="detail-value"><?= date('d/m/Y', strtotime($checklist['data'])) ?></div>
    </div>
    <div class="detail-item">
        <div class="detail-label">Ticket</div>
        <div class="detail-value"><?= htmlspecialchars($checklist['ticket']) ?></div>
    </div>
    <div class="detail-item">
        <div class="detail-label">Empresa</div>
        <div class="detail-value"><?= htmlspecialchars($checklist['empresa']) ?></div>
    </div>
    <div class="detail-item">
        <div class="detail-label">Equipamento</div>
        <div class="detail-value"><?= htmlspecialchars($checklist['equipamento']) ?></div>
    </div>
    <div class="detail-item">
        <div class="detail-label">Modelo</div>
        <div class="detail-value"><?= htmlspecialchars($checklist['modelo']) ?></div>
    </div>
    <div class="detail-item">
        <div class="detail-label">Acompanha Carregador</div>
        <div class="detail-value"><?= $checklist['acompanha_carregador'] ? 'Sim' : 'Não' ?></div>
    </div>
    <div class="detail-item">
        <div class="detail-label">Nome da Máquina</div>
        <div class="detail-value"><?= htmlspecialchars($checklist['nome_maquina']) ?></div>
    </div>
    <div class="detail-item">
        <div class="detail-label">Processador</div>
        <div class="detail-value"><?= htmlspecialchars($checklist['processador']) ?></div>
    </div>
    <div class="detail-item">
        <div class="detail-label">Memória RAM</div>
        <div class="detail-value"><?= htmlspecialchars($checklist['memoria_ram']) ?> GB</div>
    </div>
    <div class="detail-item">
        <div class="detail-label">Tipo de Armazenamento</div>
        <div class="detail-value"><?= htmlspecialchars($checklist['armazenamento_tipo']) ?></div>
    </div>
    <div class="detail-item">
        <div class="detail-label">Capacidade de Armazenamento</div>
        <div class="detail-value"><?= htmlspecialchars($checklist['capacidade_armazenamento']) ?> GB</div>
    </div>
    <div class="detail-item">
        <div class="detail-label">Defeitos</div>
        <div class="detail-value"><?= nl2br(htmlspecialchars($checklist['defeitos'])) ?></div>
    </div>
    <div class="detail-item">
        <div class="detail-label">Serviços Realizados</div>
        <div class="detail-value"><?= nl2br(htmlspecialchars($checklist['servicos_realizados'])) ?></div>
    </div>
    <div class="detail-item">
        <div class="detail-label">Observações</div>
        <div class="detail-value"><?= nl2br(htmlspecialchars($checklist['observacoes'])) ?></div>
    </div>
</div>
