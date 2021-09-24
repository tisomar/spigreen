<?php if ($objConfiguracao->getChaveApiMailforweb()) :  ?>
    <?php

    $host = 'https://mail4web.com.br'; //host do mail4web

    $funcao = '/api/contatos/importa'; //funcao da API
    $apikey = $objConfiguracao->getChaveApiMailforweb(); //chave de API do usuario

    ?>
    <form action="" method="POST" id="form-cadastro-clientes" class="form-horizontal row-border">
        <div class="panel">
                <div class="panel-body">

                    <div class="form-group">
                        <label class="col-sm-3 control-label" for="email">Escolha uma lista para exportar os contatos</label>

                        <div class="col-sm-6">

                                <select name="mfw[lista_cadastrada]" id="lista" class="form-control">
                                    <option></option>
                                     <?php foreach ($objListas as $objLista) : ?>
                                        <option value="<?php echo $objLista->id;?>"><?php echo $objLista->nome; ?></option>
                                        <?php endforeach; ?>
                                </select>
                                <br>
                                Caso você <b>deseje criar uma nova lista</b>, informe o novo nome: <input type="text" class="form-control" name="mfw[lista_nova]" value="" id="newlista" >

                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label"></label>
                        <div class="col-sm-6"><?php
                        if ($txtQtdContato <> '') {
                            echo $txtQtdContato;
                        }
                        ?></div>

                    </div>

                    <div class="form-group">

                        <label class="col-sm-3 control-label" for="contatos">Contatos filtrados (<b><?= count(unserialize($_POST['contatos']))?></b>)</label>
                        <div class="col-sm-6">
                            <div style="height: 200px; overflow-y:scroll; border: 1px solid #c4c4c4; padding: 4px;">
                                <?php

                                $contatos = unserialize($_POST['contatos']);
                                foreach ($contatos as $contato) : ?>
                                    <?php echo $contato['nome'] . '<br>';
                                endforeach;

                                ?>


                            </div>
                        </div>
                        <input type="hidden" id="contatos" name="mfw[contatos]" value='<?= $_POST['contatos']; ?>'>
                        <input type="hidden" name="url" id="url" value='<?= "$host$funcao?apikey=$apikey"; ?>'>
                        <input type="hidden" name="url_redirect" id="url_redirect" value='<?= $_POST['redirect']; ?>'>

                    </div>

                </div>

                <div class="panel-footer">
                    <div class="row">
                        <div class="col-sm-6 col-sm-offset-3">
                            <div class="btn-toolbar">
                                <button type="button" class="btn-primary btn" id="btnSubmit" name="btnSubmit" title="Exportar contatos para Mail For Web">Exportar contatos para Mail For Web</button>
                                <button class="btn-default btn" onclick="history.go(-1); return false;" title="Cancelar">Cancelar</button>
                            </div>
                        </div>
                    </div>
                </div>
        </div>
    </form>
<?php else : ?>
    <div class="container">
        <div class="row">
            <div class="col-md-12 col-lg-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h4>Mail For Web</h4>
                        <div class="options">
                        </div>
                    </div>
                    <div class="panel-body">
                        <p class="help-block">

<!--                            <h4>A empresa <b>Mail4Web</b>, parceira da <b>Spigreen</b>, está disponibilizando e-mails automáticos gratuitos ilimitados para os primeiros <b>50 cadastros da sua AGENDA</b>. <br>Caso ainda não tenha uma conta no Mail4Web <b>cadastre-se</b> gratuitamente <a href="http://mail4web.com.br/pt/planos" target="_blank"><b>clicando aqui</b></a></h4><br>-->
                            <h5>
                            Para visualizar esta página é necessário informar sua chave de API do Mailforweb na <a href="<?php echo $root_path ?>/distribuidores_novo/configuracoes/cadastro"><b>página de configurações</b></a><br>
                            Caso já possua uma conta, <a href="https://mail4web.com.br/system/account/profile/edit" target="blank">acesse aqui</a> e clique em "<b>GERAR CHAVE</b>" para gerar sua chave de usuário.</h5>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php endif ?>
