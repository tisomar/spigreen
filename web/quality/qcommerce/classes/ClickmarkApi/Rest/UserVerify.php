<?php

namespace ClickmarkApi\Rest;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

use ClickmarkApi\Credentials;
use ClickmarkApi\Contracts\SenderInterface;

class UserVerify extends SenderInterface
{
    private $email;

    public function __construct(string $email)
    {
        $this->email = $email;
    }

    /**
     * @return string
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function send(): string
    {
        try {
            $client = new Client();
            $response = $client->post(
                Credentials::URL . 'api/users/verify',
                array_merge(parent::OPTIONS, [
                    RequestOptions::QUERY => [
                        'email' => $this->email
                    ]
                ])
            );
            return ($response->getBody()->getContents());
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
