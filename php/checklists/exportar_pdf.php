<?php
require_once('../../libs/tcpdf/tcpdf.php');
include '../bd/conexao.php';

if (!isset($_GET['id'])) {
    die("Checklist inválido.");
}

$id = intval($_GET['id']);
$query = "SELECT c.*, e.nome AS empresa_nome FROM checklists c 
          INNER JOIN empresas e ON c.empresa_id = e.id WHERE c.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Checklist não encontrado.");
}

$checklist = $result->fetch_assoc();

// Buscar sistemas instalados
$query_sistemas = "SELECT s.nome FROM checklist_sistemas cs 
                   INNER JOIN sistemas s ON cs.sistema_id = s.id 
                   WHERE cs.checklist_id = ?";
$stmt_sistemas = $conn->prepare($query_sistemas);
$stmt_sistemas->bind_param("i", $id);
$stmt_sistemas->execute();
$result_sistemas = $stmt_sistemas->get_result();

$sistemas_instalados = [];
while ($sistema = $result_sistemas->fetch_assoc()) {
    $sistemas_instalados[] = $sistema['nome'];
}

// Função para exibir checkboxes corretamente
function checkbox($opcao, $selecionado) {
    return in_array($opcao, $selecionado) ? "☑ $opcao" : "☐ $opcao";
}

// **Listas fixas para checkboxes**
$sistemas_operacionais_padrao = ["Windows 7", "Windows 10", "Windows 11"];
$procedimentos_iniciais = ["Etiquetar", "Limpeza interna", "Limpeza externa"];
$backup_opcoes = ["Não realizado", "Feito e restaurado", "Feito"];

// **Corrigir a formatação da data**
$data_formatada = date("d/m/Y", strtotime($checklist['data']));

// Separar os sistemas operacionais selecionados
$so_selecionados = explode(", ", $checklist['sistema_operacional']);

// Verifica se há um sistema diferente dos padrões
$outro_sistema = array_diff($so_selecionados, $sistemas_operacionais_padrao);

// Criando o PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Sistema de Checklists');
$pdf->SetTitle('Checklist ' . $checklist['ticket']);
$pdf->SetMargins(0, 0, 0);
$pdf->AddPage();

$pdf->Image('../../assets/papel_timbrado.png', 5, 0, 210, 297, '', '', '', true, 1000, '', false, false, 0);
$pdf->Ln(30); // Aumente o número para descer mais


$pdf->SetMargins(15,15,15);

// **Definir fonte com suporte a Unicode**
$pdf->SetFont('dejavusans', '', 12);

// Cabeçalho (garantindo que o texto não fique sobre a imagem do timbre)
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 15, "Checklist de Formatação - " . $checklist['empresa_nome'], 0, 1, 'C');
$pdf->Ln(8);

$html = "<b>Checklist de Formatação -</b>". $checklist['empresa_nome'];

// Informações principais
$pdf->SetFont('dejavusans', '', 12);
$html = "
<b>Ticket:</b> " . $checklist['ticket'] . "  <b>Data:</b> " . $data_formatada . "<br><br>
<b>Nome pré-formatação:</b> " . $checklist['nome_pre_formatacao'] . "<br>
<b>Nome pós-formatação:</b> " . $checklist['nome_pos_formatacao'] . "<br>
<b>Usuário antigo:</b> " . $checklist['usuario_antigo'] . "<br>
<b>Usuário novo:</b> " . $checklist['usuario_novo'] . "<br><br>
<br>

<b>Checklist:</b><br><hr><br><br>

<b>Procedimento Inicial:</b><br>";
foreach ($procedimentos_iniciais as $proc) {
    $html .= checkbox($proc, explode(", ", $checklist['procedimento_inicial'])) . "<br>";
}

$html .= "<br><hr><br>";

$html .= "<b>Sistema operacional:</b><br>";

foreach ($sistemas_operacionais_padrao as $so) {
    $html .= checkbox($so, $so_selecionados) . "<br>";
}
if (!empty($outro_sistema)) {
    foreach ($outro_sistema as $outro) {
        $html .= "☑ $outro<br>";
    }
}
$html .= "<br><hr><br>";

$html .= "<b>Backup:</b><br>";
foreach ($backup_opcoes as $backup) {
    $html .= checkbox($backup, explode(", ", $checklist['backup'])) . "<br>";
}
$html .= "<b>Local de Salvamento:</b> " . $checklist['local_salvamento'] . "<br><br><hr><br>";


$html .= "<b>Instalações:</b><br>";

if (!empty($sistemas_instalados)) {
    $total = count($sistemas_instalados);
    $colunas = 3; // Máximo de 3 colunas
    $itens_por_coluna = ceil($total / $colunas);
    
    $html .= '<table><tr>';

    for ($i = 0; $i < $colunas; $i++) {
        $html .= '<td style="vertical-align:top;">'; // Mantém o texto alinhado no topo

        for ($j = 0; $j < $itens_por_coluna; $j++) {
            $index = $i * $itens_por_coluna + $j;
            if ($index < $total) {
                $html .= "☑ " . $sistemas_instalados[$index] . "<br>";
            }
        }

        $html .= '</td>';
    }

    $html .= '</tr></table>';
} else {
    $html .= "Nenhum sistema instalado listado.<br>";
}


$pdf->writeHTML($html, true, false, true, false, '');

// Saída do PDF
$pdf->Output("checklist_" . $checklist['id'] . ".pdf", "D");

$conn->close();

?>
