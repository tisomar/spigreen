<?php if ($frete && count($frete)): ?>
    <div class="box-primary">
        <div class="panel-body">
            Frete:
        <hr />
        <table class="table table-condensed pull-right">
            <?php foreach ($frete as $calcRow) : ?>
                <?php if ($calcRow['query']): ?>
                    <tr>
                        <td><?php echo $calcRow['modality']->getTitulo() ?></td>
                        <td><span class="text-success">R$ <?php echo $calcRow['query']->getValor(); ?></span><br></td>
                    </tr>
                <?php elseif ($calcRow['modality']): ?>
                    <tr>
                        <td colspan="2"><?php echo $calcRow['modality']->getTitulo() ?></td>
                    </tr>
                <?php endif; ?>
            <?php endforeach; ?>
        </table>
        </div>
    </div>
<?php endif; ?>

<div class="box-primary" id="<?php echo $id; ?>">
    <div class="panel-body">
        <form role="form" action="#" method="post">
            <div class="input-group">
                <input id="frete-cep" class="form-control mask-cep input-sm" type="text" placeholder="Seu CEP" name="CEP" value="<?php echo $container->getSession()->get('CEP_SIMULACAO') ?>" required>
                <span class="input-group-btn">
                    <button type="submit" class="btn btn-primary btn-sm">Consultar</button>
                </span>
            </div>
            <a href="http://www.buscacep.correios.com.br/sistemas/buscacep/" target="_blank" class="text-muted"><small>Não sabe seu CEP? Clique aqui</small></a>
        </form>

    </div>
</div>
