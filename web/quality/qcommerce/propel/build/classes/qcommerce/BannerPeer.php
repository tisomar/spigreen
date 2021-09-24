<?php

/**
 * Skeleton subclass for performing query and update operations on the 'QP1_BANNER' table.
 *
 * Banners do sistema
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class BannerPeer extends BaseBannerPeer
{

    // TIPO
    CONST DESTAQUE = 'DESTAQUE';
    CONST LATERAL = 'LATERAL';
    CONST VANTAGEM = 'VANTAGEM';
    CONST APOIO = 'APOIO';
    CONST RODAPE = 'RODAPE';
    // TARGET
    CONST TARGET_BLANK = '_blank';
    CONST TARGET_SELF = '_self';
    CONST TARGET_IFRAME = 'iframe';

    /**
     * retorna uma tag select para campo mostrar
     * @param string $strValueSelected
     * @param array $arrOptions
     * @param array $arrAttributtes
     * @return string
     */
    public static function getFormSelectMostrar($strValueSelected, $arrOptions = false, $arrAttributtes = array())
    {
        $arrAttributtes['name'] = isset($arrAttributtes['name']) ? $arrAttributtes['name'] : 'banner[MOSTRAR]';
        $arrAttributtes['id'] = isset($arrAttributtes['id']) ? $arrAttributtes['id'] : 'mostrar';
        $arrAttributtes['title'] = isset($arrAttributtes['title']) ? $arrAttributtes['title'] : 'Define se o banner estará disponivel no site ou não.';
        $arrAttributtes['class'] = isset($arrAttributtes['tooltip']) ? $arrAttributtes['tooltip'] : 'tooltip';

        if ($arrOptions === false)
        {
            $arrOptions = array(
                Banner::SIM => Banner::getDescConstMostrar(Banner::SIM),
                Banner::NAO => Banner::getDescConstMostrar(Banner::NAO),
            );
        }
        return get_form_select($arrOptions, $strValueSelected, $arrAttributtes);
    }

    /**
     * Faz um select padrao para busca de banners do site. filtra por mostrar, imagem != ''
     * @param integer $intLimit
     * @param Criteria $c
     * @return array 
     */
    public static function doSelectDefault($intLimit = false, $c = false)
    {
        if (!$c)
        {
            $c = new Criteria();
        }

        if ($intLimit)
        {
            $c->setLimit($intLimit);
            $c->add(BannerPeer::TIPO, Banner::DESTAQUE);
        }

        // mostrar = SIM
        $c->add(self::MOSTRAR, Banner::SIM);

        // ordenados pelo campo ordem
        $c->addAscendingOrderByColumn(self::ORDEM);

        return self::doSelect($c);
    }

    public static function getTipoList()
    {
        return array(
            self::DESTAQUE  => 'Banner Principal',
            self::VANTAGEM  => 'Barra de Vantagens',
            self::APOIO     => 'Banner de Apoio  (abaixo do Banner Principal)',
            self::RODAPE    => 'Banner do Rodapé',
        );
    }

    public static function getTipoDescricao($option)
    {
        $a = self::getTipoList();

        if (!isset($a[$option]))
        {
            return null;
        }

        return $a[$option];
    }

    public static function getLabelTipo($option) {

        $label = array (
            self::DESTAQUE => '<h4><label class="label label-primary">'.self::getTipoDescricao(self::DESTAQUE).'</label></h4>',
            self::VANTAGEM => '<h4><label class="label label-info">'.self::getTipoDescricao(self::VANTAGEM).'</label></h4>',
            self::APOIO => '<h4><label class="label label-warning">'.self::getTipoDescricao(self::APOIO).'</label></h4>',
            self::RODAPE => '<h4><label class="label label-danger">'.self::getTipoDescricao(self::RODAPE).'</label></h4>',
        );

        if (isset($label[$option])) {
            return $label[$option];
        }

        return $label;
    }

    public static function getDimensaoList()
    {

        return array(
            self::DESTAQUE => '1920x480 pixels (LxA)',
            self::VANTAGEM => '1140x89 pixels (LxA)',
            self::APOIO => '390x260 pixels (LxA)',
            self::RODAPE => '390x260 pixels (LxA)',
        );
    }

    public static function getMostrarList()
    {
        return array(
            "1" => 'Sim',
            "0" => 'Não',
        );
    }

    public static function getTargetList()
    {

        return array(
            self::TARGET_SELF => 'na mesma página',
            self::TARGET_BLANK => 'em uma nova página',
            self::TARGET_IFRAME => 'em um iframe',
        );
    }

    public static function getTargetDescricao($option)
    {

        $a = self::getTargetList();

        if (!isset($a[$option]))
        {
            return null;
        }

        return $a[$option];
    }

    /**
     * Adiciona +1 no contador de cliques por banner
     * 
     * @param int $bannerId
     * @return boolean Retorna TRUE se conseguiu contabilizar o click.
     */
    public static function click($bannerId)
    {
        $banner = BannerQuery::create()->findOneById($bannerId);

        if ($banner instanceof Banner)
        {
            $countClick = $banner->getCountClick() + 1;
            $banner->setCountClick($countClick);
            $banner->save();

            return true;
        }

        return false;
    }

}
