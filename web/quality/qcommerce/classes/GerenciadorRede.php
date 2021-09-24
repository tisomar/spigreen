<?php

/*
 * Classe que gerencia a inserção de clientes na rede de clientes.
 */

use Monolog\Logger;

/**
 * Description of GerenciadorRede
 *
 * @author André Garlini
 */
class GerenciadorRede
{
    /** @var PropelPDO */
    protected $con;

    /** @var Logger monolog */
    private $logger;

    /**
     * GerenciadorRede constructor.
     *
     * @param PropelPDO $con
     * @param Logger $logger
     */
    public function __construct(PropelPDO $con, Logger $logger)
    {
        $this->con = $con;
        $this->logger = $logger;
    }

    public function insereRoot(Cliente $cliente)
    {
        $this->con->beginTransaction();

        if (ClienteQuery::create()->findRoot($this->con)) :
            $this->con->rollBack();
            throw new LogicException('A rede já possui um cliente root.');
        endif;

        $this->lockRede();
        $cliente->makeRoot();
        $cliente->save($this->con);
        $this->unlockRede();
        $this->con->commit();
    }

    /**
     * Insere o cliente passado como argumento na rede de clientes. Retorna o patrocinador do cliente inserido.
     * Atenção: o patrocinador retornado pode ser diferente do passado como argumento ($patrocinadorSolicitado).
     * Caso o patrocinador informado não possa mais ter patrocinados, um patrocinador diferente é escolhido.
     *
     * @param Cliente $cliente
     * @param Cliente|null $patrocinadorSolicitado
     * @param bool $enviarNotificacoes
     * @return Cliente|null
     * @throws PropelException
     */
    public function insereRede(Cliente $cliente, Cliente $patrocinadorSolicitado = null, $enviarNotificacoes = true)
    {
        if ($cliente->isInTree()) :
            $this->logger->info('Este cliente já possui um patrocinador.');
        endif;

        if ($patrocinadorSolicitado && !$patrocinadorSolicitado->isInTree()) :
            $this->logger->info('O patrocinador não está inserido na rede.');
        endif;

        if (!$patrocinadorSolicitado) {
            $patrocinador = $this->procuraPatrocinadorDisponivel();

            if (!$patrocinador) :
                //A rede está vazia. Faz o cliente ser o root e retorna null.
                $this->insereRoot($cliente);

                return null;
            endif;
        } else {
            $patrocinador = $patrocinadorSolicitado;
        }

        $this->con->beginTransaction();
        $this->lockRede();
        $cliente->insertAsLastChildOf($patrocinador);
        $cliente->setClienteIndicadorId($patrocinador->getId());
        $cliente->setClienteIndicadorDiretoId($patrocinador->getId());
        $cliente->save($this->con);
        $this->unlockRede();
        $this->con->commit();

        // TODO: setup new local development environment for nginx.
        // TODO: setup Logger to constructor.
        if (getenv('APPLICATION_ENV') != 'dev') :
            if ($enviarNotificacoes) {
                //notifica o patrocinador
                try {
                    \QPress\Mailing\Mailing::enviarDadosNovoPatrocinado($cliente->getClienteRelatedByClienteIndicadorDiretoId(), $cliente);
                } catch (\Exception $e) {
                    $this->logger->error($e->getMessage());
                }

                //notifica o cliente
                try {
                    \QPress\Mailing\Mailing::enviarDadosPatrocinador($cliente, $cliente->getClienteRelatedByClienteIndicadorDiretoId());
                } catch (\Exception $e) {
                    $this->logger->error($e->getMessage());
                }
            }
        endif;

        return $patrocinador;
    }

    /**
     *
     * @param Cliente $cliente
     * @param string $id
     * @param bool $exibirPlano Exibir o plano ativo abaixo do nome.
     * @return string
     */
    public function geraHTMLRede(Cliente $cliente, $id = 'rede-clientes', $exibirPlano = false)
    {
        /*
         * Exemplo do formato:
         *
            <ul id="org">
              <li>Food:
                <ul>
                  <li>Beer</li>
                  <li class=collapsed>Vegetables
                    <ul>
                      <li>Carrot</li>
                      <li>Pea</li>
                    </ul>
                  </li>
                  <li>Chocolate</li>
                </ul>
              </li>
            </ul>
         *
         */

        $descCliente = function (Cliente $cliente) use ($exibirPlano) {
            if ($exibirPlano && ($plano = $cliente->getPlano())) {
                return sprintf('%s<br><small>%s</small><br><small>%s</small>', escape($cliente->getNomeCompleto()), escape($cliente->getEmail()), escape($plano->getNome()));
            }
            return escape($cliente->getNomeCompleto());
        };

        $geraHtmlFilhos = function (Cliente $base) use (&$geraHtmlFilhos, $descCliente) {
            $html = '<ul>';
            /**
             * @var $child Cliente
             */
            foreach ($base->getChildren() as $child) {
                if(!$child->getPlano() || $child->getPlano()->getPlanoClientePreferencial()) :
                   continue;
                endif;

                if ($child->isLeaf()) {
                    $html .= '<li>' . $descCliente($child) . '</li>';
                } else {
                    $html .= '<li>' . $descCliente($child) . $geraHtmlFilhos($child) . '</li>';
                }
            }
            $html .= '</ul>';
            return $html;
        };

        return sprintf(
            '<ul id="%s"><li>%s%s</li></ul>',
            escape($id),
            $descCliente($cliente),
            $cliente->isLeaf() ? '' : $geraHtmlFilhos($cliente)
        );
    }

    /**
     * Retorna o total de clientes que o cliente passado como argumento é o patrocinador direto.
     *
     * @param Cliente $cliente
     * @return int
     */
    public function getTotalPatrocinadosDiretos(Cliente $cliente)
    {
        return (int)ClienteQuery::create()
            ->filterByClienteRelatedByClienteIndicadorDiretoId($cliente)
            ->count();
    }

    /**
     * Procura um patrocinador disponivel usando como base o argumento $base.
     * O patrocinador retornado será o primeiro patrocinador sem "filhos" e com o nivel de arvore mais baixo.
     *
     * @param Cliente $base
     * @return Cliente|null
     * @throws PropelException
     */
    protected function procuraPatrocinadorDisponivel(Cliente $base = null)
    {
        return ClienteQuery::create()->findOneById(213);
    }

    /**
     * Como podemos alterar muitos registros quando inserimos um cliente na rede,
     * vamos executar um lock na rede para tentar evitar deixar a arvore inconsistente em caso de inserções simutaneas.
     */
    public function lockRede()
    {
        $sql = sprintf('LOCK TABLE %s WRITE', ClientePeer::TABLE_NAME);
        $this->con->exec($sql);
    }

    public function unlockRede()
    {
        $this->con->exec('UNLOCK TABLES');
    }


    /**
     * Insere o cliente passado como argumento na rede de clientes. Retorna o patrocinador do cliente inserido.
     * Atenção: o patrocinador retornado pode ser diferente do passado como argumento ($patrocinadorSolicitado).
     * Caso o patrocinador informado não possa mais ter patrocinados, um patrocinador diferente é escolhido.
     *
     * @param Cliente $cliente
     * @param Cliente $patrocinadorSolicitado
     * @return \Cliente|null Patrocinador do cliente inserido.
     * @throws LogicException
     */
    public function insereRedePreCadastro(Cliente $cliente, Cliente $patrocinadorSolicitado = null)
    {
        if ($cliente->isInTree()) {
            throw new LogicException('Este cliente já possui um patrocinador.');
        }

        if ($patrocinadorSolicitado && !$patrocinadorSolicitado->isInTree()) {
            throw new LogicException('O patrocinador não está inserido na rede.');
        }

        if (!$patrocinadorSolicitado) {
            throw new LogicException('O patrocinador não foi informado.');
        } else {
            $children = $patrocinadorSolicitado->getChildren(null, $this->con);
            $preCadastroPatrocinador = PreCadastroClienteQuery::create()->findOneByClienteId($patrocinadorSolicitado->getId());

            $validatePreCadastro = false;

            if ($preCadastroPatrocinador instanceof PreCadastroCliente) {
                if (!$preCadastroPatrocinador->isConcluido()) {
                    $validatePreCadastro = true;
                }
            }

            if ($validatePreCadastro) {
                if (count($children) >= 1) {
                    //Um patrocinador  no pré cadastro nao pode ter mais que um filho. Temos que encontrar outro patrocinador.

                    //Verifica se o patrocinador solicitado configurou o lado que deseja inserir os patrocinados
                    $patrocinador = $this->procuraPatrocinadorDisponivelPreCadastro($patrocinadorSolicitado);

                    if (!$patrocinador || !$patrocinador->isInTree()) {
                        throw new RuntimeException('Não foi possível encontrar um patrocinador.'); //acredito que isso nunca deveria acontecer.
                    }
                } else {
                    $patrocinador = $patrocinadorSolicitado; //podemos usar o patrocinador solicitado.
                }
            } else {
                if (count($children) >= 2) {
                    //Um patrocinador nao pode ter mais que dois filhos. Temos que encontrar outro patrocinador.

                    //Verifica se o patrocinador solicitado configurou o lado que deseja inserir os patrocinados
                    if ($patrocinadorSolicitado->getLadoInsercaoCadastrados() != Cliente::LADO_AUTOMATICO) {
                        if ($patrocinadorSolicitado->getLadoInsercaoCadastrados() == Cliente::LADO_DIREITO) {
                            $childEscolhido = $children->getLast();
                        } else {
                            $childEscolhido = $children->getFirst();
                        }
                        if ($childEscolhido->countChildren(null, $this->con) > 0) {
                            //o filho escolhido não pode ser patrocinador. Procura um disponivel.
                            $patrocinador = $this->procuraPatrocinadorDisponivel($childEscolhido);
                        } else {
                            //o proprio filho pode ser patrocinador. Retorna ele.
                            $patrocinador = $childEscolhido;
                        }
                    } else { // lado está configurado como automatico. Faz a busca iniciando do proprio patrocinador solicitado.
                        $patrocinador = $this->procuraPatrocinadorDisponivel($patrocinadorSolicitado);
                    }

                    if (!$patrocinador || !$patrocinador->isInTree()) {
                        throw new RuntimeException('Não foi possível encontrar um patrocinador.'); //acredito que isso nunca deveria acontecer.
                    }
                } else {
                    $patrocinador = $patrocinadorSolicitado; //podemos usar o patrocinador solicitado.
                }
            }
        }

        $this->con->beginTransaction();

        $this->lockRede();

        $cliente->insertAsLastChildOf($patrocinador);
        $cliente->setClienteRelatedByClienteIndicadorId($patrocinador);

        if ($patrocinadorSolicitado) {
            //Esta associacao sempre vai ficar com a patrocinador solicitado, mesmo que o cliente tenha ficado abaixo de outro patrocinador na rede (retorno desta funcao).
            $cliente->setClienteRelatedByClienteIndicadorDiretoId($patrocinadorSolicitado);
        } else {
            $cliente->setClienteRelatedByClienteIndicadorDiretoId($patrocinador);
        }

        $cliente->save($this->con);

        $this->unlockRede();

        $this->con->commit();

        return $patrocinador;
    }

    /**
     * Procura um patrocinador disponivel usando como base o argumento $base.
     * O patrocinador retornado será o primeiro patrocinador sem "filhos" e com o nivel de arvore mais baixo.
     *
     * @param Cliente $base
     * @return Cliente|null
     */
    protected function procuraPatrocinadorDisponivelPreCadastro(Cliente $base = null)
    {
        if ($base === null) {
            $base = ClienteQuery::create()->findRoot($this->con);
            if (!$base) {
                //A arvore está vazia. Não temos como encontrar um patrocinador.
                return null;
            }
            //verifica se o proprio root pode ser o patrocinador
            if ($base->countChildren() < 1) {
                return $base;
            }
        }

        if (!$base->isInTree()) {
            throw new LogicException('Patrocinador base não está inserido na rede.');
        }

        $query = ClienteQuery::create()
            //sem filhos
            ->where('Cliente.treeRight - Cliente.treeLeft = 1')

            //descendentes de base
            ->where('Cliente.treeLeft > ?', $base->getTreeLeft())
            ->where('Cliente.treeLeft < ?', $base->getTreeRight())
            ->orderBy('Cliente.treeLevel');

        return $query->findOne($this->con);
    }
}
