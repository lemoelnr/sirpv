// Espera a página editar.php carregar completamente
window.addEventListener('load', function(){

    // Procura na página uma mensagem de sucesso
    const mensagem = document.querySelector('.msg-sucesso');

    // Se existir mensagem de sucesso na tela
    if(mensagem){

        // Remove o POST do histórico para evitar reenviar formulário ao atualizar a página
        history.replaceState(null, null, window.location.href);

    }

});

// Quando o usuário clicar em qualquer lugar da página
document.addEventListener('click', function(){

    // Procura a mensagem de sucesso
    const mensagem = document.querySelector('.msg-sucesso');

    // Se a mensagem existir
    if(mensagem){

        // Esconde a mensagem
        mensagem.style.display = 'none';

    }

});

// Limpa URL da página solicitações
window.addEventListener('load', function(){
    if(window.location.search.includes('msg=editado')){
        history.replaceState(null, null, 'solicitacoes.php');
    }
});
/*
// Atualiza o status automaticamente quando o usuário muda o select
const selectStatus = document.querySelectorAll('.status-select');

selectStatus.forEach(function(select){

    select.addEventListener('change', function(){

        const id = this.getAttribute('data-id');
        const status = this.value;

        fetch('atualizar_status.php',{
            method: 'POST',
            headers: {
                'Content-Type' : 'application/x-www-form-urlencoded'
            },
            body: 'id=' + id + '&status=' + status
        })
        .then(function(resposta){
            return resposta.text();
        })
        .then(function(resultado){
            if(resultado == 'ok'){
                mostrarMensagem('Status atualizado com sucesso', 'sucesso');
            }else{
                mostrarMensagem('Erro ao atualizar status.', 'erro');
            }
        });
    });
});

function mostrarMensagem(texto, tipo){

    const mensagem = document.createElement('div');

    mensagem.classList.add('msg-flutuante');
    mensagem.classList.add(tipo);

    mensagem.innerText = texto;

    document.body.appendChild(mensagem);

    setTimeout(function(){
        mensagem.remove();
    }, 3000);

}

*/