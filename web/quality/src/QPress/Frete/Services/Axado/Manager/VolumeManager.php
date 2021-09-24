<?php
namespace QPress\Frete\Services\Axado\Manager;

use QPress\Frete\Services\Axado\Volume;

class VolumeManager {

    private $objects = array();
    private $total_price = 0;
    private $number_objects = 0;

    function __construct($volumes = null)
    {
        if (!empty($volumes) && (is_object($volumes) || is_array($volumes))) {
            foreach ($volumes as $volume) {
                $this->add(new Volume($volume));
            }
        }
    }

    public function getTotalPrice()
    {
        return $this->total_price;
    }

    /**
     * @return array
     */
    public function getObjects()
    {
        return $this->objects;
    }

    /**
     * @return int
     */
    public function getNumberObjects()
    {
        return $this->number_objects;
    }

    /**
     * @param Volume $v
     */
    public function add(Volume $v)
    {
        $this->objects[] = $v;
        $this->total_price += $v->getPreco();
        $this->number_objects++;
    }

    public function toArray() {
        $output = array();

        if ($this->getNumberObjects()) {
            /** @var $object Volume */
            foreach ($this->getObjects() as $object) {
                $output[] = $object->toArray();
            }
        }

        return $output;

    }

} 