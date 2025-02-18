<?php
require_once('../../libs/tcpdf/tcpdf.php');
include '../bd/conexao.php';

if (!isset($_GET['id'])) {
    die("Checklist inválido.");
}

$id = intval($_GET['id']);
$query = "SELECT c.*, e.nome AS empresa_nome FROM checklists_manutencao c 
          INNER JOIN empresas e ON c.empresa_id = e.id WHERE c.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Checklist não encontrado.");
}

$checklist = $result->fetch_assoc();

// **Corrigir a formatação da data**
$data_formatada = date("d/m/Y", strtotime($checklist['data']));

// Criando o PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sistema de Checklists');
$pdf->SetTitle('Checklist ' . $checklist['ticket']);
$pdf->SetMargins(15, 15, 15);
$pdf->AddPage();

$pdf->Ln(5);
$pdf->Image('../../assets/papel_timbrado.png', 5, 0, 210, 297, '', '', '', true, 1000, '', false, false, 0);
$pdf->Ln(25);
$pdf->SetFont('helvetica', 12);

$html = "<center><h1>Checklist de Manutenção - " . $checklist['empresa_nome'] . "</h1></center><br>";
$html .= "<h3>Informações Gerais</h3>";
$html .= "Ticket: " . $checklist['ticket'] . "<br>Data: " . $data_formatada . "<br><hr>";
$html .= "<h3>Equipamento</h3>";
$html .= "<b>Tipo:</b> " . ucfirst($checklist['equipamento']) . "<br>";
$html .= "<b>Modelo:</b> " . $checklist['modelo'] . "<br>";
$html .= "<b>Acompanha Carregador:</b> " . ($checklist['acompanha_carregador'] ? 'Sim' : 'Não') . "<br><hr>";
$html .= "<h3>Configuração</h3>";
$html .= "<b>Nome da Máquina:</b> " . ($checklist['nao_tem_nome'] ? 'Não possui nome' : $checklist['nome_maquina']) . "<br>";
$html .= "<b>Processador:</b> " . $checklist['processador'] . "<br>";
$html .= "<b>Memória RAM:</b> " . $checklist['memoria_ram'] . " GB<br>";
$html .= "<b>Armazenamento:</b> " . ucfirst($checklist['armazenamento_tipo']) . " - " . $checklist['capacidade_armazenamento'] . " GB<br><hr>";
$html .= "<h3>Manutenção</h3>";
$html .= "<b>Defeitos:</b> " . nl2br($checklist['defeitos']) . "<br>";
$html .= "<b>Serviços:</b> " . nl2br($checklist['servicos_realizados']) . "<br>";
$html .= "<b>Observações:</b> " . nl2br($checklist['observacoes']) . "<br>";

$pdf->writeHTML($html, true, false, true, false, '');

// Saída do PDF
$pdf->Output("checklist_" . $checklist['id'] . ".pdf", "D");

$conn->close();
?>
