<div id="menu-desktop">
    <?php if(isset($full) && $full == true): ?>
    <div class="container">
        <?php endif; ?>
        <nav class="clearfix">
            <div class="row">
                <div class="col-xs-12">
                    <?php echo CategoriaPeer::renderCategoriasDestaques(); ?>
                </div>
<!--                <div class="col-md-3 col-lg-3">-->
<!--                    <ul class="list-unstyled first-level">-->
<!--                        <li class="pull-right">-->
<!--                            <a title="Ver mais categorias" href="javascript:void(0);" class="btn-block open-all-categories collapsed" data-toggle="collapse" data-target="#categories">-->
<!--                                --><?php //if(isset($full) && $full == true): ?>
<!--                                    <span class="visible-md visible-lg">-->
<!--                                        Todas as categorias <i class="--><?php //icon('chevron-up'); ?><!--"></i><i class="--><?php //icon('chevron-down'); ?><!--"></i>-->
<!--                                    </span>-->
<!--                                    <span class="visible-xs visible-sm">-->
<!--                                        <i class="--><?php //icon('bars') ?><!--"></i>-->
<!--                                    </span>-->
<!--                                --><?php //else: ?>
<!--                                    <span class="--><?php //icon('bars') ?><!--"></span>-->
<!--                                --><?php //endif; ?>
<!--                            </a>-->
<!--                        </li>-->
<!--                    </ul>-->
<!--                </div>-->
            </div>
<!--            <div id="categories">-->
<!--                --><?php // echo CategoriaPeer::renderCategoriasTodas(); ?>
<!--            </div>-->
        </nav>
        <?php if(isset($full) && $full == true): ?>
    </div>
<?php endif; ?>
</div>
