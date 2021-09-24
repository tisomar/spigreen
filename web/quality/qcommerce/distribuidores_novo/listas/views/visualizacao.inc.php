<?php /* @var $objClienteDistribuidor ClienteDistribuidor */ ?>
<div class="well" style="background: #fff;">
    <div class="row ">
        <div class="visible-sm visible-xs" style="    position: absolute;  top: 25px; right: 30px;">
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
                        <!--<a href="javascript:;" onclick="jQuery('#modal-adicionar-listas').modal('show');">-->
                        <a href="#" class="btnEditar" data-id="<?php echo $objClienteDistribuidor->getid(); ?>">
                            <i class="entypo-pencil"></i>
                            <?php echo escape(_trans('agenda.editar_cliente')); ?>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;" onclick="jQuery('#modal-criar-atividade').modal('show');">
                            <i class="entypo-newspaper"></i>
                            <?php echo escape(_trans('agenda.criar_agendamento')); ?>
                        </a>
                    </li>
                    <li>
                        <a href="javascript:;" onclick="jQuery('#enviar-sms').modal('show');">
                            <i class="entypo-mobile"></i>
                            <?php echo escape(_trans('agenda.enviar_sms')); ?>
                        </a>
                    </li>
                    <li>
                    <li>
                        <a href="javascript:;" onclick="jQuery('#delete-item').modal('show');">
                            <i class="entypo-trash"></i>
                            <?php echo escape(_trans('agenda.excluir')); ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="col-sm-8 col-md-4">
            <div class="profile-env">
                <div class="profile-info">
                    <h3><?php echo escape($objClienteDistribuidor->getNomeCompleto()); ?></h3>
                </div>
                <div class="profile-info-sections2 ">
                    <ul class="list-unstyled profile-info-sections2">
                        <li>
                            <div class="profile-stat">
                                <h3>R$ <?php echo number_format($objClienteDistribuidor->getVirtualColumn('valor_total'), 2, ',', '.'); ?></h3>
                                <span><?php echo escape(_trans('agenda.total_venda')); ?></span>
                            </div>
                        </li><?php
                        
                        if ($objClienteDistribuidor->getVirtualColumn('ultima_compra') != '') {
                            $data = DateTime::createFromFormat('Y-m-d H:i:s', $objClienteDistribuidor->getVirtualColumn('ultima_compra'));

                            ?><li>
                                    <div class="profile-stat">
                                        <h3><?php echo $data->format('d/m/Y'); ?></h3>
                                        <span><?php echo escape(_trans('agenda.data_ultima_compra')); ?></span>
                                    </div>
                                </li><?php
                        }
                        
                        ?></ul>
                </div>
            </div>
        </div>
        <div class="col-sm-8" >
            <ul class="list-unstyled list-inline cabecalho-listas text-right hidden-xs hidden-sm">
                <li>

                    <!--<a href="javascript:;" onclick="jQuery('#modal-adicionar-listas').modal('show');"
                       class="btn btn-default btn-icon icon-left">-->
                    <a href="#" class="btnEditar btn btn-default btn-icon icon-left" data-id="<?php echo $objClienteDistribuidor->getid(); ?>">

                    <i class="entypo-pencil"></i><?php echo escape(_trans('agenda.editar')); ?></a>
                </li>
                <li>
                    <a href="javascript:;" onclick="jQuery('#enviar-email').modal('show');"
                       class="btn btn-default btn-icon icon-left" >
                        <i class="entypo-mail"></i>
                        <?php echo escape(_trans('agenda.enviar_email')); ?>
                    </a>
                </li>
                <li>
                    <a href="javascript:;" onclick="jQuery('#enviar-sms').modal('show');"
                       class="btn btn-default btn-icon icon-left" >
                        <i class="entypo-mobile"></i>
                        <?php echo escape(_trans('agenda.enviar_SMS')); ?>
                    </a>
                </li>
            </ul>
        </div>

    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4><?php echo escape(_trans('agenda.detalhes')); ?></h4>
            </div>
            <br>
            <div class="">
                <ul class="list-unstyled dados-cliente">
                    <li>
                        <span class="pull-left "> <?php echo escape(_trans('agenda.whatsapp')); ?>:&nbsp;</span>
                        <span><b><a href="https://api.whatsapp.com/send?1=pt_BR&phone=<?php echo escape($objClienteDistribuidor->getWhatsApp()) ?>"><?php echo escape($objClienteDistribuidor->getWhatsApp()) ?></a></b></span><br>
                    </li>
                    <li>
                        <span class="pull-left "> <?php echo escape(_trans('agenda.telefone')); ?>:&nbsp;</span>
                        <b><a href="tel:<?php echo escape($objClienteDistribuidor->getTelefoneCelular()) ?>"><span><?php echo escape($objClienteDistribuidor->getTelefoneCelular()) ?></span></a></b><br>
                    </li>
                    <li>
                        <span class="pull-left "> <?php echo escape(_trans('agenda.email')); ?>:&nbsp;</span>
                        <b><span><?php echo escape($objClienteDistribuidor->getEmail()) ?></span></b><br>
                    </li>
                    <li>
                        <span class="pull-left"><?php echo escape(_trans('agenda.telefone_fixo')); ?>:&nbsp;</span>
                        <b><a href="tel:<?php echo escape($objClienteDistribuidor->getTelefone()) ?>"><span><?php echo escape($objClienteDistribuidor->getTelefone()); ?></span></a></b><br>
                    </li>
                    <li>
                        <span class="pull-left "><?php echo ($objClienteDistribuidor->isPessoaJuridica() ? escape(_trans('agenda.cnpj')) : escape(_trans('agenda.cpf'))); ?>:&nbsp;</span>
                        <span><b><?php echo escape($objClienteDistribuidor->getCpfCnpj()) ?></b></span><br>
                    </li>
                    <li>
                        <span class="pull-left "><?php echo ($objClienteDistribuidor->isPessoaJuridica() ? escape(_trans('agenda.ie')) : escape(_trans('agenda.rg'))); ?>:&nbsp;</span>
                        <span><b><?php echo escape($objClienteDistribuidor->getRgIe()) ?></span></b><br>
                    </li>
                    <li>
                        <span class="pull-left "><?php echo escape(_trans('agenda.data_nascimento')); ?>:&nbsp;</span>
                        <span><b><?php echo escape($objClienteDistribuidor->getDataNascimentoDataFundacao('d/m/Y')) ?></b></span>
                    </li><br>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="panel panel-primary panel-timeline">

            <h4><?php echo escape(_trans('agenda.tomar_nota')); ?></h4><br>
            <div >
                
                <form role="form" method="post" style="margin-bottom: 50px;">
                    <div class="form-group">
                        <textarea class="form-control wysihtml5"
                                  data-stylesheet-url="assets/css/custom.css"
                                  name="observacao[OBSERVACAO]" id="observacao[OBSERVACAO]">
                        </textarea>
                    </div>
                    <button type="submit" class="btn btn-success"><?php echo escape(_trans('agenda.enviar')); ?></button>
                    <button type="button" class="btn btn-gold"><?php echo escape(_trans('agenda.cancelar')); ?></button>
                </form>
                
                <div id="historico-obs" style="margin-bottom: 50px;">
                    <h4 class="historico"><?php echo escape(_trans('agenda.historico_observacoes')); ?></h4>
                    <table class="table large-only table-striped table-listas">
                        <thead>
                            <tr>
                                <th><?php echo escape(_trans('agenda.data')); ?></th>
                                <th><?php echo escape(_trans('agenda.observacao')); ?></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody><?php
                        
                            $observacoesCliente = ClienteDistribuidorObservacaoQuery::create()
                                ->filterByClienteDistribuidor($objClienteDistribuidor)
                                ->orderByDataCadastro(Criteria::DESC)
                                ->find();
                            
                            /* @var $observacao ClienteDistribuidorObservacao */
                        foreach ($observacoesCliente as $observacao) {
                            ?><tr>
                                    <td><?php echo $observacao->getDataCadastro('d/m/Y'); ?></td>
                                    <td><?php echo nl2br($observacao->getObservacao()); ?></td>
                                    <td style="width: 50px; text-align: center;">
                                        <a href="<?php echo $root_path ?>/distribuidores_novo/listas/actions/excluir_observacao.action.php?id=<?php echo $observacao->getId(); ?>" class="btn btn-danger"><i class="entypo-cancel"></i></a>
                                    </td>
                                </tr><?php
                        }
                        
                        ?></tbody>
                    </table>
                </div>
                
                <h4 class="historico"><?php echo escape(_trans('agenda.historico_agendamentos')); ?></h4>

                <?php
                $atividades = DistribuidorEventoQuery::create()
                        ->filterByCliente(ClientePeer::getClienteLogado())
                        ->filterByClienteDistribuidor($objClienteDistribuidor)
                        ->filterByData('now', '<')
                        ->orderByData(Criteria::DESC)
                        ->find();
                ?>

                <ul class="cbp_tmtimeline"><?php
                    /* @var $atividade DistribuidorEvento */
                foreach ($atividades as $row => $atividade) {
                    $finalizado = $atividade->getStatus() == DistribuidorEvento::STATUS_FINALIZADO;

                    if ($row == 0 && $atividade->getData('d/m/Y') != date('d/m/Y')) {
                        ?><li>
                                <time class="cbp_tmtime" datetime="<?php echo date('d/m/Y'); ?>">
                                    <span class="hidden"><?php echo date('d/m/Y'); ?></span>
                                    <span class="large"><?php echo escape(_trans('agenda.hoje')); ?></span>
                                </time>

                                <div class="cbp_tmicon">
                                    <i class="entypo-user"></i>
                                </div>

                                <div class="cbp_tmlabel empty">
                                    <span><?php echo escape(_trans('agenda.sem_agendamentos')); ?></span>
                                </div>
                            </li><?php
                    }
                    ?><li>
                            <time class="cbp_tmtime" datetime="<?php echo $atividade->getData('d/m/Y'); ?>">
                                <span style="padding-top: 10px;"><?php
                                if ($atividade->getData('d/m/Y') === date('d/m/Y')) {
                                    echo escape(_trans('agenda.hoje'));
                                } else {
                                    echo $atividade->getData('d/m/Y');
                                }
                                ?></span>
                                <span></span>
                            </time>

                            <?php
                            $subject = DistribuidorEventoPeer::getSubjectByText($atividade->getAssunto());
                            ?>

                            <div class="cbp_tmicon <?php echo $subject['class']; ?>">
                                <i class="<?php echo $subject['icon']; ?>"></i>
                            </div>

                            <div class="cbp_tmlabel<?php echo ($finalizado ? '' : ' emAndamento')?>" data-id="<?php echo $atividade->getId(); ?>" style="<?php
                            
                            if ($finalizado) {
                                ?>background:<?php echo ($atividade->getDistribuidorTemplateIdPerda() ? '#ee4749' : '#00a651'); ?><?php
                            } else {
                                ?>cursor: pointer;<?php
                            }
                            
                            ?>">
                                <h2<?php echo ($finalizado ? ' style="color: #FFF;"' : ''); ?>>
                                <?php echo ($atividade->getAssunto() != '' ? escape(_trans('agenda.' . $atividade->getAssunto())) : escape(_trans('agenda.sem_assunto')));?><small><?php echo ($finalizado ? '' : ' (' . escape(_trans('agenda.em_andamento')) . ')'); ?></small>
                                    <small class="h4 pull-right"<?php echo ($finalizado ? ' style="color: #FFF;"' : ''); ?>><?php
                                    
                                    $valor = '';
                                    
                                    if ($atividade->getValor()) {
                                        if ($atividade->getDistribuidorTemplateIdPerda()) {
                                            $valor = escape(_trans('agenda.valor_perdido')) . ': ';
                                        } else {
                                            $valor = escape(_trans('agenda.valor_ganho')) . ': ';
                                        }
                                            
                                        $valor .= 'R$ ' . $atividade->getValor();
                                    }
                                    
                                    echo $valor;
                                    ?></small>
                                </h2>
                                <p style="padding-top: 10px; <?php echo ($finalizado ? 'color: #FFF;' : ''); ?>"><?php echo $atividade->getDescricao(); ?></p><?php
                                
                                $produtos = DistribuidorEventoProdutoQuery::create()
                                    ->findByDistribuidorEventoId($atividade->getId());
                                    
                                if (count($produtos) > 0) {
                                    ?><br>
                                        <span style="color: #FFF"><?php echo escape(_trans('agenda.produtos')); ?>:</span>
                                        <ul style="color: #FFF"><?php
                                    
                                        /* @var $produto DistribuidorEventoProduto */
                                        /* @var $detalhes Produto */
                                        foreach ($produtos as $produto) {
                                            $detalhes = $produto->getProduto();

                                            ?><li><?php echo $detalhes->getNome(); ?></li><?php
                                        }
                                        
                                        ?></ul><?php
                                }
                                
                                ?></div>
                        </li><?php
                }
                ?></ul>

            </div>

        </div>
    </div>
</div>

<div class="modal fade custom-width" id="modal-adicionar-listas">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo $root_path ?>/distribuidores_novo/listas/actions/cadastro.action.php?pag=visualizacao" method="POST" class="form-horizontal form-cadastro-listas">
                <input type="hidden" name="id" value="<?php echo $objClienteDistribuidor->getId(); ?>">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php echo escape(_trans('agenda.editar_cliente')); ?></h4>
                </div>
                <div class="modal-body">
                    <div class="container">
                        <!--<div class="col-md-6 col-lg-6">
                                <div class="form-group row">
                                    <div class="col-sm-2 col-md-12 col-lg-3">
                                        <label class="control-label"><?php echo escape(_trans('agenda.tipo_pessoa')); ?></label>
                                    </div>
                                    <div class="col-lg-8 radioTipo">
                                        <label class="radio-inline radio radio-replace">
                                            <input type="radio" class="tipo" id="inlineradio1" name="cliente_distribuidor[TIPO]" value="<?php echo escape(ClienteDistribuidor::TIPO_PESSOA_FISICA) ?>"<?php echo $objClienteDistribuidor->getTipo() == 'F' ? ' checked' : ''; ?>> <?php echo escape(_trans('agenda.fisica')); ?>
                                        </label>
                                        <label class="radio-inline radio radio-replace">
                                            <input type="radio" class="tipo" id="inlineradio2" name="cliente_distribuidor[TIPO]" value="<?php echo escape(ClienteDistribuidor::TIPO_PESSOA_JURIDICA) ?>"<?php echo $objClienteDistribuidor->getTipo() == 'J' ? ' checked' : ''; ?>> <?php echo escape(_trans('agenda.juridica')); ?>
                                        </label>
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-2 col-md-12 col-lg-3">
                                        <label class="control-label" for="email"><?php echo escape(_trans('agenda.email')); ?></label>
                                    </div>
                                    <div class="col-lg-8">
                                        <input type="email" class="form-control" id="email" placeholder="examplo@email.com.br" name="cliente_distribuidor[EMAIL]" required value="<?php echo escape($objClienteDistribuidor->getEmail()) ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-2 col-md-12 col-lg-3">
                                        <label class="control-label" for="nome_razao_social"><?php echo escape(_trans('agenda.nome')); ?></label>
                                    </div>
                                    <div class="col-lg-8">
                                        <input type="text" class="form-control" required="" id="nome_razao_social" placeholder="<?php echo escape(_trans('agenda.nome_completo')); ?>" name="cliente_distribuidor[NOME_RAZAO_SOCIAL]" value="<?php echo escape($objClienteDistribuidor->getNomeCompleto()) ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-2 col-md-12 col-lg-3">
                                        <label class="control-label" for="telefone_celular"><?php echo escape(_trans('agenda.celular')); ?></label>
                                    </div>
                                    <div class="col-lg-8">
                                        <input type="tel" class="form-control" data-mask="phone"
                                               id="telefone_celular" name="cliente_distribuidor[TELEFONE_CELULAR]" value="<?php echo escape($objClienteDistribuidor->getTelefoneCelular()) ?>">
                                    </div>
                                </div>
                                <div class="form-group row">
                                    <div class="col-sm-2 col-md-12 col-lg-3">
                                        <label class=" control-label" for="whatsapp"><?php echo escape(_trans('agenda.whatsapp')); ?></label>

                                    </div>
                                    <div class="col-lg-8">
                                        <input type="tel" class="form-control" data-mask="phone"
                                               id="whatsapp" name="cliente_distribuidor[WHATSAPP]" value="<?php echo escape($objClienteDistribuidor->getWhatsApp()) ?>">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="panel panel-primary">
                                    <div class="panel-heading" >
                                        <div class="panel-title text-uppercase">
                                            <?php echo escape(_trans('agenda.dados_complementares')); ?>
                                        </div>
                                    </div>
                                    <div class="panel-body">

                                        <div class="form-group row">
                                            <div class="col-sm-2 col-md-12 col-lg-3">
                                                <label class="control-label" for="telefone"><?php echo escape(_trans('agenda.telefone_fixo')); ?></label>
                                            </div>
                                            <div class="col-lg-8">
                                                <input type="tel" data-mask="phone" class="form-control mask" id="telefone" name="cliente_distribuidor[TELEFONE]" data-inputmask="'mask':'(99) 99999999[9]'" value="<?php echo escape($objClienteDistribuidor->getTelefone()) ?>">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-sm-2 col-md-12 col-lg-3">
                                                <label class="control-label" for="cpf_cnpj"><?php echo escape(_trans('agenda.cpf')); ?></label>
                                            </div>

                                            <div class="col-lg-8">
                                                <input type="text" class="form-control mask" id="cpf_cnpj" name="cliente_distribuidor[CPF_CNPJ]" data-inputmask="'mask':'999.999.999-99'" value="<?php echo escape($objClienteDistribuidor->getCpfCnpj()) ?>">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-sm-2 col-md-12 col-lg-3">
                                                <label class="control-label" for="rg_ie"><?php echo escape(_trans('agenda.rg')); ?></label>
                                            </div>

                                            <div class="col-lg-8">
                                                <input type="text" class="form-control" id="rg_ie" name="cliente_distribuidor[RG_IE]" value="<?php echo escape($objClienteDistribuidor->getRgIe()) ?>">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-sm-12 col-md-12 col-lg-3">
                                                <label class="control-label" for="data_nascimento_data_fundacao"><?php echo escape(_trans('agenda.data_nascimento')); ?></label>
                                            </div>
                                            <div class="col-lg-8">
                                                <input type="text" class="form-control datepicker" id="data_nascimento_data_fundacao" name="cliente_distribuidor[DATA_NASCIMENTO_DATA_FUNDACAO]" value="<?php echo escape($objClienteDistribuidor->getDataNascimentoDataFundacao('d/m/Y')) ?>">
                                            </div>
                                        </div>

                                        <div class="form-group row">
                                            <div class="col-sm-2 col-md-12 col-lg-3">
                                                <label class="control-label"><?php echo escape(_trans('agenda.sexo')); ?></label>
                                            </div>
                                            <div class="col-lg-8 radioSexo">
                                                <label class="radio-inline radio radio-replace">
                                                    <input type="radio" id="inlineradio3" name="cliente_distribuidor[SEXO]" value="M"<?php echo ($objClienteDistribuidor->getSexo() == 'M' ? ' checked' : '') ?>> <?php echo escape(_trans('agenda.masculino')); ?>
                                                </label>
                                                <label class="radio-inline radio radio-replace">
                                                    <input type="radio" id="inlineradio4" name="cliente_distribuidor[SEXO]" value="F"<?php echo ($objClienteDistribuidor->getSexo() == 'F' ? ' checked' : '') ?>> <?php echo escape(_trans('agenda.feminino')); ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>-->
                        <div class="col-md-5 col-lg-6">
                            <div class="form-group row">
                                <div class="col-sm-2 col-md-12 col-lg-3">
                                    <label class="control-label"><?php echo escape(_trans('agenda.tipo_pessoa')); ?></label>
                                </div>
                                <div class="col-lg-8 radioTipo">
                                    <label class="radio-inline radio radio-replace">
                                        <input type="radio" class="tipo" id="inlineradio1" name="cliente_distribuidor[TIPO]" value="<?php echo escape(ClienteDistribuidor::TIPO_PESSOA_FISICA) ?>" required> <?php echo escape(_trans('agenda.fisica')); ?>
                                    </label>
                                    <label class="radio-inline radio radio-replace">
                                        <input type="radio" class="tipo" id="inlineradio2" name="cliente_distribuidor[TIPO]" value="<?php echo escape(ClienteDistribuidor::TIPO_PESSOA_JURIDICA) ?>" checked required> <?php echo escape(_trans('agenda.juridica')); ?>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-2 col-md-12 col-lg-3">
                                    <label class="control-label" for="email"><?php echo escape(_trans('agenda.email')); ?></label>
                                </div>
                                <div class="col-lg-8">
                                    <input type="email" class="form-control" id="email" placeholder="examplo@email.com.br" name="cliente_distribuidor[EMAIL]" value="<?php echo escape($arrClienteDistribuidor['EMAIL']) ?>" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-2 col-md-12 col-lg-3">
                                    <label class="control-label" for="nome_razao_social"><?php echo escape(_trans('agenda.nome')); ?></label>
                                </div>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" id="nome_razao_social" placeholder="<?php echo escape(_trans('agenda.nome_completo')); ?>" name="cliente_distribuidor[NOME_RAZAO_SOCIAL]" value="<?php echo escape($arrClienteDistribuidor['NOME_RAZAO_SOCIAL']) ?>" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-2 col-md-12 col-lg-3">
                                    <label class="control-label" for="telefone_celular"><?php echo escape(_trans('agenda.celular')); ?></label>
                                </div>
                                <div class="col-lg-8">
                                    <input type="tel" class="form-control" data-mask="phone" id="telefone_celular" name="cliente_distribuidor[TELEFONE_CELULAR]" required>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-2 col-md-12 col-lg-3">
                                    <label class=" control-label" for="whatsapp"><?php echo escape(_trans('agenda.whatsapp')); ?></label>

                                </div>
                                <div class="col-lg-8">
                                    <input type="tel" class="form-control" data-mask="phone"
                                           id="whatsapp" name="cliente_distribuidor[WHATSAPP]" >
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-2 col-md-12 col-lg-3">
                                    <label class=" control-label" for="cep"><?php echo escape(_trans('agenda.cep')); ?></label>

                                </div>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" id="cep" name="cliente_distribuidor[CEP]" data-mask="cep" data-inputmask="'mask':'99999-999'" maxlength="9">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-2 col-md-12 col-lg-3">
                                    <label class=" control-label" for="endereco"><?php echo escape(_trans('agenda.endereco')); ?></label>

                                </div>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" id="endereco" name="cliente_distribuidor[ENDERECO]">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-2 col-md-12 col-lg-3">
                                    <label class=" control-label"><?php echo escape(_trans('agenda.numero')); ?></label>

                                </div>
                                <div class="col-lg-8">
                                    <input type="number" class="form-control" id="numero" name="cliente_distribuidor[NUMERO]">
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-2 col-md-12 col-lg-3">
                                    <label class=" control-label"><?php echo escape(_trans('agenda.bairro')); ?></label>

                                </div>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" id="bairro" name="cliente_distribuidor[BAIRRO]">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group row">
                                <div class="col-sm-2 col-md-12 col-lg-3">
                                    <label class=" control-label"><?php echo escape(_trans('agenda.complemento')); ?></label>

                                </div>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" name="cliente_distribuidor[COMPLEMENTO]" >
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-2 col-md-12 col-lg-3">
                                    <label class=" control-label"><?php echo escape(_trans('agenda.estado')); ?></label>
                                </div><?php

                                $collEstados = EstadoQuery::create()->orderByNome()->find();

                                ?><div class="col-lg-8">
                                    <select name="cliente_distribuidor[ESTADO]" id="estados" class="form-control">
                                        <option></option><?php

                                        /* @var $objEstado Estado */
                                        foreach ($collEstados as $objEstado) {
                                            ?><option value="<?php echo $objEstado->getId(); ?>">
                                            <?php echo $objEstado->getNome(); ?>
                                            </option><?php
                                        }

                                        ?></select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-2 col-md-12 col-lg-3">
                                    <label class=" control-label"><?php echo escape(_trans('agenda.cidade')); ?></label>

                                </div>
                                <div class="col-lg-8">
                                    <select name="cliente_distribuidor[CIDADE]" id="cidades" class="form-control">
                                        <option></option>
                                    </select>
                                </div>
                            </div>
                            <div class="panel panel-primary">
                                <div class="panel-heading" >
                                    <div class="panel-title text-uppercase">
                                        <?php echo escape(_trans('agenda.dados_complementares')); ?>
                                    </div>
                                </div>
                                <div class="panel-body">

                                    <div class="form-group row">
                                        <div class="col-sm-2 col-md-12 col-lg-3">
                                            <label class="control-label" for="telefone"><?php echo escape(_trans('agenda.telefone_fixo')); ?></label>
                                        </div>
                                        <div class="col-lg-8">
                                            <input type="tel" data-mask="phone" class="form-control mask" id="telefone" name="cliente_distribuidor[TELEFONE]" data-inputmask="'mask':'(99) 9999-9999'" value="">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-sm-2 col-md-12 col-lg-3">
                                            <label id="label_cpf_cnpj" class="control-label" for="cpf_cnpj"><?php echo escape(_trans('agenda.cpf')); ?></label>
                                        </div>

                                        <div class="col-lg-8">
                                            <input type="text" data-mask="cpf_cnpj" class="form-control mask" id="cpf_cnpj" name="cliente_distribuidor[CPF_CNPJ]" data-inputmask="'mask':'999.999.999-99'" value="">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-sm-2 col-md-12 col-lg-3">
                                            <label id="label_rg_ie" class="control-label" for="rg_ie"><?php echo escape(_trans('agenda.rg')); ?></label>
                                        </div>

                                        <div class="col-lg-8">
                                            <input type="text" class="form-control" id="rg_ie" name="cliente_distribuidor[RG_IE]" value="">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-sm-12 col-md-12 col-lg-3">
                                            <label class="control-label" for="data_nascimento_data_fundacao"><?php echo escape(_trans('agenda.data_nascimento')); ?></label>
                                        </div>
                                        <div class="col-lg-8">
                                            <input type="text" class="form-control datepicker" id="data_nascimento_data_fundacao" name="cliente_distribuidor[DATA_NASCIMENTO_DATA_FUNDACAO]" value="">
                                        </div>
                                    </div>

                                    <div class="form-group row">
                                        <div class="col-sm-2 col-md-12 col-lg-3">
                                            <label class="control-label"><?php echo escape(_trans('agenda.sexo')); ?></label>
                                        </div>
                                        <div class="col-lg-8 radioSexo">
                                            <label class="radio-inline radio radio-replace">
                                                <input type="radio" id="inlineradio3" name="cliente_distribuidor[SEXO]" value="M" checked=""> <?php echo escape(_trans('agenda.masculino')); ?>
                                            </label>
                                            <label class="radio-inline radio radio-replace">
                                                <input type="radio" id="inlineradio4" name="cliente_distribuidor[SEXO]" value="F"> <?php echo escape(_trans('agenda.feminino')); ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo escape(_trans('agenda.cancelar')); ?></button>
                    <button type="submit" class="btn btn-green"><?php echo escape(_trans('agenda.salvar')); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="enviar-sms">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo $root_path ?>/distribuidores_novo/listas/actions/sms.action.php?v=1" method="POST">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php echo escape(_trans('agenda.confirmacao_envio_sms')); ?></h4>
                </div>
                <div class="modal-body">
                
                    <input type="hidden" name="cliente_id" value="<?php echo $objClienteDistribuidor->getId(); ?>">
                    <div class="form-group row">
                        <div class="col-sm-3">
                            <label class="control-label"><?php echo escape(_trans('agenda.cliente')); ?></label>
                        </div>
                        <div class="col-sm-8">
                            <label class="control-label nomeCliente"><?php echo escape($objClienteDistribuidor->getNomeCompleto()); ?></label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3">
                            <label class="control-label"><?php echo escape(_trans('agenda.modelo_sms')); ?></label>
                        </div>
                        <div class="col-sm-8">
                            <select name="modeloSMS" class="select2" data-allow-clear="true" data-placeholder="<?php echo escape(_trans('agenda.escolha_modelo_mensagem')); ?>">
                                <option></option><?php

                                    $modelosSMS = DistribuidorTemplateQuery::create()
                                        ->filterByCliente(ClientePeer::getClienteLogado())
                                        ->filterByTipo(DistribuidorTemplate::TIPO_SMS)
                                        ->find();

                                    /* @var $modelo DistribuidorTemplate */
                                foreach ($modelosSMS as $modelo) {
                                    ?><option value="<?php echo $modelo->getId(); ?>"><?php echo $modelo->getAssunto(); ?></option><?php
                                }

                                ?></select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3">
                            <label class="control-label"><?php echo escape(_trans('agenda.descricao')); ?></label>
                        </div>
                        <div class="col-sm-8">
                            <textarea name="sms[MENSAGEM]" id="mensagemSMS" class="form-control" cols="30" rows="10" maxlength="160"></textarea>
                            <div id="contador"><?php echo escape(_trans('agenda.limite_caracteres')) ?>: <span>160</span></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo escape(_trans('agenda.cancelar')); ?></button>
                    <button type="submit" class="btn btn-green"><?php echo escape(_trans('agenda.enviar')); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="enviar-email">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo $root_path ?>/distribuidores_novo/listas/actions/email.action.php?v=1" method="POST">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php echo escape(_trans('agenda.confirmacao_envio_email')); ?></h4>
                </div>

                <div class="modal-body">
                    <input type="hidden" name="cliente_id" value="<?php echo $objClienteDistribuidor->getId(); ?>">
                    <div class="form-group row">
                        <div class="col-sm-3 label-atividade">
                            <label class="control-label"><?php echo escape(_trans('agenda.cliente')); ?></label>
                        </div>
                        <div class="col-sm-8">
                            <label class="control-label nomeCliente"><?php echo escape($objClienteDistribuidor->getNomeCompleto()); ?></label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3 label-atividade">
                            <label class="control-label"><?php echo escape(_trans('agenda.assunto')); ?></label>
                        </div>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" name="email[ASSUNTO]">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3 label-atividade">
                            <label class="control-label"><?php echo escape(_trans('agenda.modelo_email')); ?></label>
                        </div>
                        <div class="col-sm-8">
                            <select name="modeloEMAIL" class="select2" data-allow-clear="true" data-placeholder="<?php echo escape(_trans('agenda.escolha_modelo_mensagem')); ?>">
                                <option></option><?php

                                    $modelosEMAIL = DistribuidorTemplateQuery::create()
                                        ->filterByCliente(ClientePeer::getClienteLogado())
                                        ->filterByTipo(DistribuidorTemplate::TIPO_EMAIL)
                                        ->find();

                                    /* @var $modelo DistribuidorTemplate */
                                foreach ($modelosEMAIL as $modelo) {
                                    ?><option value="<?php echo $modelo->getId(); ?>"><?php echo $modelo->getAssunto(); ?></option><?php
                                }

                                ?></select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-3 label-atividade">
                            <label class="control-label"><?php echo escape(_trans('agenda.descricao')); ?></label>
                        </div>
                        <div class="col-sm-8">
                            <textarea name="email[MENSAGEM]" class="form-control" cols="30" rows="10"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo escape(_trans('agenda.cancelar')); ?></button>
                    <button type="submit" class="btn btn-green"><?php echo escape(_trans('agenda.enviar')); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php

    include __DIR__ . '/../../atividades/views/includes/modal_criar_atividade.php';
    
?>

<script>

    $(document).ready(function () {

        $('.emAndamento').on('click', function() {
            
            var id = $(this).data('id');
            
            $.ajax({
                url: '<?php echo $root_path ?>/distribuidores_novo/atividades/actions/busca_atividade.action.php?id='+id
            }).done(function( result ) {
                
                result = JSON.parse(result);
                
                console.log(result);
                
                $('#modal-criar-atividade input[name="id"]').val(id);
                $('#modal-criar-atividade select[name="evento[CLIENTE_DISTRIBUIDOR_ID]"]').val(result.CLIENTE_ID).trigger('change');
                $('#modal-criar-atividade select[name="evento[ASSUNTO]"]').val(result.ASSUNTO).trigger('change');
                $('#modal-criar-atividade input[name="evento[INTERESSE]"]').val(result.INTERESSE);
                $('#modal-criar-atividade input[name="evento[DATA]"]').val(result.DATA);
                $('#modal-criar-atividade textarea[name="evento[DESCRICAO]"]').val(result.DESCRICAO);
                $('#modal-criar-atividade input[name="evento[VALOR]"]').val(result.VALOR);
                $('#modal-criar-atividade textarea[name="evento[DESCRICAO_SMS]"]').val(result.DESCRICAO_SMS);
                $('#modal-criar-atividade textarea[name="evento[DESCRICAO_EMAIL]"]').val(result.DESCRICAO_EMAIL);
                $('#mensagemSMS').keydown();
                
                $('#modal-criar-atividade').modal('show');
            });
        });

        $('#mensagemSMS').on('keydown', function() {
            $('#contador span').text(160 - $(this).val().length);
        });

        $('#enviar-sms select[name="modeloSMS"]').on('change', function() {
            
            $('#enviar-sms textarea[name="sms[MENSAGEM]"]').attr('disabled', 'disabled');
            
            $.ajax({
                url: '<?php echo $root_path ?>/distribuidores_novo/listas/actions/busca_modelo.action.php?id='+$(this).find('option:selected').val()
            }).done(function( result ) {
                
                result = JSON.parse(result);
                
                $('#enviar-sms textarea[name="sms[MENSAGEM]"]').val(result);
                $('#enviar-sms textarea[name="sms[MENSAGEM]"]').removeAttr('disabled');
                $('#mensagemSMS').keydown();
            });
            
        });
        
        $('#enviar-email select[name="modeloEMAIL"]').on('change', function() {
            
            $('#enviar-email textarea[name="email[MENSAGEM]"]').attr('disabled', 'disabled');
            
            $.ajax({
                url: '<?php echo $root_path ?>/distribuidores_novo/listas/actions/busca_modelo.action.php?id='+$(this).find('option:selected').val()
            }).done(function( result ) {
                
                result = JSON.parse(result);
                
                $('#enviar-email textarea[name="email[MENSAGEM]"]').val(result);
                $('#enviar-email textarea[name="email[MENSAGEM]"]').removeAttr('disabled');
            });
            
        });

        $('a.btnEditar').on('click', function(e) {
            e.preventDefault();

            var id = $(this).data('id');

            $.ajax({
                url: '<?php echo $root_path ?>/distribuidores_novo/listas/actions/retorna_cliente.action.php?id='+id
            }).done(function( result ) {

                result = JSON.parse(result);

                console.log(result);

                $('#modal-adicionar-listas select[name="cliente_distribuidor[ESTADO]"]').val(result.estado).change();

                $('#modal-adicionar-listas input[name="id"]').val(id);
                $('#modal-adicionar-listas input[name="cliente_distribuidor[EMAIL]"]').val(result.email);
                $('#modal-adicionar-listas input[name="cliente_distribuidor[NOME_RAZAO_SOCIAL]"]').val(result.nome);
                $('#modal-adicionar-listas input[name="cliente_distribuidor[TELEFONE_CELULAR]"]').val(result.celular);
                $('#modal-adicionar-listas input[name="cliente_distribuidor[WHATSAPP]"]').val(result.whatsapp);

                $('#modal-adicionar-listas input[name="cliente_distribuidor[CEP]"]').val(result.cep);
                $('#modal-adicionar-listas input[name="cliente_distribuidor[ENDERECO]"]').val(result.endereco);
                $('#modal-adicionar-listas input[name="cliente_distribuidor[NUMERO]"]').val(result.numero);
                $('#modal-adicionar-listas input[name="cliente_distribuidor[BAIRRO]"]').val(result.bairro);
                $('#modal-adicionar-listas input[name="cliente_distribuidor[COMPLEMENTO]"]').val(result.complemento);

                $('#modal-adicionar-listas input[name="cliente_distribuidor[TELEFONE]"]').val(result.telefone);
                $('#modal-adicionar-listas input[name="cliente_distribuidor[CPF_CNPJ]"]').val(result.cpfcnpj);
                $('#modal-adicionar-listas input[name="cliente_distribuidor[RG_IE]"]').val(result.rgie);
                $('#modal-adicionar-listas input[name="cliente_distribuidor[DATA_NASCIMENTO_DATA_FUNDACAO]"]').val(result.data);

                $('#modal-adicionar-listas .radioTipo .radio-inline input[value="'+result.tipo+'"]').parent().parent().click();
                $('#modal-adicionar-listas .radioSexo .radio-inline input[value="'+result.sexo+'"]').parent().parent().click();

                setTimeout(function() {
                    $('#modal-adicionar-listas select[name="cliente_distribuidor[CIDADE]"]').val(result.cidade);
                }, 1800);

                $('#modal-adicionar-listas').modal('show');
            });

        });

        $('select[name="cliente_distribuidor[ESTADO]"]').on('change', function() {

            var estadoId = $(this).val();

            $.ajax({
                url: window.root_path +'/ajax/ajax_cidades/',
                type: 'GET',
                data: 'estadoId=' + estadoId,
                success: function(html){
                    $('#cidades').html(html);
                    $('#cidades').removeAttr('disabled');
                }
            });
            return false;

        });

        /**
         * Função responsável por buscar o CEP
         */

        $('#cep').on('change', function() {

            var cep = $(this).val();

            var pattern = /[0-9]{5}-[0-9]{3}/;

            if (pattern.test(cep)) {

                $.ajax({
                    //url: "https://qapi.com.br/correios/endereco/" + cep,
                    url: window.root_path + '/ajax/busca_cep/' + cep,
                    cache: false,
                    dataType: 'json',
                    success: function(response) {

                        var object = response;
                        if ( (typeof object != 'undefined') && (response !== null) ) {

                            console.log(object);

                            // Logadouro + Endereço
                            $('#endereco').val((object.logradouro ? object.logradouro + ' ' : ''));

                            // Bairro
                            $('#bairro').val(object.bairro);

                            $.ajax({
                                url: window.root_path +'/ajax/ajax_estados/',
                                type: 'GET',
                                data: 'sigla=' + object.uf,
                                success: function(html){
                                    $('#estados').html(html);
                                    // Cidade
                                    $.ajax({
                                        url: window.root_path +'/ajax/ajax_cidades/',
                                        type: 'GET',
                                        data: 'estadoId=' + $("#estados").val() + '&cidade=' + object.cidade,
                                        success: function(html){
                                            $('#cidades').html(html);
                                            $('#numero').focus();
                                        }
                                    });
                                }
                            });

                        } else {
                            // Sem retorno do webservice para o CEP informado.
                            alert("Não foi possivel encontrar informações relacionadas ao CEP informado!");
                        }

                    },
                    error: function(x, t, m) {

                        if (t === "timeout") {
                            alert("Não foi possivel encontrar informações relacionadas ao CEP informado!");
                        }

                    }
                });
            } else {
                // CEP com formatação incorreta
                alert("O CEP informado é inválido. Por favor, informe um CEP válido.");
            }

            return true;

        });

        
    });

</script>
