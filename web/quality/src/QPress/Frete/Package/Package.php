<?php
namespace QPress\Frete\Package;

/**
 * Class Package
 * @package QPress\Frete
 */
class Package {

    /**
     * @var Array PackageItem
     */
    private $collPackageItems;
    /**
     * @var PackageClient $client
     */
    private $client;

    /**
     * @var int
     */
    private $peso = null;
    /**
     * @var int
     */
    private $valor = null;
    /**
     * @var int
     */
    private $altura = null;
    /**
     * @var int
     */
    private $largura = null;
    /**
     * @var int
     */
    private $comprimento = null;

    /**
     *
     */
    public function __construct() {
        $this->initialize();
    }

    /**
     *
     */
    public function initialize() {
        $this->peso = null;
        $this->altura = null;
        $this->largura = null;
        $this->comprimento = null;
        $this->valor = null;
    }

    /**
     * Atualiza o peso, altura, largura, comprimento e valor.
     */
    public function updateAtributes() {

        $peso = 0;
        $valor = 0;
        $altura = 0;
        $largura = 0;
        $comprimento = 0;

        if ($this->hasItems()) {

            /* @var $item PackageItem */
            foreach ($this->getAllItems() as $packageItem) {

                // Atualiza o peso de acordo com a quantidade.
                $peso = $peso + ($packageItem->getPeso() * $packageItem->getQuantidade());

                // Atualiza o valor efetuando a soma dos valores de todos os itens.
                $valor = $valor + $packageItem->getValor();

                // Verifica o valor médio entre a Altura, Largura, Comprimento e a Quantidade.
                $cubic = ceil(pow($packageItem->getAltura() * $packageItem->getLargura() * $packageItem->getComprimento() * $packageItem->getQuantidade(), 1/3));

                // Atualiza a altura
                $altura = $altura + $cubic;

                // Atualiza a largura
                $largura = $largura + $cubic;

                // Atualiza o comprimento
                $comprimento = $comprimento + $cubic;

            }
        }

        $this->setPeso($peso);
        $this->setValor($valor);
        $this->setLargura($largura);
        $this->setAltura($altura);
        $this->setComprimento($comprimento);

    }

    /**
     * @param int $valor
     */
    public function setValor($valor)
    {
        $this->valor = $valor;
    }

    /**
     * @return int
     */
    public function getValor()
    {
        return $this->valor;
    }

    /**
     * @param int $peso
     */
    public function setPeso($peso)
    {
        $this->peso = $peso;
    }

    /**
     * @param string $format ['gr','kg']
     * @return int
     */
    public function getPeso($format = 'gr')
    {
        switch ($format) {

            case 'kg':
                return $this->peso / 1000;
            break;

            default:
                return $this->peso;
                break;
        }
    }

    /**
     * Retorna o peso cúbico em gramas.
     *
     * @param int $fatorCubico
     * @return float
     */
    public function getPesoCubico($fatorCubico = 6000) {

        if (is_null($this->getAltura()) || is_null($this->getLargura()) || is_null($this->getComprimento())) {
            $this->updateAtributes();
        }

        echo "<pre>";
        var_dump($this->getAltura());
        die;

        return ((($this->getAltura() * $this->getLargura() * $this->getComprimento()) / 6000) * 1000);

    }

    /**
     * @param int $altura
     */
    public function setAltura($altura)
    {
        $this->altura = $altura;
    }

    /**
     * @return int
     */
    public function getAltura()
    {
        return $this->altura;
    }

    /**
     * @param int $comprimento
     */
    public function setComprimento($comprimento)
    {
        $this->comprimento = $comprimento;
    }

    /**
     * @return int
     */
    public function getComprimento()
    {
        return $this->comprimento;
    }

    /**
     * @param int $largura
     */
    public function setLargura($largura)
    {
        $this->largura = $largura;
    }

    /**
     * @return int
     */
    public function getLargura()
    {
        return $this->largura;
    }

    /**
     * @param PackageClient $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * @return PackageClient
     */
    public function getClient()
    {
        return $this->client;
    }


    /**
     * @param PackageItem $item
     * @return $this
     */
    public function addItem(PackageItem $item) {
        $this->collPackageItems[$item->getIdentifier()] = serialize($item);
        $this->updateAtributes();
        return $this;
    }

    /**
     * @param $item
     * @return $this
     */
    public function removeItem($item) {
        if ($this->_contains($item)) {
            unset($this->collPackageItems[$item]);
        }
        $this->initialize();
        return $this;
    }

    /**
     * @param $item
     * @return null
     */
    public function getItem($item) {
        if ($this->_contains($item)) {
            return unserialize($this->collPackageItems[$item]);
        }
        return null;
    }

    /**
     * @return mixed
     */
    public function getAllItems() {
        if (!is_array($this->collPackageItems)) {
            $this->collPackageItems = array();
        }
        return array_map('unserialize', $this->collPackageItems);
    }

    public function hasItems() {
        return count($this->getAllItems()) > 0;
    }

    /**
     * @param $item
     * @return mixed
     */
    private function _resolveIdentifier($item) {
        if ($item instanceof PackageItem) {
            $item = $item->getIdentifier();
        }
        return $item;
    }

    /**
     * @param $item
     * @return bool
     */
    private function _contains($item) {
        $item = $this->_resolveIdentifier($item);
        return isset($this->collPackageItems[$item]);
    }

}