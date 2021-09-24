<?php

namespace ClickmarkApi\Contracts;

abstract class SenderInterface
{

    const OPTIONS = [
        'allow_redirects' => false,
        'debug' => false,
        'verify' => false,
        'decode_content' => true,
        'http_errors' => false,
        'headers' => [
            'Content-Type' => 'application/json'
        ]
    ];

    /**
     * method request to server
     * @return object
     */
    abstract public function send() : string;
}
