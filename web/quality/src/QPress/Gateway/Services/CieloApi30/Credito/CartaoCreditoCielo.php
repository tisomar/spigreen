<?php
/**
 * Created by PhpStorm.
 * User: Giovan
 * Date: 18/11/2018
 * Time: 11:14
 */

namespace QPress\Gateway\Services\CieloApi30\Credito;

use CartaoCieloDadosQuery;
use Cielo\API30\Ecommerce\Address;
use Cielo\API30\Merchant;

use Cielo\API30\Ecommerce\Environment;
use Cielo\API30\Ecommerce\Sale;
use Cielo\API30\Ecommerce\CieloEcommerce;
use Cielo\API30\Ecommerce\Payment;

use QPress\Gateway\Services\CieloApi30\Response\Response;

use Cielo\API30\Ecommerce\Request\CieloRequestException;

class CartaoCreditoCielo
{
    const TAX_AMOUNT_SERVICE = 0;

    const INICIO_PAGAMENTO      = 'inicio_pagamento';
    const CIELO_AGUARDANDO      = 'aguardando_pagamento';
    const CIELO_PAGO            = 'paga';
    const CIELO_NAO_CAPTURADO   = 'paga_nao_capturada';
    const CIELO_NEGADO          = 'negada';
    const CIELO_CANCELADO       = 'cancelada';

    const COUNTRY_SIGLAS = array(
        "AF" => "AFG",
        "ZA" => "ZAF",
        "AL" => "ALB",
        "DE" => "DEU",
        "AD" => "AND",
        "AO" => "AGO",
        "AI" => "AIA",
        "AQ" => "ATA",
        "AG" => "ATG",
        "AN" => "ANT",
        "SA" => "SAU",
        "DZ" => "DZA",
        "AR" => "ARG",
        "AM" => "ARM",
        "AW" => "ABW",
        "AU" => "AUS",
        "AT" => "AUT",
        "AZ" => "AZE",
        "BS" => "BHS",
        "BH" => "BHR",
        "BD" => "BGD",
        "BB" => "BRB",
        "BY" => "BLR",
        "BE" => "BEL",
        "BZ" => "BLZ",
        "BJ" => "BEN",
        "BM" => "BMU",
        "BO" => "BOL",
        "BA" => "BIH",
        "BW" => "BWA",
        "BR" => "BRA",
        "BN" => "BRN",
        "BG" => "BGR",
        "BF" => "BFA",
        "BI" => "BDI",
        "BT" => "BTN",
        "CV" => "CPV",
        "CM" => "CMR",
        "KH" => "KHM",
        "CA" => "CAN",
        "KZ" => "KAZ",
        "TD" => "TCD",
        "CL" => "CHL",
        "CN" => "CHN",
        "CY" => "CYP",
        "SG" => "SGP",
        "CO" => "COL",
        "CG" => "COG",
        "KP" => "PRK",
        "KR" => "KOR",
        "CI" => "CIV",
        "CR" => "CRI",
        "HR" => "HRV",
        "CU" => "CUB",
        "DK" => "DNK",
        "DJ" => "DJI",
        "DM" => "DMA",
        "EG" => "EGY",
        "SV" => "SLV",
        "AE" => "ARE",
        "EC" => "ECU",
        "ER" => "ERI",
        "SK" => "SVK",
        "SI" => "SVN",
        "ES" => "ESP",
        "US" => "USA",
        "EE" => "EST",
        "ET" => "ETH",
        "FJ" => "FJI",
        "PH" => "PHL",
        "FI" => "FIN",
        "FR" => "FRA",
        "GA" => "GAB",
        "GM" => "GMB",
        "GH" => "GHA",
        "GE" => "GEO",
        "GI" => "GIB",
        "GB" => "GBR",
        "GD" => "GRD",
        "GR" => "GRC",
        "GL" => "GRL",
        "GP" => "GLP",
        "GU" => "GUM",
        "GT" => "GTM",
        "G" => "GGY",
        "GY" => "GUY",
        "GF" => "GUF",
        "GN" => "GIN",
        "GQ" => "GNQ",
        "GW" => "GNB",
        "HT" => "HTI",
        "NL" => "NLD",
        "HN" => "HND",
        "HK" => "HKG",
        "HU" => "HUN",
        "YE" => "YEM",
        "BV" => "BVT",
        "IM" => "IMN",
        "CX" => "CXR",
        "PN" => "PCN",
        "RE" => "REU",
        "AX" => "ALA",
        "KY" => "CYM",
        "CC" => "CCK",
        "KM" => "COM",
        "CK" => "COK",
        "FO" => "FRO",
        "FK" => "FLK",
        "GS" => "SGS",
        "HM" => "HMD",
        "MP" => "MNP",
        "MH" => "MHL",
        "UM" => "UMI",
        "NF" => "NFK",
        "SC" => "SYC",
        "SB" => "SLB",
        "SJ" => "SJM",
        "TK" => "TKL",
        "TC" => "TCA",
        "VI" => "VIR",
        "VG" => "VGB",
        "WF" => "WLF",
        "IN" => "IND",
        "ID" => "IDN",
        "IR" => "IRN",
        "IQ" => "IRQ",
        "IE" => "IRL",
        "IS" => "ISL",
        "IL" => "ISR",
        "IT" => "ITA",
        "JM" => "JAM",
        "JP" => "JPN",
        "JE" => "JEY",
        "JO" => "JOR",
        "KE" => "KEN",
        "KI" => "KIR",
        "KW" => "KWT",
        "LA" => "LAO",
        "LV" => "LVA",
        "LS" => "LSO",
        "LB" => "LBN",
        "LR" => "LBR",
        "LY" => "LBY",
        "LI" => "LIE",
        "LT" => "LTU",
        "LU" => "LUX",
        "MO" => "MAC",
        "MK" => "MKD",
        "MG" => "MDG",
        "MY" => "MYS",
        "MW" => "MWI",
        "MV" => "MDV",
        "ML" => "MLI",
        "MT" => "MLT",
        "MA" => "MAR",
        "MQ" => "MTQ",
        "MU" => "MUS",
        "MR" => "MRT",
        "YT" => "MYT",
        "MX" => "MEX",
        "FM" => "FSM",
        "MZ" => "MOZ",
        "MD" => "MDA",
        "MC" => "MCO",
        "MN" => "MNG",
        "ME" => "MNE",
        "MS" => "MSR",
        "MM" => "MMR",
        "NA" => "NAM",
        "NR" => "NRU",
        "NP" => "NPL",
        "NI" => "NIC",
        "NE" => "NER",
        "NG" => "NGA",
        "NU" => "NIU",
        "NO" => "NOR",
        "NC" => "NCL",
        "NZ" => "NZL",
        "OM" => "OMN",
        "PW" => "PLW",
        "PA" => "PAN",
        "PG" => "PNG",
        "PK" => "PAK",
        "PY" => "PRY",
        "PE" => "PER",
        "PF" => "PYF",
        "PL" => "POL",
        "PR" => "PRI",
        "PT" => "PRT",
        "QA" => "QAT",
        "KG" => "KGZ",
        "CF" => "CAF",
        "CD" => "COD",
        "DO" => "DOM",
        "CZ" => "CZE",
        "RO" => "ROM",
        "RW" => "RWA",
        "RU" => "RUS",
        "EH" => "ESH",
        "VC" => "VCT",
        "AS" => "ASM",
        "WS" => "WSM",
        "SM" => "SMR",
        "SH" => "SHN",
        "LC" => "LCA",
        "BL" => "BLM",
        "KN" => "KNA",
        "MF" => "MAF",
        "ST" => "STP",
        "SN" => "SEN",
        "SL" => "SLE",
        "RS" => "SRB",
        "SY" => "SYR",
        "SO" => "SOM",
        "LK" => "LKA",
        "PM" => "SPM",
        "SZ" => "SWZ",
        "SD" => "SDN",
        "SE" => "SWE",
        "CH" => "CHE",
        "SR" => "SUR",
        "TJ" => "TJK",
        "TH" => "THA",
        "TW" => "TWN",
        "TZ" => "TZA",
        "IO" => "IOT",
        "TF" => "ATF",
        "PS" => "PSE",
        "TP" => "TMP",
        "TG" => "TGO",
        "TO" => "TON",
        "TT" => "TTO",
        "TN" => "TUN",
        "TM" => "TKM",
        "TR" => "TUR",
        "TV" => "TUV",
        "UA" => "UKR",
        "UG" => "UGA",
        "UY" => "URY",
        "UZ" => "UZB",
        "VU" => "VUT",
        "VA" => "VAT",
        "VE" => "VEN",
        "VN" => "VNM",
        "ZM" => "ZMB",
        "ZW" => "ZWE",
    );

    const responseCodeAPI = array(
        "0" => "aguardando_pagamento",
        "1" => "paga",
        "2" => "paga",
        "3" => "negada",
        "10" => "negada",
        "11" => "negada",
        "12" => "aguardando_pagamento",
        "13" => "negada",
        "20" => "paga_nao_capturada",
        "99" => "aguardando_pagamento",
    );

    const responseMessageCodeAPI = array(
        "0" => "Aguardando atualização de status",
        "1" => "Pagamento apto a ser capturado ou definido como pago",
        "2" => "Pagamento confirmado e finalizado",
        "3" => "Pagamento negado por Autorizador",
        "10" => "Pagamento cancelado",
        "11" => "Pagamento cancelado após 23:59 do dia de autorização",
        "12" => "Aguardando Status de instituição financeira",
        "13" => "Pagamento cancelado por falha no processamento ou por ação do AF",
        "20" => "Recorrência agendada",
        "99" => "Pendente de autenticação"
    );

    /**
     * @param \BasePedido $carrinho
     * @param \CartaoCieloDados|null $cartaoCieloDados
     * @return Response
     * @throws \PropelException
     */

    public function pagar(\BasePedidoFormaPagamento $pagamento, array $ambiente, \CartaoCieloDados $cartaoCieloDados = null)
    {
        $carrinho = $pagamento->getPedido();
        /** @var \CartaoCieloDados $cartaoCieloDados */
        /** @var \BaseCliente $cliente */
        /** @var \Pedido $objPedido */
        /** @var $enderecoPrincipalCliente \Endereco */
        /** @var \PedidoFormaPagamento $pagamento */

        $cliente = $carrinho->getCliente();

        // Configure o ambiente
        $environment = $ambiente['ambiente'] == 'sandbox' ? Environment::sandbox() : Environment::production();

        // Configure seu merchant
        $merchant = new Merchant($ambiente['merchant_id'], $ambiente['merchant_key']);

        // Crie uma instância de Sale informando o ID do pagamento
        $sale = new Sale($carrinho->getId());

        // Crie uma instância de Customer informando o nome do cliente
        $enderecoPrincipalCliente = $carrinho->getEndereco();
        $customer = $sale->customer($cartaoCieloDados->getNome());
        $customer->setEmail($cliente->getEmail());
        $customer->setBirthDate($cliente->getDataNascimento("Y-m-d"));
        $address = new Address();
        $address->setStreet($enderecoPrincipalCliente->getLogradouro());
        $address->setNumber($enderecoPrincipalCliente->getNumero());
        $address->setComplement($enderecoPrincipalCliente->getComplemento());
        $address->setZipCode($enderecoPrincipalCliente->getCep(true));
        $address->setCity($enderecoPrincipalCliente->getCidade() ? $enderecoPrincipalCliente->getCidade()->getNome() : "");
        $address->setState($enderecoPrincipalCliente->getCidade() && $enderecoPrincipalCliente->getCidade()->getEstado() ? $enderecoPrincipalCliente->getCidade()->getEstado()->getSigla() : "");
        $address->setCountry('BRA');
        $address->setDistrict($enderecoPrincipalCliente->getBairro());
        $customer->setAddress($address);
        $customer->setDeliveryAddress($address);

        // Crie uma instância de Payment informando o valor do pagamento
        $payment = $sale->payment(($pagamento->getValorPagamento() ?? $carrinho->getValorTotal()) * 100);

        // Seta a captura automatica como true
        $payment->setCapture(true);
        $sale->setPayment($payment);

        // Crie uma instância de Credit Card utilizando os dados de teste
        // esses dados estão disponíveis no manual de integração
        $payment->setType(Payment::PAYMENTTYPE_CREDITCARD)
            //->setInterest(0)
            ->setInstallments($pagamento->getNumeroParcelas())
            ->setServiceTaxAmount(self::TAX_AMOUNT_SERVICE)
            ->creditCard($cartaoCieloDados->getCodigo(), $pagamento->getBandeiraCielo())
            ->setExpirationDate($cartaoCieloDados->getValidade())
            ->setCardNumber(str_replace("-","",str_replace(" ","",$cartaoCieloDados->getNumero())))
            ->setHolder($cartaoCieloDados->getNome());

        // Crie o pagamento na Cielo
        try {
            // Configure o SDK com seu merchant e o ambiente apropriado para criar a venda
            $sale = (new CieloEcommerce($merchant, $environment))->createSale($sale);

            //if(null !== $sale->getPayment()){
            $tid = (null !== $sale->getPayment()) ? $sale->getPayment()->getTid() : null;

            $paymentId = $sale->getPayment()->getPaymentId();

            $cartaoCieloDados->setPedidoId($carrinho->getId());
            $cartaoCieloDados->setCieloPaymentId($paymentId);
            $cartaoCieloDados->save();

            $pagamento->setCieloPaymentId($paymentId);
            $pagamento->save();

            if (!in_array($sale->getPayment()->getStatus(), [1, 2])) {
                $tid = (null !== $sale->getPayment()) ? $sale->getPayment()->getTid() : null;
                $codigoAutorizacao = $sale->getPayment()->getAuthorizationCode() ?? null;
              
                $pagamento->setTransacaoId($tid);
                $pagamento->setCodAutorizacao($codigoAutorizacao);
                $pagamento->setStatus('NEGADO');
                $pagamento->save();

                $cartaoCieloDados->setStatus('negada');
                $cartaoCieloDados->save();

                $replay = array(
                    "pedido_id" => $carrinho->getId(),
                    'id' => $paymentId,
                    'tid' => $tid,
                    'codigo_altorizacao' => $codigoAutorizacao,
                    "status" => 'negada' ,
                    "erro" => self::responseCodeAPI[$sale->getPayment()->getStatus()] != "paga" ? $sale->getPayment()->getStatus() : false,
                    'code' => $sale->getPayment()->getReturnCode(),
                    'message' => $sale->getPayment()->getReturnMessage(),
                    'url_acesso'   => '',
                );
                $data = json_decode(json_encode($replay));
                $obj = new Response($data);
                return $obj;
            } else {
                $tid = (null !== $sale->getPayment()) ? $sale->getPayment()->getTid() : null;
                $codigoAutorizacao = $sale->getPayment()->getAuthorizationCode() ?? null;
              
                $pagamento->setTransacaoId($tid);
                $pagamento->setCodAutorizacao($codigoAutorizacao);
                $pagamento->setStatus('APROVADO');
                $pagamento->save();

                $cartaoCieloDados->setStatus('paga');
                $cartaoCieloDados->save();

                $replay = array(
                    "pedido_id" => $carrinho->getId(),
                    'id' => $paymentId,
                    'tid' => $tid,
                    'codigo_altorizacao' => $codigoAutorizacao,
                    "status" => 'paga' ,
                    'url_acesso'   => '',
                    "erro" => false,
                    'code' => $sale->getPayment()->getReturnCode(),
                    'message' => $sale->getPayment()->getReturnMessage(),
                );
                $data = json_decode(json_encode($replay));
                $obj = new Response($data);
                return $obj;
            }
        } catch (CieloRequestException $e) {

            $cartaoCieloDados->setStatus(self::CIELO_NEGADO);
            $cartaoCieloDados->save();

            $error = $e->getCieloError();

            $errorCode = $error ? $error->getCode() : $e->getCode();
            $errorMessage = $error ? $error->getMessage() : $e->getMessage();

            $pagamento->setStatus('NEGADO');
            $pagamento->save();


            $replay = array(
                "pedido_id" => $carrinho->getId(),
                'id' => '',
                'tid' => '',
                'codigo_altorizacao' => '',
                "status" => self::CIELO_NEGADO,
                "erro" => $errorCode ? $errorCode : false,
                'code' => $errorCode,
                'message' => $errorMessage,
            );
            $data = json_decode(json_encode($replay));
            $obj = new Response($data);
            return $obj;
        }

    }

    public function estornar(\BasePedidoFormaPagamento $pagamento, array $ambiente)
    {
        if (empty($pagamento->getCieloPaymentId())) :
            return false;
        endif;

        try {
            // Configure o ambiente
            $environment = $ambiente['ambiente'] == 'sandbox' ? Environment::sandbox() : Environment::production();
    
            // Configure seu merchant
            $merchant = new Merchant($ambiente['merchant_id'], $ambiente['merchant_key']);

            // Configure o SDK com seu merchant e o ambiente apropriado para criar a venda
            $sale = (new CieloEcommerce($merchant, $environment))->cancelSale($pagamento->getCieloPaymentId());

            // if (in_array($sale->getPayment()->getStatus(), [10, 11, 12, 13])) {
                // 10, 11, 12, 13
            $tid = $sale->getTid() ?? null;
            $codigoAutorizacao = $sale->getAuthorizationCode() ?? null;

            $pagamento->setTransacaoId($tid);
            $pagamento->setCodAutorizacao($codigoAutorizacao);
            $pagamento->setStatus(\PedidoFormaPagamentoPeer::STATUS_CANCELADO);
            $pagamento->setObservacao(null);
            $pagamento->save();

            $cartaoCieloDados = CartaoCieloDadosQuery::create()
                ->filterByPedidoId($pagamento->getPedidoId())
                ->filterByCieloPaymentId($pagamento->getCieloPaymentId())
                ->findOne();

            if (!empty($cartaoCieloDados)) :
                $cartaoCieloDados->setStatus('cancelado');
                $cartaoCieloDados->save();
            endif;
        } catch (CieloRequestException $e) {
            $pagamento->setStatus(\PedidoFormaPagamentoPeer::STATUS_CANCELADO);
            $pagamento->setObservacao($e->getCieloError()->getMessage());
            $pagamento->save();
        } catch (\Exception $e) {
            $pagamento->setStatus(\PedidoFormaPagamentoPeer::STATUS_CANCELADO);
            $pagamento->setObservacao($e->getMessage());
            $pagamento->save();
        }
    }

//    public function getFraudAnalysis($cliente, $objPedido)
//    {
//        /** @var \Pedido $objPedido */
//
//        $ua= $this->getBrowser();
//        $ip = !empty($_SERVER['HTTP_CLIENT_IP']) ? $_SERVER['HTTP_CLIENT_IP'] : (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR']);
//        $typeBrowser = $ua['name'];
//        //$typeBrowser="Chrome";
//        //$ip = "181.223.63.18";
//
//
//        $browser = new Browser();
//        $browser->setCookiesAccepted(true);
//        $browser->setEmail($cliente->getEmail());
//        $browser->setHostName("Teste");
//        $browser->setIpAddress($ip);
//        $browser->setType($typeBrowser);
//
//        /**
//        "FraudAnalysis":{
//        "Sequence":"AuthorizeFirst",
//        "SequenceCriteria":"Always",
//        "FingerPrintId":"074c1ee676ed4998ab66491013c565e2",
//
//        "Cart":{
//        "IsGift":false,
//        "ReturnsAccepted":true,
//        "Items":[{
//        "GiftCategory":"Undefined",
//        "HostHedge":"Off",
//        "NonSensicalHedge":"Off",
//        "ObscenitiesHedge":"Off",
//        "PhoneHedge":"Off",
//        "Name":"ItemTeste",
//        "Quantity":1,
//        "Sku":"201411170235134521346",
//        "UnitPrice":123,
//        "Risk":"High",
//        "TimeHedge":"Normal",
//        "Type":"AdultContent",
//        "VelocityHedge":"High",
//        "Passenger":{
//        "Email":"compradorteste@live.com",
//        "Identity":"1234567890",
//        "Name":"Comprador accept",
//        "Rating":"Adult",
//        "Phone":"999994444",
//        "Status":"Accepted"
//        }
//        }]
//        },
//
//         */
//
//
//
//        $arrayItens = array();
//        foreach ( $objPedido->getCarrinho()->getItemCarrinhos() as $objItem){
//            $itens = new Items();
//            $itens->setGiftCategory("Undefined");
//            $itens->setHostHedge("Off");
//            $itens->setNonSensicalHedge("Off");
//            $itens->setObscenitiesHedge("Off");
//            $itens->setName($objItem->getProduto()->getNome());
//            $itens->setQuantity($objItem->getQuantidadeRequisitada());
//            $itens->setSku($objItem->getProduto()->getReferencia());
//            $itens->setUnitPrice($objItem->getValor());
//
//            $arrayItens[] = $itens;
//        }
//
//
//        $cart = new Cart();
//        $cart->setIsGift(false);
//        $cart->setReturnsAccepted(true);
//        $cart->setItems($arrayItens);
//
//        $fraudAnalysis = new FraudAnalysis();
//        $fraudAnalysis->setSequence(FraudAnalysis::SEQUENCE_AUTHORIZE_FIRST);
//        $fraudAnalysis->setSequenceCriteria(FraudAnalysis::SEQUENCE_CRITERIA_ALWAYS);
//        $fraudAnalysis->setFingerPrintId("074c1ee676ed4998ab66491013c565e2");
//        $fraudAnalysis->setCart($cart);
//        $fraudAnalysis->setBrowser($browser);
//
//        return $fraudAnalysis;
//    }
//
//    public function  getBrowser()
//    {
//        $u_agent = $_SERVER['HTTP_USER_AGENT'];
//        $bname = 'Unknown';
//        $platform = 'Unknown';
//        $version= "";
//
//        //First get the platform?
//        if (preg_match('/linux/i', $u_agent)) {
//            $platform = 'linux';
//        }
//        elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
//            $platform = 'mac';
//        }
//        elseif (preg_match('/windows|win32/i', $u_agent)) {
//            $platform = 'windows';
//        }
//
//        // Next get the name of the useragent yes seperately and for good reason
//        if(preg_match('/MSIE/i',$u_agent) && !preg_match('/Opera/i',$u_agent))
//        {
//            $bname = 'Internet Explorer';
//            $ub = "MSIE";
//        }
//        elseif(preg_match('/Firefox/i',$u_agent))
//        {
//            $bname = 'Mozilla Firefox';
//            $ub = "Firefox";
//        }
//        elseif(preg_match('/OPR/i',$u_agent))
//        {
//            $bname = 'Opera';
//            $ub = "Opera";
//        }
//        elseif(preg_match('/Chrome/i',$u_agent))
//        {
//            $bname = 'Google Chrome';
//            $ub = "Chrome";
//        }
//        elseif(preg_match('/Safari/i',$u_agent))
//        {
//            $bname = 'Apple Safari';
//            $ub = "Safari";
//        }
//        elseif(preg_match('/Netscape/i',$u_agent))
//        {
//            $bname = 'Netscape';
//            $ub = "Netscape";
//        }
//
//        // finally get the correct version number
//        $known = array('Version', $ub, 'other');
//        $pattern = '#(?<browser>' . join('|', $known) .
//            ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
//        if (!preg_match_all($pattern, $u_agent, $matches)) {
//            // we have no matching number just continue
//        }
//
//        // see how many we have
//        $i = count($matches['browser']);
//        if ($i != 1) {
//            //we will have two since we are not using 'other' argument yet
//            //see if version is before or after the name
//            if (strripos($u_agent,"Version") < strripos($u_agent,$ub)){
//                $version= $matches['version'][0];
//            }
//            else {
//                $version= $matches['version'][1];
//            }
//        }
//        else {
//            $version= $matches['version'][0];
//        }
//
//        // check if we have a number
//        if ($version==null || $version=="") {$version="?";}
//
//        return array(
//            'userAgent' => $u_agent,
//            'name'      => $bname,
//            'version'   => $version,
//            'platform'  => $platform,
//            'pattern'    => $pattern
//        );
//    }
//
//
//    public function prettyPrint( $json )
//    {
//        $result = '';
//        $level = 0;
//        $in_quotes = false;
//        $in_escape = false;
//        $ends_line_level = NULL;
//        $json_length = strlen( $json );
//
//        for( $i = 0; $i < $json_length; $i++ ) {
//            $char = $json[$i];
//            $new_line_level = NULL;
//            $post = "";
//            if( $ends_line_level !== NULL ) {
//                $new_line_level = $ends_line_level;
//                $ends_line_level = NULL;
//            }
//            if ( $in_escape ) {
//                $in_escape = false;
//            } else if( $char === '"' ) {
//                $in_quotes = !$in_quotes;
//            } else if( ! $in_quotes ) {
//                switch( $char ) {
//                    case '}': case ']':
//                    $level--;
//                    $ends_line_level = NULL;
//                    $new_line_level = $level;
//                    break;
//
//                    case '{': case '[':
//                    $level++;
//                    case ',':
//                        $ends_line_level = $level;
//                        break;
//
//                    case ':':
//                        $post = " ";
//                        break;
//
//                    case " ": case "\t": case "\n": case "\r":
//                    $char = "";
//                    $ends_line_level = $new_line_level;
//                    $new_line_level = NULL;
//                    break;
//                }
//            } else if ( $char === '\\' ) {
//                $in_escape = true;
//            }
//            if( $new_line_level !== NULL ) {
//                $result .= "\n".str_repeat( "\t", $new_line_level );
//            }
//            $result .= $char.$post;
//        }
//
//        return $result;
//    }

}
