<?php /* @var $pager QPropelPager */  ?>
<div class="row">
    <div class="col-xs-6">
        Total de registros encontrados: <?php echo $pager->getTotalRecordCount() ?>
    </div>
    <div class="col-xs-6">
        <div class="dataTables_paginate paging_bootstrap">
            <ol class="pagination">
                <li class="prev <?php echo $pager->atFirstPage() ? 'disabled' : '' ?>">
                    <a href="<?php echo escape($pager->getPrev() !== false ? $pager->gerarUrlUsandoGlobais($pager->getPrev()) : '#') ?>"><i class="fa fa-long-arrow-left"></i> Anterior</a>
                </li>
            <?php foreach ($pager->getPrevLinks(3) as $page) :  ?>
                <li class="">
                    <a href="<?php echo escape($pager->gerarUrlUsandoGlobais($page)) ?>"><?php echo $page ?></a>
                </li>
            <?php endforeach ?>    
                <li class="active">
                    <a href="<?php echo escape($pager->gerarUrlUsandoGlobais($pager->getPage())) ?>"><?php echo $pager->getPage() ?></a>
                </li>
            <?php foreach ($pager->getNextLinks(3) as $page) :  ?>
                <li class="">
                    <a href="<?php echo escape($pager->gerarUrlUsandoGlobais($page)) ?>"><?php echo $page ?></a>
                </li>
            <?php endforeach ?>
                <li class="next <?php echo $pager->atLastPage() ? 'disabled' : '' ?>">
                    <a href="<?php echo escape($pager->getNext() !== false ? $pager->gerarUrlUsandoGlobais($pager->getNext()) : '#') ?>">Próxima <i class="fa fa-long-arrow-right"></i></a>
                </li>
            </ol>
        </div>
    </div>
</div>
