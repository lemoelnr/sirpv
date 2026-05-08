<?php
include("../includes/header.php");

$pesquisa = $_GET['pesquisa'] ?? "";
$pesquisa_sql = $conexao->real_escape_string($pesquisa);

$registros_por_pagina = 20;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;

if ($pagina < 1) {
    $pagina = 1;
}

$inicio = ($pagina - 1) * $registros_por_pagina;

/* TOTAL DE REGISTROS */
$sql_total = "
    SELECT COUNT(*) AS total 
    FROM rpvs 
    WHERE parte_credora_atual LIKE '%$pesquisa_sql%'
    OR cpf_cnpj LIKE '%$pesquisa_sql%'
    OR projudi_pje LIKE '%$pesquisa_sql%'
    OR processo_sei LIKE '%$pesquisa_sql%'
";

$resultado_total = $conexao->query($sql_total);
$total_registros = $resultado_total->fetch_assoc()['total'];
$total_paginas = ceil($total_registros / $registros_por_pagina);

/* BUSCA COM PAGINAÇÃO */
$sql = "
    SELECT * 
    FROM rpvs 
    WHERE parte_credora_atual LIKE '%$pesquisa_sql%'
    OR cpf_cnpj LIKE '%$pesquisa_sql%'
    OR projudi_pje LIKE '%$pesquisa_sql%'
    OR processo_sei LIKE '%$pesquisa_sql%'
    ORDER BY criado_em DESC
    LIMIT $inicio, $registros_por_pagina
";

$resultado = $conexao->query($sql);
?>

<header class="header">
    <div>
        <h1>Solicitações</h1>
        <p>Lista de RPVs cadastradas no sistema</p>
    </div>
    <span>Usuário: <?php echo $_SESSION['usuario']; ?></span>
</header>

<section class="section section-solicitacoes">

    <h2>RPVs cadastradas</h2>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'editado'): ?>
        <div class="msg-sucesso">
            Dados atualizados com sucesso!
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'excluido'): ?>
        <div class="msg-excluir">
            Registro excluído com sucesso!
        </div>
    <?php endif; ?>

    <form method="GET" class="form-pesquisa">
        <input 
            type="text"
            name="pesquisa"
            placeholder="Pesquisar Credor, CPF, Projudi ou SEI"
            value="<?php echo $pesquisa; ?>"
        >
        <button type="submit">Buscar</button>
        <a href="solicitacoes.php" class="btn-limpar">Limpar</a>
    </form>

    <div class="tabela-scroll">
        <table class="tabela">
            <thead>
                <tr>
                    <th>Nº RPV</th>
                    <th>Credor Atual</th>
                    <th>CPF/CNPJ</th>
                    <th>Unidade Judicial</th>
                    <th>Processo SEI</th>
                    <th>Valor Homologado</th>
                    <th>Ações</th>
                </tr>
            </thead>

            <tbody>

            <?php if ($resultado->num_rows > 0): ?>

                <?php while ($linha = $resultado->fetch_assoc()): ?>

                    <tr>
                        <td><?php echo $linha['numero_rpv']; ?></td>

                        <td><?php echo $linha['parte_credora_atual']; ?></td>

                        <td><?php echo $linha['cpf_cnpj']; ?></td>

                        <td><?php echo $linha['unidade_judicial']; ?></td>

                        <td><?php echo $linha['processo_sei']; ?></td>

                        <td>
                            R$ <?php
                            $valor_h = $linha['valor_homologado'] ?? 0;
                            echo number_format((float)$valor_h, 2, ',', '.');
                            ?>
                        </td>

                        <td>
                            <a href="editar.php?id=<?php echo $linha['id']; ?>" class="btn-editar">
                                <?php echo ($_SESSION['perfil'] == 'consulta') ? 'Visulaizar' : 'Ver+';?>
                            </a>

                            <?php if ($_SESSION['perfil'] == 'admin'): ?>

                                <a href="excluir.php?id=<?php echo $linha['id']; ?>"
                                class="btn-excluir"
                                onclick="return confirm('Deseja realmente excluir este registro?')">
                                    Excluir
                                </a>

                            <?php endif; ?>
                        </td>
                    </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>
                    <td colspan="7" class="sem-registro">
                        Nenhum registro encontrado.
                    </td>
                </tr>

            <?php endif; ?>

            </tbody>
        </table>
    </div>

    <!-- PAGINAÇÃO -->
    <?php if ($total_paginas > 1): ?>
        <div class="paginacao">

            <?php for ($i = 1; $i <= $total_paginas; $i++): ?>

                <a 
                    href="solicitacoes.php?pesquisa=<?php echo urlencode($pesquisa); ?>&pagina=<?php echo $i; ?>"
                    class="<?php echo ($i == $pagina) ? 'ativo' : ''; ?>"
                >
                    <?php echo $i; ?>
                </a>

            <?php endfor; ?>

        </div>
    <?php endif; ?>

</section>

<?php include("../includes/footer.php"); ?>