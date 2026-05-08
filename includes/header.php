<?php
// Inicia sessão
session_start();

// Verifica se está logado
if(!isset($_SESSION['usuario'])){
    header("Location: ../index.php");
    exit();
}

if(isset($apenas_admin) && $apenas_admin === true){
    if($_SESSION['perfil'] != 'admin'){
        header("Location: painel.php");
        exit();
    }
}


// Conexão com banco
include(__DIR__ . "/conexao.php");

// Descobre qual página está ativa (para menu)
$paginaAtual = basename($_SERVER['PHP_SELF']);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SIRPV</title>

    <!-- CSS principal -->
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body>

<div class="layout">

    <!-- Menu lateral -->
    <?php include(__DIR__ . "/menu.php"); ?>

    <!-- Conteúdo principal -->
    <main class="main">