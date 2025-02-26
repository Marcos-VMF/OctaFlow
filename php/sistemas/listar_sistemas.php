<?php
include '../bd/conexao.php';
include $_SERVER['DOCUMENT_ROOT'] . '/OctaFlow/navbar.php';

// Buscar todos os sistemas cadastrados
$query = "SELECT * FROM sistemas ORDER BY id ASC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Sistemas</title>
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
            --danger-color: #dc3545;
            --warning-color: #ffc107;
        }
        
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            margin: 0;
            padding: 0;
            font-weight: 400;
        }
        
        .container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
        }
        
        h2 {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 2rem;
            font-size: 1.75rem;
        }

        .card {
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 0.5rem;
            padding: 2rem;
            margin-bottom: 2rem;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .btn-add {
            background-color: var(--accent-primary);
            color: var(--text-primary);
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: opacity 0.2s;
        }

        .btn-add:hover {
            opacity: 0.9;
        }
        
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 1rem;
        }
        
        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-primary);
        }
        
        th {
            font-weight: 600;
            color: var(--text-secondary);
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        td {
            font-weight: 500;
        }
        
        tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }
        
        .actions {
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
        }
        
        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.75rem;
            display: inline-flex;
            align-items: center;
            gap: 0.375rem;
            transition: opacity 0.2s;
        }
        
        .btn:hover {
            opacity: 0.9;
        }
        
        .btn-view {
            background-color: var(--accent-primary);
            color: var(--text-primary);
        }
        
        .btn-edit {
            background-color: var(--warning-color);
            color: #000;
        }
        
        .btn-delete {
            background-color: var(--danger-color);
            color: var(--text-primary);
        }

        .empty-state {
            text-align: center;
            padding: 3rem 0;
            color: var(--text-secondary);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-state p {
            font-size: 1rem;
            font-weight: 500;
            margin: 0;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            backdrop-filter: blur(4px);
        }

        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: var(--bg-secondary);
            border-radius: 0.5rem;
            padding: 2rem;
            width: 90%;
            max-width: 600px;
            position: relative;
            border: 1px solid var(--border-color);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        .modal-title {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
            margin: 0;
        }

        .close-modal {
            background: none;
            border: none;
            color: var(--text-secondary);
            font-size: 1.5rem;
            cursor: pointer;
            padding: 0.5rem;
            line-height: 1;
            transition: color 0.2s;
        }

        .close-modal:hover {
            color: var(--text-primary);
        }

        .modal-body {
            max-height: 60vh;
            overflow-y: auto;
        }

        .company-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .company-item {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-primary);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .company-item:last-child {
            border-bottom: none;
        }

        .company-icon {
            color: var(--accent-primary);
            font-size: 1.25rem;
        }

        /* Loading Animation */
        .loading {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            color: var(--text-secondary);
        }

        .loading-spinner {
            border: 3px solid var(--border-color);
            border-top: 3px solid var(--accent-primary);
            border-radius: 50%;
            width: 24px;
            height: 24px;
            animation: spin 1s linear infinite;
            margin-right: 1rem;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: 0.375rem;
            color: var(--text-primary);
            font-family: 'Montserrat', sans-serif;
            font-size: 0.875rem;
            transition: all 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 2px rgba(0, 198, 255, 0.2);
        }

        .form-control::placeholder {
            color: var(--text-secondary);
        }

        .modal-footer {
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-color);
            display: flex;
            justify-content: flex-end;
            gap: 1rem;
        }

        .btn-modal {
            padding: 0.75rem 1.5rem;
            border-radius: 0.375rem;
            font-weight: 600;
            font-size: 0.875rem;
            border: none;
            cursor: pointer;
            transition: opacity 0.2s;
        }

        .btn-modal.primary {
            background: var(--accent-primary);
            color: var(--text-primary);
        }

        .btn-modal.secondary {
            background: var(--bg-primary);
            color: var(--text-secondary);
            border: 1px solid var(--border-color);
        }

        .btn-modal:hover {
            opacity: 0.9;
        }

        .alert {
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
            font-weight: 500;
            font-size: 0.875rem;
        }

        .alert-success {
            background: rgba(5, 150, 105, 0.1);
            color: #10b981;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .alert-error {
            background: rgba(220, 38, 38, 0.1);
            color: #ef4444;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Sistemas Cadastrados</h2>
                <a href="cadastrar_sistema.php" class="btn-add">
                    <i class="fas fa-plus"></i>
                    Novo Sistema
                </a>
            </div>
            
            <?php if ($result->num_rows > 0) { ?>
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nome do Sistema</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($sistema = $result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?= $sistema['id']; ?></td>
                                    <td><?= htmlspecialchars($sistema['nome']); ?></td>
                                    <td class="actions">
                                        <button onclick="viewCompanies(<?= $sistema['id']; ?>, '<?= htmlspecialchars($sistema['nome']); ?>')" class="btn btn-view">
                                            <i class="fas fa-building"></i>
                                            Empresas
                                        </button>
                                        <button onclick="editSystem(<?= $sistema['id']; ?>)" class="btn btn-edit">
                                            <i class="fas fa-edit"></i>
                                            Editar
                                        </button>
                                        <a href="excluir_sistema.php?id=<?= $sistema['id']; ?>" class="btn btn-delete" 
                                           onclick="return confirm('Tem certeza que deseja excluir este sistema?');">
                                            <i class="fas fa-trash"></i>
                                            Excluir
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } else { ?>
                <div class="empty-state">
                    <i class="fas fa-desktop"></i>
                    <p>Nenhum sistema cadastrado</p>
                </div>
            <?php } ?>
        </div>
    </div>

    <!-- Companies Modal -->
    <div id="companiesModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Empresas Vinculadas</h3>
                <button class="close-modal" onclick="closeModal('companiesModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div id="companiesModalContent"></div>
            </div>
        </div>
    </div>

    <!-- Edit System Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Editar Sistema</h3>
                <button class="close-modal" onclick="closeModal('editModal')">&times;</button>
            </div>
            <div class="modal-body">
                <div id="editModalContent"></div>
            </div>
        </div>
    </div>

    <script>
        function viewCompanies(systemId, systemName) {
            const modal = document.getElementById('companiesModal');
            const modalContent = document.getElementById('companiesModalContent');
            
            showModal('companiesModal');
            
            modalContent.innerHTML = `
                <div class="loading">
                    <div class="loading-spinner"></div>
                    <span>Carregando empresas...</span>
                </div>
            `;
            
            document.querySelector('#companiesModal .modal-title').textContent = `Empresas Vinculadas ao Sistema: ${systemName}`;
            
            fetch(`visualizar_empresas.php?id=${systemId}&ajax=true`)
                .then(response => response.text())
                .then(html => {
                    modalContent.innerHTML = html;
                })
                .catch(error => {
                    modalContent.innerHTML = `
                        <div class="empty-state">
                            <i class="fas fa-exclamation-circle"></i>
                            <p>Erro ao carregar empresas</p>
                        </div>
                    `;
                });
        }
        
        function editSystem(systemId) {
            const modal = document.getElementById('editModal');
            const modalContent = document.getElementById('editModalContent');
            
            showModal('editModal');
            
            modalContent.innerHTML = `
                <div class="loading">
                    <div class="loading-spinner"></div>
                    <span>Carregando dados do sistema...</span>
                </div>
            `;
            
            fetch(`editar_sistema.php?id=${systemId}&ajax=true`)
                .then(response => response.text())
                .then(html => {
                    modalContent.innerHTML = html;
                })
                .catch(error => {
                    modalContent.innerHTML = `
                        <div class="empty-state">
                            <i class="fas fa-exclamation-circle"></i>
                            <p>Erro ao carregar dados do sistema</p>
                        </div>
                    `;
                });
        }
        
        function showModal(modalId) {
            document.getElementById(modalId).classList.add('show');
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('show');
        }
        
        // Close modals when clicking outside
        document.querySelectorAll('.modal').forEach(modal => {
            modal.addEventListener('click', function(event) {
                if (event.target === this) {
                    closeModal(this.id);
                }
            });
        });

        // Handle form submission
        function submitEditForm(event, systemId) {
            event.preventDefault();
            const form = event.target;
            const formData = new FormData(form);
            
            fetch(`editar_sistema.php?id=${systemId}&ajax=true`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(result => {
                try {
                    const data = JSON.parse(result);
                    if (data.success) {
                        window.location.reload();
                    } else {
                        throw new Error(data.message || 'Erro ao atualizar sistema');
                    }
                } catch (e) {
                    const errorDiv = document.createElement('div');
                    errorDiv.className = 'alert alert-error';
                    errorDiv.textContent = e.message;
                    form.insertBefore(errorDiv, form.firstChild);
                }
            })
            .catch(error => {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-error';
                errorDiv.textContent = 'Erro ao processar a requisição';
                form.insertBefore(errorDiv, form.firstChild);
            });
        }
    </script>
</body>
</html>
