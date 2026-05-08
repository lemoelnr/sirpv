<?php include("../includes/header.php"); 


//total de RPVs pedentes de encaminhamento
$sql_pendentes = "SELECT COUNT(*) AS total
                FROM rpvs
                WHERE encaminhamento_para = 'Pendente de Encaminhamento'";

$res_pendentes = $conexao->query($sql_pendentes);
$pendentes = $res_pendentes->fetch_assoc()['total'];

// Encaminhadas no mês
$sql_mes = "SELECT COUNT(*) AS total 
            FROM rpvs 
            WHERE encaminhamento_para <> 'Pendente de Encaminhamento'
            AND MONTH(criado_em) = MONTH(CURRENT_DATE())
            AND YEAR(criado_em) = YEAR(CURRENT_DATE())";

$res_mes = $conexao->query($sql_mes);
$mes = $res_mes->fetch_assoc()['total'];


// Total geral
$sql_total = "SELECT COUNT(*) AS total FROM rpvs";

$res_total = $conexao->query($sql_total);
$total = $res_total->fetch_assoc()['total'];

//valor total requisitado
$sql_valor_requisitado = "SELECT SUM(valor_requisitado) AS total FROM rpvs";

$res_valor_requisitado = $conexao->query($sql_valor_requisitado);
$valor_requisitado = $res_valor_requisitado->fetch_assoc()['total'];

$valor_requisitado = $valor_requisitado ?? 0;

// Valor total homologado
$sql_valor_homologado = "SELECT SUM(valor_homologado) AS total FROM rpvs";

$res_valor_homologado = $conexao->query($sql_valor_homologado);
$valor_homologado = $res_valor_homologado->fetch_assoc()['total'];

$valor_homologado = $valor_homologado ?? 0;

// Valor total executado
$sql_valor_executado = "SELECT SUM(valor_executado) AS total FROM rpvs";

$res_valor_executado = $conexao->query($sql_valor_executado);
$valor_executado = $res_valor_executado->fetch_assoc()['total'];

$valor_executado = $valor_executado ?? 0;

// Economia
$sql_economia = "
    SELECT SUM(
        CASE
            WHEN valor_executado = 0 THEN 0
            ELSE valor_requisitado - valor_executado
        END
    ) AS total
    FROM rpvs
";

$res_economia = $conexao->query($sql_economia);
$economia = $res_economia->fetch_assoc()['total'];

$economia = $economia ?? 0;

?>

    <header class="header">
        <div>
            <h1>Painel Inicial</h1>
            <p>Controle de Solicitações de Pagamento - RPV</p>
        </div>

        <span>Usuário: <?php echo $_SESSION['usuario']; ?></span>
    </header>

    <section class="grid-cards">
        <div class="card">
            <h3>RPVs Pendentes</h3>
            <strong><?php echo $pendentes ?></strong>
        </div>

        <div class="card">
            <h3>Encaminhadas no mês</h3>
            <strong><?php echo $mes ?></strong>
        </div>

        <div class="card">
            <h3>Total Geral</h3>
            <strong><?php echo $total?></strong>
        </div>


    </section>

    <section class="grid-cards">

        <div class="card">
            <h3>Total Requisitado</h3>
            <strong>R$ <?php echo number_format($valor_requisitado, 2, ',', '.')?></strong>
        </div>

        <div class="card">
            <h3>Total Executado</h3>
            <strong>R$ <?php echo number_format($valor_executado, 2, ',', '.')?></strong>
        </div>

        <div class="card">
            <h3>Total Homologado</h3>
            <strong>R$ <?php echo number_format($valor_homologado, 2, ',', '.')?></strong>
        </div>

        <div class="card">
            <h3>Economia</h3>
            <strong style="color: #16a34a;">
                R$ <?php echo number_format($economia, 2, ',', '.')?>
            </strong>
        </div>

    </section>

    <section class="section">
        <h2>Bem-vindo ao sistema</h2>
        <p>Selecione uma opção no menu lateral para continuar.</p>
    </section>

    <?php include("../includes/footer.php"); ?>