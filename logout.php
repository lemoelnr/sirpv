<?php
// Inicia a sessão atual.
// Necessário para acessar os dados do usuário logado.
session_start();

// Remove todas as variáveis salvas na sessão.
// Exemplo: $_SESSION['usuario']
session_unset();

// Destrói a sessão completamente no servidor.
// Aqui o usuário deixa de estar logado.
session_destroy();

// Redireciona o usuário para a tela de login.
header("Location: index.php");

// Encerra o script imediatamente.
// Garante que nada mais será executado depois do redirecionamento.
exit();

?>