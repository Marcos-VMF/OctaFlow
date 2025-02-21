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
        <h2 class="text-center">Editar Empresa</h2>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Nome da Empresa:</label>
                <input type="text" class="form-control" name="nome_empresa" value="<?= htmlspecialchars($empresa['nome']); ?>" required>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Remover Sistemas:</label>
                <div class="form-check">
                    <?php while ($sistema = $sistemas_vinculados->fetch_assoc()) { ?>
                        <input class="form-check-input" type="checkbox" name="remover_sistemas[]" value="<?= $sistema['id']; ?>">
                        <label class="form-check-label"> <?= htmlspecialchars($sistema['nome']); ?></label><br>
                    <?php } ?>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Adicionar Sistemas:</label>
                <div class="form-check">
                    <?php while ($sistema = $sistemas_disponiveis->fetch_assoc()) { ?>
                        <input class="form-check-input" type="checkbox" name="adicionar_sistemas[]" value="<?= $sistema['id']; ?>">
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
            <button type="submit" class="btn btn-primary w-100" style="background: linear-gradient(90deg, #00bcd4, #8e44ad);">Salvar Alterações</button>
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
