<link rel="stylesheet" href="css/styles.css">
<?php
$paginaAtual = basename($_SERVER['PHP_SELF']);
?>

<aside class="sidebar">
    <h2 class="sidebar__logo">SIRPV</h2>

    <nav class="sidebar__menu">

        <a href="painel.php"
        class="<?= ($paginaAtual == 'painel.php') ? 'active' : ''; ?>">
        Início
        </a>

        <a href="formulario_rpv.php"
        class="<?= ($paginaAtual == 'formulario_rpv.php') ? 'active' : ''; ?>">
        Cadastrar RPV
        </a>

        <a href="solicitacoes.php"
        class="<?= ($paginaAtual == 'solicitacoes.php') ? 'active' : ''; ?>">
        Solicitações
        </a>

        <a href="relatorios.php"
            class="<?= ($paginaAtual == 'relatorios.php') ? 'active' : ''; ?>">
            Relatórios
        </a>


        <?php if($_SESSION['perfil'] == 'admin'): ?>
            <a href="usuarios.php"  
            class="<?= ($paginaAtual == 'usuarios.php') ? 'active' : ''; ?>">
            Usuários
            </a>
        <?php endif; ?>

        <a href="../logout.php">Sair</a>

    </nav>

    <div class="logo-bottom">
            <img src="../img/logo_branca.svg" alt="">
    </div>
</aside>