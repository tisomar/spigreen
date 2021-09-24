<?php
/**
 *  As ações deste arquivo estão em /produtos/actions/filtro.listagem.actions.php
 */
use QPress\Template\Widget;
// var_dump($request->query->get('ordenar-por'));

?>
<form role="form" class="form-filter">
    <?php if ($request->query->get('buscar-por')) : ?>
        <input type="hidden" id="filter-bar-busca" name="buscar-por" value="<?php echo escape($request->query->get('buscar-por')) ?>">
    <?php endif; ?>
    <div class="row">
        <div class="hidden-xs col-xs-12 col-sm-3">
            <?php echo get_select_filtro_exibicao($session->get('produtos-por-pagina')) ?>
        </div>
        <div class="col-xs-12 col-sm-3">
            <?php /* Este site sempre vai listar os produtos como "preco-asc" */  ?>
            <?php /* echo get_select_filtro_ordenacao($session->get('ordenar-por')) */ ?>
        </div>
        <div class="col-sm-9 d-flex">
            <div class='text-right col-sm-9'>
                <!-- SELECT DE BUSCA AVANÇADA -->
                <?php Widget::render('forms/searchAdvancedItems');?>
            </div>
            
            <div class="hidden-xs hidden-sm">
                <p>Visualização:</p>
                <div class="btn-group">
                    <button type="button" class="btn btn-default btn-sm active" data-products-visualization="grid" title="Grade">
                        <span class="<?php icon('th'); ?>"></span>
                    </button>
                    <button type="button" class="btn btn-default btn-sm"  data-products-visualization="list" title="Lista">
                        <span class="<?php icon('list'); ?>"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>

