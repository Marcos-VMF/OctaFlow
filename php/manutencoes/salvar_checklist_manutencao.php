<?php
include '../bd/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $empresa_id = $_POST['empresa_id'];
    $data = $_POST['data'];
    $ticket = $_POST['ticket'];
    $equipamento = $_POST['equipamento'];
    $modelo = !empty($_POST['modelo']) ? $_POST['modelo'] : NULL;
    $acompanha_carregador = isset($_POST['acompanha_carregador']) ? 1 : 0;
    $nome_maquina = !empty($_POST['nome_maquina']) ? $_POST['nome_maquina'] : NULL;
    $nao_tem_nome = isset($_POST['nao_tem_nome']) ? 1 : 0;
    $processador = !empty($_POST['processador']) ? $_POST['processador'] : NULL;
    $memoria_ram = !empty($_POST['memoria_ram']) ? (int)$_POST['memoria_ram'] : NULL;
    $armazenamento_tipo = $_POST['armazenamento_tipo'];
    $capacidade_armazenamento = !empty($_POST['capacidade_armazenamento']) ? (int)$_POST['capacidade_armazenamento'] : NULL;
    $defeitos = !empty($_POST['defeitos']) ? $_POST['defeitos'] : NULL;
    $servicos_realizados = !empty($_POST['servicos_realizados']) ? $_POST['servicos_realizados'] : NULL;
    $observacoes = !empty($_POST['observacoes']) ? $_POST['observacoes'] : NULL;

    // Preparar e executar a inserção no banco de dados
    $sql = "INSERT INTO checklists_manutencao (
                empresa_id, data, ticket, equipamento, modelo, acompanha_carregador,
                nome_maquina, nao_tem_nome, processador, memoria_ram, armazenamento_tipo,
                capacidade_armazenamento, defeitos, servicos_realizados, observacoes
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param(
            "issssisssisssss",
            $empresa_id, $data, $ticket, $equipamento, $modelo, $acompanha_carregador,
            $nome_maquina, $nao_tem_nome, $processador, $memoria_ram, $armazenamento_tipo,
            $capacidade_armazenamento, $defeitos, $servicos_realizados, $observacoes
        );

        if ($stmt->execute()) {
            echo "<script>alert('Checklist salvo com sucesso!'); window.location.href='listar_checklists.php';</script>";
        } else {
            echo "<script>alert('Erro ao salvar o checklist.'); history.back();</script>";
        }

        $stmt->close();
    } else {
        echo "<script>alert('Erro na preparação da consulta.'); history.back();</script>";
    }

    $conn->close();
} else {
    echo "<script>alert('Método inválido.'); history.back();</script>";
}
?>
