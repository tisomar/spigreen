<?php

class Config
{

    const PRODUTO_LAYOUT_VARIACAO_GRADE = 1;
    const PRODUTO_LAYOUT_VARIACAO_CAIXA = 2;
    const PRODUTO_LAYOUT_VARIACAO_AMBOS = 3;

    const CLIENTES_TIPO_CADASTRO_AMBOS = 1;
    const CLIENTES_TIPO_CADASTRO_PF = 2;
    const CLIENTES_TIPO_CADASTRO_PJ = 3;

    private static $values = null;

    public static function loadParameters()
    {
        $objParametros = ParametroQuery::create()->filterByIsAutoload(true)->find();
        foreach ($objParametros as $objParametro) { /* @var $objParametro Parametro */
            self::setParametro($objParametro->getAlias(), $objParametro->getValor());
        }
    }

    public static function get($key, $obrigatory = false)
    {
        if (is_null(self::$values)) {
            self::loadParameters();
        }

        if (array_key_exists($key, self::$values) === true) {
            return self::$values[$key];
        } else {
            $objParametro = ParametroQuery::create()->findOneByAlias($key);
            if (!is_null($objParametro)) {
                self::setParametro($objParametro->getAlias(), $objParametro->getValor());
                return $objParametro->getValor();
            }
        }

        if ($obrigatory) {
            throw new Exception("The parameter {$key} is required, but it wasnt found in the system.");
        }

        return false;
    }

    public static function setParametro($alias, $value)
    {
        self::$values[$alias] = $value;
    }

    public static function __callStatic($fn, $args)
    {
        return call_user_func_array(array('Config', 'get'), array($args[0]));
    }

    private static $logo = null;
    public static function getLogo()
    {
        if (self::$logo == null) {
            self::$logo = ParametroQuery::create()->findOneByAlias('sistema.logo');
        }
        return self::$logo;
    }

    private static $logoMobile = null;
    public static function getLogoMobile()
    {
        if (self::$logoMobile == null) {
            self::$logoMobile = ParametroQuery::create()->findOneByAlias('sistema.logo_mobile');
        }

        if (!self::$logoMobile->isImagemExists()) {
            self::$logoMobile = self::getLogo();
        }

        return self::$logoMobile;
    }

    private static $favicon = null;
    public static function getFavicon()
    {
        if (self::$favicon == null) {
            self::$favicon = ParametroQuery::create()->findOneByAlias('sistema.favicon');
        }
        return self::$favicon;
    }

    public static function getGateway()
    {
        return 'superpay';
        #return 'pagseguro';
    }

    /**
     * @return \QPress\Container\Container|\Symfony\Component\DependencyInjection\ContainerBuilder
     */
    public static function getContainer()
    {
        global $container;
        return $container;
    }

    /**
     * @return \QPress\Container\Container|\Symfony\Component\DependencyInjection\ContainerBuilder
     */
    public static function saveParameters()
    {
        $arrParameters = self::$values;

        foreach ($arrParameters as $keys => $values) {
            $objParametro = ParametroQuery::create()->findOneByAlias($keys);
            $objParametro->setValor($values);
            $objParametro->save();
        }
    }

    public static function saveParameter($parameter, $value)
    {

        $objParametro = ParametroQuery::create()->findOneByAlias($parameter);
        if ($objParametro instanceof  Parametro) {
            $objParametro->setValor($value);
            $objParametro->save();
        }

        self::loadParameters();
    }
}
