<form action="" method="POST" id="form-cadastro-observacoes" class="form-horizontal row-border">
    <div class="panel">
        <div class="panel-body">
            
            <div class="form-group">
                <label class="col-sm-3 control-label" for="cliente">Cliente</label>
                <div class="col-sm-6">
                    <?php
                        $cliente = !empty($arrObservacao['CLIENTE_DISTRIBUIDOR_ID'])
                                ? ClienteDistribuidorQuery::create()
                                    ->filterByClienteId(ClientePeer::getClienteLogado()->getId())
                                    ->filterById($arrObservacao['CLIENTE_DISTRIBUIDOR_ID'])
                                    ->findOne()
                                : null
                    ?>
                    <input type="hidden" class="cliente-select" id="cliente" name="observacao[CLIENTE_DISTRIBUIDOR_ID]" required data-nome-completo="<?php echo escape($cliente ? $cliente->getNomeCompleto() : '') ?>" value="<?php echo escape($arrObservacao['CLIENTE_DISTRIBUIDOR_ID']) ?>" style="width:100%"/>
                </div>
            </div>
            
            <div class="form-group">
                <label class="col-sm-3 control-label" for="observacao">Observação</label>
                <div class="col-sm-6">
                    <textarea class="form-control" id="observacao" name="observacao[OBSERVACAO]" rows="5" cols="50" required><?php echo escape($arrObservacao['OBSERVACAO']) ?></textarea>
                </div>
            </div>
            
        </div>
        
        <div class="panel-footer">
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3">
                    <div class="btn-toolbar">
                        <button class="btn-primary btn" type="submit">Salvar</button>
                        <button class="btn-default btn" onclick="location.href = '<?php echo $root_path . '/distribuidores_novo/observacoes' ?>'; return false;">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</form>
