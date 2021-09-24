<?php

// Redirecionar tela com erro
function redirect_with_error()
{
    FlashMsg::add('warning', 'Boleto banc&aacute;rio n&atilde;o encontrado');
    header('Location: ' . get_url_site() . '/minha-conta/pedidos');
    exit;
}

### Verificar se algum boleto foi
$boletoId = $router->getArgument(0);
if (empty($boletoId) || null == ClientePeer::getClienteLogado()) {
    redirect_with_error();
}

### Localizar a forma de pagamento
$pedidoFormaPagamento = PedidoFormaPagamentoQuery::create()
    ->joinWith(PedidoPeer::OM_CLASS)
    ->filterByHashBoleto($boletoId)
    ->findOne()
;

### Verificar se a forma de pagamento do pedido foi localizada
if (!$pedidoFormaPagamento instanceof PedidoFormaPagamento) {
    redirect_with_error();
}

### Autoload das classes para utilização do boleto bancário
use OpenBoleto\Agente;
use QPress\Gateway\Services\BoletoPHP\BoletoPHPInterface;

$pedido         = $pedidoFormaPagamento->getPedido();

$dataVencimento = new \DateTime($pedido->getPedidoFormaPagamento()->getDataVencimento('Y-m-d'));
$numeroPedido   = $pedido->getId();

### Dados do agente sacado
$sacado         = new Agente(
    $pedido->getCliente()->getNomeCompleto(),
    $pedido->getCliente()->getCodigoFederal(),
    $pedido->getEndereco() ? $pedido->getEndereco()->sprintf('%logradouro, %numero. %complemento') : '',
    $pedido->getEndereco() ? $pedido->getEndereco()->sprintf('%cep') : '',
    $pedido->getEndereco() ? $pedido->getEndereco()->sprintf('%cidade') : '',
    $pedido->getEndereco() ? $pedido->getEndereco()->sprintf('%uf') : ''
);

### Dados do agente cedente
$cedente        = new Agente(
    Config::get('empresa_nome_fantasia'),
    Config::get('empresa_cnpj'),
    Config::get('empresa_endereco_completo'),
    null,
    null,
    null
);

$classNamespace = 'OpenBoleto\\Banco\\';
$className      = null;

### Verificar qual o banco, para buscar a classe correta
$banco = \Config::get('boletophp.banco');
switch ($banco) {
    case BoletoPHPInterface::BANCO_DO_BRASIL:
        $className = 'BancoDoBrasil';
        break;

    case BoletoPHPInterface::BRADESCO:
        $className = 'Bradesco';
        break;

    case BoletoPHPInterface::CEF:
        $className = 'Caixa';
        break;

    case BoletoPHPInterface::ITAU:
        $className = 'Itau';
        break;

    case BoletoPHPInterface::SANTANDER:
        $className = 'Santander';
        break;

    case BoletoPHPInterface::CECRED:
        $className = 'Cecred';
        break;
}

### Caminho da classe por completo
$className = $classNamespace . $className;

### Dados para envio do boleto
$boletoData = array(
    // Parâmetros obrigatórios
    'dataVencimento'    => $dataVencimento,
    'valor'             => $pedido->getValorTotal(),
    'sequencial'        => $pedido->getId(),
    'sacado'            => $sacado,
    'cedente'           => $cedente,
    'agencia'           => \Config::get('boletophp.agencia'),
    'carteira'          => \Config::get('boletophp.carteira'),
    'conta'             => \Config::get('boletophp.conta'),
    'convenio'          => \Config::get('boletophp.convenio'),
    'especieDoc'        => 'DM'
);

### Caso do CECRED / Não registrado por padrão no OpenBoleto
if ($banco == BoletoPHPInterface::CECRED) {
    $boletoData['agenciaDv']    = \Config::get('boletophp.agencia_dv');
    $boletoData['contaDv']      = \Config::get('boletophp.conta_dv');
    $boletoData['aceite']       = 'N';
    $boletoData['resourcePath'] = APP_ROOT . '/../src/OpenBoleto/resources';
}

### Inicialização do objeto Banco específico
$boleto = new $className($boletoData);

### Exibição do boleto
echo $boleto->getOutput();
