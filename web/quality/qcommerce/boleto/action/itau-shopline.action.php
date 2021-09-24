<?php

### Configuração do header
#header("Access-Control-Allow-Origin: https://zorah.com.br, http://zorah.com.br");
header('Content-type: application/json');

### Função de redirecionamento com erro no caso do boleto nao ter sido encontrado.
function redirect_with_error()
{
    FlashMsg::add('warning', 'Boleto banc&aacute;rio n&atilde;o encontrado');
    header('Location: ' . get_url_site() . '/minha-conta/pedidos');
    exit;
}

### Verificar se algum boleto foi
$boletoId       = $router->getArgument(0);
$itauEnabled    = (bool) \Config::get('itau_shopline.enabled');

if (empty($boletoId) || false === $itauEnabled) {
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

### Localização do pedido
$pedido             = $pedidoFormaPagamento->getPedido();
$cliente            = $pedido->getCliente();

$dataVencimento = new \DateTime($pedido->getPedidoFormaPagamento()->getDataVencimento('Y-m-d'));

### Dados específico geração dos dados criptografados
$codigoEmpresa      = \Config::get('itau_shopline.codigo_empresa');
$token              = \Config::get('itau_shopline.token_empresa');

### Dados gerais
$numeroPedido       = $pedido->getId();
$valor              = number_format($pedido->getValorTotal(), 2, ',', '');
$observacao         = "Pagável em qualquer banco até o vencimento.";
$nomeSacado         = $cliente->getNomeCompleto();
$codigoInscricao    = ($cliente->isPessoaFisica()) ? '01' : '02';                   // 01 - CPF | 02 - CNPJ
$numeroInscricao    = preg_replace("/[^0-9]/", "", $cliente->getCodigoFederal());   // CPF ou CNPJ (somente dígitos)
$enderecoSacado     = $pedido->getEndereco()->sprintf('%logradouro');
$bairroSacado       = $pedido->getEndereco()->sprintf('%bairro');
$cepSacado          = preg_replace("/[^0-9]/", "", $pedido->getEndereco()->sprintf('%cep'));
$cidadeSacado       = $pedido->getEndereco()->sprintf('%cidade');
$estadoSacado       = $pedido->getEndereco()->sprintf('%uf');
$dataVencimento     = $dataVencimento->format('dmY');
$urlRetorna         = "";
$obsAd1             = "Sr. caixa, não aceitar o pagamento após o vencimento.";
$obsAd2             = "";
$obsAd3             = "";

### Dados encriptados
$itau               = new Itaucripto();
$cryptedData        = $itau->geraDados($codigoEmpresa, $numeroPedido, $valor, $observacao, $token, $nomeSacado, $codigoInscricao, $numeroInscricao, $enderecoSacado, $bairroSacado, $cepSacado, $cidadeSacado, $estadoSacado, $dataVencimento, $urlRetorna, $obsAd1, $obsAd2, $obsAd3);

### Retorno de dados
echo json_encode(array(
    'destination'   => (\Config::get('itau_shopline.environment') === 'production') ? 'https://shopline.itau.com.br/shopline/shopline.aspx' : 'https://shopline.itau.com.br/shopline/emissao_teste.asp',
    'token'         => $cryptedData,
));
