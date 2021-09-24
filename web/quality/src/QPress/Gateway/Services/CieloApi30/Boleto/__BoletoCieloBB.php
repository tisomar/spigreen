<?php
/**
 * Created by PhpStorm.
 * User: Giovan
 * Date: 23/11/2018
 * Time: 16:09
 */

namespace QPress\Gateway\Services\CieloApi30\Boleto;

use Aws\Api\ApiProvider;
use Cielo\API30\Ecommerce\Address;
use Cielo\API30\Merchant;

use Cielo\API30\Ecommerce\Environment;
use Cielo\API30\Ecommerce\Sale;
use Cielo\API30\Ecommerce\CieloEcommerce;
use Cielo\API30\Ecommerce\Payment;
use Cielo\API30\Ecommerce\CreditCard;
use QPress\Gateway\Services\CieloApi30\Response\Response;

use Cielo\API30\Ecommerce\Request\CieloRequestException;

class BoletoCieloBB
{

    const INICIO_PAGAMENTO      = 'inicio_pagamento';
    const CIELO_AGUARDANDO      = 'aguardando_pagamento';
    const CIELO_PAGO            = 'paga';
    const CIELO_NAO_CAPTURADO   = 'paga_nao_capturada';
    const CIELO_NEGADO          = 'negada';
    const CIELO_CANCELADO       = 'cancelada';

//    const COUNTRY_SIGLAS = array(
//        "AF" => "AFG",
//        "ZA" => "ZAF",
//        "AL" => "ALB",
//        "DE" => "DEU",
//        "AD" => "AND",
//        "AO" => "AGO",
//        "AI" => "AIA",
//        "AQ" => "ATA",
//        "AG" => "ATG",
//        "AN" => "ANT",
//        "SA" => "SAU",
//        "DZ" => "DZA",
//        "AR" => "ARG",
//        "AM" => "ARM",
//        "AW" => "ABW",
//        "AU" => "AUS",
//        "AT" => "AUT",
//        "AZ" => "AZE",
//        "BS" => "BHS",
//        "BH" => "BHR",
//        "BD" => "BGD",
//        "BB" => "BRB",
//        "BY" => "BLR",
//        "BE" => "BEL",
//        "BZ" => "BLZ",
//        "BJ" => "BEN",
//        "BM" => "BMU",
//        "BO" => "BOL",
//        "BA" => "BIH",
//        "BW" => "BWA",
//        "BR" => "BRA",
//        "BN" => "BRN",
//        "BG" => "BGR",
//        "BF" => "BFA",
//        "BI" => "BDI",
//        "BT" => "BTN",
//        "CV" => "CPV",
//        "CM" => "CMR",
//        "KH" => "KHM",
//        "CA" => "CAN",
//        "KZ" => "KAZ",
//        "TD" => "TCD",
//        "CL" => "CHL",
//        "CN" => "CHN",
//        "CY" => "CYP",
//        "SG" => "SGP",
//        "CO" => "COL",
//        "CG" => "COG",
//        "KP" => "PRK",
//        "KR" => "KOR",
//        "CI" => "CIV",
//        "CR" => "CRI",
//        "HR" => "HRV",
//        "CU" => "CUB",
//        "DK" => "DNK",
//        "DJ" => "DJI",
//        "DM" => "DMA",
//        "EG" => "EGY",
//        "SV" => "SLV",
//        "AE" => "ARE",
//        "EC" => "ECU",
//        "ER" => "ERI",
//        "SK" => "SVK",
//        "SI" => "SVN",
//        "ES" => "ESP",
//        "US" => "USA",
//        "EE" => "EST",
//        "ET" => "ETH",
//        "FJ" => "FJI",
//        "PH" => "PHL",
//        "FI" => "FIN",
//        "FR" => "FRA",
//        "GA" => "GAB",
//        "GM" => "GMB",
//        "GH" => "GHA",
//        "GE" => "GEO",
//        "GI" => "GIB",
//        "GB" => "GBR",
//        "GD" => "GRD",
//        "GR" => "GRC",
//        "GL" => "GRL",
//        "GP" => "GLP",
//        "GU" => "GUM",
//        "GT" => "GTM",
//        "G" => "GGY",
//        "GY" => "GUY",
//        "GF" => "GUF",
//        "GN" => "GIN",
//        "GQ" => "GNQ",
//        "GW" => "GNB",
//        "HT" => "HTI",
//        "NL" => "NLD",
//        "HN" => "HND",
//        "HK" => "HKG",
//        "HU" => "HUN",
//        "YE" => "YEM",
//        "BV" => "BVT",
//        "IM" => "IMN",
//        "CX" => "CXR",
//        "PN" => "PCN",
//        "RE" => "REU",
//        "AX" => "ALA",
//        "KY" => "CYM",
//        "CC" => "CCK",
//        "KM" => "COM",
//        "CK" => "COK",
//        "FO" => "FRO",
//        "FK" => "FLK",
//        "GS" => "SGS",
//        "HM" => "HMD",
//        "MP" => "MNP",
//        "MH" => "MHL",
//        "UM" => "UMI",
//        "NF" => "NFK",
//        "SC" => "SYC",
//        "SB" => "SLB",
//        "SJ" => "SJM",
//        "TK" => "TKL",
//        "TC" => "TCA",
//        "VI" => "VIR",
//        "VG" => "VGB",
//        "WF" => "WLF",
//        "IN" => "IND",
//        "ID" => "IDN",
//        "IR" => "IRN",
//        "IQ" => "IRQ",
//        "IE" => "IRL",
//        "IS" => "ISL",
//        "IL" => "ISR",
//        "IT" => "ITA",
//        "JM" => "JAM",
//        "JP" => "JPN",
//        "JE" => "JEY",
//        "JO" => "JOR",
//        "KE" => "KEN",
//        "KI" => "KIR",
//        "KW" => "KWT",
//        "LA" => "LAO",
//        "LV" => "LVA",
//        "LS" => "LSO",
//        "LB" => "LBN",
//        "LR" => "LBR",
//        "LY" => "LBY",
//        "LI" => "LIE",
//        "LT" => "LTU",
//        "LU" => "LUX",
//        "MO" => "MAC",
//        "MK" => "MKD",
//        "MG" => "MDG",
//        "MY" => "MYS",
//        "MW" => "MWI",
//        "MV" => "MDV",
//        "ML" => "MLI",
//        "MT" => "MLT",
//        "MA" => "MAR",
//        "MQ" => "MTQ",
//        "MU" => "MUS",
//        "MR" => "MRT",
//        "YT" => "MYT",
//        "MX" => "MEX",
//        "FM" => "FSM",
//        "MZ" => "MOZ",
//        "MD" => "MDA",
//        "MC" => "MCO",
//        "MN" => "MNG",
//        "ME" => "MNE",
//        "MS" => "MSR",
//        "MM" => "MMR",
//        "NA" => "NAM",
//        "NR" => "NRU",
//        "NP" => "NPL",
//        "NI" => "NIC",
//        "NE" => "NER",
//        "NG" => "NGA",
//        "NU" => "NIU",
//        "NO" => "NOR",
//        "NC" => "NCL",
//        "NZ" => "NZL",
//        "OM" => "OMN",
//        "PW" => "PLW",
//        "PA" => "PAN",
//        "PG" => "PNG",
//        "PK" => "PAK",
//        "PY" => "PRY",
//        "PE" => "PER",
//        "PF" => "PYF",
//        "PL" => "POL",
//        "PR" => "PRI",
//        "PT" => "PRT",
//        "QA" => "QAT",
//        "KG" => "KGZ",
//        "CF" => "CAF",
//        "CD" => "COD",
//        "DO" => "DOM",
//        "CZ" => "CZE",
//        "RO" => "ROM",
//        "RW" => "RWA",
//        "RU" => "RUS",
//        "EH" => "ESH",
//        "VC" => "VCT",
//        "AS" => "ASM",
//        "WS" => "WSM",
//        "SM" => "SMR",
//        "SH" => "SHN",
//        "LC" => "LCA",
//        "BL" => "BLM",
//        "KN" => "KNA",
//        "MF" => "MAF",
//        "ST" => "STP",
//        "SN" => "SEN",
//        "SL" => "SLE",
//        "RS" => "SRB",
//        "SY" => "SYR",
//        "SO" => "SOM",
//        "LK" => "LKA",
//        "PM" => "SPM",
//        "SZ" => "SWZ",
//        "SD" => "SDN",
//        "SE" => "SWE",
//        "CH" => "CHE",
//        "SR" => "SUR",
//        "TJ" => "TJK",
//        "TH" => "THA",
//        "TW" => "TWN",
//        "TZ" => "TZA",
//        "IO" => "IOT",
//        "TF" => "ATF",
//        "PS" => "PSE",
//        "TP" => "TMP",
//        "TG" => "TGO",
//        "TO" => "TON",
//        "TT" => "TTO",
//        "TN" => "TUN",
//        "TM" => "TKM",
//        "TR" => "TUR",
//        "TV" => "TUV",
//        "UA" => "UKR",
//        "UG" => "UGA",
//        "UY" => "URY",
//        "UZ" => "UZB",
//        "VU" => "VUT",
//        "VA" => "VAT",
//        "VE" => "VEN",
//        "VN" => "VNM",
//        "ZM" => "ZMB",
//        "ZW" => "ZWE",
//    );

//    const responseCodeAPI = array(
//        "0" => "aguardando_pagamento",
//        "1" => "paga",
//        "2" => "paga",
//        "3" => "negada",
//        "10" => "negada",
//        "11" => "negada",
//        "12" => "aguardando_pagamento",
//        "13" => "negada",
//        "20" => "paga_nao_capturada",
//        "99" => "aguardando_pagamento",
//    );

//    const responseMessageCodeAPI = array(
//        "0" => "Aguardando atualização de status",
//        "1" => "Pagamento apto a ser capturado ou definido como pago",
//        "2" => "Pagamento confirmado e finalizado",
//        "3" => "Pagamento negado por Autorizador",
//        "10" => "Pagamento cancelado",
//        "11" => "Pagamento cancelado após 23:59 do dia de autorização",
//        "12" => "Aguardando Status de instituição financeira",
//        "13" => "Pagamento cancelado por falha no processamento ou por ação do AF",
//        "20" => "Recorrência agendada",
//        "99" => "Pendente de autenticação"
//    );

    /**
     * @param \BasePedido $carrinho
     * @param array $ambiente
     * @param \BoletoCieloDados|null $boletoCieloDados
     * @throws \PropelException
     */

    public function pagar(\BasePedido $carrinho, array $ambiente, \BoletoCieloDados $boletoCieloDados = null){

        /** @var \BoletoCieloDados $boletoCieloDados */
        /** @var \BaseCliente $cliente */
        /** @var \Pedido $objPedido */
        /** @var $enderecoPrincipalCliente \Endereco */
        /** @var \BasePedidoFormaPagamento $pagamento */


        $cliente = $carrinho->getCliente();
        $pagamento = $carrinho->getPedidoFormaPagamento();

        // Configure o ambiente
        $environment = $ambiente['ambiente'] == 'sandbox' ? Environment::sandbox() : Environment::production();

        // Configure seu merchant
        $merchant = new Merchant($ambiente['merchant_id'], $ambiente['merchant_key']);

        // Crie uma instância de Sale informando o ID do pagamento
        $sale = new Sale($carrinho->getId());

        $enderecoPrincipalCliente = $carrinho->getEndereco();

        $logradouro = self::santinizeString($enderecoPrincipalCliente->getLogradouro());
        $complemento = self::santinizeString($enderecoPrincipalCliente->getComplemento());
        $numero = self::santinizeString($enderecoPrincipalCliente->getNumero());
        $bairro = self::santinizeString($enderecoPrincipalCliente->getBairro());
        $cidade = $enderecoPrincipalCliente->getCidade() ? self::santinizeString(resumo($enderecoPrincipalCliente->getCidade()->getNome(),18, '')) : "";
        $estado = $enderecoPrincipalCliente->getCidade() && $enderecoPrincipalCliente->getCidade()->getEstado() ? $enderecoPrincipalCliente->getCidade()->getEstado()->getSigla() : "";

        if(strlen($logradouro.$complemento.$numero.$bairro) > 60){
            $complemento = '';
            if(strlen($logradouro.$numero.$bairro) > 60){
                $numBairroLength = strlen($numero.$bairro);
                $logradouro = resumo($logradouro,(60 - $numBairroLength), '');
            }
        }

        // Crie uma instância de Customer informando o nome do cliente

        $customer = $sale->customer(self::santinizeString($cliente->getNomeCompleto()));
        $customer->setIdentity(str_replace("/","",str_replace(".","",str_replace("-","",$cliente->getCodigoFederal()))));
		if($cliente->isPessoaFisica()){
            $customer->setIdentityType('CPF');
        } else {
            $customer->setIdentityType('CNPJ');    
        }
//        $customer->setEmail($cliente->getEmail());
//        $customer->setBirthDate($cliente->getDataNascimento("Y-m-d"));
        $address = new Address();
        $address->setStreet($logradouro);
        $address->setNumber($numero);
        $address->setComplement($complemento);
        $address->setZipCode($enderecoPrincipalCliente->getCep(true));
        $address->setCity($cidade);
        $address->setState($estado);
        $address->setCountry('BRA');
        $address->setDistrict($bairro);
        $customer->setAddress($address);
        $customer->setDeliveryAddress($address);

        // Crie uma instância de Payment informando o valor do pagamento
        $payment = $sale->payment($carrinho->getValorTotal() * 100);

//        $fraudAnalysis = $this->getFraudAnalysis($cliente,$carrinho);
//        $payment->setFraudAnalysis($fraudAnalysis);

        // Crie uma instância de Credit Card utilizando os dados de teste
        // esses dados estão disponíveis no manual de integração
        $payment->setType(Payment::PAYMENTTYPE_BOLETO)
            //->setInterest(0)
            ->setProvider($boletoCieloDados->getProvider())
            //->setServiceTaxAmount(self::TAX_AMOUNT_SERVICE)
            ->setAssignor($boletoCieloDados->getAssignor())
            ->setExpirationDate($boletoCieloDados->getExpirationDate('Y-m-d'))
            ->setAddress($boletoCieloDados->getAddress())
            ->setIdentification($boletoCieloDados->getPedidoId())
            ->setInstructions($boletoCieloDados->getInstructions());


            //->setHolder($cartaoCieloDados->getNome());

        // Crie o pagamento na Cielo
        try {

            // Configure o SDK com seu merchant e o ambiente apropriado para criar a venda
            $sale = (new CieloEcommerce($merchant, $environment))->createSale($sale);



            //if(null !== $sale->getPayment()){
            $tid = (null !== $sale->getPayment()) ? $sale->getPayment()->getTid() : null;

            $paymentId = $sale->getPayment()->getPaymentId();

            $boletoCieloDados->setPedidoId($carrinho->getId());
            $boletoCieloDados->setCieloPaymentId($paymentId);
            $boletoCieloDados->save();

            /**
             *
             * object(Cielo\API30\Ecommerce\Payment)#615 (42) {
            ["serviceTaxAmount":"Cielo\API30\Ecommerce\Payment":private]=>
            NULL
            ["installments":"Cielo\API30\Ecommerce\Payment":private]=>
            NULL
            ["interest":"Cielo\API30\Ecommerce\Payment":private]=>
            NULL
            ["capture":"Cielo\API30\Ecommerce\Payment":private]=>
            bool(false)
            ["authenticate":"Cielo\API30\Ecommerce\Payment":private]=>
            bool(false)
            ["recurrent":"Cielo\API30\Ecommerce\Payment":private]=>
            bool(false)
            ["recurrentPayment":"Cielo\API30\Ecommerce\Payment":private]=>
            NULL
            ["creditCard":"Cielo\API30\Ecommerce\Payment":private]=>
            NULL
            ["debitCard":"Cielo\API30\Ecommerce\Payment":private]=>
            NULL
            ["authenticationUrl":"Cielo\API30\Ecommerce\Payment":private]=>
            NULL
            ["tid":"Cielo\API30\Ecommerce\Payment":private]=>
            NULL
            ["proofOfSale":"Cielo\API30\Ecommerce\Payment":private]=>
            NULL
            ["authorizationCode":"Cielo\API30\Ecommerce\Payment":private]=>
            NULL
            ["softDescriptor":"Cielo\API30\Ecommerce\Payment":private]=>
            NULL
            ["returnUrl":"Cielo\API30\Ecommerce\Payment":private]=>
            NULL
            ["provider":"Cielo\API30\Ecommerce\Payment":private]=>
            string(8) "Simulado"
            ["paymentId":"Cielo\API30\Ecommerce\Payment":private]=>
            string(36) "d9bcf94b-0302-4c80-8b21-3a9b40e7a90c"
            ["type":"Cielo\API30\Ecommerce\Payment":private]=>
            string(6) "Boleto"
            ["amount":"Cielo\API30\Ecommerce\Payment":private]=>
            int(1000)
            ["receivedDate":"Cielo\API30\Ecommerce\Payment":private]=>
            string(19) "2018-11-26 09:26:31"
            ["capturedAmount":"Cielo\API30\Ecommerce\Payment":private]=>
            NULL
            ["capturedDate":"Cielo\API30\Ecommerce\Payment":private]=>
            NULL
            ["voidedAmount":"Cielo\API30\Ecommerce\Payment":private]=>
            NULL
            ["voidedDate":"Cielo\API30\Ecommerce\Payment":private]=>
            NULL
            ["currency":"Cielo\API30\Ecommerce\Payment":private]=>
            string(3) "BRL"
            ["country":"Cielo\API30\Ecommerce\Payment":private]=>
            string(3) "BRA"
            ["returnCode":"Cielo\API30\Ecommerce\Payment":private]=>
            NULL
            ["returnMessage":"Cielo\API30\Ecommerce\Payment":private]=>
            NULL
            ["status":"Cielo\API30\Ecommerce\Payment":private]=>
            int(1)
            ["links":"Cielo\API30\Ecommerce\Payment":private]=>
            array(1) {
            [0]=>
            object(stdClass)#610 (3) {
            ["Method"]=>
            string(3) "GET"
            ["Rel"]=>
            string(4) "self"
            ["Href"]=>
            string(96) "https://apiquerysandbox.cieloecommerce.cielo.com.br/1/sales/d9bcf94b-0302-4c80-8b21-3a9b40e7a90c"
            }
            }
            ["extraDataCollection":"Cielo\API30\Ecommerce\Payment":private]=>
            NULL
            ["expirationDate":"Cielo\API30\Ecommerce\Payment":private]=>
            string(10) "2018-11-27"
            ["url":"Cielo\API30\Ecommerce\Payment":private]=>
            string(103) "https://transactionsandbox.pagador.com.br/post/pagador/reenvia.asp/d9bcf94b-0302-4c80-8b21-3a9b40e7a90c"
            ["number":"Cielo\API30\Ecommerce\Payment":private]=>
            NULL
            ["boletoNumber":"Cielo\API30\Ecommerce\Payment":private]=>
            string(3) "4-0"
            ["barCodeNumber":"Cielo\API30\Ecommerce\Payment":private]=>
            string(44) "00093772100000010009999250000000000499999990"
            ["digitableLine":"Cielo\API30\Ecommerce\Payment":private]=>
            string(54) "00099.99921 50000.000005 04999.999909 3 77210000001000"
            ["address":"Cielo\API30\Ecommerce\Payment":private]=>
            string(1) "1"
            ["assignor":"Cielo\API30\Ecommerce\Payment":private]=>
            string(1) "1"
            ["demonstrative":"Cielo\API30\Ecommerce\Payment":private]=>
            NULL
            ["identification":"Cielo\API30\Ecommerce\Payment":private]=>
            string(2) "46"
            ["instructions":"Cielo\API30\Ecommerce\Payment":private]=>
            string(1) "1"
            }
             *
             */


            if($sale->getPayment()->getStatus() != 1){

                $tid = (null !== $sale->getPayment()) ? $sale->getPayment()->getTid() : null;

                $pagamento->setTransacaoId($tid);
                $pagamento->setStatus(\PedidoFormaPagamentoPeer::STATUS_PENDENTE);
                $pagamento->save();

                $boletoCieloDados->setStatus(\PedidoFormaPagamentoPeer::STATUS_PENDENTE);
                $boletoCieloDados->save();

                $replay = array(
                    "pedido_id" => $carrinho->getId(),
                    'id' => $paymentId,
                    'tid' => $tid,
                    "status" => \PedidoFormaPagamentoPeer::STATUS_PENDENTE ,
                    "erro" => true,
                    'code' => $sale->getPayment()->getReturnCode(),
                    'message' => $sale->getPayment()->getReturnMessage(),
                    'url_acesso'   => '',
                );
                $data = json_decode(json_encode($replay));
                $obj = new Response($data);

                return $obj;
            } else {

                $tid = (null !== $sale->getPayment()) ? $sale->getPayment()->getTid() : null;

                $pagamento->setTransacaoId($tid);
                $pagamento->setUrlAcesso($sale->getPayment()->getUrl());
                $pagamento->setDataVencimento($sale->getPayment()->getExpirationDate());
                $pagamento->setStatus(\PedidoFormaPagamentoPeer::STATUS_PENDENTE);
                $pagamento->save();

                $boletoCieloDados->setStatus($sale->getPayment()->getStatus());
                $boletoCieloDados->setProvider($sale->getPayment()->getProvider());
                $boletoCieloDados->setAssignor($sale->getPayment()->getAssignor());
                $boletoCieloDados->setInstructions($sale->getPayment()->getInstructions());
                $boletoCieloDados->setAddress($sale->getPayment()->getAddress());
                $boletoCieloDados->setExpirationDate($sale->getPayment()->getExpirationDate());
                $boletoCieloDados->setDigitableLine($sale->getPayment()->getDigitableLine());
                $boletoCieloDados->setUrl($sale->getPayment()->getUrl());
                $boletoCieloDados->setBarCodeNumber($sale->getPayment()->getBarCodeNumber());
                $boletoCieloDados->setIdentification($sale->getPayment()->getIdentification());
                $boletoCieloDados->setNossoNumero($sale->getPayment()->getBoletoNumber());
                $boletoCieloDados->save();


                $replay = array(
                    "pedido_id" => $carrinho->getId(),
                    'id' => $paymentId,
                    'tid' => $tid,
                    "status" => \PedidoFormaPagamentoPeer::STATUS_PENDENTE,
                    'url_acesso'   => $sale->getPayment()->getUrl(),
                    "erro" => false,
                    'code' => $sale->getPayment()->getReturnCode(),
                    'message' => $sale->getPayment()->getReturnMessage(),
                );
                $data = json_decode(json_encode($replay));
                $obj = new Response($data);

                return $obj;

            }




        } catch (CieloRequestException $e) {

            $boletoCieloDados->setStatus(\PedidoFormaPagamentoPeer::STATUS_PENDENTE);
            $boletoCieloDados->save();

            $error = $e->getCieloError();

            $errorCode = $error ? $error->getCode() : $e->getCode();
            $errorMessage = $error ? $error->getMessage() : $e->getMessage();

            $pagamento->setStatus(\PedidoFormaPagamentoPeer::STATUS_PENDENTE);
            $pagamento->save();


            $replay = array(
                "pedido_id" => $carrinho->getId(),
                'id' => '',
                'tid' => '',
                "status" => \PedidoFormaPagamentoPeer::STATUS_PENDENTE,
                "erro" => $errorCode ? $errorCode : false,
                'code' => $errorCode,
                'message' => $errorMessage,
            );
            $data = json_decode(json_encode($replay));
            $obj = new Response($data);

            return $obj;
        }

    }

    private function getExpirationDate($days){

    }

    /**
     * @param $s
     * @return string|string[]|null
     */
    private function santinizeString($s){
        return str_replace('/',' ',str_replace('-','',str_replace(' - ',' ',preg_replace("/&([a-z])[a-z]+;/i", "$1", htmlentities(trim($s))))));
    }

}