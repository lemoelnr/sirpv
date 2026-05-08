<?php
$apenas_admin = true;

include("../includes/header.php");

// Buscar usuários
$sql = "SELECT * FROM usuarios ORDER BY criado_em DESC";
$resultado = $conexao->query($sql);
?>

<header class="header">
    <div>
        <h1>Usuários</h1>
        <p>Gerenciamento de usuários</p>
    </div>

    <span>Usuário: <?php echo $_SESSION['usuario']; ?></span>
</header>

<section class="card">

    <div class="actions">
        <a href="novo_usuario.php" class="btn btn-primary">+ Novo Usuário</a>
    </div>

    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'atualizado'): ?>
        <div class="msg-sucesso">
            Usuário atualizado com sucesso!
        </div>
    <?php endif; ?>

    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'status'): ?>
        <div class="msg-sucesso">
            Status do usuário alterado com sucesso!
        </div>
    <?php endif; ?>

    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'excluido'): ?>
        <div class="msg-excluir">
            Usuário excluído com sucesso!
        </div>
    <?php endif; ?>

    <?php if(isset($_GET['msg']) && $_GET['msg'] == 'nao_pode_excluir'): ?>
        <div class="msg-erro">
            Você não pode excluir o usuário logado.
        </div>
    <?php endif; ?>

    <table class="tabela">
        <tr>
            <th>Nome</th>
            <th>Cargo</th>
            <th>Usuário</th>
            <th>Perfil</th>
            <th>Status</th>
            <th>Ações</th>
        </tr>

        <?php while($user = $resultado->fetch_assoc()): ?>

            <tr>
                <td><?php echo $user['nome']; ?></td>
                <td><?php echo $user['cargo']; ?></td>
                <td><?php echo $user['usuario']; ?></td>
                <td><?php echo $user['perfil']; ?></td>
                <td><?php echo $user['status']; ?></td>

                <td>
                    <a href="editar_usuario.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-primary">
                        Editar
                    </a>

                    <?php if($user['usuario'] != $_SESSION['usuario']): ?>

                        <?php if($user['status'] == 'ativo'): ?>
                            <a href="alterar_status_usuario.php?id=<?php echo $user['id']; ?>&status=inativo" 
                               class="btn btn-sm btn-secondary">
                                Desativar
                            </a>
                        <?php else: ?>
                            <a href="alterar_status_usuario.php?id=<?php echo $user['id']; ?>&status=ativo" 
                               class="btn btn-sm btn-success">
                                Ativar
                            </a>
                        <?php endif; ?>

                        <a href="excluir_usuario.php?id=<?php echo $user['id']; ?>"
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Tem certeza que deseja excluir este usuário?');">
                            Excluir
                        </a>

                    <?php endif; ?>
                </td>
            </tr>

        <?php endwhile; ?>

    </table>

</section>

<?php include("../includes/footer.php"); ?>