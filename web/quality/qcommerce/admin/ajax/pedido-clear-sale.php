<?php
include __DIR__ . '/../includes/config.inc.php';
include __DIR__ . '/../includes/security.inc.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json; charset=utf-8');

$orderId = filter_input(INPUT_GET, 'id');

try {
    $pedido = PedidoQuery::create()->findPk($orderId);
    if (!$pedido) {
        json_encode([
            'success' => false,
            'order' => $orderId,
            'message' => 'Pedido não encontrado!',
        ]);
        return;
    }
    /* @var $cliente Cliente */
    $cliente = $pedido->getCliente();

    /* @var $endereco Endereco */
    $endereco = $pedido->getEndereco();

    $service = include __DIR__ . '/pedido-clear-service.php';

    $orderCode = (string) $orderId;
    $address = new \ClearSale\Address([
        'street' => $endereco->getLogradouro(),
        'number' => $endereco->getNumero(),
        'additionalInformation' => $endereco->getComplemento(),
        'county' => $endereco->getBairro(),
        'city' => ($endereco && ($cidade = $endereco->getCidade())) ? $cidade->getNome() : '',
        'state' => ($endereco && ($cidade = $endereco->getCidade()) && ($estado = $cidade->getEstado())) ? ($estado->getSigla()) : '',
        'zipcode' => only_digits($endereco->getCep()),
    ]);
    $shipping = $billing = [
        'clientID' => (string) $cliente->getId(),
        'type' => $cliente->isPessoaJuridica() ? \ClearSale\Billing::PERSON_LEGAL : \ClearSale\Billing::PERSON_NATURAL,
        'primaryDocument' => $pedido->getCobrancaDocumentoClearSale(),
        'name' => $pedido->getNomeClienteClearSale(),
        'birthDate' => $cliente->getDataNascimento('Y-m-d'),
        'email' => $cliente->getEmail(),
        'gender' => 'M',
        'address' => $address,
        'phones' => [
            new \ClearSale\Phone([
                'type' => \ClearSale\Phone::BILLING,
                'ddi' => 55,
                'ddd' => only_digits($cliente->getTelefoneDDD()),
                'number' => only_digits($cliente->getTelefoneSemDDD())
            ])
        ]
    ];
    $delivery = $pedido->getFrete();
    $deliveryTime = $pedido->getFretePrazo();
    $deliveryPrice = $pedido->getValorEntrega();
    if (!empty($delivery) && 'transportadora' == $delivery) {
        $shipping['deliveryType'] = \ClearSale\Delivery::NORMAL;
    } elseif (!empty($delivery) && stripos($delivery, 'correios') !== false) {
        $shipping['deliveryType'] = \ClearSale\Delivery::MAIL;
    }
    if (!empty($deliveryTime)) {
        $shipping['deliveryTime'] = (string) $deliveryTime;
    }
    if (!empty($deliveryPrice)) {
        $shipping['price'] = number_format($deliveryPrice, 2, '.', '');
    }
    switch ($pedido->getPedidoFormaPagamento()->getFormaPagamento()) {
        case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BOLETO:
        case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_BOLETO_BB:
        case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_BOLETO:
            $paymentType = \ClearSale\Payment::TYPE_BANK_SLEEP;
            $card = null;
            break;
        case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CARTAO_CREDITO:
        case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO:
        case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_BCASH:
        case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CARTAO_CREDITO:
        case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PAGSEGURO_DEBITO_ONLINE:
        case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_PONTOS:
        case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_CARTAO_DEBITO:
            throw new \RuntimeException('Tipo de Pagamento nao suportado.');
            break;
        case PedidoFormaPagamentoPeer::FORMA_PAGAMENTO_CIELO_CARTAO_CREDITO:
            $paymentType = \ClearSale\Payment::TYPE_CREDIT_CARD;
            switch ($pedido->getPedidoFormaPagamento()->getBandeira()) {
                case 'MASTER':
                    $brand = \ClearSale\Card::MASTERCARD;
                    break;
                case 'VISA':
                    $brand = \ClearSale\Card::VISA;
                    break;
                case 'DINERS':
                    $brand = \ClearSale\Card::DINERS;
                    break;
                case 'HIPERCARD':
                    $brand = \ClearSale\Card::HIPERCARD;
                    break;
                case 'ELO':
                    $brand = \ClearSale\Card::ELO_CARD;
                    break;
                default:
                    $brand = \ClearSale\Card::VISA;
                    break;
            }
            /**
             * @var $cieloCard CartaoCieloDados
             */
            $allCards = $pedido->getCartaoCieloDadoss() ?? [];
            $cieloCard = end($allCards);
            $card = new \ClearSale\Card([
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
    }
    $payment = new \ClearSale\Payment([
        'sequential' => 1,
        'type' => $paymentType,
        'date' => $pedido->getCreatedAt('Y-m-d H:i:s'),
        'value' => number_format($pedido->getValorTotal(false), 2, '.', ''),
        'installments' => ($formaPag = $pedido->getPedidoFormaPagamento()) ? (int) $formaPag->getNumeroParcelas() : 1,
        'currency' => \ClearSale\Currency::BRL,
        'card' => $card,
        'address' => $address,
    ]);
    $items = [];
    $vItems = 0;
    foreach ($pedido->getPedidoItems() as $item) {
        /**
         * @var $item PedidoItem
         * @var $produto Produto
         * @var $categoria Categoria
         */
        $produto = $item->getProdutoVariacao()->getProduto();
        $categoria = ($produto) ? $produto->getCategoria() : null;
        $items[] = new \ClearSale\Item([
            'code' => (string) $produto->getId(),
            'name' => $produto->getNome(),
            'value' => number_format($item->getValorUnitario(), 2, '.', ''),
            'amount' => $item->getQuantidade(),
            'isGift' => false,
            'categoryID' => $categoria ? $categoria->getId() : null,
            'categoryName' => $categoria ? $categoria->getNome() : null,
        ]);
        $vItems += $item->getValorUnitario() * $item->getQuantidade();
    }
    $situation = json_decode($pedido->getSituacaoClearSale(), true);
    if (!$situation) {
        $situation = [
            'ip' => null,
            'session_id' => null
        ];
    }
    $order = new \ClearSale\Order([
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
        'numberOfInstallments' => ($formaPag = $pedido->getPedidoFormaPagamento()) ? (int) $formaPag->getNumeroParcelas() : 1,
        'purchaseInformation' => new \ClearSale\PurchaseInformation([
            'lastDateInsertedAddress' => $endereco->getUpdatedAt('Y-m-d H:i:s'),
            'purchaseLogged' => true,
            'email' => $cliente->getEmail(),
            'login' => $cliente->getEmail(),
        ]),
        'billing' => new \ClearSale\Billing($billing),
        'shipping' => new \ClearSale\Shipping($shipping),
        'payments' => [$payment],
        'items' => $items
    ]);
    $send = $service->send($order);
    $pedido->setIntegrouClearSale(true);
    $pedido->setSituacaoClearSale('NVO');
    $pedido->save();
    echo json_encode([
        'success' => true,
        'order' => $orderId,
        'clearSale' => $send,
        'message' => 'Pedido enviado com sucesso!',
    ]);
} catch (\ClearSale\Service\ServiceResponseException $e) {
    echo json_encode([
        'success' => false,
        'order' => $orderId,
        'message' => 'Erro ao receber pedido na clear sale: ' . $e->getMessage(),
    ]);
    return;
} catch (\Exception $e) {
    echo json_encode([
        'success' => false,
        'order' => $orderId,
        'message' => 'Erro ao enviar o pedido: ' . $e->getMessage(),
    ]);
    return;
}
