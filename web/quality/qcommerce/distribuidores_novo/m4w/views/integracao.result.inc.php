<form action="" method="POST" id="form-cadastro-clientes" class="form-horizontal row-border">
    <div class="panel">
        <div class="panel-body">

            <div class="form-group">
                <label class="col-sm-3 control-label"></label>
                <div class="col-sm-6">
                    <label class="control-label">Exportação dos contato <b>EFETUADA COM SUCESSO !!!</b></label>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label"></label>
                <div class="col-sm-6">
                    <label class="control-label">Sua EXPORTAÇÃO esta em uma fila de IMPORTAÇÃO no Mail For Web. Em breve seus contatos serão sincronizados.</label>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label">Endereço de acompanhamento</label>
                <div class="col-sm-6">

                    <button type="button" title="Adicionar Cliente" class="btn btn-primary btn adicionar-novo" name="" style="margin-left: 10px;">
                        <a href="<?= $url; ?>" target="_blank" style="color: #FFFFFF !important">Clique aqui</a>
                    </button>
                </div>
            </div>

        </div>

        <div class="panel-footer">
            <div class="row">
                <div class="col-sm-6 col-sm-offset-3">
                    <div class="btn-toolbar">
                        <button class="btn-default btn" onclick="location.href = '<?= $_GET['redirect']; ?>'; return false;">Voltar</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</form>