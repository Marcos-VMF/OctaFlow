<?php
include '../bd/conexao.php';

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
    <style>
        body {
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
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Sistemas Cadastrados</h2>
        
        <table>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Ações</th>
            </tr>
            <?php while ($sistema = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $sistema['id']; ?></td>
                <td><?= htmlspecialchars($sistema['nome']); ?></td>
                <td>
                    <a href="visualizar_empresas.php?id=<?= $sistema['id']; ?>" class="btn btn-view">Empresas Vinculadas</a>
                    <a href="editar_sistema.php?id=<?= $sistema['id']; ?>" class="btn btn-edit">Editar</a>
                    <a href="excluir_sistema.php?id=<?= $sistema['id']; ?>" class="btn btn-delete" onclick="return confirm('Tem certeza que deseja excluir este sistema?');">Excluir</a>
                </td>
            </tr>
            <?php } ?>
        </table>

    </div>

</body>
</html>
