<form role="form" method="post" class="form-contact form-disabled-on-load">
    <h2>Informe os dados do resgate</h2>
    <div class="form-group">
        <label for="qtd-pontos">* Valor:</label>
        <input type="text" class="mask-money form-control" id="qtd-pontos" required name="resgate[VALOR]" min="1" max="<?php echo (int)$totalPontosDisponiveis + PHP_INT_MAX ?>" value="<?php echo escape($arrResgate['VALOR']) ?>">
    </div>
    <div class="form-group">
        <label for="banco">* Banco:</label>
        <input type="text" class="form-control" id="banco" required name="resgate[BANCO]" value="<?php echo escape($arrResgate['BANCO']) ?>">
    </div>
    <div class="form-group">
        <label for="agencia">* Agência:</label>
        <input type="text" class="form-control" id="agencia" required name="resgate[AGENCIA]" value="<?php echo escape($arrResgate['AGENCIA']) ?>">
    </div>
    <div class="form-group">
        <label for="conta">* Conta:</label>
        <input type="text" class="form-control" id="conta" required name="resgate[CONTA]" value="<?php echo escape($arrResgate['CONTA']) ?>">
    </div>
    <div class="form-group">
        <label for="conta">* Tipo Conta:</label>
        <?php echo get_form_select(array(Resgate::CONTA_CORRENTE => 'Conta Corrente', Resgate::CONTA_POUPANCA => 'Poupança'), $arrResgate['TIPO_CONTA'], array(
            'name' => 'resgate[TIPO_CONTA]',
            'required' => 'required',
            'class' => 'form-control'
        )) ?>
    </div>
    <div class="form-group">
        <label for="conta">* Pis/Pasep:</label>
        <input type="text" class="form-control" id="pispaseo" required name="resgate[PIS_PASEP]" value="<?php echo escape($arrResgate['PIS_PASEP']) ?>">
    </div>
    <div class="form-group">
        <label for="nome">* Nome titular:</label>
        <input type="text" class="form-control" id="nome" required name="resgate[NOME_CORRENTISTA]" value="<?php echo escape($arrResgate['NOME_CORRENTISTA']) ?>">
    </div>
    <div class="form-group">
        <label for="cpf">* CPF titular:</label>
        <input type="text" class="form-control mask-cpf" id="cpf" required name="resgate[CPF_CORRENTISTA]" value="<?php echo escape($arrResgate['CPF_CORRENTISTA']) ?>">
    </div>
    <div class="form-group">
    <?php if ($resgateDesabilitado): ?>
        <button disabled type="submit" class="btn btn-theme btn-block" title="Resgate de pontos desabilitado">Enviar</button>
    <?php else: ?>
        <button type="submit" class="btn btn-theme btn-block">Enviar</button>
    <?php endif; ?>
    </div>
</form>