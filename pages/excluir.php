<?php

session_start();

if(!isset($_SESSION['usuario'])){
    header("Location: ../index.php");
    exit();
}

if($_SESSION['perfil'] != 'admin'){

    header(Location: solicitacoes.php);
    exit();

}

include("../includes/conexao.php");

if(!isset($_GET['id'])){
    header("Location: solicitacoes.php");
    exit();
}

$id = (int) $_GET['id'];

$stmt = $conexao->prepare("DELETE FROM rpvs WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();

header("Location: solicitacoes.php?msg=excluido");
exit();

?>