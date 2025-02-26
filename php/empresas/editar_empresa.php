<?php
include '../bd/conexao.php';
include $_SERVER['DOCUMENT_ROOT'] . '/OctaFlow/navbar.php';

if (isset($_GET['id'])) {
    $empresa_id = $_GET['id'];

    // Buscar empresa pelo ID
    $stmt = $conn->prepare("SELECT nome FROM empresas WHERE id = ?");
    $stmt->bind_param("i", $empresa_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $empresa = $result->fetch_assoc();
    $stmt->close();

    // Buscar sistemas já vinculados à empresa
    $query_sistemas_vinculados = "
        SELECT s.id, s.nome, s.link_download 
        FROM sistemas s 
        JOIN empresa_sistemas es ON s.id = es.sistema_id 
        WHERE es.empresa_id = ?";
    $stmt = $conn->prepare($query_sistemas_vinculados);
    $stmt->bind_param("i", $empresa_id);
    $stmt->execute();
    $sistemas_vinculados = $stmt->get_result();
    $stmt->close();

    // Buscar sistemas disponíveis para vincular
    $query_sistemas_disponiveis = "
        SELECT * FROM sistemas 
        WHERE id NOT IN (SELECT sistema_id FROM empresa_sistemas WHERE empresa_id = ?)";
    $stmt = $conn->prepare($query_sistemas_disponiveis);
    $stmt->bind_param("i", $empresa_id);
    $stmt->execute();
    $sistemas_disponiveis = $stmt->get_result();
    $stmt->close();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $novo_nome = $_POST['nome_empresa'];
        $sistemas_removidos = $_POST['remover_sistemas'] ?? [];
        $sistemas_adicionados = $_POST['adicionar_sistemas'] ?? [];
        $novos_sistemas = $_POST['novo_sistema'] ?? [];
        $links_sistemas = $_POST['link_sistema'] ?? [];

        // Atualizar nome da empresa
        $stmt = $conn->prepare("UPDATE empresas SET nome = ? WHERE id = ?");
        $stmt->bind_param("si", $novo_nome, $empresa_id);
        $stmt->execute();
        $stmt->close();

        // Remover vínculos com sistemas selecionados
        foreach ($sistemas_removidos as $sistema_id) {
            $stmt = $conn->prepare("DELETE FROM empresa_sistemas WHERE empresa_id = ? AND sistema_id = ?");
            $stmt->bind_param("ii", $empresa_id, $sistema_id);
            $stmt->execute();
            $stmt->close();
        }

        // Adicionar novos vínculos
        foreach ($sistemas_adicionados as $sistema_id) {
            $stmt = $conn->prepare("INSERT INTO empresa_sistemas (empresa_id, sistema_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $empresa_id, $sistema_id);
            $stmt->execute();
            $stmt->close();
        }

        // Adicionar novos sistemas ao banco e vincular à empresa
        foreach ($novos_sistemas as $index => $nome_sistema) {
            if (!empty($nome_sistema)) {
                $link = $links_sistemas[$index] ?? null;
                $stmt = $conn->prepare("INSERT INTO sistemas (nome, link_download) VALUES (?, ?)");
                $stmt->bind_param("ss", $nome_sistema, $link);
                $stmt->execute();
                $sistema_id = $stmt->insert_id;
                $stmt->close();

                // Vincular o novo sistema à empresa
                $stmt = $conn->prepare("INSERT INTO empresa_sistemas (empresa_id, sistema_id) VALUES (?, ?)");
                $stmt->bind_param("ii", $empresa_id, $sistema_id);
                $stmt->execute();
                $stmt->close();
            }
        }

        header("Location: listar_empresas.php");
        exit();
    }
} else {
    header("Location: listar_empresas.php");
    exit();
}
?>

<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Empresa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjVs5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
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
            <h2>Editar Empresa</h2>
            <form method="POST">
                <div class="form-group">
                    <label>Nome da Empresa:</label>
                    <input type="text" name="nome_empresa" value="<?= htmlspecialchars($empresa['nome']); ?>" required>
                </div>
                
                <div class="sistemas-section">
                    <label>Remover Sistemas:</label>
                    <div class="sistemas-grid">
                        <?php while ($sistema = $sistemas_vinculados->fetch_assoc()) { ?>
                            <div class="sistema-item">
                                <input type="checkbox" name="remover_sistemas[]" value="<?= $sistema['id']; ?>" id="rem_<?= $sistema['id']; ?>">
                                <label for="rem_<?= $sistema['id']; ?>"><?= htmlspecialchars($sistema['nome']); ?></label>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                
                <div class="sistemas-section">
                    <label>Adicionar Sistemas:</label>
                    <div class="sistemas-grid">
                        <?php while ($sistema = $sistemas_disponiveis->fetch_assoc()) { ?>
                            <div class="sistema-item">
                                <input type="checkbox" name="adicionar_sistemas[]" value="<?= $sistema['id']; ?>" id="add_<?= $sistema['id']; ?>">
                                <label for="add_<?= $sistema['id']; ?>"><?= htmlspecialchars($sistema['nome']); ?></label>
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
                        <i class="fas fa-save"></i> Salvar Alterações
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
