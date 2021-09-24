<?php /** @var $object Resgate */ ?>

<div class="panel panel-gray">
    <div class="panel-heading">
        <h4>Dados da Solicitação Prêmios Acumulados</h4>
    </div>
    <div class="panel-body">
        <div class="form-group">

            <div class="col-sm-12">
                <table class="table">
                    <tbody>
                        <tr>
                            <td><b>Cliente</b></td>
                            <td><?php echo escape($object->getCliente()->getNomeCompleto()) ?></td>
                        </tr>
                         <tr>
                            <td><b>Pontos resgate</b></td>
                            <td><?php echo (float)$object->getPontosResgate() ?></td>
                        </tr>
                       <tr>
                            <td><b>Premio solicitado</b></td>
                            <td><?php echo escape($object->getPremio()) ?></td>
                        </tr>
                         <tr>
                            <td><b>Data</b></td>
                            <td><?php echo escape($object->getData('d/m/Y')) ?></td>
                        </tr>
                    <tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$class = $label = $icon = '';
switch ($object->getSituacao()) {
    case Resgate::SITUACAO_EFETUADO:
        $class = 'success';
        $label = 'success';
        $icon = 'ok';
        break;
    case Resgate::SITUACAO_NAOEFETUADO:
        $class = 'danger';
        $label = 'danger';
        $icon = 'ban-circle';
        break;
    case Resgate::SITUACAO_PENDENTE:
        $class = 'warning';
        $label = 'warning';
        $icon = 'time';
        break;
    default:
        $class = 'gray';
        break;
}
?>

<div class="panel panel-<?php echo $class ?>">
    <div class="panel-heading">
        <h4>Situação do resgate</h4>
    </div>
    <div class="panel-body">
        <div class="col-xs-12 col-sm-6">
            <h3>Situação Atual</h3>
            <h3>
                <span 
                    data-toggle="tooltip" 
                    data-placement="bottom" 
                    title="Situação do resgate."
                >
                    <label class='label label-<?php echo $label ?>'>
                        <i class='icon-<?php echo $icon ?>'></i> 
                        <?php echo escape($object->getSituacaoDesc())  ?>
                    </label>
                </span>
            </h3>

        </div>
        <div class="col-xs-12 col-sm-6" style="margin-bottom: 75px;">
            <p>Você pode alterar a situação do resgate clicando na ação desejada abaixo:</p>
            <div class="clearfix"></div>

            <?php if ($object->getSituacao() != Resgate::SITUACAO_EFETUADO) :  ?>
                <a 
                    class="statusResgate btn btn-success" 
                    href="<?php echo get_url_admin() ?>/resgate-premios-acumulados/situacao/" 
                    data-toggle="tooltip" 
                    data-placement="bottom" 
                    title="A solicitação será marcada como efetuada e um extrato será gerado." 
                    data-id="<?php echo $object->getId() ?>" 
                    data-situacao="<?php echo escape(Resgate::SITUACAO_EFETUADO) ?>" 
                    data-mensagem-confirmacao="Tem certeza que deseja efetuar este resgate?">
                    <span class="icon-ok"></span> Efetuar
                    </a>&nbsp;
            <?php endif ?>

            <?php if ($object->getSituacao() != Resgate::SITUACAO_EFETUADO) :  ?>

                <?php if ($object->getSituacao() != Resgate::SITUACAO_NAOEFETUADO) :  ?>
                    <a 
                        class="statusResgate btn btn-danger" 
                        href="<?php echo get_url_admin() ?>/resgate-premios-acumulados/situacao/"  
                        data-toggle="tooltip" 
                        data-placement="bottom" 
                        title="A solicitação será marcada como não efetuada e deixará de aparecer como pendente. Qualquer extrato gerado para esta solicitação será excluído." 
                        data-id="<?php echo $object->getId() ?>" 
                        data-situacao="<?php echo escape(Resgate::SITUACAO_NAOEFETUADO) ?>" 
                        data-mensagem-confirmacao="<?php echo escape('Tem certeza que deseja <strong>não efetuar</strong> este resgate?<br><strong>Atençao</strong>: qualquer extrato gerado para esta solicitação será excluído.</strong>') ?>">
                        <span class="icon-remove"></span> Não efetuar
                    </a>&nbsp;
                <?php endif ?>

                <?php if ($object->getSituacao() != Resgate::SITUACAO_PENDENTE) :  ?>
                    <a 
                        class="statusResgate btn btn-warning" 
                        href="<?php echo get_url_admin() ?>/resgate-premios-acumulados/situacao/" 
                        data-toggle="tooltip" 
                        data-placement="bottom" 
                        title="Marca a situação novemente como pendente. Qualquer extrato gerado para esta solicitação será excluído." 
                        data-id="<?php echo $object->getId() ?>" 
                        data-situacao="<?php echo escape(Resgate::SITUACAO_PENDENTE) ?>" 
                        data-mensagem-confirmacao="<?php echo escape('Tem certeza que deseja marcar este resgate novamente como pendente?<br><strong>Atençao</strong>: qualquer extrato gerado para esta solicitação será excluído.</strong>') ?>">
                        <span class="icon-ban-circle"></span> Pendente
                    </a>&nbsp;     
                <?php endif ?>
            <?php endif ?>
        </div>
                    
    </div>

</div>

<script type="text/javascript">
    $(document).ready(function(){
        $('a.statusResgate').click(function(){
            var $this = $(this);
            var href = $this.attr('href');
            var mensagemConfirmacao = $this.data('mensagemConfirmacao');
            var id = $this.data('id');
            var situacao = $this.data('situacao');
            if (href && id && situacao && mensagemConfirmacao) {
                bootbox.confirm(mensagemConfirmacao, function(response) {
                    if (response) {
                        /* executa a acao como um post */
                        var $form = $('<form></form>');
                        $form.attr('action', href);
                        $form.attr('method', 'POST');

                        var $inputId = $('<input type="hidden" name="id">');
                        $inputId.val(id);
                        var $inputSituacao = $('<input type="hidden" name="situacao">');
                        $inputSituacao.val(situacao);

                        $form.append($inputId);
                        $form.append($inputSituacao);

                        $form.appendTo('body').submit();
                    }
                });
            }
            return false;
        });
    });
</script>
