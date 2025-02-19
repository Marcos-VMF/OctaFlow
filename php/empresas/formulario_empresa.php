<?php
include '../bd/conexao.php';
include $_SERVER['DOCUMENT_ROOT'] . '/OctaFlow/navbar.php';

// Buscar sistemas já registrados
$sistemas_query = "SELECT * FROM sistemas";
$sistemas_result = $conn->query($sistemas_query);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Empresas</title>
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
        .section {
            margin-bottom: 15px;
            padding: 15px;
            border-radius: 5px;
            background: #f9f9f9;
        }
        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"], input[type="url"] {
            width: calc(100% - 10px);
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-top: 5px;
        }
        .checkbox-group {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Registro de Empresas</h2>
        <form action="processar_registro_empresa.php" method="POST">
            <div class="section">
                <label>Nome da Empresa:</label>
                <input type="text" name="nome_empresa" placeholder="Digite o nome da empresa" required>
            </div>
            
            <div class="section">
                <label>Sistemas Disponíveis:</label>
                <div class="checkbox-group">
                    <?php while ($sistema = $sistemas_result->fetch_assoc()) { ?>
                        <label>
                            <input type="checkbox" name="sistemas[]" value="<?= $sistema['id']; ?>">
                            <?= htmlspecialchars($sistema['nome']); ?>
                        </label>
                    <?php } ?>
                </div>
            </div>

            <div class="section" id="novos-sistemas">
                <label>Adicionar Novo Sistema:</label>
                <div class="sistema-item">
                    <input type="text" name="novo_sistema[]" placeholder="Nome do Sistema">
                    <input type="url" name="link_sistema[]" placeholder="Link de Download">
                </div>
            </div>
            <button type="button" onclick="adicionarSistema()">Adicionar Outro Sistema</button>

            <button type="submit">Registrar Empresa</button>
        </form>
    </div>

<script>
    function adicionarSistema() {
        var container = document.getElementById('novos-sistemas');
        var div = document.createElement('div');
        div.classList.add('sistema-item');
        div.innerHTML = '<input type="text" name="novo_sistema[]" placeholder="Nome do Sistema">' +
                        '<input type="url" name="link_sistema[]" placeholder="Link de Download">';
        container.appendChild(div);
    }
</script>

</body>
</html>
