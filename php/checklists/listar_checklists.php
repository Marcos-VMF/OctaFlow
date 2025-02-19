<?php
include '../bd/conexao.php';
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

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Listar Checklists</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #eef2f3; }
        .container { max-width: 900px; margin: 20px auto; background: white; padding: 20px; border-radius: 10px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        .actions a { margin-right: 10px; text-decoration: none; color: blue; cursor: pointer; }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            width: 50%;
            max-width: 600px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            position: relative;
        }
        .close-modal {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 20px;
            cursor: pointer;
        }

        /* Modal melhorado */
        .modal-content {
            background: white;
            padding: 25px;
            border-radius: 10px;
            width: 50%;
            max-width: 600px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            position: relative;
            font-family: Arial, sans-serif;
        }

        /* Botão de fechar */
        .close-modal {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 22px;
            cursor: pointer;
            color: #555;
        }
        .close-modal:hover {
            color: #000;
        }

        /* Corpo do modal */
        #modal-body {
            margin-top: 15px;
            padding: 10px;
            background: #f9f9f9;
            border-radius: 8px;
        }

        /* Estilização dos detalhes */
        .checklist-detail {
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }
        .checklist-detail:last-child {
            border-bottom: none;
        }
        .checklist-label {
            font-weight: bold;
            color: #333;
        }
        .checklist-value {
            color: #555;
            display: block;
            margin-top: 3px;
        }

    </style>
</head>
<body>
<?php if (isset($_GET['msg'])): ?>
    <p style="color: green;"><?= htmlspecialchars($_GET['msg']) ?></p>
<?php endif; ?>


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
                    <a href="editar_checklist.php?id=<?= $row['id'] ?>">Editar</a>
                    <a href="excluir_checklist.php?id=<?= $row['id'] ?>" onclick="return confirm('Tem certeza?')">Excluir</a>
                    <a href="exportar_pdf.php?id=<?= $row['id'] ?>">PDF</a>
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
