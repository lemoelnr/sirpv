<?php
$apenas_admin = true;
include("../includes/header.php");

if(!isset($_GET['id'])){
    header("Location: usuarios.php");
    exit();
}

$id = (int) $_GET['id'];

if($id <= 0){
    header("Location: usuarios.php");
    exit();
}

// Não permitir excluir o próprio usuário
$sql_check = "SELECT usuario FROM usuarios WHERE id = ?";
$stmt_check = $conexao->prepare($sql_check);
$stmt_check->bind_param("i", $id);
$stmt_check->execute();

$res_check = $stmt_check->get_result();

if($res_check && $res_check->num_rows > 0){
    $u = $res_check->fetch_assoc();

    if($u['usuario'] == $_SESSION['usuario']){
        header("Location: usuarios.php?msg=nao_pode_excluir");
        exit();
    }
}

// Excluir usuário
$sql = "DELETE FROM usuarios WHERE id = ?";
$stmt = $conexao->prepare($sql);
$stmt->bind_param("i", $id);

if($stmt->execute()){
    header("Location: usuarios.php?msg=excluido");
    exit();
}else{
    echo "Erro ao excluir usuário";
}
?>