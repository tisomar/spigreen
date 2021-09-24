<?php

use PFBC\Element;
?>
<div class="table-responsive">
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="table table-hover table-striped">
        <thead>
        <tr>
            <th>Cliente</th>
            <th>Bônus</th>
            <th>Graduação</th>
            <th>Periodo</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($pager->getResult() as $object) { /* @var $object ParticipacaoResultadoCliente */
            ?>
            <tr>
                <td data-title="Nome"><?php echo escape($object->getCliente()->getNomeCompleto()) ?></td>
                <td data-title="Bonus">R$ <?php echo number_format(escape($object->getTotalPontos()), '2', ',', ''); ?></td>
                <td data-title="Graduacao"><?php echo escape($object->getGraduacao()) ?></td>
                <td data-title="Periodo"><?php echo escape($object->getObservacao()) ?></td>
                <td class="text-right" data-title="Ações">
                    <div class="btn-group">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                            Ações <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu text-left" role="menu">
                            <li>
                                <a title="Cancelar"
                                    href="javascript:void(0);"
                                    class="cancelar"
                                    data-href="<?php echo get_url_admin() . '/bonus-aceleracao-preview/cancelar' ?>"
                                    data-id="<?php echo $object->getCliente()->getId() ?>"
                                    data-distribuicaoid="<?php echo $object->getParticipacaoResultadoId() ?>"
                                    data-message="Você deseja cancelar esta distribuição?">
                                    <span class="icon-check"></span> Cancelar
                                </a>
                            </li>
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
        $('a.cancelar').click(function(){

            var $this = $(this);
            var id = $this.data('id');
            var distribuicaoid = $this.data('distribuicaoid');
            var href = $this.data('href');
            var message = $this.data('message');

            if (id && distribuicaoid && href && message) {
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

                            var $inputdistribuicaoid= $('<input type="hidden" name="distribuicaoid">');
                            $inputdistribuicaoid.val(distribuicaoid);

                            $form.append($inputId);
                            $form.append($inputdistribuicaoid);

                            $form.appendTo('body').submit();
                        }
                    }
                });
            }
            return true;
        });
    });
</script>
