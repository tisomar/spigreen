<div class="row">
    <div class="col-xs-12">
        <div class="form-group">
            <label for="cliente">Rede Cliente</label>
            <select class="form-control select2Cliente" name="cliente" id="cliente">
                <option value="">Selecione</option>
                <?php foreach($clientes as $key => $cliente) : ?>
                    <?php $selected = ($key == $selectedClient) ? 'selected="selected"' : '';?>
                    <option value="<?= $key ?>"  <?= $selected ?>><?= $cliente ?></option>
                <?php endforeach ?>
            </select> 
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label for="periodo-inicio">Início:</label>
            <input type="text" class="form-control datepicker" id="periodo-inicio" name="inicio" value="<?php echo ($dtInicio) ? $dtInicio->format('d/m/Y') : '' ?>">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="periodo-fim">Fim:</label>
            <input type="text" class="form-control datepicker" id="periodo-fim" name="fim" value="<?php echo ($dtFim) ? $dtFim->format('d/m/Y') : '' ?>">
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label for="periodo-pagamento-inicio">Pagamento Início:</label>
            <input type="text" class="form-control datepicker" id="periodo-pagamento-inicio" name="pagamento-inicio" value="<?php echo ($dtPagamentoInicio) ? $dtPagamentoInicio->format('d/m/Y') : '' ?>">
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            <label for="periodo-pagamento-fim">Pagamento Fim:</label>
            <input type="text" class="form-control datepicker" id="periodo-pagamento-fim" name="pagamento-fim" value="<?php echo ($dtPagamentoFim) ? $dtPagamentoFim->format('d/m/Y') : '' ?>">
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label for="geracao">Geração</label>
            <input class="form-control" type="number" placeholder="1, 2, 3..." min="0" max="<?= $maxGeracao ?>" value="<?= $filtroGeracao ?>" name="geracao" id="geracao">
        </div>
    </div>
  
    <div class="col-md-3">
        <div class="form-group">
            <label for="descricaoPontos">Descrição</label>
            <select class="form-control selectpicker" name="descricaoPontos" id="descricaoPontos">
                <option value="">Selecione</option>
                <?php foreach($listaPontosDescricao as $key => $pontosDescricao) : ?>
                    <?php $selected = ($key == $selectedDescricao) ? 'selected="selected"' : '';?>
                    <option value="<?php echo $key ?>"  <?php echo $selected ?> ><?php echo $pontosDescricao ?></option>
                <?php endforeach ?>
            </select> 
        </div>
    </div>
</div>

<script>
    $(function() {
        $('.select2Cliente').select2();
    });
</script>