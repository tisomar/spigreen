<?php

namespace QPress\Gateway\Services\BoletoPHP;

use QPress\Gateway\Response\AbstractResponse;

/**
 * This file is part of the QualityPress package.
 * 
 * (c) Jorge Vahldick
 * 
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class Response extends AbstractResponse
{

    public function isSuccessful() {
        return true;
    }

    public function getStatus() {
        return $this->data->status;
    }

    public function getUrl() {
        return $this->data->url_acesso;
    }

    public function getTransactionReference()
    {
        return $this->data->id;
    }

}