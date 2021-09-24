<div id="suggestive-search" class="<?php echo isset($class) ? $class : ''; ?>">
    <form id='form-busca' role="form" class="form-search" method="get" action="<?php echo get_url_site(); ?>/busca/">
        <input
            id="suggestive-search-input"
            class="form-control auto-complete"
            type="text"
            name="buscar-por"
            placeholder="Pesquisar..."
            value="<?php echo $container->getRequest()->query->get('buscar-por') ?>"
            required
            >
        <button type="submit" class="<?php icon('search'); ?>" title="Buscar"></button>
    </form>
</div>