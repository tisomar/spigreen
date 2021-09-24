<?php

namespace QPress\Gateway\Services\PayPal\Response;

use QPress\Gateway\Response\AbstractResponse;

/**
 * Description of Response
 *
 * @author Garlini
 */
class Response extends AbstractResponse
{
    public function getStatus()
    {
        
    }

    public function getUrl()
    {
        
    }

    /**
     * 
     * @return bool
     */
    public function isSuccessful()
    {
        return (isset($this->data['successful'])) ? (bool)$this->data['successful'] : false;
    }
    
    public function isRedirect()
    {
        return isset($this->data['url']);
    }
    
    public function redirect()
    {
        if (isset($this->data['url'])) {
            header('Location: ' . $this->data['url']);
            exit;
        }
        
        throw new \LogicException('URL não definida.');
    }
    
    /**
     * 
     * @return string
     */
    public function getMessage() 
    {
        return isset($this->data['message']) ? $this->data['message'] : '';
    }

}
