// Quando a página terminar de carregar
window.addEventListener('load', function(){

    // Procura na tela algum elemento com classe msg-sucesso
    // Exemplo: mensagem de cadastro, edição ou exclusão concluída
    const mensagem = document.querySelector('.msg-sucesso, .msg-excluir, .msg-erro');

    // Se encontrou a mensagem na página
    if(mensagem){

        // Pega o nome da página atual
        // Exemplo:
        // /sistema/solicitacoes.php?msg=excluido
        // Resultado final: solicitacoes.php
        const paginaAtual = window.location.pathname.split('/').pop();

        // Remove parâmetros da URL sem recarregar a página
        // Exemplo:
        // solicitacoes.php?msg=excluido
        // vira:
        // solicitacoes.php
        history.replaceState(null, null, paginaAtual);

        // Aguarda 3 segundos (3000 milissegundos)
        // Depois esconde a mensagem automaticamente
        setTimeout(function(){
            mensagem.style.display = 'none';
        }, 3000);
    }

});


// Quando o usuário clicar em qualquer lugar da página
document.addEventListener('click', function(){

    // Procura a mensagem novamente
    const mensagem = document.querySelector('.msg-sucesso, .msg-excluir, .msg-erro');

    // Se ela existir
    if(mensagem){

        // Esconde imediatamente ao clicar
        mensagem.style.display = 'none';
    }

});

function gerarCertidao(event) {
    event.preventDefault(); // impede envio duplicado

    let senha = prompt("Digite sua senha para gerar a certidão:");

    if (!senha) {
        alert("Senha obrigatória!");
        return;
    }

    document.getElementById('senha_certidao').value = senha;
    document.getElementById('acao').value = "gerar_certidao";

    let form = document.querySelector('form');

    form.setAttribute("target", "_blank");

    form.submit();
}

function formatarCpfCnpj(campo) {
    let valor = campo.value.replace(/\D/g, '');

    if (valor.length <= 11) {
        // CPF
        valor = valor.replace(/(\d{3})(\d)/, "$1.$2");
        valor = valor.replace(/(\d{3})(\d)/, "$1.$2");
        valor = valor.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
    } else {
        // CNPJ
        valor = valor.replace(/^(\d{2})(\d)/, "$1.$2");
        valor = valor.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3");
        valor = valor.replace(/\.(\d{3})(\d)/, ".$1/$2");
        valor = valor.replace(/(\d{4})(\d)/, "$1-$2");
    }

    campo.value = valor;
}

function formatarCpfCnpj(campo) {
    let valor = campo.value.replace(/\D/g, '');

    if (valor.length <= 11) {
        // CPF
        valor = valor.replace(/(\d{3})(\d)/, "$1.$2");
        valor = valor.replace(/(\d{3})(\d)/, "$1.$2");
        valor = valor.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
    } else {
        // CNPJ
        valor = valor.replace(/^(\d{2})(\d)/, "$1.$2");
        valor = valor.replace(/^(\d{2})\.(\d{3})(\d)/, "$1.$2.$3");
        valor = valor.replace(/\.(\d{3})(\d)/, ".$1/$2");
        valor = valor.replace(/(\d{4})(\d)/, "$1-$2");
    }

    campo.value = valor;
}



function formatarMoeda(campo) {
    let valor = campo.value.replace(/\D/g, '');

    valor = (valor / 100).toFixed(2) + '';
    valor = valor.replace(".", ",");
    valor = valor.replace(/\B(?=(\d{3})+(?!\d))/g, ".");

    campo.value = valor;
}

function somenteNumeros(campo) {
    campo.value = campo.value.replace(/\D/g, '');
}

function formatarProcessoSEI(campo) {
    let valor = campo.value.replace(/\D/g, '');

    // Limita para 17 números: 13107 003741 2026 43
    valor = valor.substring(0, 17);

    // Formato final: 13107.003741/2026-43
    valor = valor.replace(/^(\d{5})(\d)/, '$1.$2');
    valor = valor.replace(/^(\d{5})\.(\d{6})(\d)/, '$1.$2/$3');
    valor = valor.replace(/(\/\d{4})(\d)/, '$1-$2');

    campo.value = valor;
}

const campoRpv = document.getElementById('numero_rpv');
const avisoRpv = document.getElementById('aviso-rpv');

campoRpv.addEventListener('keyup', function(){

    let numeroRpv = campoRpv.value;

    if(numeroRpv.length < 3){

        avisoRpv.innerHTML = '';
        return;

    }

    fetch('verificar_rpv.php?numero_rpv=' + numeroRpv)

    .then(resposta => resposta.text())

    .then(dados => {

        avisoRpv.innerHTML = dados;

    });

});