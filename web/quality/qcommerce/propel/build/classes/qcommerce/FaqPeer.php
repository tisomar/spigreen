<?php

/**
 * Skeleton subclass for performing query and update operations on the 'QP1_FAQ' table.
 *
 * Perguntas Frequentes
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class FaqPeer extends BaseFaqPeer
{

    public static function listAllActives($page = 1)
    {
        return FaqQuery::create()
                    ->filterByMostrar(true)
                    ->filterByResposta(null, Criteria::NOT_EQUAL)
                    ->orderByOrdem()
                ->paginate($page, 10);
    }
    
    public static function insertQuestion(array $data) {
        
        $erros = array();
        
        $object = new Faq();
        $object->setByArray($data);
        
        if ($object->myValidate($erros)) {
            $object->save();
            return $object;
        }
        
        
        return $erros;
        
    }

    /**
     * retorna uma tag select para campo mostrar
     * @param string $strValueSelected
     * @param array $arrOptions
     * @param array $arrAttributtes
     * @return string
     */
    public static function getFormSelectMostrar($strValueSelected, $arrOptions = false, $arrAttributtes = array())
    {
        $arrAttributtes['name'] = isset($arrAttributtes['name']) ? $arrAttributtes['name'] : 'faq[MOSTRAR]';
        $arrAttributtes['id'] = isset($arrAttributtes['id']) ? $arrAttributtes['id'] : 'mostrar';
        $arrAttributtes['title'] = isset($arrAttributtes['title']) ? $arrAttributtes['title'] : 'Indica se é para mostrar o faq no site ou não';
        $arrAttributtes['class'] = isset($arrAttributtes['tooltip']) ? $arrAttributtes['tooltip'] : 'tooltip';

        if ($arrOptions === false)
        {
            $arrOptions = array(
                Faq::SIM => Faq::getDescConstMostrar(Faq::SIM),
                Faq::NAO => Faq::getDescConstMostrar(Faq::NAO),
            );
        }
        return get_form_select($arrOptions, $strValueSelected, $arrAttributtes);
    }

    public static function getMostrarList()
    {
        return array(
            Faq::SIM => Faq::getDescConstMostrar(Faq::SIM),
            Faq::NAO => Faq::getDescConstMostrar(Faq::NAO),
        );
    }

    public static function sendResponseToClient($faq) {

        if ($faq->getEmail() && $faq->getResposta()) {
            \QPress\Mailing\Mailing::enviarRespostaFaq($faq);
        }
    }

}
