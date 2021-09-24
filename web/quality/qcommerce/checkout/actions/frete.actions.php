<?php
use QPress\Frete\Services\RetiradaLoja\RetiradaLoja;
use QPress\Frete\FreteInterface;
use \QPress\Frete\Services\Correios\Servicos\Correios04669;
use \QPress\Frete\Services\Correios\Servicos\Correios04162;

$carrinho = $container->getCarrinhoProvider()->getCarrinho();

[ $avisoEstoqueNegativo, $centroDistribuicaoId ] = $carrinho->getCentroDistribuicaoEstoqueByEstadoCliente();

$isBlockTransporteGollog = $carrinho->getBlockItemTotransporteGollog();
$isBlockTransporteTG = $carrinho->getBlockItemTotransporteTG();
$step = 'frete';
include __DIR__ . '/validate.step.actions.php';

$estados = EstadoQuery::create()
    ->distinct()
    ->useCidadeQuery()
    ->useRetiradaLojaQuery()
    ->endUse()
    ->endUse()
    ->find();

$optionEstados = '';
foreach ($estados as $estado) :
    $optionEstados .= sprintf(
        '<option value="%d">%s - %s</option>',
        $estado->getId(),
        $estado->getNome(),
        $estado->getSigla()
    );
endforeach;

// INFORMANDO O TOTAL DE PONTOS DA COMPRA
$somaTotalPontos = 0;
foreach ($carrinho->getPedidoItems() as $item):
    $valorPontos = $item->getProdutoVariacao()->getProduto()->getValorPontos();
    $qtdProdutos = $item->getQuantidade();
    $somaTotalPontos += $qtdProdutos * $valorPontos;
endforeach;

if ($container->getRequest()->isMethod('POST')) :

    $tokenRecebidoDoFront = $container->getRequest()->request->all()['tokenConfirmacaoPermiteCompra'];
    if($tokenRecebidoDoFront !== 'ABUYOK') :
        FlashMsg::add('danger', 'Alguns dos produtos de seu pedido encontram-se indisponíveis na loja selecionada!');
        redirect('/checkout/frete');
    endif;

    // Se existe esse parâmetro na requisição, siginfica que o usuário
    // selecionou o frete grátis proporcionado pelo valor da compra ser acima de 1000 pontos
    if (escape_post($container->getRequest()->request->all()['frete']) === 'frete_gratis') :
        $carrinho->addFrete(escape_post($container->getRequest()->request->all()['frete']), '', 0);
        $carrinho->setCentroDistribuicaoId($centroDistribuicaoId);
        $carrinho->save();
        redirect('/checkout/pagamento');
    endif;

    if (is_array($container->getFreteManager()->getModalidades())) :
        $opcoesDisponiveis = $container->getFreteManager()->getModalidades();
        if (isset($opcoesDisponiveis[$container->getRequest()->request->get('frete')])) :
            $frete = $opcoesDisponiveis[$container->getRequest()->request->get('frete')];
            if ($frete instanceof FreteInterface) :
                // Remove a opção de retirada em loja caso tenha sido adicionado anteriormente.
                PedidoRetiradaLojaQuery::create()
                    ->filterByPedidoId($carrinho->getId())
                    ->delete();

                // Se o frete escolhido for retirada em loja, adiciona a opção escolhida
                if ($frete instanceof RetiradaLoja) :
                    // Valida se a loja existe
                    $loja = RetiradaLojaQuery::create()->filterByHabilitado(true)->findOneById($container->getRequest()->request->get('pedido_retirada_loja'));

                    if (!is_null($loja)) :
                        $fromArray = $loja->toArray();
                        unset($fromArray['Id']);

                        $pedidoRetiradaLoja = new PedidoRetiradaLoja();
                        $pedidoRetiradaLoja->fromArray($fromArray);
                        $pedidoRetiradaLoja->setLojaId($loja->getId());
                        $pedidoRetiradaLoja->setPedidoId($carrinho->getId());
                        $pedidoRetiradaLoja->save();

                        $prazoExtenso = $pedidoRetiradaLoja->getPrazoExtenso();
                        $valor = $pedidoRetiradaLoja->getValor();

                        $carrinho->setCentroDistribuicaoId($container->getRequest()->request->get('centroDistribuicaoId'));
                    else :
                        FlashMsg::add('danger', 'A opção de entrega que você selecionou não encontra-se disponível no momento. Tente seleciona outra opção.');
                        redirect('/checkout/frete');
                    endif;
                else :
                    if ($franqueado) :
                        $cep = $franqueado->getEnderecoPrincipal()->getCep();
                    else :
                        $cep = $carrinho->getEndereco()->getCep();
                    endif;

                    $freteConsulta = $carrinho->consultaFrete($frete, $cep);

                    $prazoExtenso = $freteConsulta->getPrazoExtenso();
                    $valor = format_number($freteConsulta->getValor(), UsuarioPeer::LINGUAGEM_INGLES);

                    $carrinho->setCentroDistribuicaoId($centroDistribuicaoId);
                endif;

                $carrinho->addFrete($frete->getNome(), $prazoExtenso, $valor, true);
                redirect('/checkout/pagamento');
            endif;
        endif;
    endif;
    FlashMsg::add('info', 'Você precisa selecionar um meio de entrega.');
endif;