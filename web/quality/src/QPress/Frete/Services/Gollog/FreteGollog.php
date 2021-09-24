<?php
namespace QPress\Frete\Services\Gollog;

use QPress\Frete\DataResponse\DataResponseFrete;
use QPress\Frete\DataResponse\DataResponseFreteInterface;
use QPress\Frete\FreteInterface;
use QPress\Frete\Package\Package;
use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

class FreteGollog implements FreteInterface {

    public function getNome()
    {
        return 'gollog';
    }

    public function getTitulo()
    {
        return 'Gollog';
    }

    public function consultar(Package $package)
    {
        $client = new Client([
            'base_uri' => 'http://api-golcargo.gollog.com.br',
        ]);

        $response = new DataResponseFrete();
        
        try {
            $responseQuotation = $client->post('/api/sales/transportorder/quotation', [
                RequestOptions::HEADERS => [
                    'Content-Type' => 'application/json',
                    'companyKey' => 'G3',
                ],
                RequestOptions::JSON => [
                    'customerToken' => 'C689A15D31DE4F3094DA555ECB191896',
                    'customerDocument' => '31716218000170',
                    'originPointCode' => null,
                    'originPostalCode' => '29161382',
                    'destinationPointCode' => null,
                    'destinationPostalCode' => $package->getClient()->getCepTo(),
                    'toCollect' => false,
                    'toDelivery' => true,
                    'declaredValue' => 0,
                    'insuranceType' => 2,
                    'volumes' => array_map(function ($item) {
                        return [
                            'weight' => $item->getPeso() / 1000,
                            'lenght' => $item->getComprimento(),
                            'width' => $item->getLargura(),
                            'height' => $item->getAltura(),
                            'volume' => 0.0,
                            'nogCode' => '000',
                            'pieces' => $item->getQuantidade(),
                        ];
                    }, array_values($package->getAllItems())),
                    'products' => [
                        'GCE',
                    ],
                ],
            ]);

            $body = (string) $responseQuotation->getBody();

            if ($responseQuotation->getStatusCode() == 200 && !empty($body)) :
                $json = json_decode($body)[0] ?? null;

                if (empty($json)) :
                    $response->setDisponivel(false);

                    return $response;
                endif;

                $response->setPrazo($json->timeToDelivery);
                $response->setValor(format_money($json->totalValue));

                return $response;
            endif;

            $response->setDisponivel(false);
        } catch (\Throwable $th) {
            $response->setDisponivel(false);
        }

        return $response;
    }
}
