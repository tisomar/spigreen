<?php

/**
 * This file is part of the QualityPress package.
 *
 * (c) Jorge Vahldick
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace QPress\Facebook;

class Tracking
{

    /**
     * Lista de todos os eventos do tracking.
     *
     * @var array
     */
    protected $enabledEvents = array(
        Event::EVENT_CONTENT,
        Event::EVENT_SEARCH,
        Event::EVENT_ADD_TO_CART,
        Event::EVENT_ADD_TO_WISHLIST,
        Event::EVENT_INIT_CHECKOUT,
        Event::EVENT_PAYMENT_INFO,
        Event::EVENT_PAYMENT_CONFIRMATION,
        Event::EVENT_LEAD,
        Event::EVENT_REGISTRATION_COMPLETE
    );

    /**
     * Instância principal.
     *
     * @var     Tracking
     */
    protected static $instance;

    /**
     * Lista de eventos.
     *
     * @var     array
     */
    protected $events = array();

    /**
     * Retornar instância do objeto.
     *
     * @return  Tracking
     */
    public static function getInstance()
    {
        if (null == self::$instance) {
            $class = __CLASS__;
            self::$instance = new $class;
        }

        return self::$instance;
    }

    /**
     * Atraves de uma string ou lista de eventos, criar eventos e retorná-los.
     *
     * @param   array|string    $events
     * @param   array           $args
     *
     * @return  Event[]
     */
    public function getEvents($events, $args = array())
    {
        if (!$this->isEnabled() && null != $facebookId = $this->getFacebookId()) {
            return array();
        }

        ### String to Array
        $dataEvents = json_decode($events);
        if (json_last_error() == JSON_ERROR_NONE && null != $dataEvents) {
            $this->prepareEventsObject($dataEvents, $args);
        }

        return $this->events;
    }

    /**
     * Localizar o script de conversão por evento.
     *
     * @param   string|array    $events
     * @param   array           $data
     *
     * @return  void
     */
    protected function prepareEventsObject($events, $data = array())
    {
        foreach ($events as $k => $event) {
            if (in_array($event, $this->enabledEvents)) {
                $this->events[] = new Event($event, $data);
            }
        }
    }

    /**
     * Verificar se a exibição de script de conversão está habilitada.
     *
     * @return  bool
     */
    private function isEnabled()
    {
        return (bool) \Config::get('facebook_tracking.enabled');
    }

    /**
     * Localizar o ID de integração com o facebook
     *
     * @return  bool|string
     */
    public function getFacebookId()
    {
        return \Config::get('facebook_tracking.id');
    }

}