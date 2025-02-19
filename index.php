<?php
include 'php/bd/conexao.php';
include $_SERVER['DOCUMENT_ROOT'] . '/OctaFlow/navbar.php';

// Filtros
$empresa_id = isset($_GET['empresa_id']) ? $_GET['empresa_id'] : '';
$data = isset($_GET['data']) ? $_GET['data'] : '';
$busca = isset($_GET['busca']) ? $_GET['busca'] : '';

$query = "SELECT c.id, c.data, c.ticket, c.usuario_novo, e.nome AS empresa_nome FROM checklists c INNER JOIN empresas e ON c.empresa_id = e.id WHERE 1";

if ($empresa_id) {
    $query .= " AND c.empresa_id = '" . $conn->real_escape_string($empresa_id) . "'";
}
if ($data) {
    $query .= " AND c.data = '" . $conn->real_escape_string($data) . "'";
}
if ($busca) {
    $query .= " AND (c.usuario_novo LIKE '%" . $conn->real_escape_string($busca) . "%' OR c.ticket LIKE '%" . $conn->real_escape_string($busca) . "%')";
}

$query .= " ORDER BY c.data DESC";
$result = $conn->query($query);

// Buscar empresas para o filtro
$empresas = $conn->query("SELECT * FROM empresas ORDER BY nome ASC");
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>OctaFlow</title>
    <style>body {
            font-family: Arial, sans-serif;
            background-color: #eef2f3;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }
        th {
            background-color: #f4f4f4;
        }
        .btn {
            padding: 8px 12px;
            margin: 2px;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
            border: none;
        }
        .btn-view {
            background-color: #17a2b8;
            color: #fff;
        }
        .btn-edit {
            background-color: #ffc107;
            color: #fff;
        }
        .btn-delete {
            background-color: #dc3545;
            color: #fff;
        }</style>
  </head>
  <body>
    <div class="container">
    <h2>Checklists</h2>
    <form method="GET">
        <label>Empresa:</label>
        <select name="empresa_id">
            <option value="">Todas</option>
            <?php while ($empresa = $empresas->fetch_assoc()) { ?>
                <option value="<?= $empresa['id'] ?>" <?= ($empresa_id == $empresa['id']) ? 'selected' : '' ?>><?= htmlspecialchars($empresa['nome']) ?></option>
            <?php } ?>
        </select>
        <label>Data:</label>
        <input type="date" name="data" value="<?= htmlspecialchars($data) ?>">
        <label>Buscar:</label>
        <input type="text" name="busca" placeholder="Usuário ou Ticket" value="<?= htmlspecialchars($busca) ?>">
        <button type="submit">Filtrar</button>
    </form>

    <table>
        <tr>
            <th>Data</th>
            <th>Ticket</th>
            <th>Usuário Novo</th>
            <th>Empresa</th>
            <th>Ações</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($row['data']) ?></td>
                <td><?= htmlspecialchars($row['ticket']) ?></td>
                <td><?= htmlspecialchars($row['usuario_novo']) ?></td>
                <td><?= htmlspecialchars($row['empresa_nome']) ?></td>
                <td class="actions">
                    <a href="#" class="ver-checklist" data-id="<?= $row['id'] ?>">Ver</a>
                    <a href="php/checklists/editar_checklist.php?id=<?= $row['id'] ?>">Editar</a>
                    <a href="php/checklists/excluir_checklist.php?id=<?= $row['id'] ?>" onclick="return confirm('Tem certeza?')">Excluir</a>
                    <a href="php/checklists/exportar_pdf.php?id=<?= $row['id'] ?>">PDF</a>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>

<!-- Modal -->
<div id="modal" class="modal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <h3>Detalhes do Checklist</h3>
        <div id="modal-body">
            <p>Carregando...</p>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Função para abrir o modal e carregar os detalhes do checklist
        document.querySelectorAll('.ver-checklist').forEach(item => {
            item.addEventListener('click', function(event) {
                event.preventDefault();
                let checklistId = this.getAttribute('data-id');
                let modal = document.getElementById('modal');
                let modalBody = document.getElementById('modal-body');

                // Exibir o modal
                modal.style.display = "flex";
                modalBody.innerHTML = "<p>Carregando...</p>";

                // Fazer requisição AJAX para buscar os detalhes
                fetch("buscar_checklist.php?id=" + checklistId)
                    .then(response => response.text())
                    .then(data => {
                        modalBody.innerHTML = data;
                    })
                    .catch(error => {
                        modalBody.innerHTML = "<p>Erro ao carregar detalhes.</p>";
                    });
            });
        });

        // Fechar modal ao clicar no botão X
        document.querySelector('.close-modal').addEventListener('click', function() {
            document.getElementById('modal').style.display = "none";
        });

        // Fechar modal ao clicar fora da caixa de conteúdo
        window.onclick = function(event) {
            let modal = document.getElementById('modal');
            if (event.target === modal) {
                modal.style.display = "none";
            }
        };
    });
</script>
  </body>
</html>