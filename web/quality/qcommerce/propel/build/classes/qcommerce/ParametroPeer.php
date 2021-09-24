<?php

/**
 * Classe para padronizar a utilização de parâmetros do sistema
 * 
 * @author Felipe Corrêa
 * @since 07/03/2013
 * 
 * @package    propel.generator.qcommerce
 */
class ParametroPeer extends BaseParametroPeer
{
    public static $arrayValores = array();
    
    /**
     * Carrega os paramêtros que estão configurados com Autoload igual a 1
     * para um array estático de modo a gerar cache futuro da requisição dos 
     * parâmetros
     * 
     * @author Felipe Corrêa
     * @since 07/03/2013
     * 
     * @return void
     */
    public static function carregarParametros()
    {
        $objParametros = ParametroQuery::create()->filterByIsAutoload(true)->find();
        
        foreach ($objParametros as $objParametro) /* @var $objParametro Parametro */
        {
            self::setParametro($objParametro->getAlias(), $objParametro->getValor());
        }
    }
    
    
    /**
     * Retorna o valor do parâmetro solicitado
     * 
     * @author Felipe Corrêa
     * @since 07/03/2013
     * 
     * @param  String $parametro    Nome do parâmetro
     * @param  bool   $obrigatorio  Caso o parâmetro seja obrigatório e não for encontrado, então lançará uma excessão
     * @return mixed  Retorna false caso o parâmetro não existir
     */
    public static function getParametro($parametro, $obrigatorio = false)
    {
        
        $valorRetorno = '';
        
        // Carrega parâmetros padrões caso o array esteja vazio
        if (empty(self::$arrayValores))
        {
            self::carregarParametros();
        }
        
        // Parâmetro solicitado já está carregando, então retorna-o
        if (array_key_exists($parametro, self::$arrayValores) === true)
        {
            return self::$arrayValores[$parametro];
        }
        // Verifica se o parâmetro existe nos parâmetros cadastrados, 
        // e caso exitir retorna-o e adiciona ao array
        else
        {
            $objParametro = ParametroQuery::create()->findOneByAlias($parametro);
            
            if (!is_null($objParametro))
            {
                // Adiciona ao array
                self::setParametro($objParametro->getAlias(), $objParametro->getValor());
                
                return $objParametro->getValor();
            }
        }
        
        if ($obrigatorio == true)
        {
            throw new Exception("The parameter {$parametro} is required, but it wasnt found in the system.");
        }
        
        return false;
    }
    
    /**
     * Seta parâmetro ao array
     * 
     * @param type $parametro
     * @param type $valor
     */
    public static function setParametro($parametro, $valor)
    {
        self::$arrayValores[$parametro] = $valor;
    }
    
    /**
     * Criando atalho para através de qualquer método estático não existente
     * seja possível pegar um parâmetro
     * 
     * @uses ParametroPeer::param() Permite selecionar um parâmetro através de 
     *                              qualquer método estático
     * 
     * @author Felipe Corrêa
     * @since 07/03/2013
     */
    public static function __callStatic($fn, $args)
    {
        return call_user_func_array(array('ParametroPeer', 'getParametro'), array($args[0]));
    }
    
}
