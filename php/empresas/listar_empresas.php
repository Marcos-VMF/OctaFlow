<?php
include '../bd/conexao.php';
include $_SERVER['DOCUMENT_ROOT'] . '/OctaFlow/navbar.php';

// Buscar todas as empresas
$query = "SELECT * FROM empresas ORDER BY nome ASC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Empresas</title>
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
            color: var(--text-primary);
            margin: 0;
            padding: 0;
            line-height: 1.6;
        }

        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            position: relative;
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

        .btn-add {
            background: linear-gradient(45deg, var(--accent-secondary), var(--accent-primary));
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 500;
            font-family: 'Montserrat', sans-serif;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: opacity 0.2s;
        }

        .btn-add:hover {
            opacity: 0.9;
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
            justify-content: center;
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

        .btn i {
            font-size: 14px;
        }

        .btn-edit {
            background-color: var(--warning);
            color: black;
        }

        .btn-delete {
            background-color: var(--danger);
            color: white;
        }

        .btn-delete:hover {
            background-color: #ff4444;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .btn:active {
            transform: translateY(0);
        }

        td:last-child {
            text-align: center;
        }

        th:last-child {
            text-align: center;
        }

        .search-box {
            padding: 20px;
            background: var(--bg-tertiary);
            border-bottom: 1px solid var(--border-color);
        }

        .search-input {
            width: 100%;
            max-width: 300px;
            padding: 8px 12px;
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: 5px;
            color: var(--text-primary);
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
        }

        .search-input:focus {
            outline: none;
            border-color: var(--accent-primary);
        }

        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: var(--text-secondary);
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            display: block;
        }

        .empty-state p {
            margin: 0;
            font-size: 16px;
        }

        /* Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.3);
            z-index: 1000;
        }

        .modal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: #1a1a1a;
            width: 260px;
            height: 150px;
            padding: 12px;
            border-radius: 4px;
            z-index: 1001;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        .modal-content {
            display: flex;
            flex-direction: column;
            gap: 10px;
            background-color: var(--bg-primary);
        }

        .modal-title {
            color: #fff;
            font-size: 14px;
            text-align: center;
        }

        .modal-buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 5px;
        }

        .modal-btn {
            padding: 5px 15px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 13px;
            min-width: 70px;
        }

        .modal-btn-confirm {
            background: #dc3545;
            color: white;
        }

        .modal-btn-cancel {
            background: #333;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h2>Empresas Cadastradas</h2>
                <a href="formulario_empresa.php" class="btn-add">
                    <i class="fas fa-plus"></i> Nova Empresa
                </a>
            </div>

            <div class="search-box">
                <input type="text" id="searchInput" class="search-input" placeholder="Buscar empresa..." onkeyup="searchTable()">
            </div>

            <div class="table-responsive">
                <table id="empresasTable">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) { 
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($row['nome']) ?></td>
                                <td class="actions">
                                    <a href="editar_empresa.php?id=<?= $row['id'] ?>" class="btn btn-edit">
                                        <i class="fas fa-edit"></i> Editar
                                    </a>
                                    <button onclick="showDeleteModal(<?= $row['id'] ?>)" class="btn btn-delete">
                                        <i class="fas fa-trash"></i> Excluir
                                    </button>
                                </td>
                            </tr>
                        <?php 
                            }
                        } else {
                        ?>
                            <tr>
                                <td colspan="2">
                                    <div class="empty-state">
                                        <i class="fas fa-building"></i>
                                        <p>Nenhuma empresa cadastrada</p>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
            <!-- Add Modal HTML -->
            <div class="modal-overlay" id="deleteModal">
                <div class="modal">
                    <div class="modal-content">
                        <div class="modal-title">Tem certeza que deseja excluir essa empresa?</div>
                        <div class="modal-buttons">
                            <button class="modal-btn modal-btn-cancel" onclick="closeDeleteModal()">Cancelar</button>
                            <button class="modal-btn modal-btn-confirm" onclick="confirmDelete()">Excluir</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function searchTable() {
            var input, filter, table, tr, td, i, txtValue;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            table = document.getElementById("empresasTable");
            tr = table.getElementsByTagName("tr");

            for (i = 0; i < tr.length; i++) {
                td = tr[i].getElementsByTagName("td")[0]; // Search first column (Nome)
                if (td) {
                    txtValue = td.textContent || td.innerText;
                    if (txtValue.toUpperCase().indexOf(filter) > -1) {
                        tr[i].style.display = "";
                    } else {
                        tr[i].style.display = "none";
                    }
                }
            }
        }

        let deleteId = null;
        
        function showDeleteModal(id) {
            deleteId = id;
            document.getElementById('deleteModal').style.display = 'block';
            document.querySelector('.modal').style.display = 'block';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
            document.querySelector('.modal').style.display = 'none';
            deleteId = null;
        }

        function confirmDelete() {
            if (deleteId) {
                window.location.href = `excluir_empresa.php?id=${deleteId}`;
            }
        }

        // Close modal when clicking outside
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDeleteModal();
            }
        });
    </script>
</body>
</html>
