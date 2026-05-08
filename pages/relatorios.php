<?php
include("../includes/header.php");

$data_inicio = $_GET['data_inicio'] ?? '';
$data_fim = $_GET['data_fim'] ?? '';
$orgao = $_GET['orgao'] ?? '';
$tipo_relatorio = $_GET['tipo_relatorio'] ?? '';

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

$sql = "SELECT * FROM rpvs $where ORDER BY criado_em DESC";
$resultado = $conexao->query($sql);

// TOTAL DE REQUISIÇÕES
$sql_total_requisicoes = "SELECT COUNT(*) AS total FROM rpvs $where";
$total_requisicoes = $conexao->query($sql_total_requisicoes)->fetch_assoc()['total'] ?? 0;

// VALOR TOTAL HOMOLOGADO
$sql_total_homologado = "SELECT SUM(valor_homologado) AS total FROM rpvs $where";
$total_homologado = $conexao->query($sql_total_homologado)->fetch_assoc()['total'] ?? 0;

// Valor total executado
$sql_valor_executado = "SELECT SUM(valor_executado) AS total FROM rpvs";

$res_valor_executado = $conexao->query($sql_valor_executado);
$valor_executado = $res_valor_executado->fetch_assoc()['total'];

$valor_executado = $valor_executado ?? 0;

// VALOR TOTAL ECONOMIZADO
$sql_total_economizado = "
    SELECT SUM(
        CASE
            WHEN valor_executado = 0 THEN 0
            ELSE valor_requisitado - valor_executado
        END
    ) AS total
    FROM rpvs
    $where
";
$total_economizado = $conexao->query($sql_total_economizado)->fetch_assoc()['total'] ?? 0;

// RPVs POR ÓRGÃO
$sql_orgaos = "
SELECT encaminhamento_para, COUNT(*) AS total
FROM rpvs
$where
AND encaminhamento_para <> 'Pendente de Encaminhamento'
GROUP BY encaminhamento_para
ORDER BY total DESC
";

$resultado_orgaos = $conexao->query($sql_orgaos);

// TOTAL DE PENDENTES
$sql_pendentes_orgao = "
SELECT COUNT(*) AS total
FROM rpvs
$where
AND encaminhamento_para = 'Pendente de Encaminhamento'
";

$pendentes_orgao = $conexao->query($sql_pendentes_orgao)->fetch_assoc()['total'] ?? 0;

?>

<header class="header">
    <div>
        <h1>Relatórios Avançados</h1>
        <p>Solicitações e relatórios RPVs</p>
    </div>

    <span>Usuário: <?php echo $_SESSION['usuario']; ?></span>
</header>

<section class="section section-relatorios">

    <form method="GET" class="form-pesquisa">

        <input 
            type="date" 
            name="data_inicio" 
            value="<?php echo $data_inicio; ?>"
        >

        <input 
            type="date" 
            name="data_fim" 
            value="<?php echo $data_fim; ?>"
        >

        <select name="tipo_relatorio" required>
            <option value="">Selecione o relatório</option>

            <option value="total_requisicoes" <?php if ($tipo_relatorio == 'total_requisicoes') echo 'selected'; ?>>
                Total de Requisições
            </option>

            <option value="valor_homologado" <?php if ($tipo_relatorio == 'valor_homologado') echo 'selected'; ?>>
                Valor Total das Requisições
            </option>

            <option value="valor_economizado" <?php if ($tipo_relatorio == 'valor_economizado') echo 'selected'; ?>>
                Valor Total Economizado
            </option>
            <option value="rpvs_por_orgao" <?php if ($tipo_relatorio == 'rpvs_por_orgao') echo 'selected'; ?>>
                RPVs por Órgão
            </option>
        </select>

        <button type="submit">
            Filtrar
        </button>

        <button 
            type="submit"
            formaction="gerar_relatorio_pdf.php"
            formtarget="_blank"
            class="btn btn-primary"
        >
            Gerar PDF
        </button>

    </form>

</section>

<section class="section section-relatorios-resultado">

    <?php if ($tipo_relatorio != 'rpvs_por_orgao'): ?>

    <table class="tabela">
        <tr>
            <th>Requisitado</th>
            <th>Homologado</th>
            <th>Executado</th>
            <th>Economia</th>
        </tr>

        <?php while ($r = $resultado->fetch_assoc()): ?>
            <tr>
                <td>
                    R$ <?php echo number_format($r['valor_requisitado'], 2, ',', '.'); ?>
                </td>

                <td>
                    R$ <?php echo number_format($r['valor_homologado'], 2, ',', '.'); ?>
                </td>

                <td>
                    R$ <?php echo number_format($r['valor_executado'], 2, ',', '.'); ?>
                </td>

                <td>
                    <?php
                    $economia = 0;

                    if ((float)$r['valor_executado'] > 0) {
                        $economia = (float)$r['valor_requisitado'] - (float)$r['valor_executado'];
                    }
                    ?>

                    R$ <?php echo number_format($economia, 2, ',', '.'); ?>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

<?php endif; ?>

    <?php if ($tipo_relatorio == 'total_requisicoes'): ?>
        <h3>Total de Requisições: <?php echo $total_requisicoes; ?></h3>
    <?php endif; ?>

    <?php if ($tipo_relatorio == 'valor_homologado'): ?>
        <h3>
            Valor Total Homologado:
            R$ <?php echo number_format($total_homologado, 2, ',', '.'); ?>
        </h3>
    <?php endif; ?>

    <?php if ($tipo_relatorio == 'valor_economizado'): ?>
        <h3>
            Valor Total Economizado:
            R$ <?php echo number_format($total_economizado, 2, ',', '.'); ?>
        </h3>
    <?php endif; ?>

    <?php if ($tipo_relatorio == 'rpvs_por_orgao'): ?>

        <h3>RPVs por Órgão</h3>

        <table class="tabela">
            <tr>
                <th>Órgão</th>
                <th>Total de RPVs</th>
            </tr>

            <?php while ($orgao = $resultado_orgaos->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $orgao['encaminhamento_para']; ?></td>
                    <td><?php echo $orgao['total']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>

        <h3>Pendentes de Encaminhamento: <?php echo $pendentes_orgao; ?></h3>

    <?php endif; ?>    

</section>

<?php include("../includes/footer.php"); ?>