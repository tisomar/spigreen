<?php

use ClearSale\Address;
use ClearSale\Billing;
use ClearSale\Card;
use ClearSale\Currency;
use ClearSale\Item;
use ClearSale\Order;
use ClearSale\Payment;
use ClearSale\Phone;
use ClearSale\PurchaseInformation;
use ClearSale\Shipping;

use QPress\Gateway\Services\CieloApi30\CieloApi;

$bonusProdutos = ExtratoBonusProdutosQuery::create()->filterByClienteId(ClientePeer::getClienteLogado()->getId())->filterByIsDistribuido(false)->find();
$bonusProdutosDisponíveis = '';
foreach($bonusProdutos as $bonus) :
    $bonusProdutosDisponíveis .= '<br><br>' . $bonus->getObservacao();
endforeach;

/* @var $container \QPress\Container\Container */
$carrinho = $container->getCarrinhoProvider()->getCarrinho();
$pagamentoCartaoCredito = Config::get('sistema.tipo_cartao_credito');
$pagamentoCartaoDebito = Config::get('sistema.tipo_cartao_debito');
$pagamentoBoleto = Config::get('sistema.tipo_boleto');

### Inicialização de algumas variáveis
$step = 'pagamento';
$boletoGatewayName = null;

$request_pagamento = escape_post($container->getRequest()->request->all());

include __DIR__ . '/validate.step.actions.php';

function getKeysPagamentoDividido($values) {
    $response = [];
    
    foreach ($values as $key => $value):
        if (is_array($value)):
            foreach (getKeysPagamentoDividido($value) as $key2 => $value2):
                $response["[$key]" . $key2] = $value2;
            endforeach;
        else:
            $response["[$key]"] = $value;
        endif;
    endforeach;

    return $response;
}

function renderPagamentoDivididoFields() {
    global $request;
    $pagamentoDividido = $request->request->get('pagamento_dividido', []);

    foreach ($pagamentoDividido as $index => $value) :
        foreach (getKeysPagamentoDividido($value) as $key => $value) :
            ?>
            <input type="hidden" name="pagamento_dividido[<?= $index ?>]<?= $key ?>" value="<?= $value ?>"/>
            <?php
        endforeach;
    endforeach;

    foreach (getKeysPagamentoDividido($request->request->all()) as $key => $value) :
        if (stripos($key, 'pagamento_dividido') !== false) continue;
        ?>
        <input type="hidden" name="pagamento_dividido[<?= count($pagamentoDividido) ?>]<?= $key ?>" value="<?= $value ?>"/>
        <?php
    endforeach;
}

$isPagamentoDividido = $request->request->has('valor_pagamento');
$pagamentoDividido = [];

$valorBoleto = 0;
$valorBonusUtilizados = 0;
$valorBonusFreteUtilizados = 0;
$valorBonusCP = 0;
$valorEmLoja = 0;
$valorTranferencia = 0;

// if ($isPagamentoDividido):
$pagamentoDividido = $request->request->get('pagamento_dividido', []);
$pagamentoDividido[] = [
    'forma_pagamento' => $request->request->get('forma_pagamento', ''),
    'valor_pagamento' => $request->request->get('valor_pagamento', 0),
];
// endif;

// PEGANDO O VALOR TOTAL DISPONIVEL PARA COMPRA COM BONUS
$totalBonusDiretoDisponivel = $gerenciador->getTotalPontosDisponiveisParaResgate(ClientePeer::getClienteLogado(), null, null, Extrato::TIPO_INDICACAO_DIRETA, true);
$totalPagamento = $totalBonusDiretoDisponivel > $carrinho->getValorTotal() ? $carrinho->getValorTotal() : $totalBonusDiretoDisponivel;

$requestPayment = [];
$valorRestanteDividido = $carrinho->getValorTotal();

$estoqueRetirada = '';
foreach($carrinho->getPedidoItems() as $pedidoItem) :
    $estoqueDisponivel = $pedidoItem->getProdutoVariacao()->getEstoqueAtualCD($carrinho->getCentroDistribuicaoId());
    $qtdItens = $pedidoItem->getQuantidade();

    if( $qtdItens > $estoqueDisponivel ) :
        $estoqueRetirada = 'Estoque disponível para retirada em 10 dias úteis após a confirmação do pagamento. ';
    endif;
endforeach;

foreach ($pagamentoDividido as $pag):
    $valor = (float) str_replace(',', '.',
        preg_replace('/[^\d\,]/', '', $pag['valor_pagamento'])
    );

    if (empty($valor)) :
        $valor = 0;
    endif;

    $paymentData = [
        'forma_pagamento' => $pag['forma_pagamento']
    ];

    switch($pag['forma_pagamento']):
        case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BONUS_FRETE:
            $valorBonusFreteUtilizados += $valor;
            break;
        case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS:
            if ($valor > $totalPagamento):
                $valor = $totalPagamento;
            endif;
            
            $valorBonusUtilizados += $valor;
            break;
        case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_CARTAO_CREDITO:
            if (!empty($pag['cartao'])) :
                $dadosCartao = $pag['cartao'];
                $bandeira = $pag['BANDEIRA'];
                $numeroParcelas = $pag['numero_parcelas'];
            elseif (!empty($request_pagamento['cartao'])) :
                $dadosCartao = $request_pagamento['cartao'];
                $bandeira = $request_pagamento['BANDEIRA'];
                $numeroParcelas = $request_pagamento['numero_parcelas'];
            endif;
            
            if (!empty($dadosCartao)) :
                $cartao = [
                    'numero' => $dadosCartao['numero'],
                    'titular' => $dadosCartao['titular'],
                    'validade_mes' => $dadosCartao['validade_mes'],
                    'validade_ano' => $dadosCartao['validade_ano'],
                    'codigo_seguranca' => $dadosCartao['codigo_seguranca']
                ];
            endif;

            $paymentData = array_merge($paymentData, [
                'cartao' => $cartao ?? [],
                'BANDEIRA' => $bandeira ?? '',
                'numero_parcelas'=> $numeroParcelas ?? 1
            ]);
            break;
        case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_BOLETO_BB:
            $valorBoleto += $valor;
            break;
        case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_EM_LOJA:
            $valorEmLoja += $valor;
            break;
        case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS_CLIENTE_PREFERENCIAL:
            $valorBonusCP += $valor;
            break;
        case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_TRANSFERENCIA:
            $valorTranferencia += $valor;
            break;
    endswitch;

    
    $valorRestanteDividido = round($valorRestanteDividido - $valor, 2);
    $paymentData['valor_pagamento'] = $valor;

    $requestPayment[] = $paymentData;
endforeach;

if ($container->getRequest()->getMethod() == 'POST' && ($valorRestanteDividido == 0 || !$request->request->has('valor_pagamento'))) :
    $data = $requestPayment;

    $carrinho->setSituacaoClearSale(json_encode(['ip' => $_SERVER['REMOTE_ADDR'], 'session_id' => session_id()]));
    $carrinho->save();
   
    /**
     * Cria um registro com a forma de pagamento escolhida
     */

    try {

        # Conclui a compra.
        $con = Propel::getConnection();
        if ($con->inTransaction()) :
            $con->beginTransaction();
        endif;

        $valorRestante = $valorRestanteDividido;

        // Insere o id do pedido junto com os dados do pagamento.
        foreach ($data as $request_pagamento):

            if($request_pagamento['valor_pagamento'] == 0 && $valorRestante > 0) {
                $request_pagamento['valor_pagamento'] = $valorRestante;
            }
            
            $request_pagamento['PEDIDO_ID'] = $carrinho->getId();
            // Cria um novo registro de pagamento com status pendente
            $objFormaPagamento = new PedidoFormaPagamento();
            $objFormaPagamento->setStatus(PedidoFormaPagamentoPeer::STATUS_PENDENTE);
            $objFormaPagamento->setByArray($request_pagamento);
            $pedidoClearSale = new PedidoClearSale($carrinho->getId());
         
            // Caso o pagamento selecionado for boleto,
            // aplica-se o desconto com base na configuração de desconto total ou somente nos itens.
            if ($objFormaPagamento->getFormaPagamento() == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BOLETO ||
                $objFormaPagamento->getFormaPagamento() == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_BOLETO ||
                $objFormaPagamento->getFormaPagamento() == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_ITAUSHOPLINE) :
                $valorTotal = $carrinho->getValorTotal(false);
                $porcentagemDesconto = Config::get('boleto.desconto_pagamento_avista');
                $possuiDesconto = (bool)($porcentagemDesconto > 0);
             
                if ($possuiDesconto) :
                    if (Config::get('boleto_desconto_tipo') == PedidoFormaPagamentoPeer::BOLETO_DESCONTO_ITENS) :
                        $valorTotal = $carrinho->getValorItens() - $carrinho->getValorDesconto(false);
                    endif;

                    # Aplica o desconto
                    $valorDesconto = ($valorTotal * ($porcentagemDesconto / 100));
                    $objFormaPagamento->setValorDesconto($valorDesconto);
                endif;
            endif;

            if ($objFormaPagamento->getFormaPagamento() == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_TRANSFERENCIA) :
                $arquivoComprovante = $request->files->get('comprovante');

                if (empty($arquivoComprovante) || $arquivoComprovante->getError()) :
                    FlashMsg::add('danger', 'Ocorreu um erro no arquivo do comprovante de transferência. Por favor tente anexar novamente. ' . $arquivoComprovante->getErrorMessage());
                    redirect('/checkout/pagamento');
                endif;

                $fileUploader = QPress\Upload\FileUploader\FileUploader::getInstance()
                    ->setAllowedExtensions($objFormaPagamento->allowedExtentions)
                    ->setUploadDir($objFormaPagamento->strPathImg)
                    ->prepare($arquivoComprovante);

                if ($file = $fileUploader->move($objFormaPagamento->getFilenameComprovante())) :
                    $objFormaPagamento->setComprovante($file->getFilename());
                endif;

                if (!empty($fileUploader->getErrors())) :
                    FlashMsg::add('danger', 'Ocorreu um erro no arquivo do comprovante de transferência. Por favor tente anexar novamente. ' . $arquivoComprovante->getErrorMessage());
                    redirect('/checkout/pagamento');
                endif;
            endif;
          
            // A PARTIR DE SETEMBRO DE 2020 OS PAGAMENTO COM BONUS COMEÇARAM A SER TRATADOS COMO FORMA DE PAGAMENTO, ANTES DISSO ESSES PAGAMENTOS ERAM TRATADOS COMO DESCONTO
            // if (in_array($objFormaPagamento->getFormaPagamento(), [
            //     PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS,
            //     PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS_CLIENTE_PREFERENCIAL,
            //     PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BONUS_FRETE])) :
               
            //     $descontoPontos = DescontoPagamentoPontosQuery::create()->filterByPedido($carrinho)->findOneOrCreate();
            //     $valorDescontoAnterior = $descontoPontos->getValorDesconto() ? $descontoPontos->getValorDesconto() : 0;
              
            //     switch ($objFormaPagamento->getFormaPagamento()) :
            //         case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS:
            //             $descontoPontos->setPagamentoBonus(true);
            //             $descontoPontos->setValorDesconto($valorBonusUtilizados + $valorDescontoAnterior);
            //             break;
            //         case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS_CLIENTE_PREFERENCIAL:
            //             $descontoPontos->setPagamentoBonusCP(true);
            //             $descontoPontos->setValorDesconto($valorBonusCP + $valorDescontoAnterior);
            //             break;
            //         case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BONUS_FRETE:
            //             $descontoPontos->setPagamentoBonusFrete(true);
            //             $descontoPontos->setValorDesconto($valorBonusFreteUtilizados + $valorDescontoAnterior);
            //             break;
            //     endswitch;
                   
            //     $descontoPontos->save();

            //     // $carrinho->unregisterDescontoPontos();
            // endif;
            /**
             * Seleciona o gateway desejado para criar a transação de pagamento
             */
            $aditionalParameters = array();
            $cartAdress = true;

            switch ($objFormaPagamento->getFormaPagamento()) :
                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_BOLETO:
                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_DEBITO_ONLINE:
                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_CARTAO_CREDITO:
                    $gatewayName = 'PagSeguroTransparente';
                    $aditionalParameters = $container->getRequest()->request->get('pagseguro', array(), true);
                    break;

                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO:
                    $gatewayName = 'PagSeguro';
                    break;

                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BOLETO:
                    $gatewayName = 'BoletoPHP';
                    break;

                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CARTAO_CREDITO:
                    if (!$carrinho->getEndereco()) :
                        $cartAdress = false;
                        $enderecoCliente = $carrinho->getCliente()->getEnderecoPrincipal();

                        if ($enderecoCliente instanceof Endereco) :
                            $carrinho->setEndereco($enderecoCliente)->setCidadeId($enderecoCliente->getCidadeId())->save();
                            $cartAdress = true;
                        endif;
                    endif;

                    $gatewayName = 'SuperPayRest';
                    break;

                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAYPAL:
                    $gatewayName = 'PayPal';
                    break;

                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_ITAUSHOPLINE:
                    $gatewayName = 'ItauShopline';
                    break;

                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_FATURAMENTO_DIRETO:
                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS:
                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS_CLIENTE_PREFERENCIAL:
                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BONUS_FRETE:
                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_EM_LOJA:
                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_TRANSFERENCIA:
                    $gatewayName = null;
                    break;

                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_CARTAO_DEBITO:
                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_CARTAO_CREDITO:
                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_BOLETO_BRADESCO:
                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_BOLETO_BB:
                    if (!$carrinho->getEndereco()) :
                        $cartAdress = false;
                        $enderecoCliente = $carrinho->getCliente()->getEnderecoPrincipal();

                        if ($enderecoCliente instanceof Endereco) :
                            $carrinho->setEndereco($enderecoCliente)->setCidadeId($enderecoCliente->getCidadeId())->save();
                            $cartAdress = true;
                        endif;
                    endif;

                    $gatewayName = 'CieloApi';
                    break;

                default:
                    $gatewayName = false;
                    break;
            endswitch;
           
            if ($cartAdress == false) :
                FlashMsg::add('danger', 'Pagamento com cartão sem endereço selecionado não é permitido. <br>Favor cadastre um endereço na central do cliente.');
                redirect('/checkout/pagamento');
            endif;

            if (
                $gatewayName === false &&
                !in_array($objFormaPagamento->getFormaPagamento(), [
                    PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS,
                    PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS_CLIENTE_PREFERENCIAL,
                    PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BONUS_FRETE,
                    PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_TRANSFERENCIA
                ])
            ) :
                FlashMsg::add('danger', 'A forma de pagamento selecionada não está disponível no momento.');
                redirect('/checkout/pagamento');
            endif;

            # Adiciona a forma de pagamento ao Carrinho e salva.
            $carrinho->addPedidoFormaPagamento($objFormaPagamento);

            /**
             * Se o sistema é a versão demonstrativa, o sistema não realiza o pagamento.
             */
            if (Config::get('sistema.versao_demo')) : // TODO: investigate this. currently set to null.
                $objFormaPagamento
                    ->setUrlAcesso('javascript:alert(\'Esta versão é apenas uma demonstração para o processo de compra.\')');
                $objFormaPagamento->setStatus(PedidoFormaPagamentoPeer::STATUS_PENDENTE);
                $objFormaPagamento->setObservacao('Pagamento realizado pelo sistema demonstrativo');
                $objFormaPagamento->save();
            else :
                /**
                 * Realiza o pagamento conforme a forma de pagamento
                 */
                # Seleciona o Gateway
                if (!is_null($gatewayName)) :
                    $gateway = $container->getGatewayManager()->get($gatewayName);
                    $gateway->initialize($aditionalParameters);

                    if ($gateway instanceof CieloApi):
                        $gateway->setPostData($request_pagamento);
                    endif;

                    # Executa o pagamento e recebe o retorno do gateway
                    /* @var $objResponse \QPress\Gateway\Response\AbstractResponse */
                    $objResponse = $gateway->purchase($objFormaPagamento);

                    //Verifica se é necessario adicionar o cliente a rede de clientes.
                    if ($carrinho->precisaPatrocinador()) :
                        $carrinho->associaPatrocinadorNaConfirmacaoPagamento();
                    endif;

                    if ($objFormaPagamento->getFormaPagamento()
                        == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_CARTAO_DEBITO
                        && $objResponse->isRedirect()) :
                        # Salva a url de redirecionamento
                        $objFormaPagamento
                            ->setUrlAcesso($objResponse->getUrlAuthentication()) // TODO: method does not exist?
                            ->save();

                        # Finaliza o carrinho de compras.
                        $container->getCarrinhoProvider()->finalizarCarrinho($carrinho);

                        # Redireciona o cliente.
                        $objResponse->redirect();
                        exit; // ------------------
                    endif;

                    # Caso houver algum erro, o sistema cancela a forma de pagamento e recarrega a página de pagamento novamente
                    # para o cliente selecionar outra forma de pagamento.
                    if (!$objResponse->isSuccessful()) :
                        $objFormaPagamento->setStatus(PedidoFormaPagamentoPeer::STATUS_CANCELADO);
                        $objFormaPagamento
                            ->setObservacao($objResponse->getCode() . ' - ' . $objResponse->getMessage());
                        $objFormaPagamento->save();
                        
                        throw new Exception($objResponse->getCode() . ' - ' . $objResponse->getMessage());
                    endif;

                    # Salva a url de acesso e a referencia da transação
                    $objFormaPagamento
                        ->setUrlAcesso($objResponse->getUrl())
                        ->setTransacaoId($objResponse->getTransactionReference())
                        ->save();

                    # Verifica se a forma de pagamento é fora da loja e redireciona o cliente.
                    if ($objResponse->isRedirect()) :
                        # Salva a url de redirecionamento
                        $objFormaPagamento
                            ->setUrlAcesso($objResponse->getUrl())
                            ->save();

                        # Finaliza o carrinho de compras.
                        $container->getCarrinhoProvider()->finalizarCarrinho($carrinho);

                        # Redireciona o cliente.
                        $objResponse->redirect();
                        exit; // ------------------
                    endif;

                    # Caso não necessite redirecionar, verifica o status do pagamento e conclui a compra.
                    switch ($objResponse->getStatus()) :
                        case PedidoFormaPagamentoPeer::STATUS_PENDENTE:
                            $response['message'] = 'Assim que seu pagamento for confirmado daremos sequência no processo de despacho da sua compra.';
                            $objFormaPagamento->setStatus(PedidoFormaPagamentoPeer::STATUS_PENDENTE);

                            break;

                        case PedidoFormaPagamentoPeer::STATUS_NEGADO:
                            $response['message'] = 'Recebemos a informação da operadora que seu pagamento foi negado.';
                            $objFormaPagamento->setStatus(PedidoFormaPagamentoPeer::STATUS_NEGADO);

                            break;

                        case PedidoFormaPagamentoPeer::STATUS_CANCELADO:
                            $response['message'] = 'Falha ao tentar se comunicar com a operadora';
                            $objFormaPagamento->setStatus(PedidoFormaPagamentoPeer::STATUS_CANCELADO);

                            break;

                        case PedidoFormaPagamentoPeer::STATUS_APROVADO:
                            $response['message'] = 'Seu pagamento foi confirmado com sucesso. Nossos atendentes dar&atilde;o sequ&ecirc;ncia no processo de entrega.' .
                                ' A cada mudan&ccedil;a de status voc&ecirc; receber&aacute; um e-mail informando o status do pedido.<br>' .
                                ' Agradecemos a prefer&ecirc;ncia!';

                            $objFormaPagamento->setStatus(PedidoFormaPagamentoPeer::STATUS_APROVADO);

                            break;

                        default:
                            $response['message'] = $objResponse->getStatus();

                            break;
                    endswitch;

                    # Se a forma de pagamento for boleto, calcula a data de vencimento.
                    if ($objFormaPagamento->getFormaPagamento() == PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BOLETO) :
                        $diasParaVencimento = Config::get('boleto.quantidade_dias_vencimento');
                        $objFormaPagamento->setDataVencimento(date('Y-m-d', strtotime('+' . $diasParaVencimento . ' days')));
                    endif;

                    $objFormaPagamento->save();

                    # Caso o status seja cancelado ou negado, redireciona o cliente para tentar um repagamento com outra forma.
                    $isSuccess = !in_array($objFormaPagamento
                        ->getStatus(), array(PedidoFormaPagamentoPeer::STATUS_NEGADO,
                        PedidoFormaPagamentoPeer::STATUS_CANCELADO));

                    if ($isSuccess == false) :
                        FlashMsg::add('danger', $response['message']);
                        redirect('/checkout/pagamento/retry');
                    endif;
                else :
                    if ($carrinho->precisaPatrocinador()) :
                        /* também precisamos desta chamada aqui. Antes não estava associando o patrocinador quando a forma de pagamento era faturamento direto */
                        $carrinho->associaPatrocinadorNaConfirmacaoPagamento();
                    endif;
                endif;
            endif;

            if (in_array($objFormaPagamento->getFormaPagamento(), [
                PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS,
                PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS_CLIENTE_PREFERENCIAL,
                PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BONUS_FRETE])) :
                $objFormaPagamento->setStatus(PedidoFormaPagamentoPeer::STATUS_APROVADO);
                $objFormaPagamento->save();
            endif;
        endforeach;

        // Pedido realizado por cliente preferencial
        if ($carrinho->getCliente()->isClientePreferencial()) :
            $carrinho->setCompraClientePreferencial(true);
        endif;

    } catch (Exception $e) {
        FlashMsg::add('danger', 'Não foi possível finalizar seu pedido. Tente outra forma de pagamento ou entre em contato conosco.');
        FlashMsg::add('danger', $e->getMessage());

        $pagamentosAprovados = PedidoFormaPagamentoQuery::create()
            ->filterByPedidoId($carrinho->getId())
            ->filterByStatus([PedidoFormaPagamentoPeer::STATUS_APROVADO, PedidoFormaPagamentoPeer::STATUS_PENDENTE])
            ->find();
        
        foreach ($pagamentosAprovados as $objFormaPagamento) :
            try {
                if (in_array($objFormaPagamento->getFormaPagamento(), [
                    PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_CARTAO_DEBITO,
                    PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_CARTAO_CREDITO
                ])): 
                    $gateway = $container->getGatewayManager()->get('CieloApi');
                    $gateway->initialize([]);

                    # Executa o pagamento e recebe o retorno do gateway
                    /* @var $objResponse \QPress\Gateway\Response\AbstractResponse */
                    $objResponse = $gateway->void($objFormaPagamento);
                else :
                    $objFormaPagamento->setStatus(PedidoFormaPagamentoPeer::STATUS_CANCELADO);
                    $objFormaPagamento->save();
                endif;
            } catch (Exception $e) {}
        endforeach;

        redirect('/checkout/pagamento/retry');
        exit;
    }

    $pagamentoBonus = PedidoFormaPagamentoQuery::create()
        ->filterByPedidoId($carrinho->getId())
        ->filterByFormaPagamento([
            PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS,
            PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS_CLIENTE_PREFERENCIAL,
            PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BONUS_FRETE
        ])
        ->find();

    foreach ($pagamentoBonus as $objFormaPagamento) :
        //Se for pagamento por bônus/pontos, cria um extrato de utilização e aprova o pagamento do pedido automaticamente.
        if (in_array($objFormaPagamento->getFormaPagamento(), [
            PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS,
            PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS_CLIENTE_PREFERENCIAL,
            PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BONUS_FRETE
        ])) :
            switch ($objFormaPagamento->getFormaPagamento()) :
                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS:
                    $gerenciador = new GerenciadorPontos($con, $logger);

                    $gerenciador->criaExtratoParaPagamentoDePedido(
                        $carrinho,
                        $valorBonusUtilizados
                    );
                    break;
                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS_CLIENTE_PREFERENCIAL:
                    $gerenciador = new GerenciadorPontosClientePreferencial();

                    $gerenciador->criarExtrato(
                        $carrinho,
                        new DateTime(),
                        '-',
                        $valorBonusCP,
                        sprintf('Pagamento do pedido %s.', $carrinho->getId())
                    );
                    break;
                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BONUS_FRETE:
                    $extrato = new Extrato();
                    $extrato->setCliente($carrinho->getCliente());
                    $extrato->setTipo(Extrato::TIPO_BONUS_FRETE);
                    $extrato->setOperacao('-');
                    $extrato->setPontos($valorBonusFreteUtilizados);
                    $extrato->setPedido($carrinho);
                    $extrato->setObservacao("Pagamento parcial com bônus frete do Pedido {$carrinho->getId()}");
                    $extrato->save();

                    break;
            endswitch;
        elseif ($carrinho->getDescontoPontos()) : // Quando a forma de pagamento por bonus era somente desconto
            // O cliente utilizou bônus/pontos para pagar o pedido parcialmente (recebeu como desconto).
            // Neste caso temos que criar um extrato de utilizacao com o valor do desconto recebido.

            if ($carrinho->getDescontoPontos()->getPagamentoBonus()) :
                $gerenciador = new GerenciadorPontos($con, $logger);

                $gerenciador->criaExtratoParaPagamentoDePedido(
                    $carrinho,
                    $carrinho->getDescontoPontos()->getValorDesconto(),
                    true
                );
            elseif ($carrinho->getDescontoPontos()->getPagamentoBonusCP()) :
                $gerenciador = new GerenciadorPontosClientePreferencial();

                $gerenciador->criarExtrato(
                    $carrinho,
                    new DateTime(),
                    '-',
                    (double)$carrinho->getDescontoPontos()->getValorDesconto(),
                    sprintf('Pagamento parcial do pedido %s.', $carrinho->getId())
                );
            else :
                $extrato = new Extrato();
                $extrato->setCliente($carrinho->getCliente());
                $extrato->setTipo(Extrato::TIPO_BONUS_FRETE);
                $extrato->setOperacao('-');
                $extrato->setPontos($carrinho->getDescontoPontos()->getValorDesconto());
                $extrato->setPedido($carrinho);
                $extrato->setObservacao("Pagamento parcial com bônus frete do Pedido {$carrinho->getId()}");
                $extrato->save();
            endif;
        endif;
    endforeach;

    $totalPontosPedido = 0;

    foreach ($carrinho->getPedidoItems() as $item):
        $totalPontosPedido += $item->getValorPontosUnitario() * $item->getQuantidade();
    endforeach;

    $carrinho->setValorPontos($totalPontosPedido);
    $carrinho->save();

    try {
        $container->getCarrinhoProvider()->finalizarCarrinho($carrinho); // TODO: this line fails
    } catch (Exception $e) {
        $logger->error($e->getMessage());
    }

    $con->commit();
    $container->getSession()->remove('CEP_SIMULACAO');

    // Executa o pedido clear sale automaticamente após a confirmação do pagamento (solicitação do cliente)
    try {
        // Se $gateway for null, a forma de pagamento é faturamento direto ou por pontos
        // não deve executar pedido clear sale
        if ($gateway !== null) :
            // $pedidoClearSale->executarPedido();
        endif;
    } catch (\RuntimeException $re) {
        $logger->error($re);
    } catch (\Exception $e) {
        $logger->error($e->getMessage());
    }

    # Redireciona para a tela de confirmação.
    redirect('/checkout/confirmacao/' . md5($carrinho->getId()));
endif;

// Obtém o endereco atual de entrega.
$endereco = $carrinho->getEndereco();

/**
 * Cálculo de frete
 */
$cep = ($carrinho->getEndereco()) ? $carrinho->getEndereco()->getCep() : '';

if (!$carrinho->getEndereco()) :
    $clienteCarrinho = $carrinho->getCliente();
    $endereco = $clienteCarrinho->getEnderecoPrincipal();
endif;

if (Config::get('sistema.versao_demo')) :
    FlashMsg::add('info', 'Os valores não serão cobrados ou debitados em seu cartão, você pode utilizar para efeito de simulação com total segurança!');
endif;

class PedidoClearSale {
    private $pedidoId;

    /**
     * PedidoClearSale constructor.
     * @param $pedidoId
     */
    public function __construct($pedidoId)
    {
        $this->pedidoId = $pedidoId;
    }

    /**
     * @throws Exception
     */
    public function executarPedido()
    {
        try {
            $pedido = PedidoQuery::create()->findPk($this->pedidoId);

            if (!$pedido) :
                FlashMsg::danger('Pedido não encontrado!');
                return;
            endif;

            /* @var $cliente Cliente */
            $cliente = $pedido->getCliente();
            /* @var $endereco Endereco */
            $endereco = $pedido->getEndereco();
            $service = include __DIR__ . '/../../admin/ajax/pedido-clear-service.php';
            $orderCode = (string)$this->pedidoId;
            $address = new Address([
                'street' => $endereco->getLogradouro(),
                'number' => $endereco->getNumero(),
                'additionalInformation' => $endereco->getComplemento(),
                'county' => $endereco->getBairro(),
                'city' => ($endereco && ($cidade = $endereco->getCidade())) ? $cidade->getNome() : '',
                'state' => ($endereco && ($cidade = $endereco->getCidade())
                    && ($estado = $cidade->getEstado())) ? ($estado->getSigla()) : '',
                'zipcode' => only_digits($endereco->getCep()),
            ]);
            $shipping = $billing = [
                'clientID' => (string)$cliente->getId(),
                'type' => $cliente->isPessoaJuridica() ? Billing::PERSON_LEGAL : Billing::PERSON_NATURAL,
                'primaryDocument' => $pedido->getCobrancaDocumentoClearSale(),
                'name' => $pedido->getNomeClienteClearSale(),
                'birthDate' => $cliente->getDataNascimento('Y-m-d'),
                'email' => $cliente->getEmail(),
                'gender' => 'M',
                'address' => $address,
                'phones' => [
                    new Phone([
                        'type' => Phone::BILLING,
                        'ddi' => 55,
                        'ddd' => only_digits($cliente->getTelefoneDDD()),
                        'number' => only_digits($cliente->getTelefoneSemDDD())
                    ])
                ]
            ];
            $delivery = $pedido->getFrete();
            $deliveryTime = $pedido->getFretePrazo();
            $deliveryPrice = $pedido->getValorEntrega();

            if (!empty($delivery) && 'transportadora' == $delivery) :
                $shipping['deliveryType'] = \ClearSale\Delivery::NORMAL;
            elseif (!empty($delivery) && stripos($delivery, 'correios') !== false) :
                $shipping['deliveryType'] = \ClearSale\Delivery::MAIL;
            endif;

            if (!empty($deliveryTime)) :
                $shipping['deliveryTime'] = (string)$deliveryTime;
            endif;

            if (!empty($deliveryPrice)) :
                $shipping['price'] = number_format($deliveryPrice, 2, '.', '');
            endif;

            switch ($pedido->getPedidoFormaPagamento()->getFormaPagamento()) :
                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BOLETO:
                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_BOLETO_BB:
                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_BOLETO:
                    $paymentType = Payment::TYPE_BANK_SLEEP;
                    $card = new Card();
                    break;
                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CARTAO_CREDITO:
                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO:
                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BCASH:
                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_DEBITO_ONLINE:
                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS:
                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS_CLIENTE_PREFERENCIAL:
                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BONUS_FRETE:
                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_CARTAO_DEBITO:
                    throw new \RuntimeException('Tipo de Pagamento nao suportado.');
                    break;
                case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_CARTAO_CREDITO:
                    $paymentType = Payment::TYPE_CREDIT_CARD;

                    switch ($pedido->getPedidoFormaPagamento()->getBandeira()) :
                        case 'MASTER':
                            $brand = Card::MASTERCARD;
                            break;
                        case 'DINERS':
                            $brand = Card::DINERS;
                            break;
                        case 'HIPERCARD':
                            $brand = Card::HIPERCARD;
                            break;
                        case 'ELO':
                            $brand = Card::ELO_CARD;
                            break;
                        case 'VISA':
                        default:
                            $brand = Card::VISA;
                            break;
                    endswitch;

                    /**
                     * @var $cieloCard CartaoCieloDados
                     */
                    $allCards = $pedido->getCartaoCieloDadoss() ?? [];
                    $cieloCard = end($allCards);
                    $card = new Card([
                        'bin' => substr(only_digits($cieloCard->getNumero()), 0, 6),
                        'end' => substr(only_digits($cieloCard->getNumero()), -4, 4),
                        'type' => $brand,
                        'validityDate' => $cieloCard->getValidade(),
                        'ownerName' => $cieloCard->getNome(),
                        'document' => only_digits($cieloCard->getCpf()),
                    ]);

                    break;
                default:
                    break;
            endswitch;
            $payment = new Payment([
                'sequential' => 1,
                'date' => $pedido->getCreatedAt('Y-m-d H:i:s'),
                'value' => number_format($pedido->getValorTotal(false), 2, '.', ''),
                'type' => $paymentType,
                'installments' => ($formaPag = $pedido->getPedidoFormaPagamento())
                    ? (int)$formaPag->getNumeroParcelas() : 1,
                'currency' => Currency::BRL,
                'card' => $card,
                'address' => $address,
            ]);
            $items = [];
            $vItems = 0;

            foreach ($pedido->getPedidoItems() as $item) :
                /**
                 * @var $item PedidoItem
                 * @var $produto Produto
                 * @var $categoria Categoria
                 */
                $produto = $item->getProdutoVariacao()->getProduto();
                $categoria = ($produto) ? $produto->getCategoria() : null;

                $items[] = new Item([
                    'code' => (string)$produto->getId(),
                    'name' => $produto->getNome(),
                    'value' => number_format($item->getValorUnitario(), 2, '.', ''),
                    'amount' => $item->getQuantidade(),
                    'isGift' => false,
                    'categoryID' => $categoria ? $categoria->getId() : null,
                    'categoryName' => $categoria ? $categoria->getNome() : null,
                ]);
                $vItems += $item->getValorUnitario() * $item->getQuantidade();
            endforeach;

            $situation = json_decode($pedido->getSituacaoClearSale(), true);

            if (!$situation) :
                $situation = [
                    'ip' => null,
                    'session_id' => null
                ];
            endif;

            $order = new Order([
                'code' => $orderCode,
                'sessionID' => $situation['session_id'] ?? md5(uniqid('session')),
                'ip' => $situation['ip'] ?? null,
                'b2bB2c' => 'b2c',
                'isGift' => false,
                'origin' => 'Site',
                'country' => 'Brasil',
                'nationality' => 'Brasileiro',
                'date' => $pedido->getCreatedAt('Y-m-d H:i:s'),
                'email' => $cliente->getEmail(),
                'totalValue' => number_format($pedido->getValorTotal(false), 2, '.', ''),
                'itemValue' => number_format($vItems, 2, '.', ''),
                'numberOfInstallments' => ($formaPag = $pedido->getPedidoFormaPagamento())
                    ? (int)$formaPag->getNumeroParcelas() : 1,
                'purchaseInformation' => new PurchaseInformation([
                    'lastDateInsertedAddress' => $endereco->getUpdatedAt('Y-m-d H:i:s'),
                    'purchaseLogged' => true,
                    'email' => $cliente->getEmail(),
                    'login' => $cliente->getEmail(),
                ]),
                'billing' => new Billing($billing),
                'shipping' => new Shipping($shipping),
                'payments' => [$payment],
                'items' => $items
            ]);

            $send = $service->send($order);
            $pedido->setIntegrouClearSale(true);
            $pedido->setSituacaoClearSale('NVO');
            $pedido->save();
        } catch (\ClearSale\Service\ServiceResponseException $sre) {
            throw $sre;
        } catch (\PDOException $pe) {
            throw $pe;
        } catch (\Exception $e) {
            throw $e;
//            FlashMsg::danger('Não foi possível enviar o pedido a clear sale. Por favor, tente novamente! ' . $e->getMessage());
        }
    }
}