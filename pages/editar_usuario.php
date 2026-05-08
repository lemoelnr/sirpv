<?php
$apenas_admin = true;
include("../includes/header.php");

if (!isset($_GET['id'])) {
    header("Location: usuarios.php");
    exit();
}

$id = (int) $_GET['id'];

$sql = "SELECT * FROM usuarios WHERE id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();

$resultado = $stmt->get_result();
$usuario = $resultado->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuário</title>
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>

<div class="layout">


    <main class="main">

        <header class="header">
            <h1>Editar Usuário</h1>
            <span>Usuário: <?php echo $_SESSION['usuario']; ?></span>
        </header>

        <section class="card form-card">

            <form action="atualizar_usuario.php" method="POST">

                <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">

                <div class="form-group">
                    <label>Nome</label>
                    <input type="text" name="nome" value="<?php echo $usuario['nome']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Usuário</label>
                    <input type="text" name="usuario" value="<?php echo $usuario['usuario']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Nova Senha</label>
                    <input type="password" name="senha" placeholder="Deixe em branco para não alterar">
                </div>

                <div class="form-group">
                    <label>Cargo</label>
                    <input type="text" name="cargo" value="<?php echo $usuario['cargo']; ?>">
                </div>

                <div class="form-group">
                    <label>Perfil</label>
                    <select name="perfil">

                        <option value="admin" <?php if ($usuario['perfil'] == 'admin') echo 'selected'; ?>>
                            Admin
                        </option>

                        <option value="editor" <?php if ($usuario['perfil'] == 'editor') echo 'selected'; ?>>
                            Editor
                        </option>

                        <option value="consulta" <?php if ($usuario['perfil'] == 'consulta') echo 'selected'; ?>>
                            Consulta
                        </option>

                    </select>
                </div>

                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="ativo" <?php if ($usuario['status'] == 'ativo') echo 'selected'; ?>>
                            Ativo
                        </option>
                        <option value="inativo" <?php if ($usuario['status'] == 'inativo') echo 'selected'; ?>>
                            Inativo
                        </option>
                    </select>
                </div>

                <div class="actions">
                    <a href="usuarios.php" class="btn btn-secondary">Cancelar</a>
                    <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                </div>

            </form>

        </section>

    </main>

</div>

</body>
</html>