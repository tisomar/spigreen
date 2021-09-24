<?php

namespace ClickmarkApi\Rest;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;

use ClickmarkApi\Credentials;
use ClickmarkApi\Contracts\SenderInterface;
use ClickmarkApi\Models\User;

class UserRegistry extends SenderInterface
{
    /**
     * @var User
     */
    private $user;

    /**
     * UserRegistry constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * method request to server
     * @return object
     */
    public function send(): string
    {
        try {
            $client = new Client();
            $response = $client->post(
                Credentials::URL . 'api/users',
                array_merge(parent::OPTIONS, [
                    RequestOptions::QUERY => [
                        'email' => $this->user->getEmail(),
                        'password' => $this->user->getPassword(),
                        'name' => $this->user->getName(),
                        'empresas_id' => $this->user->getEmpresasId(),
                        'roles_id' => $this->user->getRolesId(),
                    ]
                ])
            );
            return ($response->getBody()->getContents());
        } catch (\Exception $ex) {
            throw $ex;
        }
    }
}
