<?php 
session_start();

if(!isset($_SESSION['usuario'])){
    header("Location: ../index.php");
    exit();
}

include("../includes/conexao.php");

// Função para corrigir acentos no FPDF
function txt($texto){
    return iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $texto);
}

// Função para formatar moeda
function moedaCertidao($valor){
    return 'R$ ' . number_format((float)$valor, 2, ',', '.');
}

$id = $_GET['id'];

$sql = "SELECT * FROM rpvs WHERE id = $id";
$resultado = $conexao->query($sql);
$rpv = $resultado->fetch_assoc();

if(!$rpv){
    echo "RPV não encontrada.";
    exit();
}

// =======================
// LÓGICA DA CERTIDÃO
// =======================

if($rpv['parte_credora_inicial'] == $rpv['parte_credora_atual']){
    $resultado_credor = "são a mesma pessoa";
}else{
    $resultado_credor = "não são a mesma pessoa";
}

if($rpv['valor_requisitado'] == $rpv['valor_homologado']){
    $resultado_valor = "são iguais";
}else{
    $resultado_valor = "não são iguais";
}

$cpf_cnpj = $rpv['cpf_cnpj'];

$sql_outras = "SELECT COUNT(*) AS total
               FROM rpvs
               WHERE cpf_cnpj = '$cpf_cnpj'
               AND id != $id";

$result_outras = $conexao->query($sql_outras);
$dados_outras = $result_outras->fetch_assoc();

if($dados_outras['total'] > 0){
    $resultado_outras = "há";
}else{
    $resultado_outras = "não há";
}

// =======================
// DADOS DO USUÁRIO
// =======================

$usuario_logado = $_SESSION['usuario'];

$sql_usuario = "SELECT * FROM usuarios WHERE usuario = '$usuario_logado'";
$result_usuario = $conexao->query($sql_usuario);
$dados_usuario = $result_usuario->fetch_assoc();

$data = date("d/m/Y");

require('../pdf/fpdf.php');

$pdf = new FPDF();
$pdf->AddPage();

// Brasão
$pdf->Image('../img/brasao.png', 92, 8, 25);
$pdf->Ln(25);

// Cabeçalho
$pdf->SetFont('Arial','',10);
$pdf->Cell(0,5,txt('Governo do Estado de Roraima'),0,1,'C');
$pdf->Cell(0,5,txt('Procuradoria-Geral do Estado de Roraima'),0,1,'C');

$pdf->SetFont('Arial','I',10);
$pdf->Cell(0,5,txt('"Amazônia: patrimônio dos brasileiros"'),0,1,'C');

$pdf->Ln(8);

// Título
$pdf->SetFont('Arial','B',12);
$pdf->Cell(0,8,txt('CERTIDÃO DE ANÁLISE DE RPV'),0,1,'C');

$pdf->Ln(10);

// Corpo
$pdf->SetFont('Arial','',10);

$pdf->Cell(0,6,txt('Credor inicial: ') . txt($rpv['parte_credora_inicial']),0,1);
$pdf->Cell(0,6,txt('Credor atual: ') . txt($rpv['parte_credora_atual']),0,1);
$pdf->Cell(0,6,txt('CPF/CNPJ: ') . txt($rpv['cpf_cnpj']),0,1);
$pdf->Cell(0,6,txt('Processo SEI: ') . txt($rpv['processo_sei']),0,1);
$pdf->Cell(0,6,txt('Processo PROJUDI/PJE: ') . txt($rpv['projudi_pje']),0,1);
$pdf->Cell(0,6,txt('Nº da RPV: ') . txt($rpv['numero_rpv']),0,1);

$pdf->Cell(
    0,
    6,
    txt('Valor requisitado: ') . txt(moedaCertidao($rpv['valor_requisitado'])),
    0,
    1
);

$pdf->Cell(
    0,
    6,
    txt('Valor homologado: ') . txt(moedaCertidao($rpv['valor_homologado'])),
    0,
    1
);

$pdf->Cell(
    0,
    6,
    txt('Valor executado: ') . txt(moedaCertidao($rpv['valor_executado'])),
    0,
    1
);

$pdf->Ln(5);

$pdf->MultiCell(
    0,
    5,
    txt('  Certificamos, para os devidos fins, que após análise dos dados registrados no sistema de controle de solicitações de pagamento — RPV:')
);

$pdf->Ln(4);

$pdf->MultiCell(
    0,
    6,
    txt('- O credor inicial e o credor atual ' . $resultado_credor . '.')
);

$pdf->MultiCell(
    0,
    6,
    txt('- O valor requisitado e o valor homologado ' . $resultado_valor . '.')
);

$pdf->MultiCell(
    0,
    6,
    txt('- Consta que ' . $resultado_outras . ' outras solicitações de pagamento vinculadas ao mesmo CPF/CNPJ no sistema.')
);

$pdf->Ln(5);

$pdf->Cell(0,6,txt('Boa Vista/RR, ' . $data . '.'),0,1);

$pdf->Ln(15);

$pdf->Cell(0,6,'________________________________________',0,1,'C');

$pdf->SetFont('Arial','',10);
$pdf->Cell(0,6,txt($dados_usuario['nome']),0,1,'C');
$pdf->Cell(0,6,txt($dados_usuario['cargo']),0,1,'C');
$pdf->Cell(0,6,txt('Matrícula: ' . $dados_usuario['matricula']),0,1,'C');

$pdf->Ln(48);

$pdf->SetFont('Arial','I',8);
$pdf->MultiCell(
    0,
    4,
    txt('Documento assinado eletronicamente pelo servidor acima identificado, mediante autenticação com senha pessoal no Sistema de Controle de Solicitações de Pagamento — SIRPV.')
);
$pdf->Output();
?>