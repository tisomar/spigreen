<?php foreach ($pager->getResult() as $object) : /* @var $object Marca */ ?>
    <div class="col-xs-12 col-sm-6 col-md-3">
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
                <div class="text-center" style="width: 175px; height: 144px">
                    <?php echo $object->getThumb('width=155&height=124&cropratio=1.25:1', array('class' => 'center-block')); ?>
                    <a class="btn btn-link" href="<?php echo $config['routes']['registration'] . '?id=' . $object->getId() ?>" class="">
                        <span class="icon-edit"></span> Editar
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
<div class="clearfix"></div>
<div class="col-xs-12">
    <?php echo $pager->showPaginacao(); ?>
</div>
