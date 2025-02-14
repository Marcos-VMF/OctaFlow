<?php
include '../bd/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome_empresa = $_POST['nome_empresa'];
    $sistemas_selecionados = $_POST['sistemas'] ?? [];
    $novos_sistemas = $_POST['novo_sistema'] ?? [];
    $links_sistemas = $_POST['link_sistema'] ?? [];

    // Inserir empresa e recuperar o ID
    $stmt = $conn->prepare("INSERT INTO empresas (nome) VALUES (?)");
    $stmt->bind_param("s", $nome_empresa);
    $stmt->execute();
    $empresa_id = $stmt->insert_id;
    $stmt->close();

    // Associar sistemas já existentes à empresa
    foreach ($sistemas_selecionados as $sistema_id) {
        $stmt = $conn->prepare("INSERT INTO empresa_sistemas (empresa_id, sistema_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $empresa_id, $sistema_id);
        $stmt->execute();
        $stmt->close();
    }

    // Adicionar novos sistemas ao banco e associá-los à empresa
    foreach ($novos_sistemas as $index => $nome_sistema) {
        if (!empty($nome_sistema)) {
            $link = $links_sistemas[$index] ?? null;
            $stmt = $conn->prepare("INSERT INTO sistemas (nome, link_download) VALUES (?, ?)");
            $stmt->bind_param("ss", $nome_sistema, $link);
            $stmt->execute();
            $sistema_id = $stmt->insert_id;
            $stmt->close();

            // Associar novo sistema à empresa
            $stmt = $conn->prepare("INSERT INTO empresa_sistemas (empresa_id, sistema_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $empresa_id, $sistema_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    echo "Empresa e sistemas registrados com sucesso!";
}
?>
