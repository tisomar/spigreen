<?php

use PFBC\Element;
?>
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
        <tr>
            <th>Data</th>
            <th>Status</th>
            <th>Bônus</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($pager->getResult() as $object) { /* @var $object ParticipacaoResultado */
            $linkPreview = get_url_admin() . '/bonus-desempenho-preview/list?participacao_resultado_id=' . $object->getId();
            $linkRelatorio = get_url_admin() . '/bonus-desempenho-relatorio/list?participacao_resultado_id=' . $object->getId();
            ?>
            <tr>
                <td data-title="Data"><?php echo escape($object->getData('d/m/Y')) ?></td>
            <?php if ($object->getStatus() == ParticipacaoResultado::STATUS_PREVIEW) :  ?>
                <td data-title="Status"><a href="<?php echo escape($linkPreview) ?>"><?php echo escape($object->getStatusDesc()); ?></a></td>
            <?php elseif ($object->getStatus() == ParticipacaoResultado::STATUS_DISTRIBUIDO) :  ?>    
                <td data-title="Status"><a href="<?php echo escape($linkRelatorio) ?>"><?php echo escape($object->getStatusDesc()); ?></a></td>
            <?php else : ?>
                <td data-title="Status"><?php echo escape($object->getStatusDesc()); ?></td>
            <?php endif ?>
                <td data-title="Total Pontos">R$ <?= number_format($object->getTotalPontos(), 2, ',', '.') ?></td>
                <td class="text-right" data-title="Ações">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            Ações <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu text-left" role="menu">

                        <?php if ($object->getStatus() == ParticipacaoResultado::STATUS_PREVIEW) :  ?>    
                            <li><a href="<?php echo escape($linkPreview) ?>"><span class="icon-search"></span> Preview</a></li>
                            <li><a title="Cancelar" href="javascript:void(0);" class="confirmacao" data-href="<?php echo get_url_admin() . '/bonus-desempenho/confirmar' ?>" data-id="<?php echo $object->getId() ?>" data-message="Você tem certeza de que realmente deseja confirmar esta participação nos resultados?"><span class="icon-check"></span> Confirmar</a></li>
                        <?php elseif ($object->getStatus() == ParticipacaoResultado::STATUS_DISTRIBUIDO) :  ?>         
                            <li><a href="<?php echo escape($linkRelatorio) ?>"><span class="icon-list-alt"></span> Relatório</a></li>
                        <?php endif ?>
                        <?php if (in_array($object->getStatus(), array(ParticipacaoResultado::STATUS_AGUARDANDO_PREVIEW, ParticipacaoResultado::STATUS_PROCESSANDO_PREVIEW, ParticipacaoResultado::STATUS_PREVIEW, ParticipacaoResultado::STATUS_AGUARDANDO))) :  ?>
                            <li><a title="Cancelar" href="javascript:void(0);" class="confirmacao" data-href="<?php echo get_url_admin() . '/bonus-desempenho/cancelar' ?>" data-id="<?php echo $object->getId() ?>" data-message="Você tem certeza de que realmente deseja cancelar esta participação nos resultados?"><span class="icon-remove"></span> Cancelar</a></li>
                        <?php endif ?>
                        <?php if ($object->getStatus() != ParticipacaoResultado::STATUS_DISTRIBUIDO) :  ?>
                            <li class="divider"></li>
                            <li><a class="text-danger" title="Excluir" href="javascript:void(0);" data-href="<?php echo delete($_class, $object->getId()) ?>" data-action="delete" ><i class="icon-trash"></i> Excluir</a></li>
                        <?php endif ?>
                        </ul>
                    </div>
                </td>
            </tr>
        <?php } ?>
        <?php
        if ($pager->count() == 0) {
            ?>
            <tr>
                <td colspan="10">Nenhum registro encontrado</td>
            </tr>
            <?php
        }
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
