<?php
include 'php/bd/conexao.php';
include $_SERVER['DOCUMENT_ROOT'] . '/OctaFlow/navbar.php';

// Get total counts
$total_checklists = $conn->query("SELECT COUNT(*) as count FROM checklists")->fetch_assoc()['count'];
$total_empresas = $conn->query("SELECT COUNT(*) as count FROM empresas")->fetch_assoc()['count'];
$total_manutencoes = $conn->query("SELECT COUNT(*) as count FROM checklists_manutencao")->fetch_assoc()['count'];

// Get recent checklists
$recent_checklists = $conn->query("
    SELECT c.id, c.data, c.ticket, e.nome as empresa_nome 
    FROM checklists c 
    JOIN empresas e ON c.empresa_id = e.id 
    ORDER BY c.data DESC LIMIT 5
");

// Get recent maintenance
$recent_maintenance = $conn->query("
    SELECT cm.id, cm.data, cm.equipamento, e.nome as empresa_nome 
    FROM checklists_manutencao cm 
    JOIN empresas e ON cm.empresa_id = e.id 
    ORDER BY cm.data DESC LIMIT 5
");
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OctaFlow - Dashboard</title>
    <link href='//fonts.googleapis.com/css?family=Montserrat:thin,extra-light,light,100,200,300,400,500,600,700,800' rel='stylesheet' type='text/css'>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --bg-primary: #2f2f2f;
            --bg-secondary: #3f3f3f;
            --text-primary: #ffffff;
            --text-secondary: #e0e0e0;
            --accent-primary: #00c6ff;
            --border-color: #555;
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            margin: 0;
            padding: 0;
            font-weight: 400;
        }
        
        h1, h2, h3 {
            font-weight: 600;
            color: var(--text-primary);
        }

        h1 {
            font-size: 2rem;
            margin-bottom: 2rem;
        }

        h3 {
            font-size: 1.25rem;
            margin: 0;
        }
        
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background-color: var(--bg-secondary);
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }
        
        .stat-card {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .stat-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 1.5rem;
        }
        
        .stat-icon.pink {
            background: rgba(236, 72, 153, 0.1);
            color: #ec4899;
        }
        
        .stat-icon.blue {
            background: rgba(37, 99, 235, 0.1);
            color: #2563eb;
        }
        
        .stat-icon.purple {
            background: rgba(147, 51, 234, 0.1);
            color: #9333ea;
        }
        
        .stat-info {
            flex: 1;
        }
        
        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0;
            color: var(--text-primary);
        }
        
        .stat-label {
            color: var(--text-secondary);
            margin: 0;
            font-weight: 500;
            font-size: 1rem;
        }
        
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 1.5rem;
        }
        
        .recent-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .recent-item {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .recent-item:last-child {
            border-bottom: none;
        }
        
        .recent-item-info {
            flex: 1;
        }
        
        .recent-item-title {
            font-weight: 600;
            margin-bottom: 0.25rem;
            color: var(--text-primary);
        }
        
        .recent-item-subtitle {
            color: var(--text-secondary);
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .view-all {
            color: var(--accent-primary);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .view-all:hover {
            text-decoration: underline;
        }
        
        .btn-secondary {
            background-color: var(--accent-primary);
            color: var(--text-primary);
            border: none;
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            border-radius: 0.25rem;
            cursor: pointer;
            font-weight: 600;
        }
        
        .btn-secondary:hover {
            background-color: var(--accent-primary);
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mt-4 mb-4">Dashboard</h1>
        
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div class="stat-info">
                    <p class="stat-value"><?= $total_checklists ?></p>
                    <p class="stat-label">Checklists</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon pink">
                    <i class="fas fa-building"></i>
                </div>
                <div class="stat-info">
                    <p class="stat-value"><?= $total_empresas ?></p>
                    <p class="stat-label">Empresas</p>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="fas fa-wrench"></i>
                </div>
                <div class="stat-info">
                    <p class="stat-value"><?= $total_manutencoes ?></p>
                    <p class="stat-label">Manutenções</p>
                </div>
            </div>
        </div>
        
        <div class="dashboard-grid">
            <div class="card">
                <div class="card-header">
                    <h3>Checklists Recentes</h3>
                    <a href="/OctaFlow/php/checklists/listar_checklists.php" class="view-all">Ver todos</a>
                </div>
                <ul class="recent-list">
                    <?php while ($checklist = $recent_checklists->fetch_assoc()) { ?>
                        <li class="recent-item">
                            <div class="recent-item-info">
                                <div class="recent-item-title">
                                    <?= htmlspecialchars($checklist['empresa_nome']) ?>
                                </div>
                                <div class="recent-item-subtitle">
                                    Ticket: <?= htmlspecialchars($checklist['ticket']) ?> • 
                                    <?= date('d/m/Y', strtotime($checklist['data'])) ?>
                                </div>
                            </div>
                            <a href="/OctaFlow/php/checklists/buscar_checklist.php?id=<?= $checklist['id'] ?>" 
                               class="btn btn-secondary btn-sm">Ver</a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h3>Manutenções Recentes</h3>
                    <a href="/OctaFlow/php/manutencoes/listar_checklists.php" class="view-all">Ver todos</a>
                </div>
                <ul class="recent-list">
                    <?php while ($manutencao = $recent_maintenance->fetch_assoc()) { ?>
                        <li class="recent-item">
                            <div class="recent-item-info">
                                <div class="recent-item-title">
                                    <?= htmlspecialchars($manutencao['equipamento']) ?>
                                </div>
                                <div class="recent-item-subtitle">
                                    <?= htmlspecialchars($manutencao['empresa_nome']) ?> • 
                                    <?= date('d/m/Y', strtotime($manutencao['data'])) ?>
                                </div>
                            </div>
                            <a href="/OctaFlow/php/manutencoes/buscar_checklist.php?id=<?= $manutencao['id'] ?>" 
                               class="btn btn-secondary btn-sm">Ver</a>
                        </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </div>
</body>
</html>