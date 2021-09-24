<form role="form" class="form-disabled-on-load" method="post" action="<?php echo ROOT_PATH ?>/checkout/patrocinador" id="form-patrocinador">
    <div class="form-group">
        <label for="codigo-patrocinador">Código ou e-mail patrocinador:</label>
        <input type="text" class="form-control" id="codigo-patrocinador" name="codigo_patrocinador" value="<?php echo escape($container->getSession()->get('CODIGO_PATROCINADOR', '')) ?>">
        <?php  if (!$container->getSession()->get('PATROCINADOR_CONFIRMADO', '0')) :  ?>
            <em><span class="<?php icon('info'); ?> pull-first"></span>Confirme o código em branco para que o sistema escolha um patrocinador automaticamente para você.</em>
        <?php endif ?>
    </div>
<?php if ($container->getSession()->get('PATROCINADOR_CONFIRMADO', '0')) :  ?>
    <div class="form-group">
    <?php if ($patrocinador = ClienteQuery::create()->findPk($container->getSession()->get('PATROCINADOR_ID', ''))) :  ?>
        <?php $enderecoPatrocinador = EnderecoQuery::create()->filterByCliente($patrocinador)->orderById()->findOne()  ?>
        <?php $cidadeNome = ($enderecoPatrocinador && ($cidade = $enderecoPatrocinador->getCidade())) ? $cidade->getNome() : ''  ?>
        <?php $uf = (isset($cidade) && ($estado = $cidade->getEstado())) ? $estado->getSigla() : ''  ?>
        <strong>Patrocinador:</strong> <?php echo $patrocinador->getNomeCompleto() ?>
        <a href="<?php echo ROOT_PATH ?>/checkout/patrocinador?action=delete" class="pull-right"  data-text="Você realmente deseja não utilizar este patrocinador?" data-action="delete" data-type="redirect" data-form="#form-patrocinador">
            <span class="<?php icon('close'); ?>"></span>
            <span class="hidden-xs">remover patrocinador</span>
        </a>
        <input type="hidden" id="patrocinador-desc" value="<?php echo $patrocinadorDesc ?>">
    <?php else : ?>
        <strong>Patrocinador:</strong> Escolheremos um patrocinador para você.
    <?php endif ?>
    </div>
<?php endif ?>
    <button type="submit" disabled="disabled" id="confirma-patrocinador" class="btn btn-success btn-block">
        <span class="<?php icon('') ?>"></span> Confirmar
    </button>
    <input type="hidden" id="patrocinador-confirmado" value="<?php echo $container->getSession()->get('PATROCINADOR_CONFIRMADO', '0')  ?>">
</form>

<script type="text/javascript">
    var patrocinadorAntigo = $('#codigo-patrocinador').val();
    $(document).ready(function() {
        $('#codigo-patrocinador').on('change', function () {
            var patrocinador = $(this).val();
            if(patrocinador != patrocinadorAntigo){
                $('#confirma-patrocinador').removeAttr('disabled');
            }
        })
    });
</script>
