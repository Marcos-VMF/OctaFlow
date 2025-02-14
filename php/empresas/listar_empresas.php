<?php
include '../bd/conexao.php';

// Buscar todas as empresas cadastradas
$query = "SELECT * FROM empresas ORDER BY id ASC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Empresas</title>
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
        .btn-edit {
            background-color: #ffc107;
            color: #fff;
        }
        .btn-delete {
            background-color: #dc3545;
            color: #fff;
        }
        .btn-add {
            display: block;
            width: fit-content;
            background-color: #28a745;
            color: #fff;
            margin: 20px auto;
            padding: 10px 15px;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>Empresas Cadastradas</h2>
        
        <table>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Ações</th>
            </tr>
            <?php while ($empresa = $result->fetch_assoc()) { ?>
            <tr>
                <td><?= $empresa['id']; ?></td>
                <td><?= htmlspecialchars($empresa['nome']); ?></td>
                <td>
                    <a href="editar_empresa.php?id=<?= $empresa['id']; ?>" class="btn btn-edit">Editar</a>
                    <a href="excluir_empresa.php?id=<?= $empresa['id']; ?>" class="btn btn-delete" onclick="return confirm('Tem certeza que deseja excluir esta empresa?');">Excluir</a>
                </td>
            </tr>
            <?php } ?>
        </table>

        <a href="formulario_empresa.php" class="btn btn-add">Cadastrar Nova Empresa</a>
    </div>

</body>
</html>
