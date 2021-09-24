<?php
if ($pager->haveToPaginate()): /* @var $pager PropelModelPager */

    if (!isset($queryString) || (in_array($queryString, array('?', '/?', '/')))):
        $queryString = "";
    endif;

    $toFirst = $href . $pager->getFirstPage() . $queryString;
    $toLast = $href . $pager->getLastPage() . $queryString;

    $toPrev = $href . $pager->getPreviousPage() . $queryString;
    $toNext = $href . $pager->getNextPage() . $queryString;

    $arrPaginas = $pager->getLinks();
    ?>
    <div <?php echo $align ? 'class="text-' . $align . '"' : ''; ?>>
        <p>
            Resultado(s) <?php echo $pager->getFirstIndex(); ?> - <?php echo $pager->getLastIndex(); ?> de <?php echo $pager->getNbResults(); ?>
        </p>
        <ul class="pagination">
            <li><a href="<?php echo $toFirst ?>" title="Primeira página"><span class="<?php icon('angle-double-left'); ?>"></span></a></li>
            <li><a href="<?php echo $toPrev ?>" title="Página anterior" class="<?php icon('angle-left'); ?>"></a></li>

            <?php foreach ($arrPaginas as $numPagina): ?>
                <li class="<?php echo $numPagina == $pager->getPage() ? 'active' : '' ?>">
                    <a href="<?php echo $href . $numPagina . $queryString; ?>">
                        <?php echo $numPagina ?>
                    </a>
                </li>
            <?php endforeach; ?>

            <li><a href="<?php echo $toNext ?>"  title="Próxima página" class="<?php icon('angle-right'); ?>"></a></li>
            <li><a href="<?php echo $toLast ?>" title="Última página" class="<?php icon('angle-double-right'); ?>"></a></li>
        </ul>
    </div>
    <?php
endif;