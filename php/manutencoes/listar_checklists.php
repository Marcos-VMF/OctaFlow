<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listagem de Checklists</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .filters {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }
        input, select {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        .actions button {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .view { background-color: #28a745; color: white; }
        .edit { background-color: #ffc107; color: white; }
        .delete { background-color: #dc3545; color: white; }
        .pdf { background-color: #17a2b8; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Checklists de Manutenção</h2>
        <div class="filters">
            <select name="empresa" id="empresa">
                <option value="">Todas as Empresas</option>
                <?php 
                include '../bd/conexao.php';
                $query_empresas = "SELECT * FROM empresas ORDER BY nome ASC";
                $result_empresas = $conn->query($query_empresas);
                while ($empresa = $result_empresas->fetch_assoc()) { 
                ?>
                    <option value="<?= $empresa['id']; ?>"><?= htmlspecialchars($empresa['nome']); ?></option>
                <?php } ?>
            </select>
            <input type="date" id="data" name="data">
            <input type="text" id="search" placeholder="Buscar por Nome ou Ticket">
            <button onclick="filtrarChecklists()">Filtrar</button>
        </div>
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
                <?php 
                $query_checklists = "SELECT c.id, c.data, c.ticket, e.nome AS empresa, c.equipamento FROM checklists_manutencao c 
                                    JOIN empresas e ON c.empresa_id = e.id ORDER BY c.data DESC";
                $result_checklists = $conn->query($query_checklists);
                while ($checklist = $result_checklists->fetch_assoc()) { 
                ?>
                    <tr>
                        <td><?= htmlspecialchars($checklist['data']); ?></td>
                        <td><?= htmlspecialchars($checklist['ticket']); ?></td>
                        <td><?= htmlspecialchars($checklist['empresa']); ?></td>
                        <td><?= htmlspecialchars($checklist['equipamento']); ?></td>
                        <td class="actions">
                            <button class="view" onclick="verChecklist(<?= $checklist['id']; ?>)">Ver</button>
                            <button class="edit" onclick="editarChecklist(<?= $checklist['id']; ?>)">Editar</button>
                            <button class="delete" onclick="excluirChecklist(<?= $checklist['id']; ?>)">Excluir</button>
                            <button class="pdf" onclick="exportarPDF(<?= $checklist['id']; ?>)">PDF</button>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script>
        function filtrarChecklists() {
            var empresa = document.getElementById("empresa").value;
            var data = document.getElementById("data").value;
            var search = document.getElementById("search").value;
            window.location.href = `listar_checklists.php?empresa=${empresa}&data=${data}&search=${search}`;
        }

        function verChecklist(id) {
            window.location.href = `ver_checklist.php?id=${id}`;
        }

        function editarChecklist(id) {
            window.location.href = `editar_checklist.php?id=${id}`;
        }

        function excluirChecklist(id) {
            if (confirm("Tem certeza que deseja excluir este checklist?")) {
                window.location.href = `excluir_checklist.php?id=${id}`;
            }
        }

        function exportarPDF(id) {
            window.location.href = `exportar_pdf.php?id=${id}`;
        }
    </script>
</body>
</html>
