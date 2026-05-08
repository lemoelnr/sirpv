<?php include("../includes/header.php"); ?>

<header class="header">
    <div>
        <h1>Cadastrar RPV</h1>
        <p>Preencha os dados da solicitação de pagamento.</p>
    </div>
    <span>Usuário: <?php echo $_SESSION['usuario']; ?></span>
</header>

<section class="section">
    <h2>Nova Solicitação</h2>

    <?php if(isset($_GET['sucesso'])): ?>
        <div class="msg-sucesso">
            RPV salva com sucesso!
        </div>
    <?php endif; ?>
    
    <?php if(isset($_GET['erro']) && $_GET['erro'] == 'valor_executado'): ?>
        <div class="msg-erro">
            Valor executado não pode ser maior que o valor requisitado.
        </div>
    <?php endif; ?>

    <form action="salvar_rpv.php" method="POST" class="form">
        <div class="form-group pequeno">
            <label>Processo SEI</label>
                <input 
                    type="text" 
                    name="processo_sei"
                    maxlength="20"
                    placeholder="00000.000000/0000-00"
                    onkeyup="formatarProcessoSEI(this)"
                    required
                >
        </div>

        <div class="form-group">
            <label>PROJUDI / PJE</label>
                <input 
                    type="text" 
                    name="projudi_pje"
                    required
>
        </div>

        <div class="form-group pequeno">
            <label>Unidade Judicial</label>
            <input type="text" name="unidade_judicial" required>
        </div>

        <div class="form-group">
            <label>Nº RPV</label>
            <input 
                type="text" 
                name="numero_rpv" 
                id="numero_rpv"
                required
            >

            <p id="aviso-rpv" class="aviso-rpv"></p>
        </div>

        <div class="form-group pequeno">
            <label>Parte Credora Inicial</label>
            <input type="text" name="parte_credora_inicial" >
        </div>

        <div class="form-group pequeno">
            <label>Parte Credora Atual</label>
            <input type="text" name="parte_credora_atual" >
        </div>

        <div class="form-group pequeno">
            <label>CPF / CNPJ</label>
        <input 
            type="text"
            name="cpf_cnpj"
            maxlength="18"
            onkeyup="formatarCpfCnpj(this)"
            onblur="validarCpfCnpj(this)"
            required
        >
        </div>

        <div class="linha-valor">
            <div class="form-group valor-box">
                <label>Valor Requisitado</label>
                <input 
                    type="text"
                    name="valor_requisitado"
                    placeholder="0,00"
                    onkeyup="formatarMoeda(this)"
                    required
                >
            </div>

            <div class="form-group valor-box">
                <label>Valor Homologado</label>
                <input 
                    type="text"
                    name="valor_homologado"
                    placeholder="0,00"
                    onkeyup="formatarMoeda(this)"
                >
            </div>

            <!--- campo novo-->
            <div class="form-group valor-box">
                <label>Valor Executado</label>
                <input 
                    type="text"
                    name="valor_executado"
                    placeholder="0,00"
                    onkeyup="formatarMoeda(this)"
                >
            </div>
        </div>
            

        <div class="form-group orgao-box">
            <label>Órgão Destino</label>
            <select name="encaminhamento_para" required>
                <option value="Pendente de Encaminhamento">Pendente de Encaminhamento</option>
                <option value="SEFAZ">SEFAZ</option>
                <option value="SESAU">SESAU</option>
                <option value="DETRAN">DETRAN</option>
                <option value="ITERAIMA">ITERAIMA</option>
                <option value="FEMARH">FEMARH</option>
            </select>
        </div>

        <input type="hidden" name="senha_certidao" id="senha_certidao">
        <input type="hidden" name="acao" id="acao">

        <div class="actions">
            <button type="reset" class="btn btn-light">Limpar</button>
            <button type="submit" name="acao" value="salvar" class="btn btn-primary">Salvar RPV</button>
            <button type="button" onclick="gerarCertidao(event)" class="btn btn-primary">Salvar e Gerar Certidão</button>
        </div>
    </form>
</section>

<?php include("../includes/footer.php"); ?>