<?php 
include("conexao.php");

$id = $_POST['id'];
$status = $_POST['status'];

$sql = "UPDATE rpvs SET status='$status' WHERE id=$id";
if($conexao->query($sql)){
    echo "ok";
}else{
    echo "erro";
}

?>