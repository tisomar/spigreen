<?php $clienteLogado = ClientePeer::getClienteLogado(true);

$preCadastrado = PreCadastroClienteQuery::create()
    ->filterByConcluido(0)
    ->findOneByClienteId($clienteLogado->getId());

$preActive = false;

if ($preCadastrado instanceof PreCadastroCliente) {
    $preActive = true;
}

?>
<!-- <style>
    a.link-treinamentos {
        background-color: #A1E63A;
        display: flex;
        border-radius: 4px;
        height: 40px;
        font-weight: bold;
        color: #ffffff;
        justify-content: space-between;
        align-items: center;
        padding: 0 15px;
        font-size: 16px;
        margin-bottom: 15px;
    }
</style> -->
<!-- </?php if ($clienteLogado->isClienteDistribuidor()) : ?>
    <a href="/minha-conta/treinamentos/login" target="_blank" class="link-treinamentos">
        Spi Academy
        <i class="fa fa-external-link"></i>
    </a>
</?php endif ?> -->

<!-- MENU COM DROPDOWN QUANDO EM VISUAL MOBILE -->
<!-- <nav class="menu-central"> -->
    <div class="visible-xs visible-sm">
        <div class="form-group">
            <select class="form-control input-sm mobile select-with-links">
                <?php if ($clienteLogado->isClienteDistribuidor()) : ?>
                    <option value="<?php echo get_url_site(); ?>/minha-conta/plano-carreira" <?php echo ('minha-conta-plano-carreira' == $strIncludesKey) ? 'selected' : ''; ?>">
                        Plano de Carreira
                    </option>
                    <option value="<?php echo get_url_site(); ?>/minha-conta/banner-graduacao" <?php echo ('minha-conta-banner-graduacao' == $strIncludesKey) ? 'selected' : ''; ?>">
                        Banner Graduação
                    </option>
                <?php endif ?>
                <?php if ($clienteLogado->isClientePreferencial()) : ?>
                    <option value="<?php echo get_url_site(); ?>/minha-conta/extrato-cliente-preferencial" <?php echo ('minha-conta-extrato-cliente-preferencial' == $strIncludesKey) ? 'selected' : ''; ?>>
                        Extrato Pontos
                    </option>
                <?php endif ?>
                <?php if ($clienteLogado && $clienteLogado->getPlanoId() && !$clienteLogado->isClientePreferencial()) : ?>
                    <option value="<?php echo get_url_site(); ?>/minha-conta/extrato-pontos-rede" <?php echo ('minha-conta-extrato-meus-pontos' == $strIncludesKey) ? 'selected' : ''; ?>>
                        Pontos de Rede
                    </option>
                <?php endif ?>
                <option value="<?php echo get_url_site(); ?>/minha-conta/pedidos" <?php echo ('minha-conta-pedidos' == $strIncludesKey or 'minha-conta-pedidos-detalhes' == $strIncludesKey) ? 'selected' : ''; ?>>
                    Meus Pedidos
                </option>
                <option value="<?php echo get_url_site(); ?>/minha-conta/ticket" <?php echo ('minha-conta-ticket' == $strIncludesKey or 'minha-conta-ticket-detalhes' == $strIncludesKey) ? 'selected' : ''; ?>>
                    Suporte
                </option>
                <option value="<?php echo get_url_site(); ?>/minha-conta/alertas" <?php echo ('minha-conta-alertas' == $strIncludesKey) ? 'selected' : ''; ?>>
                    Mensagens
                </option>
                <?php if ($clienteLogado->isClienteDistribuidor()) : ?>
                    <option value="<?php echo get_url_site(); ?>/minha-conta/suporte" <?php echo ('minha-conta-suporte' == $strIncludesKey) ? 'selected' : ''; ?>>
                        Material de Apoio
                    </option>
                <?php endif ?>
                <option value="<?php echo get_url_site(); ?>/minha-conta/avaliacoes" <?php echo ('minha-conta-avaliacoes' == $strIncludesKey) ? 'selected' : ''; ?>>
                    Minhas Avaliações
                </option>
                <option value="<?php echo get_url_site(); ?>/minha-conta/dados" <?php echo ('minha-conta-dados' == $strIncludesKey) ? 'selected' : ''; ?>>
                    Dados Cadastrais
                </option>
                <option value="<?php echo get_url_site(); ?>/minha-conta/enderecos" <?php echo ('minha-conta-enderecos' == $strIncludesKey) ? 'selected' : ''; ?>>
                    Meus Endereços
                </option>
                <option value="<?php echo get_url_site() ?>/minha-conta/nova-senha" <?php echo ('minha-conta-nova-senha' == $strIncludesKey) ? 'selected' : ''; ?>>
                    Redefinir Senha
                </option>
                <?php if ($clienteLogado->isClienteDistribuidor()) : ?>
                    <option value="<?php echo get_url_site(); ?>/minha-conta/visualizar-rede" <?php echo ('minha-conta-visualizar-rede' == $strIncludesKey) ? 'selected' : ''; ?>>
                        Visualizar Rede
                    </option>
                    <option value="<?php echo get_url_site(); ?>/minha-conta/visualizacao-clientes-preferencais-finais" <?php echo ('minha-conta-visualizacao-clientes-preferencais-finais' == $strIncludesKey) ? 'selected' : ''; ?>>
                        Clientes Preferenciais e Finais
                    </option>
                <?php endif; ?>
                <?php if ($clienteLogado->isClienteComPlano() && Config::get('cliente.habilita_hotsite') == 1) : ?>
                    <option value="<?php echo get_url_site(); ?>/minha-conta/hotsite" <?php echo ('minha-conta-hotsite' == $strIncludesKey) ? 'selected' : ''; ?>>
                        Hotsite
                    </option>
                <?php endif; ?>
                <?php if ($clienteLogado->isClienteDistribuidor()) : ?>
                    <option value="<?php echo get_url_site(); ?>/minha-conta/meu-plano" <?php echo ('minha-conta-meu-plano' == $strIncludesKey) ? 'selected' : ''; ?>">
                    Financeiro
                    </option>
                <?php endif; ?>
                <?php if ($clienteLogado->isClienteComPlano()) : ?>
                    <option value="<?php echo get_url_site(); ?>/minha-conta/extrato-pontos-direta" <?php echo ('minha-conta-extrato-pontos-direta' == $strIncludesKey) ? 'selected' : ''; ?>>
                        Bônus de Equipe Direte
                    </option>
                <?php endif; ?>
                <?php if ($clienteLogado->isClienteDistribuidor()) : ?>
                    <option value="<?php echo get_url_site(); ?>/minha-conta/extrato-pontos-indireta" <?php echo ('minha-conta-extrato-pontos-indireta' == $strIncludesKey) ? 'selected' : ''; ?>>
                        Bônus de Equipe Indireta
                    </option>
                    <option value="<?php echo get_url_site(); ?>/minha-conta/extrato-pontos-recompra" <?php echo ('minha-conta-extrato-pontos-recompra' == $strIncludesKey) ? 'selected' : ''; ?>>
                        Bônus de Equipe Produtividade
                    </option>
                    <option value="<?php echo get_url_site(); ?>/minha-conta/extrato-bonus-cliente-preferencial" <?php echo ('minha-conta-extrato-bonus-cliente-preferencial' == $strIncludesKey) ? 'selected' : ''; ?>>
                        Bônus de Cliente Preferencial
                    </option>
                    <option value="<?php echo get_url_site(); ?>/minha-conta/extrato-bonus-produtos" <?php echo ('minha-conta-extrato-bonus-produtos' == $strIncludesKey) ? 'selected' : ''; ?>>
                        Bônus Produtos
                    </option>
                    <option value="<?php echo get_url_site(); ?>/minha-conta/extrato-bonus-lideranca" <?php echo ('minha-conta-extrato-bonus-lideranca' == $strIncludesKey) ? 'selected' : ''; ?>>
                        Bônus Liderança
                    </option>
                <?php endif ?>
                <?php if (!$clienteLogado->isClienteFinal()) : ?>
                    <option value="<?php echo get_url_site(); ?>/minha-conta/extrato-bonus-frete" <?php echo ('minha-conta-extrato-bonus-frete' == $strIncludesKey) ? 'selected' : ''; ?>>
                        Bônus Frete
                    </option>
                <?php endif ?>
                <?php if ($clienteLogado->isClienteDistribuidor()) : ?>
                    <option value="<?php echo get_url_site(); ?>/minha-conta/resgate" <?php echo ('minha-conta-resgate' == $strIncludesKey) ? 'selected' : ''; ?>>
                        Solicitar Resgate
                    </option>
                    <option value="<?php echo get_url_site(); ?>/minha-conta/resgate-premios" <?php echo ('minha-conta-resgate-premios' == $strIncludesKey) ? 'selected' : ''; ?>>
                        Solicitar Resgate Prêmios
                    </option>
                    <option value="<?php echo get_url_site(); ?>/minha-conta/extrato-resgate" <?php echo ('minha-conta-extrato-resgate' == $strIncludesKey) ? 'selected' : ''; ?>>
                        Extrato de Solicitação de Resgate
                    </option>
                    <option value="<?php echo get_url_site(); ?>/minha-conta/extrato-resgate-pontos-acumulados" <?php echo ('minha-conta-extrato-resgate-pontos-acumulados' == $strIncludesKey) ? 'selected' : ''; ?>>
                        Extrato de Solicitação de Resgate Pontos Acumulados
                    </option>
                    <option value="<?php echo get_url_site() ?>/minha-conta/transferencia" <?php echo ('minha-conta-transferencia' == $strIncludesKey) ? 'selected' : ''; ?>>
                        Solicitar Transferência
                    </option>
                    <option value="<?php echo get_url_site(); ?>/minha-conta/extrato-transferencia" <?php echo ('minha-conta-extrato-transferencia' == $strIncludesKey) ? 'selected' : ''; ?>>
                        Extrato de Transferências recebidas
                    </option>
                    <option value="<?php echo get_url_site(); ?>/minha-conta/extrato-transferencia-enviada" <?php echo ('minha-conta-extrato-transferencia-enviada' == $strIncludesKey) ? 'selected' : ''; ?>>
                        Extrato de Transferências enviadas
                    </option>
                    <option value="<?php echo get_url_site(); ?>/minha-conta/participantes-rede" <?php echo ('minha-conta-participantes-rede' == $strIncludesKey) ? 'selected' : ''; ?>>
                        Total participantes rede
                    </option>
                    <option value="<?php echo get_url_site(); ?>/minha-conta/participantes-inativos" <?php echo ('minha-conta-participantes-inativos' == $strIncludesKey) ? 'selected' : ''; ?>>
                        DIS inativos
                    </option>
                <?php endif ?>
            </select>
        </div>
    </div>
<!-- </nav> -->

<!-- NOVO MENU LATERAL -->
<div class="hidden-xs hidden-sm">
    <div class="wrapper-office">
        <div class="sidebar-office-wrapper">
            <ul class="sidebar-office-nav">
                <?php if ($clienteLogado->isClienteDistribuidor() && !$clienteLogado->isClientePreferencial()) : ?>
                    <nav>
                        <!-- MENU MATERIAL DE APOIO -->
                        <span class="<?php icon('image'); ?>"></span>
                        <a href="<?php echo get_url_site(); ?>/minha-conta/banner-graduacao"
                            class="<?php echo ($strIncludesKey == 'minha-conta-banner-graduacao') ? "active" : "" ?>"
                            style="color: #666">
                            <span>Banner Graduação</span>
                        </a>
                        <span class="<?php icon('invisible'); ?>"></span>
                    </nav>
                <?php endif ?>

                <nav class="accordion">
                    <span class="<?php icon('user'); ?>"></span>
                    Meu Perfil
                    <span class="<?php icon('chevron-right'); ?>"></span>
                </nav>
                <div class="accordion">
                    <li>
                        <!-- Suporte -->
                        <a href="<?php echo get_url_site(); ?>/minha-conta/ticket"
                           class="sidebar-office-open <?php echo ($strIncludesKey == 'minha-conta-ticket' or $strIncludesKey == 'minha-conta-ticket-detalhes') ? "active" : "" ?>">
                            <span> - Suporte</span>
                        </a>
                        <!-- MEUS PEDIDOS -->
                        <a href="<?php echo get_url_site(); ?>/minha-conta/pedidos"
                           class="sidebar-office-open <?php echo ($strIncludesKey == 'minha-conta-pedidos' or $strIncludesKey == 'minha-conta-pedido-detalhes') ? "active" : "" ?>">
                            <span> - Meus Pedidos</span>
                        </a>

                        <?php if ($clienteLogado->isClienteComPlano()) : ?>
                            <!-- HOTSITE -->
                            <?php if (Config::get('cliente.habilita_hotsite') == 1) : ?>
                                <a href="<?php echo get_url_site(); ?>/minha-conta/hotsite"
                                   class="sidebar-office-open <?php echo ('minha-conta-hotsite' == $strIncludesKey) ? "active" : ''; ?>">
                                    <span> - Hotsite</span>
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>

                        <!-- MINHAS AVALIAÇÕES -->
                        <a href="<?php echo get_url_site(); ?>/minha-conta/avaliacoes"
                           class="sidebar-office-open <?php echo ($strIncludesKey == 'minha-conta-avaliacoes') ? "active" : "" ?>">
                            <span> - Minhas Avaliações</span>
                        </a>

                        <!-- DADOS CADASTRAIS -->
                        <a href="<?php echo get_url_site(); ?>/minha-conta/dados"
                           class="sidebar-office-open <?php echo (($strIncludesKey == 'minha-conta-dados') or ($strIncludesKey == 'cadastro')) ? "active" : "" ?>">
                            <span> - Dados Cadastrais</span>
                        </a>

                        <!-- MENSAGENS -->
                        <a href="<?php echo get_url_site(); ?>/minha-conta/alertas"
                           class="sidebar-office-open <?php echo ('minha-conta-alertas' == $strIncludesKey) ? "active" : ''; ?>">
                            <span> - Mensagens</span>
                            <?php $messages = ClientePeer::getClienteLogado()->quantidadeMensagensPendentes(); ?>

                            <span id="toRead" class="badge pull-right"
                                  style="background-color: #C41F26; font-size: 14px; visibility: <?php echo $messages == 0 ? 'hidden' : 'visible' ?>">
                        <?php echo $messages ?></span>
                        </a>

                        <!-- ENDEREÇOS -->
                        <a href="<?php echo get_url_site(); ?>/minha-conta/enderecos"
                           class="sidebar-office-open <?php echo ($strIncludesKey == 'minha-conta-enderecos') ? "active" : "" ?>">
                            <span> - Meus Endereços</span>
                        </a>

                        <!-- REDEFINIR SENHA -->
                        <a href="<?php echo get_url_site(); ?>/minha-conta/nova-senha"
                           class="sidebar-office-open <?php echo ('minha-conta-nova-senha' == $strIncludesKey) ? "active" : ''; ?>">
                            <span> - Redefinir Senha</span>
                        </a>
                    </li>
                </div>

                <!-- APENAS CLIENTES DISTRIBUIDORES -->
                <?php if ($clienteLogado->isClienteComPlano()) : ?>
                    <?php if (!$clienteLogado->isClientePreferencial()) : ?>
                    <nav>
                        <!-- MENU MATERIAL DE APOIO -->
                        <span class="<?php icon('question-circle'); ?>"></span>
                        <a href="<?php echo get_url_site(); ?>/minha-conta/suporte"
                            class="<?php echo ($strIncludesKey == 'minha-conta-suporte') ? "active" : "" ?>"
                            style="color: #666">
                            <span>Material de apoio</span>
                        </a>
                        <span class="<?php icon('invisible'); ?>"></span>
                    </nav>
                    <nav class="accordion">
                        <span class="<?php icon('graduation-cap'); ?>"></span>
                        <span>Plano de Carreira</span>
                        <span class="<?php icon('chevron-right'); ?>"></span>
                    </nav>
                    <!-- MENU PLANO DE CARREIRA -->
                    <div class="accordion">
                        <li>
                            <!-- PLANO CARREIRA -->
                            <a href="<?php echo get_url_site(); ?>/minha-conta/plano-carreira"
                               class="sidebar-office-open <?php echo ('minha-conta-plano-carreira' == $strIncludesKey) ? "active" : ''; ?>">
                                <span> - Seu Plano</span>
                            </a>
                            <!-- PONTOS DE REDE -->
                            <?php if ($clienteLogado && $clienteLogado->getPlanoId() && !$clienteLogado->isClientePreferencial()) : ?>
                                <a href="<?php echo get_url_site(); ?>/minha-conta/extrato-pontos-rede"
                                   class="sidebar-office-open <?php echo ('minha-conta-extrato-meus-pontos' == $strIncludesKey) ? "active" : ''; ?>">
                                    <span> - Pontos de Rede</span>
                                </a>
                            <?php endif ?>
                            <!-- VISUALIZAR REDE -->
                            <a href="<?php echo get_url_site(); ?>/minha-conta/visualizar-rede"
                               class="sidebar-office-open <?php echo ('minha-conta-visualizar-rede' == $strIncludesKey) ? "active" : ''; ?>">
                                <span> - Visualizar Rede</span>
                            </a>
                            <!-- BONUS CLIENTE PREFERENCIAL E FINAL -->
                            <a href="<?php echo get_url_site(); ?>/minha-conta/visualizacao-clientes-preferencais-finais"
                               class="sidebar-office-open <?php echo ('minha-conta-visualizacao-clientes-preferencais-finais' == $strIncludesKey) ? "active" : ''; ?>">
<!--                                <span> - E-commerce</span>-->
                                <span> - Clientes preferenciais e finais</span>
                            </a>
                        </li>
                    </div>
                    <?php endif ?>

                    <!-- MENU BÔNUS -->
                    <nav class="accordion">
                        <span class="<?php icon('area-chart'); ?>"></span>
                        <span>Bônus</span>
                        <span class="<?php icon('chevron-right'); ?>"></span>
                    </nav>
                    <div class="accordion">
                        <li>
                            <!-- BONUS DE EQUIPE DIRETA -->
                            <a href="<?php echo get_url_site(); ?>/minha-conta/extrato-pontos-direta"
                               class="sidebar-office-open <?php echo ('minha-conta-extrato-pontos-direta' == $strIncludesKey) ? "active" : ''; ?>">
                                <span> - Expansão Direta</span>
                            </a>
                        <?php if (!$clienteLogado->isClientePreferencial()) : ?>
                            <!-- BONUS DE EQUIPE INDIRETA -->
                            <a href="<?php echo get_url_site(); ?>/minha-conta/extrato-pontos-indireta"
                               class="sidebar-office-open <?php echo ('minha-conta-extrato-pontos-indireta' == $strIncludesKey) ? "active" : ''; ?>">
                                <span> - Expansão Indireta</span>
                            </a>
                            <!-- BONUS DE PRODUTIVIDADE -->
                            <a href="<?php echo get_url_site(); ?>/minha-conta/extrato-pontos-recompra"
                               class="sidebar-office-open <?php echo ('minha-conta-extrato-pontos-recompra' == $strIncludesKey) ? "active" : ''; ?>">
                                <span> - Bônus Produtividade</span>
                            </a>
                            <!-- BONUS DE CLIENTE PREFERENCIAL -->
                            <a href="<?php echo get_url_site(); ?>/minha-conta/extrato-bonus-cliente-preferencial"
                               class="sidebar-office-open <?php echo ('minha-conta-extrato-bonus-cliente-preferencial' == $strIncludesKey) ? "active" : ''; ?>">
                                <span> - Bônus de Cliente Preferencial</span>
                            </a>
                            <!-- BONUS PRODUTOS -->
                              <a href="<?php echo get_url_site(); ?>/minha-conta/extrato-bonus-produtos"
                               class="sidebar-office-open <?php echo ('minha-conta-extrato-bonus-produtos' == $strIncludesKey) ? "active" : ''; ?>">
                                <span> - Bônus Produtos</span>
                            </a>
                        <?php endif ?>
                            <!-- BONUS FRETE -->
                            <a href="<?php echo get_url_site(); ?>/minha-conta/extrato-bonus-frete"
                               class="sidebar-office-open <?php echo ('minha-conta-extrato-bonus-frete' == $strIncludesKey) ? "active" : ''; ?>">
                                <span> - Bônus Frete</span>
                            </a>
                        <?php if (!$clienteLogado->isClientePreferencial()) : ?>
                            <!-- BONUS LIDERANÇA -->
                            <a href="<?php echo get_url_site(); ?>/minha-conta/extrato-bonus-lideranca"
                               class="sidebar-office-open <?php echo ('minha-conta-extrato-bonus-lideranca' == $strIncludesKey) ? "active" : ''; ?>">
                                <span> - Bônus Liderança</span>
                            </a>
                        <?php endif ?>
                        </li>
                    </div>

                    <?php if (!$clienteLogado->isClientePreferencial()) : ?>
                    <!-- MENU FINANCEIRO -->
                    <nav class="accordion">
                        <span class="<?php icon('bank'); ?>"></span>
                        <span>Financeiro</span>
                        <span class="<?php icon('chevron-right'); ?>"></span>
                    </nav>
                    <div class="accordion">
                        <li>
                            <!-- FINANCEIRO -->
                            <a href="<?php echo get_url_site(); ?>/minha-conta/meu-plano"
                               class="sidebar-office-open <?php echo ('minha-conta-meu-plano' == $strIncludesKey) ? "active" : ''; ?>">
                                <span> - Meu Plano</span>
                            </a>
                            <!-- SOLICITAR RESGATE -->
                            <a href="<?php echo get_url_site(); ?>/minha-conta/resgate"
                               class="sidebar-office-open <?php echo ('minha-conta-resgate' == $strIncludesKey) ? "active" : ''; ?>">
                                <span> - Solicitar Resgate</span>
                            </a>
                            <!-- SOLICITAR RESGATE DE PREMIOS -->
                            <a href="<?php echo get_url_site(); ?>/minha-conta/resgate-premios"
                               class="sidebar-office-open <?php echo ('minha-conta-resgate-premios' == $strIncludesKey) ? "active" : ''; ?>">
                                <span> - Solicitar Resgate Prêmios</span>
                            </a>
                            <!-- SOLICITAR TRANSFERENCIA -->
                            <a href="<?php echo get_url_site(); ?>/minha-conta/transferencia"
                               class="sidebar-office-open <?php echo ('minha-conta-transferencia' == $strIncludesKey) ? "active" : ''; ?>">
                                <span> - Solicitar Transferência</span>
                            </a>
                            <!-- EXTRATO DE SOLICITACAO DE RESGATE -->
                            <a href="<?php echo get_url_site(); ?>/minha-conta/extrato-resgate"
                               class="sidebar-office-open <?php echo ('minha-conta-extrato-resgate' == $strIncludesKey) ? "active" : ''; ?>">
                                <span> - Extrato de Solicitação de Resgate</span>
                            </a>
                            <!-- EXTRATO DE SOLICITACAO DE RESGATE PONTOS ACUMULADOS -->
                            <a href="<?php echo get_url_site(); ?>/minha-conta/extrato-resgate-pontos-acumulados"
                               class="sidebar-office-open <?php echo ('minha-conta-extrato-resgate-pontos-acumulados' == $strIncludesKey) ? "active" : ''; ?>">
                                <span> - Extrato de Solicitação de Resgate Pontos Acumulados</span>
                            </a>
                            <!-- EXTRATO DE TRANSFERENCIA RECEBIDA -->
                            <a href="<?php echo get_url_site(); ?>/minha-conta/extrato-transferencia"
                               class="sidebar-office-open <?php echo ('minha-conta-extrato-transferencia' == $strIncludesKey) ? "active" : ''; ?>">
                                <span> - Extrato de Transferência Recebida</span>
                            </a>
                            <!-- EXTRATO DE TRANSFERENCIA ENVIADA -->
                            <a href="<?php echo get_url_site(); ?>/minha-conta/extrato-transferencia-enviada"
                               class="sidebar-office-open <?php echo ('minha-conta-extrato-transferencia-enviada' == $strIncludesKey) ? "active" : ''; ?>">
                                <span> - Extrato de Transferência Enviada</span>
                            </a>
                        </li>
                    </div>

                     <!-- MENU REDE -->
                    <nav class="accordion">
                        <span class="<?php icon('area-chart'); ?>"></span>
                        <span>Gestão de rede</span>
                        <span class="<?php icon('chevron-right'); ?>"></span>
                    </nav>
                    <div class="accordion">
                        <li>
                            <!-- PARTICIPANTES DE REDE -->
                            <a href="<?php echo get_url_site(); ?>/minha-conta/participantes-rede"
                            class="sidebar-office-open <?php echo ('minha-conta-meu-plano' == $strIncludesKey) ? "active" : ''; ?>">
                                <span> - Total participantes rede</span>
                            </a>
                        </li>
                        <li>
                            <!-- DIS INATIVO -->
                            <a href="<?php echo get_url_site(); ?>/minha-conta/participantes-inativos"
                            class="sidebar-office-open <?php echo ('minha-conta-meu-plano' == $strIncludesKey) ? "active" : ''; ?>">
                                <span> - DIS inativos</span>
                            </a>
                        </li>
                        <!-- <li> -->
                            <!-- GRADUACÃO PARTICIPANTES -->
                            <!-- <a href="</?php echo get_url_site(); ?>/minha-conta/participantes-graduacao" -->
                            <!-- class="sidebar-office-open </?php echo ('minha-conta-meu-plano' == $strIncludesKey) ? "active" : ''; ?>"> -->
                                <!-- <span> - Participantes graduação</span> -->
                            <!-- </a> -->
                        <!-- </li> -->
                    </div>
                    <?php endif ?>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<style>
    nav.accordion {
        cursor: pointer;
    }

    div.accordion {
        display: none;
    }
</style>

<script>
    $(document).ready(function () {
        $('nav.accordion').click(function () {
            $('div.accordion:visible').slideUp("fast");
            $(this).next('div.accordion').stop().slideToggle("slow");
        });
    });
</script>

<!-- MENU ANTIGO -->
<!--<div class="hidden-xs hidden-sm">
        <?php if ($clienteLogado->isClienteDistribuidor()) : ?>
            <a href="<?php echo get_url_site(); ?>/minha-conta/plano-carreira" class="text-right list-group-item <?php echo ('minha-conta-plano-carreira' == $strIncludesKey) ? "active" : ''; ?>">
                <span class="pull-left">Plano de Carreira</span>
                <span class="<?php icon('chevron-right'); ?>"></span>
            </a>
        <?php endif ?>
        <?php if ($clienteLogado && $clienteLogado->getPlanoId() && !$clienteLogado->isClientePreferencial()) : ?>
            <a href="<?php echo get_url_site(); ?>/minha-conta/extrato-pontos-rede" class="text-right list-group-item <?php echo ('minha-conta-extrato-meus-pontos' == $strIncludesKey) ? "active" : ''; ?>">
                <span class="pull-left">Pontos de Rede</span>
                <span class="<?php icon('chevron-right'); ?>"></span>
            </a>
        <?php endif ?>
        <a href="<?php echo get_url_site(); ?>/minha-conta/pedidos" class="text-right list-group-item <?php echo ($strIncludesKey == 'minha-conta-pedidos' or $strIncludesKey == 'minha-conta-pedido-detalhes') ? "active" : "" ?>">
            <span class="pull-left">Meus Pedidos</span>
            <span class="<?php icon('chevron-right'); ?>"></span>
        </a>
        <a href="<?php echo get_url_site(); ?>/minha-conta/alertas" class="text-right list-group-item <?php echo ('minha-conta-alertas' == $strIncludesKey) ? "active" : ''; ?>">
            <span class="pull-left">Mensagens</span>
            <span class="<?php icon('chevron-right'); ?>"></span>
            <?php $messages = ClientePeer::getClienteLogado()->quantidadeMensagensPendentes(); ?>

            <span id="toRead" class="badge pull-right"
                  style="background-color: #C41F26; font-size: 14px; visibility: <?php echo $messages == 0 ? 'hidden' : 'visible' ?>">
                    <?php echo $messages ?></span>
        </a>
        <?php if ($clienteLogado->isClienteDistribuidor()) : ?>
            <a href="<?php echo get_url_site(); ?>/minha-conta/suporte" class="text-right list-group-item <?php echo ($strIncludesKey == 'minha-conta-suporte') ? "active" : "" ?>">
                <span class="pull-left">Material de apoio</span>
                <span class="<?php icon('chevron-right'); ?>"></span>
            </a>
        <?php endif; ?>
        <?php if ($clienteLogado->getId() == 222) : ?>
            <a href="http://spigreen.clickmark.com.br/" target="_blank" class="text-right list-group-item">
                <span class="pull-left">Treinamentos</span>
                <span class="<?php icon('chevron-right'); ?>"></span>
            </a>
        <?php endif; ?>
        <a href="<?php echo get_url_site(); ?>/minha-conta/avaliacoes" class="text-right list-group-item <?php echo ($strIncludesKey == 'minha-conta-avaliacoes') ? "active" : "" ?>">
            <span class="pull-left">Minhas Avaliações</span>
            <span class="<?php icon('chevron-right'); ?>"></span>
        </a>
        <a href="<?php echo get_url_site(); ?>/minha-conta/dados" class="text-right list-group-item <?php echo (($strIncludesKey == 'minha-conta-dados') or ($strIncludesKey == 'cadastro')) ? "active" : "" ?>">
            <span class="pull-left">Dados Cadastrais</span>
            <span class="<?php icon('chevron-right'); ?>"></span>
        </a>
        <a href="<?php echo get_url_site(); ?>/minha-conta/enderecos" class="text-right list-group-item <?php echo ($strIncludesKey == 'minha-conta-enderecos') ? "active" : "" ?>">
            <span class="pull-left">Meus Endereços</span>
            <span class="<?php icon('chevron-right'); ?>"></span>
        </a>
        <a href="<?php echo get_url_site(); ?>/minha-conta/nova-senha" class="text-right list-group-item <?php echo ('minha-conta-nova-senha' == $strIncludesKey) ? "active" : ''; ?>">
            <span class="pull-left">Redefinir Senha</span>
            <span class="<?php icon('chevron-right'); ?>"></span>
        </a>
        <?php if ($clienteLogado->isClienteDistribuidor()) : ?>
            <a href="<?php echo get_url_site(); ?>/minha-conta/visualizar-rede" class="text-right list-group-item <?php echo ('minha-conta-visualizar-rede' == $strIncludesKey) ? "active" : ''; ?>">
                <span class="pull-left">Visualizar Rede</span>
                <span class="<?php icon('chevron-right'); ?>"></span>
            </a>
            <a href="<?php echo get_url_site(); ?>/minha-conta/visualizacao-clientes-preferencais-finais" class="text-right list-group-item <?php echo ('minha-conta-visualizacao-clientes-preferencais-finais' == $strIncludesKey) ? "active" : ''; ?>">
                <span class="pull-left">Clientes preferenciais e finais</span>
                <span class="<?php icon('chevron-right'); ?>"></span>
            </a>
            <?php if (Config::get('cliente.habilita_hotsite') == 1) : ?>
                <a href="<?php echo get_url_site(); ?>/minha-conta/hotsite" class="text-right list-group-item <?php echo ('minha-conta-hotsite' == $strIncludesKey) ? "active" : ''; ?>">
                    <span class="pull-left">Hotsite</span>
                    <span class="<?php icon('chevron-right'); ?>"></span>
                </a>
            <?php endif; ?>
            <a href="<?php echo get_url_site(); ?>/minha-conta/meu-plano" class="text-right list-group-item <?php echo ('minha-conta-meu-plano' == $strIncludesKey) ? "active" : ''; ?>">
                <span class="pull-left">Financeiro</span>
                <span class="<?php icon('chevron-right'); ?>"></span>
            </a>
            <?php
                $preActive = false;
            ?>
            <a href="<?php echo get_url_site(); ?>/minha-conta/extrato-pontos-direta" class="text-right list-group-item <?php echo ('minha-conta-extrato-pontos-direta' == $strIncludesKey) ? "active" : ''; ?>">
                <span class="pull-left">Bônus de Equipe Direta</span>
                <span class="<?php icon('chevron-right'); ?>"></span>
            </a>
            <a href="<?php echo get_url_site(); ?>/minha-conta/extrato-pontos-indireta" class="text-right list-group-item <?php echo ('minha-conta-extrato-pontos-indireta' == $strIncludesKey) ? "active" : ''; ?>">
                <span class="pull-left">Bônus de Equipe Indireta</span>
                <span class="<?php icon('chevron-right'); ?>"></span>
            </a>
            <a href="<?php echo get_url_site(); ?>/minha-conta/extrato-pontos-recompra" class="text-right list-group-item <?php echo ('minha-conta-extrato-pontos-recompra' == $strIncludesKey) ? "active" : ''; ?>">
                <span class="pull-left">Bônus de Equipe Produtividade</span>
                <span class="<?php icon('chevron-right'); ?>"></span>
            </a>
            <a href="<?php echo get_url_site(); ?>/minha-conta/extrato-bonus-cliente-preferencial" class="text-right list-group-item <?php echo ('minha-conta-extrato-bonus-cliente-preferencial' == $strIncludesKey) ? "active" : ''; ?>">
                <span class="pull-left">Bônus de Cliente Preferencial</span>
                <span class="<?php icon('chevron-right'); ?>"></span>
            </a>
        <?php endif ?>
        <?php if (!$clienteLogado->isClienteFinal()) : ?>
            <a href="<?php echo get_url_site(); ?>/minha-conta/extrato-bonus-frete" class="text-right list-group-item <?php echo ('minha-conta-extrato-bonus-frete' == $strIncludesKey) ? "active" : ''; ?>">
                <span class="pull-left">Bônus Frete</span>
                <span class="<?php icon('chevron-right'); ?>"></span>
            </a>
        <?php endif ?>
        <?php if ($clienteLogado->isClienteDistribuidor()) : ?>
            <a href="<?php echo get_url_site(); ?>/minha-conta/resgate" class="text-right list-group-item <?php echo ('minha-conta-resgate' == $strIncludesKey) ? "active" : ''; ?>">
                <span class="pull-left">Solicitar Resgate</span>
                <span class="<?php icon('chevron-right'); ?>"></span>
            </a>
            <a href="<?php echo get_url_site(); ?>/minha-conta/extrato-resgate" class="text-right list-group-item <?php echo ('minha-conta-extrato-resgate' == $strIncludesKey) ? "active" : ''; ?>">
                <span class="pull-left">Extrato de Solicitação de Resgate</span>
                <span class="<?php icon('chevron-right'); ?>"></span>
            </a>
            <a href="<?php echo get_url_site(); ?>/minha-conta/transferencia" class="text-right list-group-item <?php echo ('minha-conta-transferencia' == $strIncludesKey) ? "active" : ''; ?>">
                <span class="pull-left">Solicitar Transferência</span>
                <span class="<?php icon('chevron-right'); ?>"></span>
            </a>
            <a href="<?php echo get_url_site(); ?>/minha-conta/extrato-transferencia" class="text-right list-group-item <?php echo ('minha-conta-extrato-transferencia' == $strIncludesKey) ? "active" : ''; ?>">
                <span class="pull-left">Extrato de Transferência Recebida</span>
                <span class="<?php icon('chevron-right'); ?>"></span>
            </a>
            <a href="<?php echo get_url_site(); ?>/minha-conta/extrato-transferencia-enviada" class="text-right list-group-item <?php echo ('minha-conta-extrato-transferencia-enviada' == $strIncludesKey) ? "active" : ''; ?>">
                <span class="pull-left">Extrato de Transferência Enviada</span>
                <span class="<?php icon('chevron-right'); ?>"></span>
            </a>
        <?php endif ?>
    </div>-->

