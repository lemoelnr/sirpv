<?php
require('../pdf/fpdf.php');
include("../includes/conexao.php");

function txt($texto) {
    return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $texto);
}

function limitarTexto($texto, $limite) {
    $texto = trim($texto ?? '');

    if (mb_strlen($texto, 'UTF-8') > $limite) {
        return mb_substr($texto, 0, $limite, 'UTF-8') . '...';
    }

    return $texto;
}

// FILTROS
$data_inicio = $_GET['data_inicio'] ?? '';
$data_fim = $_GET['data_fim'] ?? '';
$orgao = $_GET['orgao'] ?? '';
$tipo_relatorio = $_GET['tipo_relatorio'] ?? '';

if (empty($tipo_relatorio)) {
    echo "Selecione um tipo de relatório antes de gerar o PDF.";
    exit();
}

$where = "WHERE 1=1";

if ($data_inicio) {
    $where .= " AND DATE(criado_em) >= '$data_inicio'";
}

if ($data_fim) {
    $where .= " AND DATE(criado_em) <= '$data_fim'";
}

if ($orgao) {
    $where .= " AND encaminhamento_para = '$orgao'";
}

// TÍTULO E COLUNA DO RELATÓRIO
$titulo_relatorio = 'Relatório de RPVs';
$coluna_valor = 'Valor';

if ($tipo_relatorio == 'total_requisicoes') {
    $titulo_relatorio = 'Total de Requisições';
    $coluna_valor = 'Qtd';
}

if ($tipo_relatorio == 'valor_homologado') {
    $titulo_relatorio = 'Valor Total das Requisições';
    $coluna_valor = 'Valor Homologado';
}

if ($tipo_relatorio == 'valor_economizado') {
    $titulo_relatorio = 'Valor Total Economizado';
    $coluna_valor = 'Economia';
}

if ($tipo_relatorio == 'rpvs_por_orgao') {
    $titulo_relatorio = 'RPVs por Órgão';
    $coluna_valor = 'Total';
}

if ($tipo_relatorio == 'rpvs_por_orgao') {

    $sql_orgaos = "
        SELECT encaminhamento_para, COUNT(*) AS total
        FROM rpvs
        $where
        AND encaminhamento_para <> 'Pendente de Encaminhamento'
        GROUP BY encaminhamento_para
        ORDER BY total DESC
    ";

    $resultado_orgaos = $conexao->query($sql_orgaos);

    $sql_pendentes = "
        SELECT COUNT(*) AS total
        FROM rpvs
        $where
        AND encaminhamento_para = 'Pendente de Encaminhamento'
    ";

    $pendentes = $conexao->query($sql_pendentes)->fetch_assoc()['total'] ?? 0;

    $pdf = new FPDF('P', 'mm', 'A4');
    $pdf->AddPage();

    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, txt('RPVs por Órgão'), 0, 1, 'C');

    $pdf->Ln(8);

    $pdf->SetFont('Arial', 'B', 11);
    $pdf->Cell(130, 8, txt('Órgão'), 1);
    $pdf->Cell(40, 8, 'Total de RPVs', 1, 1, 'C');

    $pdf->SetFont('Arial', '', 11);

    while ($linha = $resultado_orgaos->fetch_assoc()) {
        $pdf->Cell(130, 8, txt($linha['encaminhamento_para']), 1);
        $pdf->Cell(40, 8, $linha['total'], 1, 1, 'C');
    }

    $pdf->Ln(8);

    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(0, 10, txt('Pendentes de Encaminhamento: ') . $pendentes, 0, 1);

    $pdf->Output("relatorio_rpvs_por_orgao.pdf", "I");
    exit();
}

$sql = "SELECT * FROM rpvs $where";
$resultado = $conexao->query($sql);

// PDF em paisagem
$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AddPage();

// Brasão
$pdf->Image('../img/brasao.png', 136, 8, 22);
$pdf->Ln(24);

// Cabeçalho institucional
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, txt('Governo do Estado de Roraima'), 0, 1, 'C');
$pdf->Cell(0, 5, txt('Procuradoria-Geral do Estado de Roraima'), 0, 1, 'C');

$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 5, txt('"Amazônia: patrimônio dos brasileiros"'), 0, 1, 'C');

$pdf->Ln(8);

// Título do relatório
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 8, txt($titulo_relatorio), 0, 1, 'C');

$pdf->Ln(8);

// LARGURAS DAS COLUNAS
$largura_rpv = 48;
$largura_credor = 95;
$largura_cpf = 38;
$largura_orgao = 60;
$largura_valor = 36;

// CABEÇALHO
$pdf->SetFont('Arial', 'B', 9);

$pdf->Cell($largura_rpv, 8, 'RPV', 1);
$pdf->Cell($largura_credor, 8, 'Credor', 1);
$pdf->Cell($largura_cpf, 8, 'CPF', 1);
$pdf->Cell($largura_orgao, 8, txt('Órgão'), 1);
$pdf->Cell($largura_valor, 8, txt($coluna_valor), 1, 1);

// DADOS
$pdf->SetFont('Arial', '', 9);

$total = 0;

while ($r = $resultado->fetch_assoc()) {

    $pdf->Cell($largura_rpv, 8, txt(limitarTexto($r['numero_rpv'], 22)), 1);
    $pdf->Cell($largura_credor, 8, txt(limitarTexto($r['parte_credora_atual'], 38)), 1);
    $pdf->Cell($largura_cpf, 8, txt(limitarTexto($r['cpf_cnpj'], 18)), 1);
    $pdf->Cell($largura_orgao, 8, txt(limitarTexto($r['encaminhamento_para'], 28)), 1);

    if ($tipo_relatorio == 'total_requisicoes') {
        $valor = 1;
    }

    if ($tipo_relatorio == 'valor_homologado') {
        $valor = $r['valor_homologado'];
    }

    if ($tipo_relatorio == 'valor_economizado') {

        if ($r['valor_executado'] > 0) {
            $valor = $r['valor_requisitado'] - $r['valor_executado'];
        } else {
            $valor = 0;
        }
    }

    $total += $valor;

    if ($tipo_relatorio == 'total_requisicoes') {
        $pdf->Cell($largura_valor, 8, $valor, 1, 1, 'C');
    } else {
        $pdf->Cell(
            $largura_valor,
            8,
            'R$ ' . number_format($valor, 2, ',', '.'),
            1,
            1,
            'R'
        );
    }
}

// TOTAL
$pdf->Ln(5);
$pdf->SetFont('Arial', 'B', 12);

if ($tipo_relatorio == 'total_requisicoes') {
    $pdf->Cell(0, 10, txt('Total de Requisições: ') . $total, 0, 1, 'R');
}

if ($tipo_relatorio == 'valor_homologado') {
    $pdf->Cell(0, 10, 'Total: R$ ' . number_format($total, 2, ',', '.'), 0, 1, 'R');
}

if ($tipo_relatorio == 'valor_economizado') {
    $pdf->Cell(0, 10, txt('Total Economizado: R$ ') . number_format($total, 2, ',', '.'), 0, 1, 'R');
}

$pdf->Output("relatorio.pdf", "I");
?>