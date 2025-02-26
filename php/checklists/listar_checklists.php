<?php
include '../bd/conexao.php';
include $_SERVER['DOCUMENT_ROOT'] . '/OctaFlow/navbar.php';

// Get the filter parameters
$empresa_id = isset($_GET['empresa_id']) ? intval($_GET['empresa_id']) : null;
$data = isset($_GET['data']) ? $_GET['data'] : '';
$busca = isset($_GET['busca']) ? $_GET['busca'] : '';

// Build the base query
$query = "SELECT c.*, e.nome AS empresa_nome 
          FROM checklists c 
          INNER JOIN empresas e ON c.empresa_id = e.id 
          WHERE 1=1";

// Add filters
if ($empresa_id) {
    $query .= " AND c.empresa_id = $empresa_id";
}
if ($data) {
    $query .= " AND DATE(c.data) = '$data'";
}
if ($busca) {
    $query .= " AND (c.ticket LIKE '%$busca%' OR e.nome LIKE '%$busca%')";
}

// Order by date
$query .= " ORDER BY c.data DESC";

$result = $conn->query($query);
$empresas = $conn->query("SELECT * FROM empresas ORDER BY nome");
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Checklists</title>
    <link href='//fonts.googleapis.com/css?family=Montserrat:thin,extra-light,light,100,200,300,400,500,600,700,800' rel='stylesheet' type='text/css'>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
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
            max-width: 1200px;
            margin: 50px auto;
            padding: 20px;
        }

        .card {
            background: var(--bg-secondary);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .card-header {
            padding: 20px;
            background: var(--bg-tertiary);
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid var(--border-color);
        }

        h2 {
            margin: 0;
            font-weight: 600;
            color: var(--text-primary);
            font-size: 1.5rem;
        }

        .filters {
            display: flex;
            gap: 15px;
            padding: 20px;
            background: var(--bg-tertiary);
            border-bottom: 1px solid var(--border-color);
            flex-wrap: wrap;
            align-items: center;
        }

        input, select {
            padding: 8px 12px;
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: 5px;
            color: var(--text-primary);
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
            min-width: 200px;
        }

        input:focus, select:focus {
            outline: none;
            border-color: var(--accent-primary);
        }

        .table-responsive {
            overflow-x: auto;
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
        }

        th {
            background-color: var(--bg-tertiary);
            color: var(--text-primary);
            font-weight: 600;
        }

        tr:hover {
            background-color: var(--bg-tertiary);
        }

        .actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 6px 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 500;
            transition: opacity 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-family: 'Montserrat', sans-serif;
            text-decoration: none;
        }

        .btn i {
            font-size: 14px;
        }

        .btn-view {
            background-color: var(--success);
            color: white;
        }

        .btn-edit {
            background-color: var(--warning);
            color: black;
        }

        .btn-delete {
            background-color: var(--danger);
            color: white;
        }

        .btn-pdf {
            background-color: var(--info);
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .btn-new {
            background: linear-gradient(45deg, var(--accent-secondary), var(--accent-primary));
            color: white;
            padding: 8px 16px;
            font-size: 14px;
        }

        .filter-btn {
            background: linear-gradient(45deg, var(--accent-secondary), var(--accent-primary));
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .filter-btn:hover {
            opacity: 0.9;
        }

        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .modal-content {
            background: var(--bg-secondary);
            padding: 25px;
            border-radius: 10px;
            width: 90%;
            max-width: 600px;
            position: relative;
            max-height: 90vh;
            display: flex;
            flex-direction: column;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .modal-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
        }

        .modal-close {
            background: none;
            border: none;
            color: var(--text-secondary);
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            transition: background-color 0.2s;
        }

        .modal-close:hover {
            background-color: var(--bg-tertiary);
        }

        .modal-body {
            margin-bottom: 20px;
            overflow-y: auto;
            flex: 1;
            padding-right: 10px;
        }

        .modal-body::-webkit-scrollbar {
            width: 8px;
        }

        .modal-body::-webkit-scrollbar-track {
            background: var(--bg-tertiary);
            border-radius: 4px;
        }

        .modal-body::-webkit-scrollbar-thumb {
            background: var(--accent-primary);
            border-radius: 4px;
        }

        .modal-body::-webkit-scrollbar-thumb:hover {
            background: var(--accent-secondary);
        }

        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding-top: 15px;
            border-top: 1px solid var(--border-color);
        }

        .btn-cancel {
            background-color: var(--bg-tertiary);
            color: var(--text-primary);
        }

        .btn-confirm {
            background-color: var(--danger);
            color: white;
        }

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 15px;
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
            word-break: break-word;
        }

        .detail-value ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .detail-value ul li {
            position: relative;
            padding-left: 20px;
            margin-bottom: 4px;
        }

        .detail-value ul li:before {
            content: "•";
            position: absolute;
            left: 5px;
            color: var(--accent-primary);
        }

        .detail-value ul li:last-child {
            margin-bottom: 0;
        }

        @media (max-width: 768px) {
            .modal-content {
                width: 95%;
                margin: 20px;
                padding: 20px;
            }
            .detail-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Checklists</h2>
                <a href="checklist.php" class="btn btn-new">
                    <i class="fas fa-plus"></i> Nova Checklist
                </a>
            </div>

            <div class="filters">
                <form method="GET" class="filter-form">
                    <select name="empresa_id" id="empresa">
                        <option value="">Todas as Empresas</option>
                        <?php while ($empresa = $empresas->fetch_assoc()) { ?>
                            <option value="<?= $empresa['id'] ?>" <?= ($empresa_id == $empresa['id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($empresa['nome']) ?>
                            </option>
                        <?php } ?>
                    </select>
                    <input type="date" name="data" id="data" value="<?= htmlspecialchars($data) ?>">
                    <input type="text" name="busca" id="search" placeholder="Buscar por ticket..." value="<?= htmlspecialchars($busca) ?>">
                    <button type="submit" class="filter-btn">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="limparFiltros()">
                        <i class="fas fa-eraser"></i> Limpar Filtros
                    </button>
                </form>
            </div>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Ticket</th>
                            <th>Usuário Novo</th>
                            <th>Empresa</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($row['data'])) ?></td>
                            <td><?= htmlspecialchars($row['ticket']) ?></td>
                            <td><?= htmlspecialchars($row['usuario_novo']) ?></td>
                            <td><?= htmlspecialchars($row['empresa_nome']) ?></td>
                            <td class="actions">
                                <button onclick="viewChecklist(<?= $row['id'] ?>)" class="btn btn-view">
                                    <i class="fas fa-eye"></i> Ver
                                </button>
                                <a href="editar_checklist.php?id=<?= $row['id'] ?>" class="btn btn-edit">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                                <button onclick="confirmDelete(<?= $row['id'] ?>)" class="btn btn-delete">
                                    <i class="fas fa-trash"></i> Excluir
                                </button>
                                <a href="exportar_pdf.php?id=<?= $row['id'] ?>" class="btn btn-pdf">
                                    <i class="fas fa-file-pdf"></i> PDF
                                </a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- View Modal -->
    <div id="viewModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Detalhes do Checklist</h3>
                <button class="modal-close" onclick="closeViewModal()">&times;</button>
            </div>
            <div class="modal-body" id="checklistDetails">
                <!-- Content will be filled via JavaScript -->
            </div>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Confirmar Exclusão</h3>
                <button class="modal-close" onclick="closeDeleteModal()">&times;</button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir este checklist?</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-cancel" onclick="closeDeleteModal()">Cancelar</button>
                <button class="btn btn-confirm" onclick="executeDelete()">Excluir</button>
            </div>
        </div>
    </div>

    <script>
        let deleteId = null;

        function viewChecklist(id) {
            document.getElementById('viewModal').style.display = 'flex';
            fetch(`buscar_checklist.php?id=${id}`)
                .then(response => response.json())
                .then(checklist => {
                    // Format sistemas_instalados as a bulleted list
                    const sistemasHtml = checklist.sistemas_instalados ? 
                        `<ul>${checklist.sistemas_instalados.split(',').map(sistema => 
                            `<li>${sistema.trim()}</li>`
                        ).join('')}</ul>` : 
                        'Nenhum sistema registrado';

                    document.getElementById('checklistDetails').innerHTML = `
                        <div class="detail-grid">
                            <div class="detail-item">
                                <div class="detail-label">Data</div>
                                <div class="detail-value">${formatDate(checklist.data)}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Empresa</div>
                                <div class="detail-value">${checklist.empresa}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Ticket</div>
                                <div class="detail-value">${checklist.ticket || '-'}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Nome Pré-formatação</div>
                                <div class="detail-value">${checklist.nome_pre_formatacao || '-'}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Nome Pós-formatação</div>
                                <div class="detail-value">${checklist.nome_pos_formatacao || '-'}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Usuário Antigo</div>
                                <div class="detail-value">${checklist.usuario_antigo || '-'}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Usuário Novo</div>
                                <div class="detail-value">${checklist.usuario_novo || '-'}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Procedimento Inicial</div>
                                <div class="detail-value">${checklist.procedimento_inicial || '-'}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Sistema Operacional</div>
                                <div class="detail-value">${checklist.sistema_operacional || '-'}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Backup</div>
                                <div class="detail-value">${checklist.backup || '-'}</div>
                            </div>
                            ${checklist.backup === 'Feito' ? `
                                <div class="detail-item">
                                    <div class="detail-label">Local de Salvamento</div>
                                    <div class="detail-value">${checklist.local_salvamento || '-'}</div>
                                </div>
                            ` : ''}
                            <div class="detail-item">
                                <div class="detail-label">Sistemas Instalados</div>
                                <div class="detail-value">${sistemasHtml}</div>
                            </div>
                        </div>
                    `;
                })
                .catch(error => {
                    console.error('Erro:', error);
                    document.getElementById('checklistDetails').innerHTML = '<p>Erro ao carregar os detalhes do checklist.</p>';
                });
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('pt-BR');
        }

        function closeViewModal() {
            document.getElementById('viewModal').style.display = 'none';
        }

        function confirmDelete(id) {
            deleteId = id;
            document.getElementById('deleteModal').style.display = 'flex';
        }

        function closeDeleteModal() {
            deleteId = null;
            document.getElementById('deleteModal').style.display = 'none';
        }

        function executeDelete() {
            if (deleteId) {
                window.location.href = `excluir_checklist.php?id=${deleteId}`;
            }
        }

        function limparFiltros() {
            document.querySelector('select[name="empresa_id"]').value = '';
            document.querySelector('input[name="data"]').value = '';
            document.querySelector('input[name="busca"]').value = '';
            document.querySelector('.filter-form').submit();
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                event.target.style.display = 'none';
                if (event.target.id === 'deleteModal') {
                    deleteId = null;
                }
            }
        }
    </script>
</body>
</html>
