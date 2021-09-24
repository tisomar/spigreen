<div class="row">        
    <div class="col-md-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h4>Mail4Web</h4>
                <div class="options">
                    
                </div>
            </div>
            <div class="panel-body">
        <?php if (isset($utilizacaoContaMFW)) :  ?>
                <div class="row">
                    <div class="col-md-12 clearfix">
                        <h4 class="pull-left" style="margin:0 0 10px">Utilização de Créditos</h4>
<!--                        <div class="btn-group pull-right">
                            <a href="javascript:;" id="updatePieCharts" class="btn btn-default-alt btn-sm">Refresh</a>
                        </div>-->
                    </div>
                    
                <?php if ($utilizacaoContaMFW['categoria_plano'] != 'BASEADO_CONTATOS') :  ?>
                    <?php $percent = $utilizacaoContaMFW['max_mensagens'] > 0 ? (int)((($utilizacaoContaMFW['total_mensagens'] * 100) / $utilizacaoContaMFW['max_mensagens'])) : 0  ?>
                    <?php $percent = $percent > 100 ? 100 : $percent  ?>
                    <div class="col-xs-6 col-sm-6 col-md-4" style="padding-top:10px;padding-bottom:10px;">
                        <div class="easypiechart" id="mensagensMFW" data-percent="<?php echo $percent ?>">
                            <span class="percent"></span>
                        </div>
                        <label for="newvisits"><b>E-mails Disparados</b><br>(<?= $utilizacaoContaMFW['total_mensagens']; ?>)</label>
                        <hr class="hidden-md hidden-lg">
                    </div>
                <?php endif ?>

                    <?php $percent = $utilizacaoContaMFW['max_contatos'] > 0 ? (int)((($utilizacaoContaMFW['total_contatos'] * 100) / $utilizacaoContaMFW['max_contatos'])) : 0  ?>
                    <?php $percent = $percent > 100 ? 100 : $percent  ?>
                    <div class="col-xs-6 col-sm-6 col-md-4" style="padding-top:10px;padding-bottom:10px;">
                        <span class="easypiechart" id="contatosMFW" data-percent="<?php echo $percent ?>">
                            <span class="percent"></span>
                        </span>
                        <label for="bouncerate"><b>Contatos cadastrados</b><br>(<?= number_format($utilizacaoContaMFW['total_contatos'], 2, ',', '.'); ?>)</label>
                        <hr class="hidden-md hidden-lg">
                    </div>

                <?php  if (!empty($utilizacaoContaMFW['nome_pacote_sms'])) : ?>
                    <?php $percent = $utilizacaoContaMFW['max_sms'] > 0 ? (int)((($utilizacaoContaMFW['total_sms'] * 100) / $utilizacaoContaMFW['max_sms'])) : 0  ?>
                    <?php $percent = $percent > 100 ? 100 : $percent  ?>
                    <div class="col-xs-6 col-sm-6 col-md-4" style="padding-top:10px;padding-bottom:10px;">
                        <span class="easypiechart" id="smsMFW" data-percent="<?php echo $percent ?>">
                            <span class="percent"></span>
                        </span>
                        <label for="clickrate"><b>Créditos SMS</b><br>(<?php echo $utilizacaoContaMFW['total_sms']; ?>)</label>
                    </div>
                <?php endif ?>
                </div>
        <?php endif ?>
<!--                <div class="row">-->
<!--                --><?php //if (!isset($utilizacaoContaMFW)):  ?>
<!--                    <h4>A empresa <b>Mail4Web</b>, parceira da <b>Rede Fácil Brasil</b>, está disponibilizando e-mails automáticos gratuitos ilimitados para os primeiros <b>50 cadastros da sua AGENDA</b>. <br>Caso ainda não tenha uma conta no Mail4Web <b>cadastre-se</b> gratuitamente <a href="http://mail4web.com.br/pt/planos" target="_blank"><b>clicando aqui</b></a></h4>-->
<!--                --><?php //else: ?>
<!--                    <div class="col-md-12 clearfix">-->
<!--                        <h3 class="pull-left" style="margin:30px 0 0px">Pacotes de E-mails e SMS</h3><br><br>-->
<!--                        <p style="margin:30px 0 0; font-size: 16px;">-->
<!--                            Você pode enviar e-mails <b>ILIMITADOS</b> para até <b>50</b> contatos <b>GRÁTIS</b>!-->
<!--                            <br>Precisando de um pacote maior de e-mails e/ou SMS, clique para comprar <b><a href="https://mail4web.com.br/system/account/plans/" target="_blank"><u>PACOTE E-MAILS</u></a> ou <a href="https://mail4web.com.br/system/account/sms" target="_blank"><u>PACOTE SMS</u></a></b>-->
<!--                        </p>-->
<!--                        </h3>-->
<!--                    </div>-->
<!--                --><?php //endif ?>
<!--                </div>-->
            </div>
        </div>
    </div>
</div>
