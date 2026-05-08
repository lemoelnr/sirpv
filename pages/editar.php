<?php
include("../includes/header.php");

// Verifica se veio um ID pela URL
if (!isset($_GET['id'])) {
    header("Location: solicitacoes.php");
    exit();
}

// Converte o ID para número inteiro
$id = (int) $_GET['id'];

// Se o ID for inválido, volta para solicitações
if ($id <= 0) {
    header("Location: solicitacoes.php");
    exit();
}

// Função para proteger textos exibidos na tela
function limpar($valor) {
    return htmlspecialchars($valor ?? '', ENT_QUOTES, 'UTF-8');
}

// Converte moeda brasileira para formato do banco
// Exemplo: 1.500,25 vira 1500.25
function moedaParaBanco($valor) {
    $valor = str_replace('.', '', $valor);
    $valor = str_replace(',', '.', $valor);
    return $valor;
}

// Converte valor do banco para formato brasileiro
// Exemplo: 1500.25 vira 1.500,25
function moedaParaTela($valor) {
    if ($valor === null || $valor === '') {
        return '';
    }

    return number_format((float)$valor, 2, ',', '.');
}

// Se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Só admin e editor podem alterar
    $pode_editar = ($_SESSION['perfil'] == 'admin' || $_SESSION['perfil'] == 'editor');

    if (!$pode_editar) {
        header("Location: solicitacoes.php");
        exit();
    }

    // Recebe os dados do formulário
    $numero_rpv = trim($_POST['numero_rpv'] ?? '');
    $processo_sei = trim($_POST['processo_sei'] ?? '');
    $projudi_pje = trim($_POST['projudi_pje'] ?? '');
    $unidade_judicial = trim($_POST['unidade_judicial'] ?? '');
    $parte_credora_inicial = trim($_POST['parte_credora_inicial'] ?? '');
    $parte_credora_atual = trim($_POST['parte_credora_atual'] ?? '');
    $cpf_cnpj = trim($_POST['cpf_cnpj'] ?? '');

    // Os três valores são independentes
    // Valor requisitado = valor determinado pelo juiz
    // Valor executado = valor que a parte pediu
    // Valor homologado = valor que vai para pagamento
    $valor_requisitado = moedaParaBanco(trim($_POST['valor_requisitado'] ?? '0'));
    $valor_executado = moedaParaBanco(trim($_POST['valor_executado'] ?? '0'));
    $valor_homologado = moedaParaBanco(trim($_POST['valor_homologado'] ?? '0'));

    $erros = [];


    // Recebe o órgão de destino
    $encaminhamento_para = trim($_POST['encaminhamento_para'] ?? '');

    // Lista de órgãos permitidos
    $orgaos_permitidos = [
        'Pendente de Encaminhamento',
        'SEFAZ',
        'SESAU',
        'DETRAN',
        'ITERAIMA',
        'FEMARH'
    ];

    // Se vier órgão inválido, volta para pendente
    if (!in_array($encaminhamento_para, $orgaos_permitidos)) {
        $encaminhamento_para = 'Pendente de Encaminhamento';
    }

    // Validação de campos obrigatórios
    if (
        empty($numero_rpv) ||
        empty($processo_sei) ||
        empty($projudi_pje) ||
        empty($cpf_cnpj)
    ) {
        $erros[] = "Preencha todos os campos obrigatórios.";
    }

    // Só salva se NÃO existir mensagem de erro
        if (empty($erros)) {

        // Atualiza os dados da RPV no banco
        $sql_update = "
            UPDATE rpvs SET
                numero_rpv = ?,
                processo_sei = ?,
                projudi_pje = ?,
                unidade_judicial = ?,
                parte_credora_inicial = ?,
                parte_credora_atual = ?,
                cpf_cnpj = ?,
                valor_requisitado = ?,
                valor_executado = ?,
                valor_homologado = ?,
                encaminhamento_para = ?
            WHERE id = ?
        ";

        $stmt = $conexao->prepare($sql_update);

        $stmt->bind_param(
            "sssssssssssi",
            $numero_rpv,
            $processo_sei,
            $projudi_pje,
            $unidade_judicial,
            $parte_credora_inicial,
            $parte_credora_atual,
            $cpf_cnpj,
            $valor_requisitado,
            $valor_executado,
            $valor_homologado,
            $encaminhamento_para,
            $id
        );

        if ($stmt->execute()) {
            header("Location: solicitacoes.php?msg=editado");
            exit();
        } else {
            $mensagem = "Erro ao atualizar os dados.";
        }
    }
}

// Busca os dados da RPV para mostrar na tela
$sql = "SELECT * FROM rpvs WHERE id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows == 0) {
    echo "Solicitação não encontrada.";
    include("../includes/footer.php");
    exit();
}

$rpv = $resultado->fetch_assoc();

// Define quem pode editar
$pode_editar = ($_SESSION['perfil'] == 'admin' || $_SESSION['perfil'] == 'editor');

// Se não puder editar, os campos ficam somente leitura
$bloqueado = $pode_editar ? '' : 'readonly';
?>

<header class="header">
    <div>
        <h1>Ver+ Solicitação</h1>
        <p>Visualize os detalhes completos da RPV e altere informações quando necessário</p>
    </div>

    <span>Usuário: <?php echo limpar($_SESSION['usuario']); ?></span>
</header>

<section class="section section-editar">

    <h2>Dados completos da Solicitação</h2>

    <?php if (!empty($erros)): ?>
        <div class="msg-erro">
            <?php foreach ($erros as $erro): ?>
                <p><?php echo limpar($erro); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST" class="form-grid form-ver">

        <div class="form-group">
            <label>Nº RPV</label>
            <input type="text" name="numero_rpv" value="<?php echo limpar($rpv['numero_rpv']); ?>" <?php echo $bloqueado; ?> required>
        </div>

        <div class="form-group">
            <label>Processo SEI</label>
            <input type="text" name="processo_sei" value="<?php echo limpar($rpv['processo_sei']); ?>" <?php echo $bloqueado; ?> required>
        </div>

        <div class="form-group">
            <label>Projudi/PJE</label>
            <input type="text" name="projudi_pje" value="<?php echo limpar($rpv['projudi_pje']); ?>" <?php echo $bloqueado; ?> required>
        </div>

        <div class="form-group">
            <label>Unidade Judicial</label>
            <input type="text" name="unidade_judicial" value="<?php echo limpar($rpv['unidade_judicial']); ?>" <?php echo $bloqueado; ?>>
        </div>

        <div class="form-group">
            <label>Parte Credora Atual</label>
            <input type="text" name="parte_credora_atual" value="<?php echo limpar($rpv['parte_credora_atual']); ?>" <?php echo $bloqueado; ?> >
        </div>

        <div class="form-group">
            <label>Parte Credora Inicial</label>
            <input type="text" name="parte_credora_inicial" value="<?php echo limpar($rpv['parte_credora_inicial']); ?>" <?php echo $bloqueado; ?>>
        </div>

        <div class="form-group">
            <label>CPF/CNPJ</label>
            <input type="text" name="cpf_cnpj" value="<?php echo limpar($rpv['cpf_cnpj']); ?>" onkeyup="formatarCpfCnpj(this)" required <?php echo $bloqueado; ?>>
        </div>

        <div class="form-group">
            <label>Valor Requisitado</label>
            <input type="text" name="valor_requisitado" value="<?php echo moedaParaTela($rpv['valor_requisitado']); ?>" <?php echo $bloqueado; ?> onkeyup="formatarMoeda(this)">
        </div>

        <div class="form-group">
            <label>Valor Executado</label>
            <input type="text" name="valor_executado" value="<?php echo moedaParaTela($rpv['valor_executado']); ?>" <?php echo $bloqueado; ?> onkeyup="formatarMoeda(this)">
        </div>

        <div class="form-group">
            <label>Valor Homologado</label>
            <input type="text" name="valor_homologado" value="<?php echo moedaParaTela($rpv['valor_homologado']); ?>" <?php echo $bloqueado; ?> onkeyup="formatarMoeda(this)">
        </div>

        <div class="form-group">
            <label>Órgão Destino</label>
            <select name="encaminhamento_para" required <?php if(!$pode_editar) echo 'disabled'; ?> >
                <option value="Pendente de Encaminhamento" <?php if ($rpv['encaminhamento_para'] == 'Pendente de Encaminhamento') echo 'selected'; ?>>Pendente de Encaminhamento</option>
                <option value="SEFAZ" <?php if ($rpv['encaminhamento_para'] == 'SEFAZ') echo 'selected'; ?>>SEFAZ</option>
                <option value="SESAU" <?php if ($rpv['encaminhamento_para'] == 'SESAU') echo 'selected'; ?>>SESAU</option>
                <option value="DETRAN" <?php if ($rpv['encaminhamento_para'] == 'DETRAN') echo 'selected'; ?>>DETRAN</option>
                <option value="ITERAIMA" <?php if ($rpv['encaminhamento_para'] == 'ITERAIMA') echo 'selected'; ?>>ITERAIMA</option>
                <option value="FEMARH" <?php if ($rpv['encaminhamento_para'] == 'FEMARH') echo 'selected'; ?>>FEMARH</option>
            </select>
        </div>

        <div class="actions">
            <a href="solicitacoes.php" class="btn btn-voltar">Voltar</a>

            <?php if($pode_editar): ?>
                <button type="submit" class="btn">Salvar Alterações</button>
            <?php endif; ?>    
            
            <a href="gerar_certidao.php?id=<?php echo $id; ?>" target="_blank" class="btn btn-primary">Gerar Certidão</a>
        </div>

    </form>

</section>

<?php include("../includes/footer.php"); ?>