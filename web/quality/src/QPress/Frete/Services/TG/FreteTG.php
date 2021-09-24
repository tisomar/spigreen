<?php
namespace QPress\Frete\Services\TG;

use QPress\Frete\DataResponse\DataResponseFrete;
use QPress\Frete\DataResponse\DataResponseFreteInterface;
use QPress\Frete\FreteInterface;
use QPress\Frete\Package\Package;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class FreteTG implements FreteInterface {

    public function getNome()
    {
        return 'tg';
    }

    public function getTitulo()
    {
        return 'Transportadora TG';
    }

    public function consultar(Package $package)
    {

        $response = new DataResponseFrete();

        try {

            $itens = array_map(function ($item) {
                return [
                    'weight' => $item->getPeso() / 1000,
                    'lenght' => $item->getComprimento(),
                    'width' => $item->getLargura(),
                    'height' => $item->getAltura(),
                    'volume' => 0.0,
                    'nogCode' => '000',
                    'pieces' => $item->getQuantidade(),
                    'value' => $item->getValor()
                ];
            }, array_values($package->getAllItems()));

            $requestData= [ 'dominio' => 'TGT',
                'login' => 'spigreen',
                'senha' => '123',
                'cnpjPagador' => '31716218000332',
                'cepOrigem' => '29161382',
                'cepDestino' => $package->getClient()->getCepTo(),
                'valorNF' => $itens[0]['value'] ,
                'quantidade' => $itens[0]['pieces'],
                'peso' => $itens[0]['weight'],
                'volume' => $itens[0]['volume'] ,
                'mercadoria' => '1',
                'cnpjDestinatario' => 'N',
                'entDificil' => 'N',
                'destContribuinte' => 'N'
            ];

            $urlWsdl = "https://ssw.inf.br/ws/sswCotacao/index.php?wsdl";

            $soapClient = new \SoapClient($urlWsdl, [
                'exceptions' => true
            ]);

            $retorno = $soapClient->__soapCall("cotar", $requestData);

            $retornoXML = new \SimpleXMLElement($retorno);

            if (!empty($retornoXML)) {

                $response->setPrazo($retornoXML->prazo);
                $response->setValor(format_money((float)$retornoXML->totalFrete));
                return $response;

            }
            $response->setDisponivel(false);
        } catch (SoapFault $exception) {
            echo $exception->getMessage();
            return;
        }

        return $response;
    }

}
