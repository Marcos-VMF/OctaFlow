<?php
include '../bd/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $empresa_id = $_POST['empresa_id'];
    $data = $_POST['data'];
    $ticket = $_POST['ticket'];
    $nome_pre_formatacao = $_POST['nome_pre_formatacao'];
    $nome_pos_formatacao = $_POST['nome_pos_formatacao'];
    $usuario_antigo = $_POST['usuario_antigo'];
    $usuario_novo = $_POST['usuario_novo'];


    // Concatenar valores dos checkboxes
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


    // Inserir checklist no banco
    $stmt = $conn->prepare("INSERT INTO checklists 
        (empresa_id, data, ticket, nome_pre_formatacao, nome_pos_formatacao, usuario_antigo, usuario_novo, procedimento_inicial, sistema_operacional, backup, local_salvamento) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssssssss", $empresa_id, $data, $ticket, $nome_pre_formatacao, $nome_pos_formatacao, $usuario_antigo, $usuario_novo, $procedimentos, $sistema_operacional, $backup, $local_salvamento);
    $stmt->execute();
    $checklist_id = $stmt->insert_id;
    $stmt->close();

    // Associar sistemas ao checklist
    if (isset($_POST['sistemas'])) {
        foreach ($_POST['sistemas'] as $sistema_id) {
            $stmt = $conn->prepare("INSERT INTO checklist_sistemas (checklist_id, sistema_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $checklist_id, $sistema_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    echo "Checklist salvo com sucesso!";
    header("Location: checklist.php"); // Redireciona de volta ao formulário
    exit();
}
?>
