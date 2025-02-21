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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjVs5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link href='//fonts.googleapis.com/css?family=Montserrat:thin,extra-light,light,100,200,300,400,500,600,700,800' rel='stylesheet' type='text/css'>
    <style>
        body {
            font-family: 'Montserrat';
            background-color:rgb(94, 94, 94);
        }
        .container {
            max-width: 600px;
            margin-top: 50px;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h2{
            margin-bottom: 20px;
        }
        
        label{
            font-size: 16px;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Registro de Empresas</h2>
        <form action="processar_registro_empresa.php" method="POST">
            <div class="mb-3">
                <label class="form-label">Nome da Empresa:</label>
                <input type="text" class="form-control" name="nome_empresa" placeholder="Digite o nome da empresa" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Sistemas Disponíveis:</label>
                <div class="form-check">
                    <?php while ($sistema = $sistemas_result->fetch_assoc()) { ?>
                        <input class="form-check-input" type="checkbox" name="sistemas[]" value="<?= $sistema['id']; ?>">
                        <label class="form-check-label"> <?= htmlspecialchars($sistema['nome']); ?></label><br>
                    <?php } ?>
                </div>
            </div>
            
            <div class="mb-3" id="novos-sistemas">
                <label class="form-label">Adicionar Novo Sistema:</label>
                <div class="input-group mb-2">
                    <input type="text" class="form-control" name="novo_sistema[]" placeholder="Nome do Sistema">
                    <input type="text" class="form-control" name="link_sistema[]" placeholder="Link de Download">
                </div>
            </div>
            <button type="button" class="btn btn-secondary mb-3" onclick="adicionarSistema()">Adicionar Outro Sistema</button>
            <button type="submit" class="btn btn-primary w-100" style="background: linear-gradient(90deg, #00bcd4, #8e44ad);">Registrar Empresa</button>
        </form>
    </div>

    <script>
        function adicionarSistema() {
            const novosSistemas = document.getElementById('novos-sistemas');
            const div = document.createElement('div');
            div.classList.add('input-group', 'mb-2');
            div.innerHTML = '<input type="text" class="form-control" name="novo_sistema[]" placeholder="Nome do Sistema">' +
                            '<input type="text" class="form-control" name="link_sistema[]" placeholder="Link de Download">';
            novosSistemas.appendChild(div);
        }
    </script>
</body>
</html>
