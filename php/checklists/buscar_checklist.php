<?php
include '../bd/conexao.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Buscar os detalhes do checklist pelo ID
    $query = "SELECT c.id, c.data, c.ticket, c.nome_pre_formatacao, c.nome_pos_formatacao, 
                     c.usuario_antigo, c.usuario_novo, c.procedimento_inicial, 
                     c.sistema_operacional, c.backup, c.local_salvamento, 
                     e.nome AS empresa_nome 
              FROM checklists c 
              JOIN empresas e ON c.empresa_id = e.id 
              WHERE c.id = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $checklist = $result->fetch_assoc();
        echo "<p><strong>Empresa:</strong> " . htmlspecialchars($checklist['empresa_nome']) . "</p>";
        echo "<p><strong>Data:</strong> " . htmlspecialchars($checklist['data']) . "</p>";
        echo "<p><strong>Ticket:</strong> " . htmlspecialchars($checklist['ticket']) . "</p>";
        echo "<p><strong>Nome Pré-Formatação:</strong> " . htmlspecialchars($checklist['nome_pre_formatacao']) . "</p>";
        echo "<p><strong>Nome Pós-Formatação:</strong> " . htmlspecialchars($checklist['nome_pos_formatacao']) . "</p>";
        echo "<p><strong>Usuário Antigo:</strong> " . htmlspecialchars($checklist['usuario_antigo']) . "</p>";
        echo "<p><strong>Usuário Novo:</strong> " . htmlspecialchars($checklist['usuario_novo']) . "</p>";
        echo "<p><strong>Procedimento Inicial:</strong> " . nl2br(htmlspecialchars($checklist['procedimento_inicial'])) . "</p>";
        echo "<p><strong>Sistema Operacional:</strong> " . nl2br(htmlspecialchars($checklist['sistema_operacional'])) . "</p>";
        echo "<p><strong>Backup:</strong> " . nl2br(htmlspecialchars($checklist['backup'])) . "</p>";
        echo "<p><strong>Local de Salvamento:</strong> " . htmlspecialchars($checklist['local_salvamento']) . "</p>";
    } else {
        echo "<p>Checklist não encontrado.</p>";
    }

    $stmt->close();
}
?>
