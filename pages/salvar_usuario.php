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
$nome = $_POST['nome'];
$cargo = $_POST['cargo'];
$usuario = $_POST['usuario'];
$senha = $_POST['senha'];
$perfil = $_POST['perfil'];
$status = $_POST['status'];

// Criptografando a senha
$senha_hash = password_hash($senha, PASSWORD_DEFAULT);

// Verifica se o usuário já existe
$sql_verifica = "SELECT id FROM usuarios WHERE usuario = '$usuario'";
$resultado_verifica = $conexao->query($sql_verifica);

if($resultado_verifica->num_rows > 0){
    header("Location: novo_usuario.php?erro=usuario_existe");
    exit();
}

// Inserindo no banco
$sql = "INSERT INTO usuarios (nome, usuario, senha, perfil, cargo, status)
        VALUES ('$nome', '$usuario', '$senha_hash', '$perfil', '$cargo', '$status')";

if($conexao->query($sql)){
    header("Location: usuarios.php?msg=cadastrado");
    exit();
}else{
    echo "Erro ao cadastrar usuário.";
}
?>