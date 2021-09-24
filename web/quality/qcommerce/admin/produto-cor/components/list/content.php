<?php if ($pager->count() > 0) : ?>
    <?php foreach ($pager->getResult() as $object) : /* @var $object Banner */ ?>
        <div class="col-xs-12 col-sm-12 col-md-4">
            <div class="panel panel-gray">
                <div class="panel-heading">
                    <h4>
                        <?php echo resumo($object->getNome(), 30); ?>
                    </h4>
                    <div class="options">
                        <a class="" href="#" class="" data-action="delete" data-href="<?php echo delete($_class, $object->getId()); ?>">
                                <span class="text-danger">
                                    <span class="icon-trash"></span>
                                </span>
                        </a>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="col-xs-3">
                        <?php echo $object->getBoxColor(32, 32); ?>
                    </div>
                    <div class="col-xs-9">
                        <div class="text-right">
                            <a class="btn btn-link" href="<?php echo $config['routes']['registration'] . '?id=' . $object->getId() ?>" class="">
                                <span class="icon-edit"></span> Editar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php else : ?>
    Nenhum registro encontrado.
<?php endif; ?>

<div class="clearfix"></div>
<div class="col-xs-12">
    <?php echo $pager->showPaginacao(); ?>
</div>
