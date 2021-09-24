<?php

use PFBC\Element;
?>
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
        <tr>
            <th>Graduação</th>
            <th>Pontos</th>
            <th>Avatar</th>
            <th>Banner</th>
            <!--<th>Valor Prêmio</th>-->
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($pager->getResult() as $object): /* @var $object PlanoCarreira */    ?>
            <tr>
                <td data-title="Graduação"><?php echo escape($object->getGraduacao()); ?></td>
                <td data-title="Pontos"><?php echo number_format($object->getPontos(), 0, '', '.') ?></td>
                <?php if($object->getImagem() != null): ?>
                <td data-title="IMAGEM"><img src="<?php echo asset('/admin/arquivos/') . $object->getImagem(); ?>" alt="imagem avatar" width='50vmin'></td>
                <?php else:?>
                <td data-title="IMAGEM"><img src="<?php echo asset('/admin/arquivos/avatanull.jpg') ?>" alt="imagem avatar" width='50vmin'></td>
                <?php endif ?>
                <?php if($object->getBannerGraduacao() != null): ?>
                <td data-title="BANNER"><img src="<?php echo asset('/admin/arquivos/') . $object->getBannerGraduacao(); ?>" alt="imagem banner" width='50vmin'></td>
                <?php else:?>
                <td data-title="BANNER"><img src="<?php echo asset('/admin/arquivos/avatanull.jpg') ?>" alt="imagem banner" width='50vmin'></td>
                <?php endif ?>
                <!--<td data-title="Valor Prêmio">R$ <?php //echo number_format($object->getValorPremio(), 2, ',', '.') ?></td>-->
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
