<form role="form" method="post" class="form-banco form-disabled-on-load">
    <h2>Informe os dados do banco</h2>
    
    <input type="hidden" id="idBanco" name="cadastroBanco[ID]">
    
    <div class="form-group">
        <label for="banco">* Banco:</label>
        <input type="text" class="form-control" id="banco" required name="cadastroBanco[BANCO]" value="<?php echo escape($arrResgate['BANCO']) ?>">
    </div>
    <div class="form-group">
        <label for="agencia">* Agência:</label>
        <input type="text" class="form-control" id="agencia" required name="cadastroBanco[AGENCIA]" value="<?php echo escape($arrResgate['AGENCIA']) ?>">
    </div>
    <div class="form-group">
        <label for="conta">* Conta:</label>
        <input type="text" class="form-control" id="conta" required name="cadastroBanco[CONTA]" value="<?php echo escape($arrResgate['CONTA']) ?>">
    </div>
    <div class="form-group">
        <label for="conta">* Tipo Conta:</label>
        <?php echo get_form_select(array(Resgate::CONTA_CORRENTE => 'Conta Corrente', Resgate::CONTA_POUPANCA => 'Poupança'), $arrResgate['TIPO_CONTA'], array(
            'name' => 'cadastroBanco[TIPO_CONTA]',
            'id' => 'tipo_conta',
            'required' => 'required',
            'class' => 'form-control'
        )) ?>
    </div>
    <div class="form-group">
        <label for="conta">* Pis/Pasep (Obrigatório apenas para Pessoa Física):</label>
        <input type="text" class="form-control" id="pispaseo" name="cadastroBanco[PIS_PASEP]" value="<?php echo escape($arrResgate['PIS_PASEP']) ?>">
    </div>
    <div class="form-group">
        <label for="nome">* Nome titular:</label>
        <input type="text" class="form-control" id="nome" required name="cadastroBanco[NOME_CORRENTISTA]" value="<?php echo escape($arrResgate['NOME_CORRENTISTA']) ?>">
    </div>
    <div class="form-group">
        <label for="cpf">* CPF titular:</label>
        <input type="text" class="form-control mask-cpf" id="cpf" required name="cadastroBanco[CPF_CORRENTISTA]" value="<?php echo escape($arrResgate['CPF_CORRENTISTA']) ?>">
    </div>
    <div class="form-group">
        <label for="cpf"> CNPJ:</label>
        <input type="text" class="form-control mask-cnpj" id="cnpj" name="cadastroBanco[CNPJ_CORRENTISTA]">
    </div>
    <div class="form-group">
    <?php if ($resgateDesabilitado): ?>
        <button disabled type="submit" class="btn btn-theme btn-block" title="Resgate de pontos desabilitado">Enviar</button>
    <?php else: ?>
        <button type="submit" class="btn btn-theme btn-block">Enviar</button>
    <?php endif; ?>
    </div>
</form>