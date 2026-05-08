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

// Pegando dados do formulário
$id = $_POST['id'];
$nome = $_POST['nome'];
$usuario = $_POST['usuario'];
$cargo = $_POST['cargo'];
$perfil = $_POST['perfil'];
$status = $_POST['status'];
$senha = $_POST['senha'];

// Atualizando no banco
if(!empty($senha)){
    $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

    $sql = "UPDATE usuarios SET
        nome = '$nome',
        usuario = '$usuario',
        cargo = '$cargo',
        perfil = '$perfil',
        status = '$status',
        senha = '$senha_hash'
        WHERE id = $id";
}else{
    $sql = "UPDATE usuarios SET
        nome = '$nome',
        usuario = '$usuario',
        cargo = '$cargo',
        perfil = '$perfil',
        status = '$status'
        WHERE id = $id";
}

if($conexao->query($sql)){
    header("Location: usuarios.php?msg=atualizado");
    exit();
}else{
    echo "Erro ao atualizar usuário";
}
?>