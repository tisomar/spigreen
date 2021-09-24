<?php

namespace Integrations\Models\Bling;

use Integrations\AbstractIntegration;
use Integrations\Helper\Helper;
use Integrations\Manager\ServiceManager;
use Integrations\Models\Bling\Services\Cliente\ClienteBling;
use Integrations\Models\Bling\Services\ContasPagar\ContasPagarBling;
use Integrations\Models\Bling\Services\Pedido\PedidoBling;
use Integrations\Models\Bling\Services\Produto\ProdutoBling;
use Integrations\Models\Bling\Services\ContasReceber\ContasReceberBling;
use Integrations\Models\Bling\Services\ServiceInterface;
use Integrations\Request\Request;

class Bling extends AbstractIntegration
{

    const PRODUCTION_URL = "https://bling.com.br/Api/v2/";
    /**
     * Parameter name For SetIdCode
     */
    const PARAMENTER_NAME_ID = 'setCodigoid';
    const PARAMENTER_NAME_IS_SERVICE = 'setIsService';

    public $requestMethod = '';
    public $services = '';
    public $serviceActive = null;
    private $dataOutput = '';
    private $apikey = '';

    /**
     * @var Request
     */
    private $request;

    /**
     * Bling constructor.
     * @param array $parameters
     */
    public function __construct(array $parameters = array())
    {
        $this->apikey = getenv('BLING_API_KEY'); // Use nginx fastcgi_params
        $this->setServiceManager();
        $this->request = new Request(self::PRODUCTION_URL);

        parent::__construct($parameters);
    }

    public function getName()
    {
        return 'Bling';
    }

    public function getDefaultParameters()
    {
        return array(
        );
    }

    public function getParametersRequest($outputType)
    {
        $keyData = 'xml';

        if ($outputType == 'JSON') {
            $keyData = 'json';
        }

        return array(
            'apikey' => $this->apikey,
            $keyData => $this->dataOutput
        );
    }

    /**
     * @param $outputType
     * @return mixed
     */
    public function sendRequest($outputType)
    {
        $url = $this->serviceActive->generatePatch();

        if ($this->getMethod() == 'GET') :
            $url .= '&apikey=' . $this->apikey;
        endif;

        $response = $this->send(
            $this->request,
            $url,
            $this->getMethod(),
            $this->setDefaultValidationRequest($outputType),
            $this->getParametersRequest($outputType)
        );

        return $response;
    }

    public function getServiceManager()
    {

        return $this->services;
    }

    private function setServiceManager()
    {

        if (!$this->services instanceof ServiceManager) {
            $this->services = new ServiceManager();
        }

        $this->services->register(new ProdutoBling());
        $this->services->register(new ClienteBling());
        $this->services->register(new PedidoBling());
        $this->services->register(new ContasPagarBling());
        $this->services->register(new ContasReceberBling());

        return $this->services;
    }

    /**
     *
     * Adiciona o serviço a ser executado.
     *
     * @param $serviceName
     * @throws \RuntimeException
     * @return Bling
     */

    public function setService($serviceName)
    {

        if ($this->isServiceActived()) :
            throw new \RuntimeException('Já tem um serviço ativo, remova ele para executar a ação atual. 
                Ao remover será perdido todos os dados já adicionados.');
        endif;

        $serviceActve = $this->services->get($serviceName);

        if (is_null($serviceActve)) :
            throw new \RuntimeException('Serviço não cadastrado');
        endif;

        $this->serviceActive = $serviceActve;

        return $this;
    }

    /**
     * Retorna o Nome do serviço ativo ou null se não tiver
     *
     * @return ServiceInterface|null
     */
    public function getServiceActive()
    {
        $service = null;

        if ($this->serviceActive instanceof ServiceInterface) {
            $service = $this->serviceActive->getName();
        }

        return  $service;
    }

    /**
     * Remove o serviço ativo
     *
     * @return Bling
     */

    public function removeService()
    {
        $this->serviceActive = null;
        return $this;
    }

    public function isServiceActived()
    {
        return ($this->serviceActive instanceof ServiceInterface) ? true : false;
    }

    protected function setParameter($value, $parameterName)
    {

        if (!Helper::isNotEmptyAndValid($value)) {
            return;
        }

        if ($this->isServiceActived()) {
            $this->serviceActive->set($value, $parameterName);
        }
    }

    protected function setMethod($method)
    {
        $this->requestMethod = $method;
    }

    public function getMethod()
    {
        return $this->requestMethod;
    }

    /**
     * @param array $dados
     * @param string $method
     * @param string|null $typeDados
     * @param string|null $outputType
     * @return $response
     */

    public function gravar(array $dados, $method, $typeDados = null, $outputType = 'XML')
    {

        if (!Helper::isNotEmptyAndValid($dados)) {
            return;
        }

        $this->setMethod($method);
        $this->setParameter($dados, $typeDados);


        $function = 'get' . ucfirst(mb_strtolower($typeDados));

        $this->dataOutput = $this->build($this->serviceActive->$function(), $this->serviceActive->getNodeName(), $outputType);

        if ($this->validating($outputType)) {
            $this->setDefaultValidationRequest($this->getContentTypeByOutputType($outputType));
            $response =  $this->sendRequest($outputType);
        }

        return $response;
    }

    public function consultar($dados, $method, $typeDados = null, $codigoId = null, $outputType = 'XML')
    {

        if (Helper::isNotEmptyAndValid($codigoId)) {
            $this->setParameter($codigoId, self::PARAMENTER_NAME_ID);
        }

        if ($this->getServiceActive() == 'PedidoBling' && mb_strtolower(mb_substr($codigoId, -1, 1)) == 's') {
            $this->setParameter(1, self::PARAMENTER_NAME_IS_SERVICE);
        }

        //echo'<pre>';var_dump($this->serviceActive);die;

        $this->setMethod($method);
        $this->setParameter($dados, $typeDados);

        //$function = 'get'.ucfirst(mb_strtolower($typeDados));

        //$this->dataOutput = $this->build($this->serviceActive->$function(), $this->serviceActive->getNodeName(), $outputType);

        if ($this->validating($outputType)) {
            $this->setDefaultValidationRequest($this->getContentTypeByOutputType($outputType));
            return $this->sendRequest($outputType);
        }
    }

    public function deletar($dados, $method, $typeDados = null, $outputType = 'XML')
    {

        if (!Helper::isNotEmptyAndValid($dados)) {
            return;
        }

        /*if(Helper::isNotEmptyAndValid($codigoId)){
            $this->setParameter($codigoId, $this->serviceActive->getIdSetName());
        }*/

        $this->setMethod($method);
        $this->setParameter($dados, $typeDados);

        $function = 'get' . ucfirst(mb_strtolower($typeDados));

        $this->dataOutput = $this->build($this->serviceActive->$function(), $this->serviceActive->getNodeName(), $outputType);

        if ($this->validating($outputType)) {
            $this->setDefaultValidationRequest($this->getContentTypeByOutputType($outputType));

            return $this->sendRequest($outputType);
        }
    }

    /**
     * @param $dados
     * @param $method
     * @param null $typeDados
     * @param null $codigoid
     * @param string $outputType
     * @return mixed|void
     */
    public function alterar($dados, $method, $typeDados = null, $codigoid = null, $outputType = 'XML')
    {
        if (!Helper::isNotEmptyAndValid($dados)) :
            return;
        endif;

        if (Helper::isNotEmptyAndValid($codigoid)) :
            $this->setParameter($codigoid, self::PARAMENTER_NAME_ID);
        endif;

        $this->setMethod($method);
        $this->setParameter($dados, $typeDados);
        $function = 'get' . ucfirst(mb_strtolower($typeDados));
        $this->dataOutput =
            $this->build($this->serviceActive->$function(), $this->serviceActive->getNodeName(), $outputType);

        // TODO: create better validation, but we need to return a response!
        if ($this->validating($outputType)) :
            $this->setDefaultValidationRequest($this->getContentTypeByOutputType($outputType));

            return $this->sendRequest($outputType);
        else :
            return $this->sendRequest($outputType);
        endif;
    }

    /**
     * @param $outputType
     * @return bool
     */
    public function validating($outputType)
    {
        $serviceValidate = $this->serviceActive->validate($this->getMethod());
        $validate = true;

        if ($this->getMethod() != 'GET' && empty($this->dataOutput)) :
            $validate = false;
        endif;

        if (is_null($this->getContentTypeByOutputType($outputType))) :
            $validate = false;
        endif;

        if ($validate === false || $serviceValidate === false) :
            return false;
        endif;

        return true;
    }

    protected function getContentTypeByOutputType($outputType)
    {

        $validContents = array('XML' => array('Content-Type' => 'text/xml', ),
            'JSON' => array('Content-Type' => 'application/json'));

        return isset($validContents[$outputType]) ? $validContents[$outputType] : null;
    }

    protected function setDefaultValidationRequest($contentOutputType)
    {

        $headers = array();

        $headers[CURLOPT_SSL_VERIFYPEER] = false;
        $headers[CURLOPT_RETURNTRANSFER] = true;
        $headers[CURLOPT_TIMEOUT] = 5;
        $headers[CURLOPT_CONNECTTIMEOUT] = 5;
        //$headers[CURLOPT_HEADER] = true;    // we want headers

        /** @todo implements Content Type Header */


        //$this->request->setDefaultOptions($contentOutputType);

        return $headers;
    }

    // ------------------------------------------------------------------------
//    static public function capturar($id = null)
//    {
//        $gateway = new self;
//        $gateway->request_url_append .= '/' . $id . '/capturar';
//        return $gateway;
//    }
//
//    static public function cancelar($id = null)
//    {
//        $gateway = new self;
//        $gateway->request_url_append .= '/' . $id . '/estornar';
//        return $gateway;
//    }
}
