<?php
include '../bd/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $data = $_POST['data'];
    $ticket = $_POST['ticket'];
    $nome_pre_formatacao = $_POST['nome_pre_formatacao'];
    $nome_pos_formatacao = $_POST['nome_pos_formatacao'];
    $usuario_antigo = $_POST['usuario_antigo'];
    $usuario_novo = $_POST['usuario_novo'];

    $procedimentos = isset($_POST['procedimento_inicial']) ? implode(", ", $_POST['procedimento_inicial']) : "";

    $sistema_operacional = isset($_POST['sistema_operacional']) ? $_POST['sistema_operacional'] : "";

    if ($sistema_operacional === "Outro" && !empty($_POST['outro_sistema'])) {
        $sistema_operacional = $_POST['outro_sistema'];
    }



    $backup = isset($_POST['backup']) ? $_POST['backup'] : "";
    $local_salvamento = isset($_POST['local_salvamento']) ? $_POST['local_salvamento'] : "";
    if ($backup !== "Feito") {
        $local_salvamento = ""; // Garante que só salva um local se "Feito" estiver marcado
    }


    // Atualizar os dados no banco
    $query = "UPDATE checklists 
SET empresa_id = ?, data = ?, ticket = ?, nome_pre_formatacao = ?, nome_pos_formatacao = ?, usuario_antigo = ?, usuario_novo = ?, procedimento_inicial = ?, sistema_operacional = ?, backup = ?, local_salvamento = ? 
WHERE id = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssssi", $data, $ticket, $nome_pre_formatacao, $nome_pos_formatacao, $usuario_antigo, $usuario_novo, $backup, $local_salvamento, $id);
    
    if ($stmt->execute()) {
        echo "Checklist atualizado com sucesso!";
        header("Location: listar_checklists.php");
        exit();
    } else {
        echo "Erro ao atualizar checklist!";
    }
    
    $stmt->close();
}
?>
