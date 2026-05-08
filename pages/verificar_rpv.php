<?php

include("../includes/conexao.php");

$numero_rpv = $_GET['numero_rpv'] ?? '';

$sql = "SELECT cpf_cnpj, projudi_pje
        FROM rpvs
        WHERE numero_rpv = '$numero_rpv'";

$resultado = $conexao->query($sql);

$total = $resultado->num_rows;

if($total > 0){

    $dados = $resultado->fetch_assoc();

    echo "⚠ Já existem $total RPVs cadastradas com este número.<br>";

    echo "CPF/CNPJ encontrado: " . $dados['cpf_cnpj'] . "<br>";

    echo "Processo judicial encontrado: " . $dados['projudi_pje'];

}