
<div class='col-xs-12 col-sm-4 pull-right'>
    <div class="search">
        <div id="suggestive-search">
            <select name="ordenar-por" class="form-control input-sm" id="pesquisaMelhoresItens" form="form-busca">
                <option value="">Busca avançada</option>
                <option value="preco-asc">Menor valor</option>
                <option value="mais-vendidos">Mais vendidos</option>
                <option value="melhor-avaliados">Avaliação</option>
                <option value="nome-asc">Ordem Alfabetica</option>
            </select>
        </div>
    </div>
</div>

<script>
    $('#pesquisaMelhoresItens').change(function() {
        var formPesquisa = $('#form-busca');
        formPesquisa.submit();
    });
</script>