<?php

namespace QPress\Carrinho\Storage;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CarrinhoStorage implements CarrinhoStorageInterface
{
    const KEY = '_qcommerce.carrinho-id';

    /**
     * @var string
     */
    protected $key;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @param SessionInterface $session
     * @param string           $key
     */
    
    public function __construct(SessionInterface $session, $key = self::KEY)
    {
        
        $this->session = $session;
        $this->key = $key;
    }

    /**
     * {@inheritdoc}
     */
    public function getCurrentCarrinhoId()
    {
        return $this->session->get($this->key);
    }

    /**
     * {@inheritdoc}
     */
    public function setCurrentCarrinhoId(\Carrinho $carrinho)
    {
        $this->session->set($this->key, $carrinho->getId());
    }

    /**
     * {@inheritdoc}
     */
    public function resetCurrentCarrinhoId()
    {
        $this->session->remove($this->key);
    }

    /**
     * Verifica se o carrinho atual está contido na sessão
     * @return booleans TRUE para caso esteja
     */
    public function hasCarrinhoId()
    {
        return $this->session->has($this->key);
    }
	
	/**
	 * Verifica se o pedido é de algum franqueado
	 * @return booleans TRUE para caso esteja
	 */
	public function hasFromFranqueado()
	{
		return $this->session->has('fromFranqueado');
	}
	
	/**
	 * Retorna o slug do franqueado
	 * @return booleans TRUE para caso esteja
	 */
	public function getSlugFranqueado()
	{
		return $this->session->get('slugFranqueado');
	}
	
	/**
	 * {@inheritdoc}
	 */
	public function resetSessionFranqueado()
	{
		$this->session->remove('fromFranqueado');
		$this->session->remove('slugFranqueado');
	}
}
