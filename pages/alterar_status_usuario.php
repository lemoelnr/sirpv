<?php
session_start();

if(!isset($_SESSION['usuario'])){
    header("Location: ../index.php");
    exit();
}

if($_SESSION['perfil'] != 'admin'){
    header("Location: painel.php");
    exit();
}

include("../includes/conexao.php");

$id = (int) $_GET['id'];
$status = $_GET['status'];

if($status != 'ativo' && $status != 'inativo'){
    header("Location: usuarios.php");
    exit();
}

$sql = "UPDATE usuarios SET status = '$status' WHERE id = $id";

if($conexao->query($sql)){
    header("Location: usuarios.php?msg=status");
    exit();
}else{
    echo "Erro ao alterar status do usuário";
}
?>