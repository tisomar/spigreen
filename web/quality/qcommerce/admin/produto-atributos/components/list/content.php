<?php include __DIR__ . '/../../config/menu.php'; ?>

<?php
$objProduto = ProdutoPeer::retrieveByPK($_GET['reference']);
if ($objProduto->getTipoProduto() == "SIMPLES") : ?>
<div class='well well-sm'>
    <i class="icon-info-sign"></i> Atributos de produtos são
    as opções na qual o cliente poderá selecionar na página de detalhes do produto.
    <br />Por exemplo. No ramo vestuario, estas opções podem ser tamanho e/ou cor.<br /><br />
    Obs.: Uma vez definidos os atributos, você só poderá adicionar ou remover atributos iniciando
    o processo novamente. Isto poderá ser feito clicando no botão "Reiniciar" no final desta página.
</div>
<hr />

<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
            <tr>
                <th>Ordem</th>
                <th>Atributo</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php if ($pager->count() > 0) : ?>
                <?php foreach ($pager as $object) : ?>
                    <?php /* @var $object ProdutoAtributo */ ?>
                    <tr>
                        <td><?php echo escape($object->getOrdem()); ?>º</td>
                        <td><?php echo escape($object->getDescricao()); ?></td>
                        <td class="text-right">
                            <?php
                            _renderGroupButtons(
                                new PFBC\Element\EditButton($config['routes']['registration'] . '&id=' . $object->getId())
                            );
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="4">Nenhum atributo cadastrado</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

    <?php if ($pager->count() > 0) :
        $objProduto = ProdutoPeer::retrieveByPK($_GET['reference']);

        if (!$objProduto->hasVariacoes()) {
            ?>
        <hr />
        <div class="panel">
            <div class="panel-heading"><i class="icon-arrow-right"></i> Próximo passo:</div>
            <div class="panel-body">
                Já defini os atributos que este produto terá e desejo
                <a class="" href="<?php echo get_url_admin() . '/produto-variacoes/list/?context=Produto&reference=' . $_GET['reference'] ?>">
                    gerar as variações</a>.
            </div>
        </div>
            <?php
        }
        ?>
    <hr />
    <div class="panel panel-gray">
        <div class="panel-heading"><i class="icon-refresh"></i> Reiniciar variações:</div>
        <div class="panel-body">
            Para cancelar ou reiniciar todo o processo de cadastro de atributos e variações para este produto 
            clique abaixo:
            <br>
            <br>
            <span class="text-danger"><b>Atenção! Esta ação removerá todos os atributos e variações atuais e não poderá ser desfeita.</b></span>
            <br>
            <br>
            <a id="redefinir-variacoes" class="btn btn-danger" href="<?php echo get_url_admin() . '/' . $router->getModule() . '/redefinir/?reference=' . $_GET['reference'] . '&_=' . microtime(false) ?>">
                <i class="icon-exclamation-sign"></i> Reiniciar
            </a>
        </div>
        <script>
            $(function() {
                $('#redefinir-variacoes').click(function(e) {
                    e.preventDefault();
                    var url = $(this).attr('href');
                    bootbox.confirm("Atenção!<br /><br />Ao completar esta ação, você terá que cadastrar todas as variações \n\
                            para este produto novamente.<br /><br />Tem certeza de que deseja continuar?", function(result) {

                        if (result) {
                            window.location = url;
                        }

                    });
                });
            });
        </script>
    </div>
    <?php endif; ?>
<?php else : ?>
    <div class="panel">
        <div class="panel-body">
            Produto composto não pode ter variações, modifique a opção do produto nas suas caracteristicas para adicionar Atibutos e variações.
            <br/>
            <a class="" href="<?php echo get_url_admin() . '/produtos/registration?id=' . $_GET['reference'] ?>">
                Cliente aqui para voltar ao cadastro do produto</a>.
        </div>
    </div>
<?php endif; ?>


