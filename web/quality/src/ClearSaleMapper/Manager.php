<?php
namespace ClearSaleMapper;

class Manager
{
    /**
     * @var Manager
     */
    protected static $instance;

    /**
     * @var array
     */
    public $meta = [];

    protected function __construct()
    {
    }

    /**
     * @return Manager
     */
    public static function getInstance()
    {
        if (!is_null(self::$instance)) {
            return self::$instance;
        }
        return self::$instance = new self();
    }

    /**
     * @param string $key
     * @return string[]|string|null
     */
    public static function get($key = null)
    {
        $instance = self::getInstance();
        if (is_null($key)) {
            return $instance->meta;
        }
        if (!array_key_exists($key, $instance->meta)) {
            return null;
        }
        return $instance->meta[$key];
    }

    /**
     * @param string $key
     * @param string $value
     */
    public static function set($key, $value)
    {
        $instance = self::getInstance();
        $instance->meta[$key] = $value;
    }
}
