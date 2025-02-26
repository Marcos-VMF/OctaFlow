<?php
include '../bd/conexao.php';

if (!isset($_GET['id'])) {
    if (isset($_GET['ajax'])) {
        echo json_encode(['success' => false, 'message' => 'Sistema não encontrado']);
        exit;
    }
    header("Location: listar_sistemas.php");
    exit();
}

$sistema_id = $_GET['id'];

// Buscar sistema pelo ID
$stmt = $conn->prepare("SELECT nome, link_download FROM sistemas WHERE id = ?");
$stmt->bind_param("i", $sistema_id);
$stmt->execute();
$result = $stmt->get_result();
$sistema = $result->fetch_assoc();
$stmt->close();

if (!$sistema) {
    if (isset($_GET['ajax'])) {
        echo json_encode(['success' => false, 'message' => 'Sistema não encontrado']);
        exit;
    }
    header("Location: listar_sistemas.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $novo_nome = $_POST['nome_sistema'];
    $novo_link = $_POST['link_download'];

    // Validar dados
    if (empty($novo_nome)) {
        if (isset($_GET['ajax'])) {
            echo json_encode(['success' => false, 'message' => 'O nome do sistema é obrigatório']);
            exit;
        }
        die("O nome do sistema é obrigatório");
    }

    // Atualizar sistema
    $stmt = $conn->prepare("UPDATE sistemas SET nome = ?, link_download = ? WHERE id = ?");
    $stmt->bind_param("ssi", $novo_nome, $novo_link, $sistema_id);
    
    if ($stmt->execute()) {
        if (isset($_GET['ajax'])) {
            echo json_encode(['success' => true]);
            exit;
        }
        header("Location: listar_sistemas.php");
        exit();
    } else {
        if (isset($_GET['ajax'])) {
            echo json_encode(['success' => false, 'message' => 'Erro ao atualizar sistema']);
            exit;
        }
        die("Erro ao atualizar sistema");
    }
    $stmt->close();
}

// Se for uma requisição AJAX, retornar o formulário
if (isset($_GET['ajax'])) {
    ?>
    <form onsubmit="submitEditForm(event, <?= $sistema_id ?>)">
        <div class="form-group">
            <label class="form-label" for="nome_sistema">Nome do Sistema</label>
            <input type="text" id="nome_sistema" name="nome_sistema" class="form-control" 
                   value="<?= htmlspecialchars($sistema['nome']); ?>" required>
        </div>
        
        <div class="form-group">
            <label class="form-label" for="link_download">Link de Download</label>
            <input type="url" id="link_download" name="link_download" class="form-control" 
                   value="<?= htmlspecialchars($sistema['link_download']); ?>" required>
        </div>

        <div class="modal-footer">
            <button type="button" class="btn-modal secondary" onclick="closeModal('editModal')">Cancelar</button>
            <button type="submit" class="btn-modal primary">Salvar Alterações</button>
        </div>
    </form>
    <?php
    exit;
}

// Se não for AJAX, redirecionar para a lista
header("Location: listar_sistemas.php");
exit();
?>
