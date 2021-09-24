<form action="" method="POST" id="form-convite-depoimento" class="form-horizontal row-border">
    <div class="panel">
        <div class="panel-body">
            
            <div class="form-group">
                <label class="col-sm-3 control-label" for="cliente">Cliente</label>
                <div class="col-sm-6">
                    <?php
                        $cliente = !empty($arrConvite['CLIENTE_DISTRIBUIDOR_ID'])
                                ? ClienteDistribuidorQuery::create()
                                    ->filterByClienteId(ClientePeer::getClienteLogado()->getId())
                                    ->filterById($arrConvite['CLIENTE_DISTRIBUIDOR_ID'])
                                    ->findOne()
                                : null
                    ?>
                    <input type="hidden" class="cliente-select" id="cliente" name="convite[CLIENTE_DISTRIBUIDOR_ID]" required data-nome-completo="<?php echo escape($cliente ? $cliente->getNomeCompleto() : '') ?>" value="<?php echo escape($arrConvite['CLIENTE_DISTRIBUIDOR_ID']) ?>" style="width:100%"/>
                </div>
            </div>
                        
        </div>
        
        <div class="panel-footer">
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3">
                    <div class="btn-toolbar">
                        <button class="btn-primary btn" type="submit">Enviar</button>
                        <button class="btn-default btn" onclick="location.href = '<?php echo $root_path . '/distribuidores_novo/depoimentos' ?>'; return false;">Cancelar</button>
                    </div>
                </div>
            </div>
        </div>
        
    </div>
</form>
