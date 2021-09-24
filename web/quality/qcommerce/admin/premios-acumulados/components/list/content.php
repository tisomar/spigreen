<?php

use PFBC\Element;
?>
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
        <tr>
            <th>Pontos resgate</th>
            <th>Primeiro prêmio</th>
            <th>Segundo prêmio</th>
            <th>Percentual VME</th>
            <th>Graduação mínima</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($pager->getResult() as $object): /* @var $object PlanoCarreira */   
            if($object->getGraduacaoMinimaId() != null) :
                $graduacao = PlanoCarreiraQuery::create()
                    ->filterById($object->getGraduacaoMinimaId())
                    ->findOne()
                    ->getGraduacao();
            else:
                $graduacao = 'Graduação';
            endif;
            ?>
            <tr>
                <td data-title="Graduação"><?php echo escape($object->getPontosResgate()); ?></td>
                <td data-title="PrimeiroPremio"><?php echo $object->getPrimeiroPremio() ?></td>
                <td data-title="SegundoPremio"><?php echo $object->getSegundoPremio() ?></td>
                <td data-title="PercentualVme"><?php echo $object->getPercentualVme() ?></td>
                <td data-title="GraduacaoMimina"><?php echo $graduacao ?></td>
                <td class="text-right" data-title="Ações">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            Ações <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu text-left" role="menu">
                            <li><a title="Editar"  href="<?php echo $config['routes']['registration'] . '?id=' . $object->getId() ?>"><span class="icon-edit"></span> Editar</a></li>
                            <li class="divider"></li>
                            <li><a class="text-danger" title="Excluir" href="javascript:void(0);" data-href="<?php echo delete($_class, $object->getId()) ?>" data-action="delete" ><i class="icon-trash"></i> Excluir</a></li>
                        </ul>
                    </div>
                </td>
            </tr>
        <?php endforeach ?>
        <?php if ($pager->count() == 0): ?>
            <tr>
                <td colspan="10">Nenhum registro encontrado</td>
            </tr>
        <?php endif?>
        </tbody>

    </table>
</div>
<div class="col-xs-12">
    <?php echo $pager->showPaginacao(); ?>
</div>
