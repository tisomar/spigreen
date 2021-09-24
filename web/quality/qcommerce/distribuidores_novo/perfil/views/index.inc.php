
<div class="profile-env">
    <header class="row">
        <div class="col-sm-3">
<!--<!--            <div class="profile-picture">-->
<!--<!--                <img src="--><?php ////echo $root_path ?><!--<!--/distribuidor_scripts/assets/img/niveis/--><?php ////echo $images[$nivel]; ?><!--<!--.png" width="50" height="50" class="img-responsive img-circle" style="margin-top: 25px" />-->
<!--<!--            </div>-->
        </div>

        <div class="col-sm-9">
            <div class="row">
                <ul class="profile-info-sections">
                    <div class="col-sm-6">
                        <li>
                            <div class="profile-name">
                                <strong><?php echo $user->getNomeCompleto(); ?></strong>
<!--                                <span>--><?php //echo $nivel; ?><!--</span>-->
                            </div>
                        </li>
                    </div>
                    <div class="col-sm-6">
                        <li>
                            <div class="profile-stat">
                                <h3><?php echo number_format(ClientePeer::getClienteLogado()->getTotalPontosMes(), 2, ',', '.'); ?></h3>
                                <span><a href="#"><?php echo escape(_trans('agenda.pa_mensal')) ?></a></span>
                            </div>
                        </li>
                    </div>
<!--                    <div class="col-sm-4">-->
<!--                        <li>-->
<!--                            <div class="profile-stat">-->
<!--                                <h3>--><?php //echo number_format(ClientePeer::getClienteLogado()->getTotalPontosMesRedeFromCache()[0],2,',','.'); ?><!--</h3>-->
<!--                                <span>--><?php //echo escape(_trans('agenda.pa_rede_mensal')) ?><!--</span>-->
<!--                            </div>-->
<!--                        </li>-->
<!--                    </div>-->
                </ul>
            </div>
        </div>

    </header>
    <br><br>
    <section class="profile-info-tabs">

        <div class="row">

            <div class="col-sm-offset-2 col-sm-4">

                <ul class="user-details">
                    <li>
                        <i class="entypo-location"></i>
                        <?php echo $endereco != null ? $endereco->getEnderecoSemFormatacao() : ''; ?>
                    </li>
                    <li>
                        <i class="entypo-mail"></i>
                        <?php echo $user->getEmail(); ?>
                    </li>
                    <li>
                        <i class="entypo-mobile"></i>
                        <?php echo $user != null ? $user->getTelefone() : ''; ?>
                        <br><br>
                    </li>
                    <li>
                        <strong><?php echo escape(_trans('agenda.nome')) ?>:</strong>
                        <?php echo $user->getNomeCompleto(); ?>
                    </li>
                    <li>
                        <strong><?php echo ($user->isPessoaJuridica() ? escape(_trans('agenda.cnpj')) : escape(_trans('agenda.cpf'))); ?>:</strong>
                        <?php echo $user->getCpf(); ?>
                    </li>
<!--                    <li>-->
<!--                        <strong>--><?php //echo ($user->isPessoaJuridica() ? escape(_trans('agenda.ie')) : escape(_trans('agenda.rg'))); ?><!--:</strong>-->
<!--                        --><?php //echo $user->getRgIe(); ?>
<!--                    </li>-->
                    <li>
                        <strong><?php echo ($user->isPessoaJuridica() ? escape(_trans('agenda.dt_fundacao')) : escape(_trans('agenda.dt_nascimento'))); ?>:</strong>
                        <?php echo $user->getDataNascimento(); ?>
                    </li>
                </ul>
            </div>
<!--            <div class="col-sm-5">-->
<!--                <div class="panel panel-primary panel-resumo-mensal" style="background: #FFF !important">-->
<!--                    <h3 class="cabecalho-table">--><?php //echo escape(_trans('agenda.meu_plano')) ?><!--</h3>-->
<!--                    <div class="panel-body">-->
<!--                        <div class="tab-content">-->
<!--                            <table class="table" style="margin: 0">-->
<!--                                <tbody >-->
<!--                                    <tr>-->
<!--                                        <td>--><?php //echo escape(_trans('agenda.contatos')) ?><!--</td>-->
<!--                                        <td>-->
<!--                                            <div class="progress">--><?php
//                                                if(isset($utilizacaoContaMFW['max_contatos']) && $utilizacaoContaMFW['max_contatos'] > 0){
//                                                    $widthContato = $utilizacaoContaMFW['total_contatos'] * 100 / $utilizacaoContaMFW['max_contatos'];
//                                                }else{ 0;}
//                                                ?>
<!--                                            <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="--><?php //echo isset($utilizacaoContaMFW['total_contatos']) ? $utilizacaoContaMFW['total_contatos'] : 0; ?><!--" aria-valuemin="0" aria-valuemax="--><?php //echo isset($utilizacaoContaMFW['max_contatos']) ?  $utilizacaoContaMFW['max_contatos'] : 0; ?><!--" style="width: --><?php //echo number_format(isset($widthContato) ? $widthContato : 0, 2, ".", ""); ?>
<!--/*                                            </div>*/-->
<!--/*                                            <small style="display: block; margin-top: -10px; padding-right: 3px; text-align: right;">*/--><?php ////echo
//                                                number_format(isset($utilizacaoContaMFW['total_contatos']) ? $utilizacaoContaMFW['total_contatos'] : 0, 0, "", ".") . ' / ' . number_format(isset($utilizacaoContaMFW['max_contatos']) ? $utilizacaoContaMFW['max_contatos'] : 0, 0, "", "."); ?><!--</small>-->
<!--                                        </td>-->
<!--                                    </tr>-->
<!--                                    <tr>-->
<!--                                        <td>-->
<!--                                            --><?php //echo escape(_trans('agenda.creditos_sms')) ?>
<!--                                        </td>-->
<!--                                        <td>-->
<!--                                            <div class="progress">--><?php
//                                                if (isset($utilizacaoContaMFW['total_sms']) && (isset($utilizacaoContaMFW['max_sms'])  && $utilizacaoContaMFW['max_sms'] > 0)){
//                                                    $widthSMS = $utilizacaoContaMFW['total_sms'] * 100 / $utilizacaoContaMFW['max_sms'];
//                                                }else{ 0;}
////                                                var_dump($utilizacaoContaMFW['total_sms']);die;
//                                                ?>
<!--                                                <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="--><?php //echo isset($utilizacaoContaMFW['total_sms']) ? $utilizacaoContaMFW['total_sms'] : 0; ?><!--" aria-valuemin="0" aria-valuemax="--><?php //echo isset($utilizacaoContaMFW['max_sms']) ? $utilizacaoContaMFW['max_sms'] : 0; ?><!--" style="width: --><?php //echo number_format(isset($widthSMS) ? $widthSMS : 0, 2, ".", ""); ?>
<!--/*                                            </div>*/-->
<!--/*                                            <small style="display: block; margin-top: -10px; padding-right: 3px; text-align: right;">*/-->
                                                <?php //echo number_format(isset($utilizacaoContaMFW['total_sms']) ? $utilizacaoContaMFW['total_sms'] : 0, 0, "", ".") . ' / ' . number_format(isset($utilizacaoContaMFW['max_sms']) ? $utilizacaoContaMFW['max_sms'] : 0, 0, "", "."); ?><!--</small>-->
<!--                                        </td>-->
<!--                                    </tr>-->
<!--                                    <tr>-->
<!--                                        <td colspan="2">-->
<!--                                            <a href="--><?php //echo $root_path ?><!--/distribuidores_novo/credito-contato/" class="btn btn-blue pull-right">--><?php //echo escape(_trans('agenda.contrate_contato')) ?><!--</a>-->
<!--                                            <a href="--><?php //echo $root_path ?><!--/distribuidores_novo/credito-sms/" class="btn btn-blue pull-right" style="margin-right: 2px;">--><?php //echo escape(_trans('agenda.contrate_SMS')) ?><!--</a>-->
<!--                                        </td>-->
<!--                                    </tr>-->
<!--                                </tbody>-->
<!--                            </table>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </div>-->
<!--            </div>-->
            <div class="clear"></div>
            <!-- tabs for the profile links -->
            <ul class="nav nav-tabs col-sm-offset-2">
                <li class="active"><a href="javascript:;"><?php echo escape(_trans('agenda.configuracoes')) ?></a></li>
                <li class=""><a href="https://www.redefacilbrasil.com.br/web/novidades/detalhes/niveis-do-sistema-de-pontuacao-rede-facil-brasil" target="_blank"><?php echo escape(_trans('agenda.informacoes')) ?></a></li>
            </ul>
        </div>
    </section>
    <div class="container">
        <div class="profile-dados" id="profile-dados">

            <form action="" method="POST" id="form-configuracoes-gerais" role="form" class="form-horizontal form-groups-bordered">
                <div class="panel-body">
                    <input type="hidden" name="configuracao[RECEBER_NOTIFICACAO_EVENTOS_ATRASADOS]" value="1">
                    <input type="hidden" name="configuracao[DIAS_ALERTA_EVENTOS_ATRASADOS]" value="2">

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="telefone-notificacao"><?php echo escape(_trans('agenda.telefone_notificacao')) ?></label>
                        <div class="col-sm-5">
                            <input type="tel" min="0" class="form-control" id="telefone-notificacao" name="configuracao[TELEFONE_NOTIFICACAO]" value="<?php echo escape($arrConfiguracao['TELEFONE_NOTIFICACAO']) ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="chave-api-mailforweb"><?php echo escape(_trans('agenda.chave_m4w')) ?></label>
                        <div class="col-sm-5">
                            <input type="text" min="0" class="form-control" id="chave-api-mailforweb" name="configuracao[CHAVE_API_MAILFORWEB]" value="<?php echo escape($arrConfiguracao['CHAVE_API_MAILFORWEB']) ?>" placeholder="Chave">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-sm-3"></div>
                        <div class="col-sm-5">
                            <button type="submit" class="btn btn-green btn-icon icon-left" style="display: initial">
                                <?php echo escape(_trans('agenda.salvar')) ?>
                                <i class="entypo-check"></i>
                            </button>
                        </div>
                    </div>
                    <hr>
                    
                    <div class="panel-heading metas">
                        <div class="panel-title">
                            <?php echo escape(_trans('agenda.definir_metas')) ?><br>
                            <a class="btn btn-blue btn-icon icon-left add-meta pull-left" 
                               href="javascript:;" onclick="jQuery('#modal-cadastro-meta-anual').modal('show');" style="color: #FFF; margin-right: 2px;" >
                                <i class="entypo-plus"></i><?php echo escape(_trans('agenda.definir_meta_anual')) ?>
                            </a>
                            <a class="btn btn-blue btn-icon icon-left add-meta pull-left" 
                               href="javascript:;" onclick="jQuery('#modal-cadastro-meta').modal('show');" style="color: #FFF" >
                                <i class="entypo-plus"></i><?php echo escape(_trans('agenda.definir_meta_mensal')) ?>
                            </a>
                        </div>
                    </div>
                    <div class="panel-body">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th><?php echo escape(_trans('agenda.mes')) ?></th>
                                    <th><?php echo escape(_trans('agenda.meta')) ?></th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody><?php

                            if (count($pager) > 0) {
                                foreach ($pager as $metaVenda) {
                                    ?><tr>
                                            <td><?php echo escape($metaVenda->getDataInicial('m/Y')) ?></td>
                                            <td>R$ <?php echo escape(number_format($metaVenda->getMeta(), 2, ',', '.')) ?></td>
                                            <td style="width: 50px; text-align: center;">
                                                <a href="<?php echo $root_path ?>/distribuidores_novo/perfil/actions/excluir.action.php?id=<?php echo $metaVenda->getId(); ?>" class="btn btn-danger"><i class="entypo-cancel"></i></a>
                                            </td>
                                        </tr><?php
                                }
                            } else {
                                ?><tr>
                                        <td colspan="3"><?php echo escape(_trans('agenda.nenhuma_meta')) ?></td>
                                    </tr><?php
                            }
                                
                            ?></tbody>
                        </table>
                    </div>
                </div>
            </form>
            
        </div>
    </div>
</div>

<div class="modal fade" id="modal-cadastro-meta">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?php echo escape(_trans('agenda.cadastro_meta')) ?></h4>
            </div>
            
            <form action="<?php echo $root_path . '/distribuidores_novo/perfil/actions/cadastro.meta.action.php'; ?>" method="POST">
                <div class="modal-body">

                        <div class="form-group row">
                            <div class="col-sm-3 label-atividade">
                                <label class="control-label"><?php echo escape(_trans('agenda.data')) ?></label>
                            </div>
                            <div class="col-sm-9">
                                <input type="text" class="form-control datepicker" data-format="mm/yyyy" name="meta_venda[MES]" data-start-view="1" data-minViewMode="1">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-3 label-atividade">
                                <label class="control-label"><?php echo escape(_trans('agenda.meta')) ?></label>
                            </div>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <span class="input-group-addon">R$</span>
                                    <input type="text"  class="form-control" name="meta_venda[META]">
                                </div>
                            </div>
                        </div>


                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo escape(_trans('agenda.cancelar')) ?></button>
                    <button type="submit" class="btn btn-green"><?php echo escape(_trans('agenda.salvar')) ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-cadastro-meta-anual">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title"><?php echo escape(_trans('agenda.cadastro_meta_anual')) ?></h4>
            </div>
            
            <form action="<?php echo $root_path . '/distribuidores_novo/perfil/'; ?>" method="POST">
                <div class="modal-body">
                    <div class="form-group row">
                        <div class="col-sm-3 label-atividade">
                            <label class="control-label"><?php echo escape(_trans('agenda.meta_vendas')) ?></label>
                        </div>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <span class="input-group-addon">R$</span>
                                <input id="meta" type="text" name="configuracao[META_VENDAS_MENSAL]" placeholder="Meta de venda" class="form-control" value="<?php echo escape($arrConfiguracao['META_VENDAS_MENSAL']) ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo escape(_trans('agenda.cancelar')) ?></button>
                    <button type="submit" class="btn btn-green"><?php echo escape(_trans('agenda.salvar')) ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
