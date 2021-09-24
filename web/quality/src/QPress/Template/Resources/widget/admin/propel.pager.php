<?php
$range      = range(1, $pager->getTotalPages(), 1);
$options    = array_combine($range, $range);
$attributte = array('class' => 'input-sm', 'id' => 'pageGoTo');

$selectElementPage = get_form_select($options, $pager->getPage(), $attributte);

?>

<div class="pull-left">
    <div class="row">
        <div class="col-xs-12">
            <span>Total de registros encontrados: <?php echo $pager->getTotalRecordCount() ?></span>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <form class="form-inline" role="form">
                <div class="pagination">
                    Mostrando página <?php echo $selectElementPage ?> de <?php echo $pager->getTotalPages() ?>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="pull-right">
    <div class="row">
        <div class="col-xs-12">
            <ul class="pagination">

                <li class="<?php echo $pager->getPrev() === false ? 'disabled' : '' ?>">
                    <a href="javascript:;" data-page="<?php echo $pager->getPrev() ?>">&larr; <span class="hidden-sm">anterior</span></a>
                </li>

                <?php foreach ($pager->getPrevLinks(3) as $nPage): ?>
                    <li>
                        <a href="javascript:;" data-page="<?php echo $nPage ?>"><?php echo $nPage ?></a>
                    </li>
                <?php endforeach; ?>

                <li class="active">
                    <a href="javascript:;" data-page="<?php echo $pager->getPage() ?>"><?php echo $pager->getPage() ?></a>
                </li>

                <?php foreach ($pager->getNextLinks(3) as $nPage): ?>
                    <li>
                        <a href="javascript:;" data-page="<?php echo $nPage ?>"><?php echo $nPage ?></a>
                    </li>
                <?php endforeach; ?>

                <li class="<?php echo $pager->getNext() === false ? 'disabled' : '' ?>">
                    <a href="javascript:;" data-page="<?php echo $pager->getNext() ?>"><span class="hidden-sm">próxima &rarr;</span></a>
                </li>

            </ul>

        </div>

    </div>

</div>

<script>
    $(function() {
        var submitFormFiltro = function(page) {
            $('[name=\'page\']').val(page).parents('form').submit();
        }
        $('#pageGoTo').on('change', function(){
            submitFormFiltro($(this).val());
        });
        $('.pagination a').on('click', function(){
            if ($(this).parent().hasClass('disabled')) {
                return false;
            }
            submitFormFiltro($(this).data('page'));
        });
    });
</script>
