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

        .details {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .detail-item {
            background: var(--bg-tertiary);
            padding: 12px;
            border-radius: 6px;
            border: 1px solid var(--border-color);
        }

        .detail-label {
            color: var(--text-secondary);
            font-size: 0.85rem;
            font-weight: 500;
            margin-bottom: 4px;
        }

        .detail-value {
            color: var(--text-primary);
            font-size: 0.95rem;
        }

        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-family: 'Montserrat', sans-serif;
            text-decoration: none;
            min-width: 100px;
            justify-content: center;
        }

        .btn-primary {
            background: linear-gradient(45deg, var(--accent-secondary), var(--accent-primary));
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .btn:active {
            transform: translateY(0);
        }

        .actions {
            display: flex;
            justify-content: center;
            margin-top: 30px;
            gap: 10px;
        }

        @media (max-width: 768px) {
            .details {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2>Detalhes do Checklist</h2>
            <div class="details">
                <div class="detail-item">
                    <div class="detail-label">Data</div>
                    <div class="detail-value"><?= htmlspecialchars($checklist['data']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Ticket</div>
                    <div class="detail-value"><?= htmlspecialchars($checklist['ticket']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Empresa</div>
                    <div class="detail-value"><?= htmlspecialchars($checklist['empresa']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Equipamento</div>
                    <div class="detail-value"><?= htmlspecialchars($checklist['equipamento']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Modelo</div>
                    <div class="detail-value"><?= htmlspecialchars($checklist['modelo']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Acompanha Carregador</div>
                    <div class="detail-value"><?= $checklist['acompanha_carregador'] ? 'Sim' : 'Não'; ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Nome da Máquina</div>
                    <div class="detail-value"><?= htmlspecialchars($checklist['nome_maquina']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Processador</div>
                    <div class="detail-value"><?= htmlspecialchars($checklist['processador']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Memória RAM</div>
                    <div class="detail-value"><?= htmlspecialchars($checklist['memoria_ram']); ?> GB</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Tipo de Armazenamento</div>
                    <div class="detail-value"><?= htmlspecialchars($checklist['armazenamento_tipo']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Capacidade de Armazenamento</div>
                    <div class="detail-value"><?= htmlspecialchars($checklist['capacidade_armazenamento']); ?> GB</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Defeitos</div>
                    <div class="detail-value"><?= htmlspecialchars($checklist['defeitos']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Serviços Realizados</div>
                    <div class="detail-value"><?= htmlspecialchars($checklist['servicos_realizados']); ?></div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Observações</div>
                    <div class="detail-value"><?= htmlspecialchars($checklist['observacoes']); ?></div>
                </div>
            </div>
            <div class="actions">
                <a href="listar_checklists.php" class="btn btn-primary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
            </div>
        </div>
    </div>
</body>
</html>