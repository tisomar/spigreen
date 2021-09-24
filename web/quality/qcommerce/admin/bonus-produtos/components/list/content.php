<!-- <a href="http://localhost/cron/distribui_pontos_rede_unilevel" target="_blank" rel="noopener noreferrer"><button>Distribuir</button></a> -->
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
        <tr>
            <th>Data Geração</th>
            <th>Status</th>
            <th>Total clientes</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($pager->getResult() as $object) : /* @var $object Distribuicao */
            $checkOperacaoClientes = ExtratoBonusProdutosQuery::create()
            ->filterByOperacao('-')
            ->filterByDistribuicaoId($object->getId())
            ->find();

            $countOperacaoDistribuir = count($checkOperacaoClientes);

            $linkPreview = get_url_admin() . '/bonus-produtos-preview/list?distribuicao_id=' . $object->getId();
            $linkRelatorio = get_url_admin() . '/bonus-produtos-relatorio/list?distribuicao_id=' . $object->getId();
            ?>
            <tr>
                <td data-title="Data"><?php echo escape($object->getData('d/m/Y')) ?></td>

                <?php if ($object->getStatus() == Distribuicao::STATUS_PREVIEW) :  ?>
                    <td data-title="Status">
                        <a href="<?php echo escape($linkPreview) ?>">
                            <?php echo escape($object->getStatus()); ?>
                        </a>
                    </td>
                <?php elseif($object->getStatus() == Distribuicao::STATUS_DISTRIBUIDO): ?>
                    <td data-title="Status">
                        <a href="<?php echo escape($linkRelatorio) ?>">
                            <?php echo escape($object->getStatus()); ?>
                        </a>
                    </td>
                <?php else : ?>
                    <td data-title="Status"><?php echo escape($object->getStatus()); ?></td>
                <?php endif ?>

                <td data-title="Total Pontos">
                    <?php echo $object->getTotalClientes(); ?>
                </td>
                
                <td class="text-right" data-title="Ações">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            Ações <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu text-left" role="menu">
                            <?php if ($object->getStatus() == Distribuicao::STATUS_PREVIEW) : ?>
                                <li>
                                    <a href="<?php echo escape($linkPreview) ?>">
                                        <span class="icon-search"></span> Preview
                                    </a>
                                </li>

                            <?php endif ?>
                            
                            <?php if($countOperacaoDistribuir > 0 ) : ?>
                                <li>
                                    <a href="<?php echo escape($linkRelatorio) ?>">
                                        <span class="icon-list-alt"></span> Relatório
                                    </a>
                                </li>
                            <?php endif ?>

                            <?php if ($countOperacaoDistribuir == 0 ) :  ?>
                                <li>
                                    <a title="Cancelar"
                                       href="javascript:void(0);"
                                       class="confirmacao"
                                       data-href="<?php echo get_url_admin() . '/bonus-produtos/cancelar' ?>"
                                       data-id="<?php echo $object->getId() ?>"
                                       data-message="Você tem certeza de que realmente deseja cancelar esta distribuição?">
                                        <span class="icon-remove"></span> Cancelar
                                    </a>
                                </li>
                            <?php endif ?>

                            <?php if ($countOperacaoDistribuir == 0) :  ?>
                                <li class="divider"></li>
                                <li>
                                    <a class="text-danger" 
                                       title="Excluir" 
                                       href="javascript:void(0);" 
                                       data-href="<?php echo delete($_class, $object->getId()) ?>" 
                                       data-action="delete" >
                                       <i class="icon-trash"></i> Excluir
                                    </a>
                                </li>
                            <?php endif ?>
                        </ul>
                    </div>
                </td>
            </tr>
        <?php
        endforeach;

        if ($pager->count() == 0) :
            ?>
            <tr>
                <td colspan="10">Nenhum registro encontrado</td>
            </tr>
        <?php
        endif;
        ?>
        </tbody>

    </table>
</div>
<div class="col-xs-12">
    <?php echo $pager->showPaginacao(); ?>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('a.confirmacao').click(function(){
            var $this = $(this);
            var id = $this.data('id');
            var href = $this.data('href');
            var message = $this.data('message');
            if (id && href && message) {
                bootbox.confirm({
                    message: message,
                    buttons: {
                        confirm: {
                            label: 'Sim'
                        },
                        cancel: {
                            label: 'Não'
                        }
                    },
                    callback: function(result){
                        if (result) {
                            /* executa a acao como um post */
                            var $form = $('<form></form>');
                            $form.attr('action', href);
                            $form.attr('method', 'POST');

                            var $inputId = $('<input type="hidden" name="id">');
                            $inputId.val(id);

                            $form.append($inputId);

                            $form.appendTo('body').submit();
                        }
                    }
                });
            }
            return true;
        });
    });
</script>
