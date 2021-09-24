<div id="specifications">
    <h2 class="h3">Informações Técnicas</h2>

    <table class="table table-striped">
        <tbody>
            <tr>
                <?php if (Config::get('mostrar_marcas') && $objProdutoDetalhe->getMarca()) : ?>
                    <td>Marca</td>
                    <td>
                        <a href="<?php echo $objProdutoDetalhe->getMarca()->getUrlListagem() ?>">
                            <?php echo escape($objProdutoDetalhe->getMarca()->getNome()); ?>
                        </a>
                    </td>
                <?php endif; ?>
            </tr>
            <tr>
                <?php if (escape($objProdutoDetalhe->getSku()) != '') : ?>
                    <td>Referência</td>
                    <td><?php echo escape($objProdutoDetalhe->getSku()) ?></td>
                <?php endif; ?>
            </tr>
            <tr>
                <td>Peso</td>
                <td>3.5kg</td>
            </tr>
            <tr>
                <td>Cor</td>
                <td>Preto</td>
            </tr>
            <tr>
                <td>Garantia</td>
                <td>12 meses pelo fabricante</td>
            </tr>
        </tbody>
    </table>
</div>
