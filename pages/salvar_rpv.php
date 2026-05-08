<?php
session_start();

if(!isset($_SESSION['usuario'])){
    header("Location: ../index.php");
    exit();
}

include("../includes/conexao.php");

// =========================
// PEGANDO DADOS DO FORMULÁRIO
// =========================

$processo_sei = $conexao->real_escape_string($_POST['processo_sei']);
$projudi_pje = $conexao->real_escape_string($_POST['projudi_pje']);
$unidade_judicial = $conexao->real_escape_string($_POST['unidade_judicial']);
$numero_rpv = $conexao->real_escape_string($_POST['numero_rpv']);

$parte_credora_inicial = $conexao->real_escape_string($_POST['parte_credora_inicial']);
$parte_credora_atual = $conexao->real_escape_string($_POST['parte_credora_atual']);

$cpf_cnpj = $conexao->real_escape_string($_POST['cpf_cnpj']);

$valor_requisitado = $_POST['valor_requisitado'];
$valor_homologado = $_POST['valor_homologado'];
$valor_executado = $_POST['valor_executado'] ?? '0';

$encaminhamento_para = $conexao->real_escape_string($_POST['encaminhamento_para']);

$acao = $_POST['acao'] ?? 'salvar';
$senha_certidao = $_POST['senha_certidao'] ?? '';


// =========================
// TRATAMENTO DE VALORES (MOEDA)
// =========================

// Converte: 1.234,56 → 1234.56
$valor_requisitado = str_replace('.', '', $valor_requisitado);
$valor_requisitado = str_replace(',', '.', $valor_requisitado);

$valor_homologado = str_replace('.', '', $valor_homologado);
$valor_homologado = str_replace(',', '.', $valor_homologado);

$valor_executado = str_replace('.', '', $valor_executado);
$valor_executado = str_replace(',', '.', $valor_executado);

if ($valor_requisitado === '') {
    $valor_requisitado = 0;
}

if ($valor_homologado === '') {
    $valor_homologado = 0;
}

if ($valor_executado === '') {
    $valor_executado = 0;
}

// =========================
// MONTANDO O INSERT
// =========================

$sql = "INSERT INTO rpvs(
    processo_sei,
    projudi_pje,
    unidade_judicial,
    numero_rpv,
    parte_credora_inicial,
    parte_credora_atual,
    cpf_cnpj,
    valor_requisitado,
    valor_homologado,
    valor_executado,
    encaminhamento_para
) VALUES (
    '$processo_sei',
    '$projudi_pje',
    '$unidade_judicial',
    '$numero_rpv',
    '$parte_credora_inicial',
    '$parte_credora_atual',
    '$cpf_cnpj',
    '$valor_requisitado',
    '$valor_homologado',
    '$valor_executado',
    '$encaminhamento_para'
)";


// =========================
// EXECUTA
// =========================

if($conexao->query($sql)){

    $id = $conexao->insert_id;

    // =========================
    // GERAR CERTIDÃO
    // =========================
    if($acao == 'gerar_certidao'){

        $usuario_logado = $conexao->real_escape_string($_SESSION['usuario']);

        $sql_usuario = "SELECT * FROM usuarios WHERE usuario = '$usuario_logado'";
        $result_usuario = $conexao->query($sql_usuario);
        $dados_usuario = $result_usuario->fetch_assoc();

        if(!$dados_usuario || !password_verify($senha_certidao, $dados_usuario['senha'])){
            echo "Senha incorreta para gerar certidão.";
            exit();
        }

        header("Location: gerar_certidao.php?id=$id");
        exit();

    } else {

        header("Location: formulario_rpv.php?sucesso=1");
        exit();
    }

} else {
    echo "Erro ao salvar: " . $conexao->error;
}
?>