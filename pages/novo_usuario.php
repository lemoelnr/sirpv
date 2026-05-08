<?php
$apenas_admin = true;

include("../includes/header.php");
?>

<header class="header">
    <div>
        <h1>Novo Usuário</h1>
        <p>Cadastro de usuário do sistema</p>
    </div>

    <span>Usuário: <?php echo $_SESSION['usuario']; ?></span>
</header>

<section class="card form-card">

    <?php if(isset($_GET['erro']) && $_GET['erro'] == 'usuario_existe'): ?>
        <div class="msg-erro">
            Este nome de usuário já existe. Escolha outro.
        </div>
    <?php endif; ?>

    <form action="salvar_usuario.php" method="POST">

        <div class="form-group">
            <label>Nome completo</label>
            <input type="text" name="nome" required>
        </div>

        <div class="form-group">
            <label>Cargo</label>
            <input type="text" name="cargo">
        </div>

        <div class="form-group">
            <label>Usuário</label>
            <input type="text" name="usuario" required>
        </div>

        <div class="form-group">
            <label>Senha</label>
            <input type="password" name="senha" required>
        </div>

        <div class="form-group">
            <label>Perfil</label>
            <select name="perfil" required>
                <option value="admin">Admin</option>
                <option value="editor">Editor</option>
                <option value="consulta">Consulta</option>
            </select>
        </div>

        <div class="form-group">
            <label>Status</label>
            <select name="status" required>
                <option value="ativo">Ativo</option>
                <option value="inativo">Inativo</option>
            </select>
        </div>

        <div class="actions">
            <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Salvar Usuário</button>
        </div>

    </form>

</section>

<?php include("../includes/footer.php"); ?>