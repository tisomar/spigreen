<?php



/**
 * Skeleton subclass for performing query and update operations on the 'qp1_google_shopping_item' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class GoogleShoppingItemPeer extends BaseGoogleShoppingItemPeer
{
    public static function getAdultoOptions() {
        return array (
            'NAOINFORMADO' => 'Não informado',
            'SIM' => 'Sim',
            'NAO' => 'Não'
        );
    }

    public static function getFaixaEtariaOptions() {
        return array (
            'NAOINFORMADO' => 'Não informado',
            'RECEMNASCIDO' => 'Recem Nascido',
            '3TO12M' => 'De 3 a 12 Meses',
            '1TO5Y' => 'De 1 a 5 Anos',
            'CHILDREN' => 'Infantil',
            'ADULT' => 'Adulto'
        );
    }

    public static function getGeneroOptions() {
        return array (
            'NAOINFORMADO' => 'Não informado',
            'MASCULINO' => 'Masculino',
            'FEMININO' => 'Feminino',
            'UNISEX' => 'Unisex'
        );
    }

    public static function getCondicaoOptions() {
        return array (
            'NOVO' => 'Novo',
            'USADO' => 'Usado',
            'RECONDICIONADO' => 'Recondicionado'
        );
    }

    public static function translateProperty($propertyName, $value) {
        switch($propertyName) {
            case self::CONDICAO:
                switch($value) {
                    case 'NOVO': return 'new';
                    case 'USADO': return 'used';
                    case 'RECONDICIONADO': return 'refurbished';
                }
                break;

            case self::GENERO:
                switch($value) {
                    case 'MASCULINO': return 'male';
                    case 'FEMININO': return 'female';
                    case 'UNISEX': return 'unisex';
                }
                break;

            case self::FAIXA_ETARIA:
                switch($value) {
                    case 'RECEMNASCIDO': return 'newborn';
                    case '3TO12M': return 'infant';
                    case '1TO5Y': return 'toddler';
                    case 'CHILDREN': return 'kids';
                    case 'ADULT': return 'adult';
                }
                break;

            case self::ADULTO:
                switch($value) {
                    case 'SIM': return 'TRUE';
                    case 'NAO': return 'FALSE';
                }
                break;
        }
        return '';
    }
}
