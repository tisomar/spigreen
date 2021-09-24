<?php

namespace ClickmarkApi\Models;

class User
{
    /**
     * @var string
     */
    private $name = '';
    /**
     * @var string
     */
    private $email = '';
    /**
     * @var string
     */
    private $password = '';
    /**
     * @var int
     */
    private $empresas_id = 2;
    /**
     * @var int
     */
    private $roles_id = 2;

    /**
     * User constructor.
     * @param string $name
     * @param string $email
     * @param string $password
     * @param int $empresas_id
     */
    public function __construct(string $name, string $email, string $password)
    {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
    }

    public function getName(): string
    {
        return $this->name;
    }
    
    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }
    
    public function setEmail(string $email): self
    {
        $this->email = $email;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
    
    public function setPassword(string $password): self
    {
        $this->password = $password;
        return $this;
    }

    public function getEmpresasId(): int
    {
        return $this->empresas_id;
    }
    
    public function setEmpresasId(int $empresas_id): self
    {
        $this->empresas_id = $empresas_id;
        return $this;
    }

    public function getRolesId(): int
    {
        return $this->roles_id;
    }
    
    public function setRolesId(int $roles_id): self
    {
        $this->roles_id = $roles_id;
        return $this;
    }
}