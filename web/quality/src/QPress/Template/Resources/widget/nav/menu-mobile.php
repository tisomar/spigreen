<?php

$preActive = false;

if (ClientePeer::isAuthenticad()) :
    $preCadastrado = PreCadastroClienteQuery::create()
        ->filterByConcluido(0)
        ->findOneByClienteId(ClientePeer::getClienteLogado(true)->getId());

    if ($preCadastrado instanceof PreCadastroCliente) :
        $preActive = true;
    endif;
endif;
?>

<nav id="menu" class="hidden">
    <div>
        <section class="top">
            <div>
                <?php if (ClientePeer::isAuthenticad()) : ?>
                    Olá <span class="user-name"><?php echo ClientePeer::getClienteLogado()->getPrimeiroNome(); ?></span>!
                <?php else : ?>
                    Olá Visitante!
                    <a href="<?php echo get_url_site() ?>/cadastro" class="visible-xs">
                        Faça seu cadastro aqui!
                    </a>
                <?php endif; ?>
            </div>
        </section>
        <ul>
            <li>
                <a href="<?php echo get_url_site(); ?>/home">
                    <span class="<?php icon('home'); ?>"></span>
                    Página Inicial
                </a>
            </li>
            <li>
            <?php if ($clienteLogado = ClientePeer::getClienteLogado(true)) : ?>
                <a href="<?php echo get_url_site(); ?>/minha-conta/plano-carreira">
                    <span class="<?php icon('user'); ?>"></span>
                    Minha conta
                </a>
            <?php endif; ?>
                <?php if ($clienteLogado = ClientePeer::getClienteLogado(true)) : ?>
                    <ul>
                        <?php if ($clienteLogado->isClienteDistribuidor()) :  ?>
                            <li>
                                <a href="<?php echo get_url_site(); ?>/minha-conta/plano-carreira">
                                    <span class="<?php icon('graduation-cap'); ?>"></span>
                                    Plano de Carreira
                                </a>
                            </li>
                        <?php endif ?>
                        <?php if ($clienteLogado->isClientePreferencial()) :  ?>
                            <li>
                                <a href="<?php echo get_url_site(); ?>/minha-conta/extrato-cliente-preferencial">
                                    <span class="<?php icon('table'); ?>"></span>
                                    Extrato Pontos
                                </a>
                            </li>
                        <?php endif ?>

                        <?php if ($clienteLogado && $clienteLogado->getPlanoId() && !$clienteLogado->isClientePreferencial()) :  ?>
                        <li>
                            <a href="<?php echo get_url_site();?>/minha-conta/extrato-pontos-rede">
                                <span class="<?php icon('area-chart'); ?>"></span>
                                Pontos de Rede
                            </a>
                        </li>
                        <?php endif ?>
                        <li>
                            <a href="<?php echo get_url_site(); ?>/minha-conta/pedidos">
                                <span class="<?php icon('shopping-cart'); ?>"></span>
                                Meus Pedidos
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo get_url_site(); ?>/minha-conta/pedidos">
                                <span class="<?php icon('comments'); ?>"></span>
                                Mensagens
                            </a>
                        </li>
                        <?php if ($clienteLogado->isClienteDistribuidor()) :  ?>
                            <li>
                                <a href="<?php echo get_url_site(); ?>/minha-conta/suporte">
                                    <span class="<?php icon('question-circle'); ?>"></span>
                                    Material de Apoio
                                </a>
                            </li>
                        <?php endif ?>
                        <?php if ($clienteLogado->getId() == 222) :  ?>
                            <li>
                                <a href="http://spigreen.clickmark.com.br/" target="_blank">
                                    <span class="<?php icon('question-circle'); ?>"></span>
                                    Treinamentos
                                </a>
                            </li>
                        <?php endif ?>
                        <li>
                            <a href="<?php echo get_url_site(); ?>/minha-conta/avaliacoes">
                                <span class="<?php icon('star'); ?>"></span>
                                Minhas Avaliações
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo get_url_site(); ?>/minha-conta/dados">
                                <span class="<?php icon('user'); ?>"></span>
                                Dados Cadastrais
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo get_url_site(); ?>/minha-conta/enderecos">
                                <span class="<?php icon('truck'); ?>"></span>
                                Meus Endereços
                            </a>
                        </li>
                        <li>
                            <a href="<?php echo get_url_site(); ?>/minha-conta/nova-senha">
                                <span class="<?php icon('unlock-alt'); ?>"></span>
                                Redefinir Senha
                            </a>
                        </li>
                        <?php if ($clienteLogado->isClienteDistribuidor()) :  ?>
                            <li>
                                <a href="<?php echo get_url_site(); ?>/minha-conta/visualizar-rede">
                                    <span class="<?php icon('code-fork'); ?>"></span>
                                    Visualizar Rede
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo get_url_site(); ?>/minha-conta/visualizacao-clientes-preferencais-finais">
                                    <span class="<?php icon('users'); ?>"></span>
                                    Clientes Preferenciais e Finais
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php
                        $habilitaHotsite = Config::get('cliente.habilita_hotsite') == 1
                            && $clienteLogado->isClienteDistribuidor();

                        if ($habilitaHotsite) :
                            ?>
                            <li>
                                <a href="<?php echo get_url_site(); ?>/minha-conta/hotsite">
                                    <span class="<?php icon('edit'); ?>"></span>
                                    Hotsite
                                </a>
                            </li>
                        <?php
                        endif
                        ?>
                        <?php if ($clienteLogado->isClienteDistribuidor()) :  ?>
                            <li>
                                <a href="<?php echo get_url_site(); ?>/minha-conta/meu-plano">
                                    <span class="<?php icon('bank'); ?>"></span>
                                    Financeiro
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo get_url_site(); ?>/minha-conta/extrato-pontos-direta">
                                    <span class="<?php icon('area-chart'); ?>"></span>
                                    Bônus de Equipe Direta
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo get_url_site(); ?>/minha-conta/extrato-pontos-indireta">
                                    <span class="<?php icon('area-chart'); ?>"></span>
                                    Bônus de Equipe Indireta
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo get_url_site(); ?>/minha-conta/extrato-pontos-recompra">
                                    <span class="<?php icon('area-chart'); ?>"></span>
                                    Bônus de Equipe Produtividade
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo get_url_site(); ?>/minha-conta/extrato-bonus-cliente-preferencial">
                                    <span class="<?php icon('area-chart'); ?>"></span>
                                    Bônus de Cliente Preferencial
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if (!$clienteLogado->isClienteFinal()) : ?>
                            <li>
                                <a href="<?php echo get_url_site(); ?>/minha-conta/extrato-bonus-frete">
                                    <span class="<?php icon('area-chart'); ?>"></span>
                                    Bônus Frete
                                </a>
                            </li>
                        <?php endif; ?>
                        <?php if ($clienteLogado->isClienteDistribuidor()) : ?>
                            <li>
                                <a href="<?php echo get_url_site(); ?>/minha-conta/extrato-bonus-lideranca">
                                    <span class="<?php icon('area-chart'); ?>"></span>
                                    Bônus Liderança
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo get_url_site(); ?>/minha-conta/resgate">
                                    <span class="<?php icon('money'); ?>"></span>
                                    Solicitar Resgate
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo get_url_site(); ?>/minha-conta/extrato-resgate">
                                    <span class="<?php icon('area-chart'); ?>"></span>
                                    Extrato de Solicitação de Resgate
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo get_url_site(); ?>/minha-conta/transferencia">
                                    <span class="<?php icon('money'); ?>"></span>
                                    Solicitar Transferência
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo get_url_site(); ?>/minha-conta/extrato-transferencia">
                                    <span class="<?php icon('area-chart'); ?>"></span>
                                    Extrato de Transferência Recebida
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo get_url_site(); ?>/minha-conta/extrato-transferencia-enviada">
                                    <span class="<?php icon('area-chart'); ?>"></span>
                                    Extrato de Transferência Enviada
                                </a>
                            </li>
                        <?php endif; ?>
                        <li>
                            <a href="<?php echo get_url_site(); ?>/login/logout">
                                <span class="<?php icon('sign-out'); ?>"></span>
                                Sair
                            </a>
                        </li>
                    </ul>
                <?php endif; ?>
            </li>

            <?php if (!ClientePeer::isAuthenticad()) : ?>
                <li>
                    <a href="<?php echo get_url_site(); ?>/login">
                        <span class="<?php icon('sign-in'); ?>"></span>
                        Entrar
                    </a>
                </li>
            <?php endif; ?>

            <?php if (ClientePeer::isAuthenticad() && ClientePeer::getClienteLogado(true)->isConsumidorFinal()) : ?>
                <li>
                    <a href="<?php echo get_url_site(); ?>/login/actions/reseller.actions">
                        <span class="<?php icon('money'); ?>"></span>
                        Revender <br>Produtos <?php
                        $nomeFantasia = trim(Config::get('empresa_nome_fantasia'));
                        echo !empty($nomeFantasia) ? $nomeFantasia : 'Spigreen' ?>
                    </a>
                </li>
            <?php endif; ?>

            <li>
                <a href="<?php echo get_url_site(); ?>/minha-conta/ticket">
                    <span class="<?php icon('headphones'); ?>"></span>
                    SUPORTE AO D.I.S
                </a>
            </li>
        </ul>
        <br>
        <p>
            Categorias
        </p>
        <?php echo CategoriaPeer::renderCategoriasMobile(); ?>
    </div>
</nav>
