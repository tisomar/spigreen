<?php if (!is_null($frete)) { ?>
    <div class="box-primary">
        <div class="panel-body">
            Frete:
            <span class="text-success">R$ <?php echo $frete->getValor() ?></span><br>
            <span class="text-muted small">
                Prazo estimado de entrega <?php if (!is_null($frete->endereco)) echo 'para ' . $frete->endereco['cidade'] . '/' . $frete->endereco['uf']; ?> em até <?php echo $frete->getPrazoExtenso(); ?>.
            </span>
        </div>
    </div>
<?php } ?>

<div class="box-primary" id="<?php echo $id; ?>">
    <div class="panel-body">
        <form role="form" action="#" method="post">
            <div class="input-group">
                <input id="frete-cep" class="form-control mask-cep input-sm" type="text" placeholder="Seu CEP" name="CEP" value="<?php echo $container->getSession()->get('CEP_SIMULACAO') ?>" required>
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-primary btn-sm">Consultar</button>
                </span>
            </div>
            <a href="http://m.correios.com.br/movel/buscaCep.do" target="_blank" class="text-muted"><small>Não sabe seu frete? Clique aqui</small></a>
        </form>

    </div>
</div>