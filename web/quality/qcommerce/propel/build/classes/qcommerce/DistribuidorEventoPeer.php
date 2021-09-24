<?php



/**
 * Skeleton subclass for performing query and update operations on the 'qp1_distribuidor_evento' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class DistribuidorEventoPeer extends BaseDistribuidorEventoPeer
{
    public static function getSubjects() {

        $assuntos = array(
            array(
                'text' => 'ligar',
                'icon' => 'entypo-phone',
                'category' => 'phone',
                'class' => 'bg-secondary'),
            array(
                'text' => 'email',
                'icon' => 'entypo-mail',
                'category' => 'mail',
                'class' => ''),
            array(
                'text' => 'reuniao',
                'icon' => 'entypo-users',
                'category' => 'users',
                'class' => ''),
            array(
                'text' => 'sms',
                'icon' => 'entypo-mobile',
                'category' => 'mobile',
                'class' => ''),
            array(
                'text' => 'entrega',
                'icon' => 'entypo-newspaper',
                'category' => 'newspaper',
                'class' => '')
        );

        return $assuntos;
    }

    public static function getSubjectByText($text) {

        foreach(self::getSubjects() as $subject) {
            if($subject['text'] == $text) {
                return $subject;
            }
        }

        return null;
    }
}
