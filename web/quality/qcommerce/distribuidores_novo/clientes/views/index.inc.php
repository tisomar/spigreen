<div class="page">
    <div id="slideout">
        <div class="heading-slideout ">
            <h3 class="modal-title"><?php echo escape(_trans('agenda.escolha_filtro')); ?></h3>
        </div>
        <div class="content-slideout">
            <button type="button" class="btn btn-close">x</button>
            <p><?php echo escape(_trans('agenda.informe_filtrar_clientes')); ?></p>
            <?php include __DIR__ . '/includes/filter.php'; ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12 visible-xs visible-sm">
            <?php include __DIR__ . '/includes/search.php'; ?>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-4 col-sm-4 col-lg-10">
            <div class="hidden-lg">
                <button type="button" class="btn btn-default btn-icon icon-left btn-filtrar" style="">
                    <?php echo escape(_trans('agenda.filtrar')); ?>
                    <i class="fa fa-filter"></i>
                </button>
            </div>
            <div class="visible-lg">
                <?php include __DIR__ . '/includes/filter.php'; ?>
            </div>
        </div>
        <div class="col-xs-8 col-sm-8 col-lg-2">
            <ul class="list-inline list-unstyled exporta-mailforweb">
                <li>
                    <button id="btnExport" type="button" class="btn btn-info btn-icon icon-left pull-right">
                        <?php echo escape(_trans('agenda.exportar_m4w')); ?>
                        <i class="entypo-export"></i>
                    </button>
                </li>
            </ul>

        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <form action="<?= $root_path . '/distribuidores_novo/m4w/integracao'; ?>" id="form-distribuidores" method="post" class="form-inline">
                <table class="table large-only table-striped table-clientes">
                    <thead>
                        <tr>
                            <th>

                            </th>
                            <th class="text-center">
                                $
                            </th>
                            <th><?php echo escape(_trans('agenda.nome')); ?></th>
                            <th><?php echo escape(_trans('agenda.email')); ?></th>
                            <th><?php echo escape(_trans('agenda.telefone')); ?></th>
                            <th><?php echo escape(_trans('agenda.ultima_compra')); ?></th>
                            <th class="text-center"><?php echo substr(escape(_trans('agenda.agendamentos')), 0, 5); ?>.</th>
                            <th><?php echo escape(_trans('agenda.data_cadastro')); ?></th>
                            <th class="hidden-xs"></th>
                            <th class="visible-lg text-center"></th>
                        </tr>
                    </thead>

                    <tbody><?php

                    if (count($pager) > 0) {
                        foreach ($pager as $cliente) : /* @var $cliente ClienteDistribuidor */ ?>
                                <?php

                                $cto["nome"]    = $cliente->getNomeRazaoSocial();
                                $cto["email"]   = $cliente->getEmail();

                                $contatos[] = $cto;

                                $linkVisualizar = $root_path . '/distribuidores_novo/clientes/visualizacao?id=' . $cliente->getid();?>
                                <tr data-id="<?php echo $cliente->getId(); ?>" data-nome="<?php echo $cliente->getNomeRazaoSocial(); ?>">
                                    <td>
                                        <div class="checkbox checkbox-replace color-green" id="">

                                        </div>
                                        <div class="visible-xs pull-right menudrop-icon">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" title="">
                                                <div class="menu-dots" id="js-menu-dots">
                                                    <span class="dot"></span>
                                                    <span class="dot"></span>
                                                    <span class="dot"></span>
                                                </div>
                                            </a>

                                            <ul class="dropdown-menu menu-icons">

                                                <li>
                                                    <a href="<?php echo escape($linkVisualizar) ?>">
                                                        <i class="entypo-eye"></i>
                                                        <?php echo escape(_trans('agenda.visualizar_cliente')); ?>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="#" class="btnEditar" data-id="<?php echo $cliente->getid(); ?>">
                                                        <i class="entypo-pencil"></i>
                                                        <?php echo escape(_trans('agenda.editar_cliente')); ?>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:;" data-id="<?php echo $cliente->getId(); ?>" class="btnAtividadeMobile">
                                                        <i class="entypo-newspaper"></i>
                                                        <?php echo escape(_trans('agenda.criar_agendamento')); ?>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:;" onclick="jQuery('#enviar-sms').modal('show');">
                                                        <i class="entypo-mobile"></i>
                                                        <?php echo escape(_trans('agenda.enviar_SMS')); ?>
                                                    </a>
                                                </li>
                                                <li>
                                                <li>
                                                    <a href="javascript:;" data-id="<?php echo $cliente->getId(); ?>" data-nome="<?php echo $cliente->getNomeCompleto(); ?>" class="btnDeletar" >
                                                        <i class="entypo-trash"></i>
                                                        <?php echo escape(_trans('agenda.excluir')); ?>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="visible-sm visible-md visible-lg text-center"><?php

                                        if ($cliente->getVirtualColumn('valor_total') != null && $cliente->getVirtualColumn('valor_total') > 0) {
                                            ?><i class="entypo-thumbs-up" style="color: #00a651"></i>
                                                <br>
                                                <small>R$ <?php echo number_format($cliente->getVirtualColumn('valor_total'), 2, ',', '.'); ?></small><?php
                                        }

                                        ?></div>
                                        <div class="visible-xs"><?php

                                        if ($cliente->getVirtualColumn('valor_total') != null && $cliente->getVirtualColumn('valor_total') > 0) {
                                            ?><i class="entypo-thumbs-up" style="color: #00a651"></i>
                                                <small>R$ <?php echo number_format($cliente->getVirtualColumn('valor_total'), 2, ',', '.'); ?></small><?php
                                        }

                                        ?></div>
                                    </td>
                                    <td class="name">
                                        <a href="<?php echo escape($linkVisualizar) ?>" title="<?php echo escape(_trans('agenda.visualizar_cliente')); ?>">
                                        <?php echo escape($cliente->getNomeCompleto()); ?>
                                        <?php echo $cliente->getLead() ? '<i class="indicacao entypo-star"></i>' : ''; ?>
                                        </a>
                                    </td>
                                    <td class="mail">
                                        <a href="mailto:<?php echo escape($cliente->getEmail()) ?>">
                                        <?php echo escape($cliente->getEmail()) ?>
                                        </a>
                                    </td>
                                    <td class="phone-mobile">
                                        <a href="tel:<?php echo escape($cliente->getTelefoneCelular()) ?>">
                                        <?php echo escape($cliente->getTelefoneCelular()) ?>
                                        </a>
                                    </td>

                                    <td class="dt-compra"><?php

                                    if ($cliente->getVirtualColumn('ultima_compra') != '') {
                                        $data = DateTime::createFromFormat('Y-m-d H:i:s', $cliente->getVirtualColumn('ultima_compra'));

                                        echo $data->format('d/m/Y');
                                    }

                                    ?></td>
                                    <td>
                                        <div class="visible-sm visible-md visible-lg text-center"><?php

                                        echo $cliente->getVirtualColumn('tem_agendamento');

                                        ?></div>
                                        <div class="visible-xs"><?php

                                        echo $cliente->getVirtualColumn('tem_agendamento');

                                        ?></div>
                                    </td>
                                    <td class="dt-cadastro">
                                        <?php echo $cliente->getDataCadastro('d/m/Y'); ?>
                                    </td>
                                    <td class="visible-sm visible-md">
                                        <div class=" pull-right menudrop-icon">
                                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true" title="">
                                                <div class="menu-dots" id="js-menu-dots">
                                                    <span class="dot"></span>
                                                    <span class="dot"></span>
                                                    <span class="dot"></span>
                                                </div>
                                            </a>
                                            <ul class="dropdown-menu menu-icons">

                                                <li>
                                                    <a href="<?php echo escape($linkVisualizar) ?>">
                                                        <i class="entypo-eye"></i>
                                                    <?php echo escape(_trans('agenda.visualizar_cliente')); ?>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:;" onclick="jQuery('#modal-adicionar-clientes').modal('show');">
                                                        <i class="entypo-pencil"></i>
                                                    <?php echo escape(_trans('agenda.editar_cliente')); ?>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:;" data-id="<?php echo $cliente->getId(); ?>" class="btnAtividadeMobile">
                                                        <i class="entypo-newspaper"></i>
                                                    <?php echo escape(_trans('agenda.criar_agendamento')); ?>
                                                    </a>
                                                </li>
                                                <li>
                                                    <a href="javascript:;" onclick="jQuery('#enviar-sms').modal('show');">
                                                        <i class="entypo-mobile"></i>
                                                    <?php echo escape(_trans('agenda.enviar_SMS')); ?>
                                                    </a>
                                                </li>
                                                <li>
                                                <li>
                                                    <a href="javascript:;" data-id="<?php echo $cliente->getId(); ?>" data-nome="<?php echo $cliente->getNomeCompleto(); ?>" class="btnDeletar" >
                                                        <i class="entypo-trash"></i>
                                                    <?php echo escape(_trans('agenda.excluir')); ?>
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                    <td  class="visible-lg btn-atividade" style="width: 10%">
                                        <a href="javascript:;" data-id="<?php echo $cliente->getId(); ?>" class="btn btn-warning btn-icon icon-left pull-right btnAtividade">
                                            <i class="entypo-newspaper"></i>
                                        <?php echo escape(_trans('agenda.criar_agendamento')); ?>
                                        </a>
                                    </td>

                                    <td class="visible-lg btn-buttons text-center">
                                        <div class="tooltip">
                                            <a href="<?php echo escape($linkVisualizar) ?>" class="btn btn-default" style="    padding: 6px 6px;">
                                                <i class="entypo-eye"></i> </a>
                                            <span class="tooltiptext tooltip-top"><?php echo escape(_trans('agenda.visualizar_cliente')); ?></span>
                                        </div>
                                        <div class="tooltip">
                                            <a href="#" class="btn btn-default btnEditar" data-id="<?php echo $cliente->getid(); ?>" style="padding: 6px 6px;">
                                                <i class="entypo-pencil"></i>
                                            </a>
                                            <span class="tooltiptext tooltip-top"><?php echo escape(_trans('agenda.editar_cliente')); ?></span>
                                        </div>

                                        <div class="tooltip">
                                            <a href="#" class="btn btn-default btnEnviaSMS" style="padding: 6px 7px;">
                                                <i class="entypo-mobile"></i></a>
                                            <span class="tooltiptext tooltip-top"><?php echo escape(_trans('agenda.enviar_SMS')); ?></span>
                                        </div>
                                        <div class="tooltip">
                                            <a href="#" class="btn btn-default btnEnviaEMAIL" style="padding: 6px 6px;">
                                                <i class="entypo-mail"></i></a>
                                            <span class="tooltiptext tooltip-top"><?php echo escape(_trans('agenda.enviar_email')); ?></span>
                                        </div>
                                        <div class="tooltip">
                                            <a href="#" class="btn btn-default btnExcluir" data-nome="<?php echo $cliente->getNomeCompleto(); ?>" data-id="<?php echo $cliente->getId(); ?>" style="padding: 6px 6px;">
                                                <i class="entypo-trash"></i> </a>
                                            <span class="tooltiptext tooltip-top"><?php echo escape(_trans('agenda.excluir')); ?></span>
                                        </div>
                                    </td>
                                </tr>
                        <?php endforeach ?>
                                <input type="hidden" name="contatos" id="contatos" value='<?= serialize($contatos) ?>'>
                    <?php } else {
                        ?><tr>
                                    <td colspan="9"><?php echo escape(_trans('agenda.nenhum_cliente')); ?></td>
                                </tr><?php
                    }
                    ?><input type="hidden" name="redirect" id="redirect" value="<?= $root_path . '/distribuidores_novo/clientes/'; ?>">
                    </tbody>
                </table>
            </form>
        </div>
    </div>
</div>

<?php

    include __DIR__ . '/includes/modais.php';
    include __DIR__ . '/../../atividades/views/includes/modal_criar_atividade.php';
    include __DIR__ . '/../../home/views/includes/modais.php';
?>

<script type="text/javascript">
    $(document).ready(function($) {

        $('#mensagemSMS').on('keydown', function() {
            $('#contador span').text(160 - $(this).val().length);
        });

        $('input[name="cliente_distribuidor[TIPO]"]').on('change', function() {

            if($(this).val() === 'J') {
                $('label#label_cpf_cnpj').text('<?php echo escape(_trans('agenda.cnpj')); ?>');
                $('label#label_rg_ie').text('<?php echo escape(_trans('agenda.ie')); ?>');
            } else {
                $('label#label_cpf_cnpj').text('<?php echo escape(_trans('agenda.cpf')); ?>');
                $('label#label_rg_ie').text('<?php echo escape(_trans('agenda.rg')); ?>');
            }

        });

        $('a.btnEditar').on('click', function(e) {
            e.preventDefault();

            var id = $(this).data('id');

            $.ajax({
                url: '<?php echo $root_path ?>/distribuidores_novo/clientes/actions/retorna_cliente.action.php?id='+id
            }).done(function( result ) {

                result = JSON.parse(result);

                console.log(result);

                $('#modal-adicionar-clientes select[name="cliente_distribuidor[ESTADO]"]').val(result.estado).change();

                $('#modal-adicionar-clientes input[name="id"]').val(id);
                $('#modal-adicionar-clientes input[name="cliente_distribuidor[EMAIL]"]').val(result.email);
                $('#modal-adicionar-clientes input[name="cliente_distribuidor[NOME_RAZAO_SOCIAL]"]').val(result.nome);
                $('#modal-adicionar-clientes input[name="cliente_distribuidor[TELEFONE_CELULAR]"]').val(result.celular);
                $('#modal-adicionar-clientes input[name="cliente_distribuidor[WHATSAPP]"]').val(result.whatsapp);

                $('#modal-adicionar-clientes input[name="cliente_distribuidor[CEP]"]').val(result.cep);
                $('#modal-adicionar-clientes input[name="cliente_distribuidor[ENDERECO]"]').val(result.endereco);
                $('#modal-adicionar-clientes input[name="cliente_distribuidor[NUMERO]"]').val(result.numero);
                $('#modal-adicionar-clientes input[name="cliente_distribuidor[BAIRRO]"]').val(result.bairro);
                $('#modal-adicionar-clientes input[name="cliente_distribuidor[COMPLEMENTO]"]').val(result.complemento);

                $('#modal-adicionar-clientes input[name="cliente_distribuidor[TELEFONE]"]').val(result.telefone);
                $('#modal-adicionar-clientes input[name="cliente_distribuidor[CPF_CNPJ]"]').val(result.cpfcnpj);
                $('#modal-adicionar-clientes input[name="cliente_distribuidor[RG_IE]"]').val(result.rgie);
                $('#modal-adicionar-clientes input[name="cliente_distribuidor[DATA_NASCIMENTO_DATA_FUNDACAO]"]').val(result.data);

                $('#modal-adicionar-clientes .radioTipo .radio-inline input[value="'+result.tipo+'"]').parent().parent().click();
                $('#modal-adicionar-clientes .radioSexo .radio-inline input[value="'+result.sexo+'"]').parent().parent().click();

                setTimeout(function() {
                    $('#modal-adicionar-clientes select[name="cliente_distribuidor[CIDADE]"]').val(result.cidade);
                }, 1800);

                $('#modal-adicionar-clientes').modal('show');
            });

        });

        $('.btnExcluir').on('click', function(e) {
            e.preventDefault();
            $('#delete-item input[name="id"]').val($(this).data('id'));
            $('#delete-item span#name').text($(this).data('nome'));
            $('#delete-item').modal('show');
        });

        $('.btnDeletar').on('click', function(e) {
            e.preventDefault();
            $('#delete-item input[name="id"]').val($(this).data('id'));
            $('#delete-item span#name').text($(this).data('nome'));
            $('#delete-item').modal('show');
        });

        $('#modal-criar-atividade').on('hidden.bs.modal', function() {
            $('#modal-criar-atividade #mensagemSMS').val('');
            $('#modal-criar-atividade #mensagemSMS').keydown();
        });

        $('#modal-adicionar-clientes').on('hidden.bs.modal', function() {
            $('#modal-adicionar-clientes input[name="id').val('-1');
            $('#modal-adicionar-clientes input[name="cliente_distribuidor[EMAIL]"]').val('');
            $('#modal-adicionar-clientes input[name="cliente_distribuidor[NOME_RAZAO_SOCIAL]"]').val('');
            $('#modal-adicionar-clientes input[name="cliente_distribuidor[TELEFONE_CELULAR]"]').val('');
            $('#modal-adicionar-clientes input[name="cliente_distribuidor[WHATSAPP]"]').val('');
            $('#modal-adicionar-clientes input[name="cliente_distribuidor[TELEFONE]"]').val('');
            $('#modal-adicionar-clientes input[name="cliente_distribuidor[CPF_CNPJ]"]').val('');
            $('#modal-adicionar-clientes input[name="cliente_distribuidor[RG_IE]"]').val('');
            $('#modal-adicionar-clientes input[name="cliente_distribuidor[DATA_NASCIMENTO_DATA_FUNDACAO]"]').val('');

            $('#modal-adicionar-clientes .radio-inline').removeClass('checked');
            $('#modal-adicionar-clientes .radio-inline input[type="radio"]').removeAttr('checked');

            $('#modal-adicionar-clientes .radioTipo .radio-inline input[value="F"]').attr('checked', 'checked');
            $('#modal-adicionar-clientes .radioTipo .radio-inline input[value="F"]').parent().parent().addClass('checked');

            $('#modal-adicionar-clientes .radioSexo .radio-inline input[value="M"]').attr('checked', 'checked');
            $('#modal-adicionar-clientes .radioSexo .radio-inline input[value="M"]').parent().parent().addClass('checked');
        });

        $('#enviar-sms textarea[name="sms[MENSAGEM]"]').on('keydown', function() {
            $('#contador span').text(160 - $(this).val().length);
        });

        $('#enviar-sms select[name="modeloSMS"]').on('change', function() {

            $('#enviar-sms textarea[name="sms[MENSAGEM]"]').attr('disabled', 'disabled');

            $.ajax({
                url: '<?php echo $root_path ?>/distribuidores_novo/clientes/actions/busca_modelo.action.php?id='+$(this).find('option:selected').val()
            }).done(function( result ) {

                result = JSON.parse(result);

                $('#enviar-sms textarea[name="sms[MENSAGEM]"]').val(result);
                $('#enviar-sms textarea[name="sms[MENSAGEM]"]').removeAttr('disabled');
                $('#enviar-sms textarea[name="sms[MENSAGEM]"]').keydown();
            });
        });

        $('#modal-criar-atividade select[name="modeloATIVIDADE"]').on('change', function() {

            $('#modal-criar-atividade textarea[name="evento[DESCRICAO]"]').attr('disabled', 'disabled');

            $.ajax({
                url: '<?php echo $root_path ?>/distribuidores_novo/clientes/actions/busca_modelo.action.php?id='+$(this).find('option:selected').val()
            }).done(function( result ) {

                result = JSON.parse(result);

                $('#modal-criar-atividade textarea[name="evento[DESCRICAO]"]').val(result);
                $('#modal-criar-atividade textarea[name="evento[DESCRICAO]"]').removeAttr('disabled');
            });

        });

        $('#modal-criar-atividade select[name="modeloSMS"]').on('change', function() {

            $('#modal-criar-atividade textarea[name="evento[DESCRICAO_SMS]"]').attr('disabled', 'disabled');

            $.ajax({
                url: '<?php echo $root_path ?>/distribuidores_novo/clientes/actions/busca_modelo.action.php?id='+$(this).find('option:selected').val()
            }).done(function( result ) {

                result = JSON.parse(result);

                $('#modal-criar-atividade textarea[name="evento[DESCRICAO_SMS]"]').val(result);
                $('#modal-criar-atividade textarea[name="evento[DESCRICAO_SMS]"]').removeAttr('disabled');
                $('#modal-criar-atividade textarea[name="evento[DESCRICAO_SMS]"]').keydown();
            });

        });

        $('#modal-criar-atividade select[name="modeloEMAIL"]').on('change', function() {

            $('#modal-criar-atividade textarea[name="evento[DESCRICAO_EMAIL]"]').attr('disabled', 'disabled');

            $.ajax({
                url: '<?php echo $root_path ?>/distribuidores_novo/clientes/actions/busca_modelo.action.php?id='+$(this).find('option:selected').val()
            }).done(function( result ) {

                result = JSON.parse(result);

                $('#modal-criar-atividade textarea[name="evento[DESCRICAO_EMAIL]"]').val(result);
                $('#modal-criar-atividade textarea[name="evento[DESCRICAO_EMAIL]"]').removeAttr('disabled');
            });

        });

        $('#enviar-email select[name="modeloEMAIL"]').on('change', function() {

            $.ajax({
                url: '<?php echo $root_path ?>/distribuidores_novo/clientes/actions/busca_modelo.action.php?id='+$(this).find('option:selected').val()
            }).done(function( result ) {

                result = JSON.parse(result);

                $('#enviar-email iframe').contents().find('.wysihtml5-editor').html(result);
            });
        });

        $('.btnAtividade').on('click', function(e) {
            e.preventDefault();

            var id = $(this).parent().parent().data('id');

            $('#modal-criar-atividade select[name="evento[CLIENTE_DISTRIBUIDOR_ID]"]').val(id).trigger('change');
            $('#modal-criar-atividade').modal('show');
        });

        $('.btnAtividadeMobile').on('click', function(e) {
            e.preventDefault();

            var id = $(this).data('id');

            $('#modal-criar-atividade select[name="evento[CLIENTE_DISTRIBUIDOR_ID]"]').val(id).trigger('change');
            $('#modal-criar-atividade').modal('show');
        });

        $('.btnEnviaSMS').on('click', function(e) {
            e.preventDefault();

            var id = $(this).parent().parent().parent().data('id');
            var nome = $(this).parent().parent().parent().data('nome');

            $('#enviar-sms input[name="cliente_id"]').val(id);
            $('#enviar-sms .nomeCliente').text(nome);

            $('#enviar-sms').modal('show');

        });

        $('.btnEnviaEMAIL').on('click', function(e) {
            e.preventDefault();

            var id = $(this).parent().parent().parent().data('id');
            var nome = $(this).parent().parent().parent().data('nome');

            $('#enviar-email input[name="cliente_id"]').val(id);
            $('#enviar-email .nomeCliente').text(nome);

            $('#enviar-email').modal('show');
        });

        $('.btnEnviaCONVITE').on('click', function(e) {
            e.preventDefault();

            var id = $(this).parent().parent().parent().data('id');
            var nome = $(this).parent().parent().parent().data('nome');

            $('#enviar-convite input[name="convite[CLIENTE_DISTRIBUIDOR_ID]"]').val(id);
            $('#enviar-convite .nomeCliente').text(nome);

            $('#enviar-convite').modal('show');
        });

        $('#comprou').on('change',  (function() {
            if($(this).val() === 'Sim') {
                jQuery('#modal-comprou').modal('show', {backdrop: 'static'});
            }
        }));

        $('input.icheck').iCheck({
            checkboxClass: 'icheckbox_minimal',
            radioClass: 'iradio_minimal'
        });

        $('input.icheck-2').iCheck({
            checkboxClass: 'icheckbox_minimal-blue',
            radioClass: 'iradio_minimal-blue'
        });

        var icheck_skins = $(".icheck-skins a");

        icheck_skins.click(function(ev)
        {
            ev.preventDefault();

            icheck_skins.removeClass('current');
            $(this).addClass('current');

            updateiCheckSkinandStyle();
        });

        $("#icheck-style").change(updateiCheckSkinandStyle);

        $('.adicionar-clientes').on('click', function(){
            $.post(
                "<?php echo $root_path . '/distribuidores_novo/home/actions/get.quantidade.clientes.actions.php' ?>",
                null,
                function (response) {
                    $('#modal-adicionar-clientes').modal('show');
                },
                'json'
            )
        });
    });

    function updateiCheckSkinandStyle() {
        var skin = $(".icheck-skins a.current").data('color-class'),
            style = $("#icheck-style").val();

        var cb_class = 'icheckbox_' + style + (skin.length ? ("-" + skin) : ''),
            rd_class = 'iradio_' + style + (skin.length ? ("-" + skin) : '');

        if(style == 'futurico' || style == 'polaris') {
            cb_class = cb_class.replace('-' + skin, '');
            rd_class = rd_class.replace('-' + skin, '');
        }

        $('input.icheck-2').iCheck('destroy');
        $('input.icheck-2').iCheck({
            checkboxClass: cb_class,
            radioClass: rd_class
        });
    }

</script>

<nav id="bt-menu" class="bt-menu">
    <a href="javascript:void(0)"  class="bt-menu-trigger bt-item-page adicionar-clientes"><span><?php echo escape(_trans('agenda.menu')); ?></span></a>
</nav>

<style>

    body {
        overflow-x: hidden;
    }

</style>
