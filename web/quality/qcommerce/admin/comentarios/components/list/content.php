<?php
function stars($nota)
{
    $html = "<span class='" . $nota . "'>";
    for ($i = 1; $i <= 5; $i++) {
        $icon = "icon-star" . ($nota < $i ? '-empty' : '');
        $html .= '<i class="text-warning ' . $icon . '"></i> ';
    }
    $html .= "</span>";
    return $html;
}
?>

<?php foreach ($pager->getResult() as $object) : /* @var $object ProdutoComentario */ ?>
    <div class="col-sm-12 ">
        <div class="panel panel-<?php echo $object->getStatusClass() ?>">
            <div class="panel-body">
                <p>
                    Avaliação enviada por
                    <a target="_blank" href="<?php echo get_url_admin() . '/clientes/registration/?id=' . $object->getCliente()->getId() ?>">
                        <?php echo $object->getCliente()->getNomeCompleto(); ?>
                    </a> em <?php echo $object->getData('d/m/Y'); ?>
                </p>
                <div class="alert">
                    <h4>
                        <?php echo $object->getTitulo() ?>
                        <small><?php echo stars($object->getNota()); ?></small>
                    </h4>
                    <p><i><?php echo nl2br($object->getDescricao()); ?></i></p>
                </div>
                <p>Produto: <a target="_blank" href="<?php echo get_url_admin() . '/produtos/registration/?id=' . $object->getProduto()->getId() ?>">
                        <?php echo $object->getProduto()->getNome(); ?>
                    </a>
                </p>
                <div>
                    <div class="col-xs-6">
                        <?php if ($object->getStatus() == ProdutoComentario::STATUS_PENDENTE) : ?>
                            <a href="<?php echo get_url_admin() . '/' . $router->getModule() . '/aprovar?id=' . $object->getId() ?>" class="btn btn-primary"><span class="icon-ok"></span> Aprovar</a>
                        <?php else : ?>
                            <a href="#" class="disabled btn btn-success"><span class="icon-ok"></span> Aprovado</a>
                        <?php endif; ?>
                    </div>
                    <div class="col-xs-6">
                        <a data-action="delete" data-href="<?php echo delete($_class, $object->getId()) ?>" href="#" class="pull-right btn btn-brown"><span class="icon-trash"></span> Deletar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php endforeach; ?>

<div class="col-xs-12">
    <?php echo $pager->showPaginacao(); ?>
</div>
