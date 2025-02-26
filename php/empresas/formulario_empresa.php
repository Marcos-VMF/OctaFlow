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
        }

        .card {
            background: var(--bg-secondary);
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            padding: 20px;
        }

        h2 {
            margin: 0 0 30px 0;
            font-weight: 600;
            color: var(--text-primary);
            font-size: 1.5rem;
            text-align: center;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: var(--text-primary);
            font-weight: 500;
            font-size: 14px;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: 5px;
            color: var(--text-primary);
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
            margin-bottom: 10px;
        }

        input[type="text"]:focus {
            outline: none;
            border-color: var(--accent-primary);
        }

        .sistemas-section {
            margin-bottom: 30px;
            padding: 15px;
            background: var(--bg-tertiary);
            border-radius: 8px;
        }

        .sistemas-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-top: 10px;
        }

        .sistema-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px;
            background: var(--bg-secondary);
            border-radius: 5px;
            transition: background-color 0.2s;
        }

        .sistema-item:hover {
            background: var(--border-color);
        }

        input[type="checkbox"] {
            appearance: none;
            -webkit-appearance: none;
            width: 18px;
            height: 18px;
            border: 2px solid var(--border-color);
            border-radius: 4px;
            background: var(--bg-primary);
            cursor: pointer;
            position: relative;
            transition: all 0.2s;
        }

        input[type="checkbox"]:checked {
            background: var(--accent-primary);
            border-color: var(--accent-primary);
        }

        input[type="checkbox"]:checked::after {
            content: '✓';
            position: absolute;
            color: white;
            font-size: 12px;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
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
            min-width: 100px;
            justify-content: center;
        }

        .btn-primary {
            background: linear-gradient(45deg, var(--accent-secondary), var(--accent-primary));
            color: white;
            width: 100%;
        }

        .btn-secondary {
            background: var(--bg-tertiary);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .btn:active {
            transform: translateY(0);
        }

        .input-group {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            .sistemas-grid {
                grid-template-columns: 1fr;
            }

            .input-group {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2>Registro de Empresas</h2>
            <form action="processar_registro_empresa.php" method="POST">
                <div class="form-group">
                    <label>Nome da Empresa:</label>
                    <input type="text" name="nome_empresa" required>
                </div>

                <div class="sistemas-section">
                    <label>Sistemas Disponíveis:</label>
                    <div class="sistemas-grid">
                        <?php while ($sistema = $sistemas_result->fetch_assoc()) { ?>
                            <div class="sistema-item">
                                <input type="checkbox" name="sistemas[]" value="<?= $sistema['id'] ?>" id="sistema_<?= $sistema['id'] ?>">
                                <label for="sistema_<?= $sistema['id'] ?>"><?= htmlspecialchars($sistema['nome']) ?></label>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <div class="sistemas-section" id="novos-sistemas">
                    <label>Adicionar Novo Sistema:</label>
                    <div class="input-group">
                        <input type="text" name="novo_sistema[]" placeholder="Nome do Sistema">
                        <input type="text" name="link_sistema[]" placeholder="Link de Download">
                    </div>
                </div>

                <button type="button" class="btn btn-secondary" onclick="adicionarSistema()">
                    <i class="fas fa-plus"></i> Adicionar Outro Sistema
                </button>

                <div style="margin-top: 20px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Registrar Empresa
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function adicionarSistema() {
            const novosSistemas = document.getElementById('novos-sistemas');
            const div = document.createElement('div');
            div.classList.add('input-group');
            div.innerHTML = `
                <input type="text" name="novo_sistema[]" placeholder="Nome do Sistema">
                <input type="text" name="link_sistema[]" placeholder="Link de Download">
            `;
            novosSistemas.appendChild(div);
        }
    </script>
</body>
</html>
