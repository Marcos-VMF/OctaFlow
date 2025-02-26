<?php
    include '../bd/conexao.php';
    include $_SERVER['DOCUMENT_ROOT'] . '/OctaFlow/navbar.php';

    // Captura os parâmetros de filtro da URL
    $empresa_id = isset($_GET['empresa']) ? intval($_GET['empresa']) : null;
    $data = isset($_GET['data']) ? $_GET['data'] : null;
    $search = isset($_GET['search']) ? $_GET['search'] : null;

    // Construir a consulta base
    $query_checklists = "SELECT c.id, c.data, c.ticket, e.nome AS empresa, c.equipamento 
                        FROM checklists_manutencao c 
                        JOIN empresas e ON c.empresa_id = e.id 
                        WHERE 1=1";

    // Adicionar filtros à consulta
    if ($empresa_id) {
        $query_checklists .= " AND c.empresa_id = $empresa_id";
    }
    if ($data) {
        $query_checklists .= " AND c.data = '$data'";
    }
    if ($search) {
        $query_checklists .= " AND (c.ticket LIKE '%$search%' OR e.nome LIKE '%$search%')";
    }

    // Ordenar os resultados
    $query_checklists .= " ORDER BY c.data DESC";

    // Executar a consulta
    $result_checklists = $conn->query($query_checklists);

    // Buscar empresas para o filtro
    $empresas = $conn->query("SELECT * FROM empresas ORDER BY nome ASC");
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

        .btn-new {
            background: linear-gradient(45deg, var(--accent-secondary), var(--accent-primary));
            color: white;
            padding: 8px 16px;
            font-size: 14px;
        }

        .btn:hover {
            opacity: 0.9;
        }

        .filter-btn {
            background: linear-gradient(45deg, var(--accent-secondary), var(--accent-primary));
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
        }

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

        @media (max-width: 768px) {
            .container {
                margin: 20px auto;
                padding: 15px;
            }

            .filters {
                flex-direction: column;
            }

            input, select {
                width: 100%;
            }

            .actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
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
                <h2>Checklists de Manutenção</h2>
                <a href="form_manutencao.php" class="btn btn-new">
                    <i class="fas fa-plus"></i> Nova Checklist
                </a>
            </div>
            <div class="filters">
                <form method="GET" id="filterForm">
                    <div class="filter-group">
                        <select id="empresa" name="empresa">
                            <option value="">Todas as empresas</option>
                            <?php while ($empresa = $empresas->fetch_assoc()): ?>
                                <option value="<?= $empresa['id'] ?>" <?= $empresa_id == $empresa['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($empresa['nome']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <input type="date" id="data" name="data" value="<?= $data ?>">
                        <input type="text" id="search" name="search" placeholder="Buscar por ticket ou empresa..." value="<?= $search ?>">
                        <button type="submit" class="filter-btn">
                            <i class="fas fa-search"></i> Filtrar
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="limparFiltros()">
                            <i class="fas fa-eraser"></i> Limpar Filtros
                        </button>
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Ticket</th>
                            <th>Empresa</th>
                            <th>Equipamento</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($checklist = $result_checklists->fetch_assoc()): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($checklist['data'])) ?></td>
                                <td><?= htmlspecialchars($checklist['ticket']) ?></td>
                                <td><?= htmlspecialchars($checklist['empresa']) ?></td>
                                <td><?= htmlspecialchars($checklist['equipamento']) ?></td>
                                <td class="actions">
                                    <button onclick="viewChecklist(<?= $checklist['id'] ?>)" class="btn btn-view">
                                        <i class="fas fa-eye"></i> Ver
                                    </button>
                                    <a href="editar_checklist.php?id=<?= $checklist['id'] ?>" class="btn btn-edit">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <a href="exportar_pdf.php?id=<?= $checklist['id'] ?>" class="btn btn-pdf">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </a>
                                    <button onclick="showDeleteModal(<?= $checklist['id'] ?>)" class="btn btn-delete">
                                        <i class="fas fa-trash"></i> Excluir
                                    </button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal de Visualização -->
    <div id="viewModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Detalhes do Checklist</h3>
                <button class="modal-close" onclick="closeViewModal()">&times;</button>
            </div>
            <div class="modal-body" id="viewModalContent">
                <!-- Conteúdo será preenchido via JavaScript -->
            </div>
        </div>
    </div>

    <!-- Modal de Exclusão -->
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
                <button class="btn btn-confirm" onclick="deleteChecklist()">Confirmar</button>
            </div>
        </div>
    </div>

    <script>
        let checklistIdToDelete = null;

        function filtrarChecklists() {
            var empresa = document.getElementById("empresa").value;
            var data = document.getElementById("data").value;
            var search = document.getElementById("search").value;
            window.location.href = `listar_checklists.php?empresa=${empresa}&data=${data}&search=${search}`;
        }

        function viewChecklist(id) {
            document.getElementById('viewModal').style.display = 'flex';
            fetch(`get_checklist_details.php?id=${id}`)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('viewModalContent').innerHTML = data;
                })
                .catch(error => {
                    console.error('Erro:', error);
                    document.getElementById('viewModalContent').innerHTML = 'Erro ao carregar os detalhes.';
                });
        }

        function closeViewModal() {
            document.getElementById('viewModal').style.display = 'none';
        }

        function showDeleteModal(id) {
            checklistIdToDelete = id;
            document.getElementById('deleteModal').style.display = 'flex';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
            checklistIdToDelete = null;
        }

        function deleteChecklist() {
            if (checklistIdToDelete) {
                fetch(`excluir_checklist.php?id=${checklistIdToDelete}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            window.location.reload();
                        } else {
                            alert('Erro ao excluir o checklist.');
                        }
                    })
                    .catch(error => {
                        console.error('Erro:', error);
                        alert('Erro ao excluir o checklist.');
                    });
            }
        }

        function limparFiltros() {
            document.getElementById('empresa').value = '';
            document.getElementById('data').value = '';
            document.getElementById('search').value = '';
            document.getElementById('filterForm').submit();
        }

        // Fechar modais quando clicar fora
        window.onclick = function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                event.target.style.display = 'none';
                if (event.target.id === 'deleteModal') {
                    checklistIdToDelete = null;
                }
            }
        }
    </script>
</body>
</html>
