<?php
session_start();

include("includes/conexao.php");

$usuario = trim($_POST['usuario'] ?? '');
$senha = $_POST['senha'] ?? '';

if (empty($usuario) || empty($senha)) {
    header("Location: index.php");
    exit();
}

$sql = "SELECT * FROM usuarios 
        WHERE usuario = ? 
        AND status = 'ativo'
        LIMIT 1";

$stmt = $conexao->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();

$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {

    $dados_usuario = $resultado->fetch_assoc();

    if (password_verify($senha, $dados_usuario['senha'])) {

        session_regenerate_id(true);

        $_SESSION['usuario'] = $dados_usuario['usuario'];
        $_SESSION['perfil'] = $dados_usuario['perfil'];

        header("Location: pages/painel.php");
        exit();

    }
}

header("Location: index.php?erro=login");
exit();
?>