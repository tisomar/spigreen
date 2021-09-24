<div class="col-xs-12">
    <div class="panel">
        <div class="panel-heading">
            <h4><i class="glyphicon glyphicon-filter"></i> Filtros</h4>
            <div class="options">
                <a class="panel-collapse" href="#"><i class="glyphicon glyphicon-chevron-down"></i></a>
            </div>
        </div>
        <div class="panel-body collapse in">
            <form action="" id="form-filter" method="get" class="form-inline">
                <div class="form-group">
                    <?php
                        $cliente = !empty($arrFilter['cliente'])
                                ? ClienteDistribuidorQuery::create()
                                    ->filterByClienteId(ClientePeer::getClienteLogado()->getId())
                                    ->filterById($arrFilter['cliente'])
                                    ->findOne()
                                : null
                    ?>
                    <input type="hidden" class="cliente-select" name="filter[cliente]" data-nome-completo="<?php echo escape($cliente ? $cliente->getNomeCompleto() : '') ?>" value="<?php echo escape($arrFilter['cliente']) ?>">
                    
                    <?php
                        $produto = !empty($arrFilter['produto'])
                                ? ProdutoQuery::create()
                                        ->filterById($arrFilter['produto'])
                                        ->findOne()
                                : null
                    ?>
                    <input type="hidden" class="produto-select" name="filter[produto]" data-nome-produto="<?php echo escape($produto ? $produto->getNome() : '') ?>" value="<?php echo escape($arrFilter['produto']) ?>">
                    
                    <input type="text" name="filter[depoimento]" value="<?php echo escape($arrFilter['depoimento']) ?>" class="form-control" id="form-filter-element-1" placeholder="Depoimento">
                    
                    <input type="text" name="filter[data]" value="<?php echo escape($arrFilter['data']) ?>" class="form-control datepicker" id="form-filter-element-2" placeholder="Data">
                                        
                </div>
                <div class="form-group">
                    <button type="submit" title="Filtrar" class="btn btn-primary btn btn-primary" name="" id="form-filter-element-4"><i class="glyphicon glyphicon-search"></i> Filtrar</button>  
                    <button type="button" title="Cancelar" class="btn btn-default btn" name="" onclick="javascript:window.location.href='<?php echo $root_path ?>/distribuidores_novo/depoimentos';" id="form-filter-element-5"><i class="glyphicon glyphicon-remove"></i> Listar todos</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="container">
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th style="padding-right:100px">Cliente</th>
                    <th>Produto</th>
                    <th>Avaliação</th>
                    <th>Depoimento</th>
                    <th>Data</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
    <?php if (count($pager) > 0) :  ?>
        <?php foreach ($pager as $depoimento) : /* @var $depoimento DistribuidorDepoimento */  ?>
                <tr>
                    <td align="left"><?php echo escape($depoimento->getClienteDistribuidor()->getNomeCompleto()) ?></td>
                    <td><?php echo escape(($produto = $depoimento->getProduto()) ? $produto->getNome() : '') ?></td>
                    <td><?php echo escape($depoimento->getAvaliacao()) ?></td>
                    <td><?php echo resumo($depoimento->getDepoimento(), 100) ?></td>
                    <td><?php echo escape($depoimento->getDataCadastro('d/m/Y')) ?></td>
                    <td data-title="Ações" class="text-right">
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                                Ações <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu text-left" role="menu">
                                <li>
                                    <a class="text-danger confirma-action" href="#" data-id="<?php echo $depoimento->getId() ?>" data-action="<?php echo $root_path ?>/distribuidores_novo/depoimentos/actions/excluir.action.php" title="Excluir"><i class="glyphicon glyphicon-trash"></i> Excluir</a>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
        <?php endforeach ?>
    <?php else : ?>
                <tr>
                    <td colspan="3">Nenhum depoimento encontrado.</td>
                </tr>
    <?php endif ?>
            </tbody>
        </table>
    </div>

    <?php require __DIR__ . '/../../includes/paginacao.inc.php';  ?>

</div>
