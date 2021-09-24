<?php

use QPress\Container\Container;
use QPress\Correios\CorreiosEndereco;

/**
 * Skeleton subclass for performing query and update operations on the 'QP1_CLIENTE' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class ClientePeer extends BaseClientePeer
{
    const SENHA_TAMANHO_MINIMO = 6;
    
    const CLIENTE_LOGADO = 'CLIENTE_LOGADO';
    
    const STATUS_PENDENTE = 0;
    const STATUS_APROVADO = 1;
    const STATUS_REPROVADO = 2;
    
    /**
     * Retorna cliente do banco de dados de acordo com login e senha de parametros
     *
     * @param string $login O login do cliente
     * @param string $senha a senha do cliente, sera usada função md5() nesse parametro
     * @return Cliente instancia de cliente se existir, null senao
     */
    public static function retrieveByLoginSenha($email, $senha)
    {
        $email = filter_var($email, FILTER_SANITIZE_STRING);
        $senha = filter_var($senha, FILTER_SANITIZE_STRING);

        $senhaCriptografada = (sha1($senha));

        return ClienteQuery::create()
                        ->add(ClientePeer::EMAIL, $email, Criteria::EQUAL)
                        ->add(ClientePeer::SENHA, $senhaCriptografada, Criteria::EQUAL)
                    ->findOne();
    }

    public static function retrieveByEmail($email)
    {
        $c = new Criteria();
        $c->add(self::EMAIL, $email);

        return self::doSelectOne($c);
    }

    /**
     *
     * Indica se tem algum cliente logado no sistema
     *
     * @return boolean
     */
    public static function isAuthenticad()
    {
        return self::getClienteLogado() instanceof Cliente;
    }

    public static function getIsMaster()
    {
        return false;
    }

    /**
     * @param Container $container
     * @return bool|Cliente
     * @throws PropelException
     *
     */
    public static function getFranqueadoSelecionado(Container $container)
    {
        $franqueadoCliente = false;

        if ($container->getSession()->has('fromFranqueado')) {
            $slug = $container->getSession()->get('slugFranqueado');

            $objHotsite = HotsiteQuery::create()
                ->filterBySlug($slug)
                ->findOne();

            if ($objHotsite instanceof Hotsite) {
                $franqueadoCliente = $objHotsite->getCliente();
            }
        }

        return $franqueadoCliente;
    }

    /**
     *
     * Coloca cliente de parametro como cliente logado no sistema
     *
     * @param Cliente $cliente
     */
    public static function setClienteLogado(Cliente $cliente)
    {
        $_SESSION[self::CLIENTE_LOGADO] = serialize($cliente);

        global $container;
        $container->getCarrinhoProvider()->getCarrinho()->recalcularPedido();
    }

    /**
     *
     * Retorna cliente logado no sistema
     *
     * @return Cliente
     */
    public static function getClienteLogado($reloadDB = false)
    {
        return isset($_SESSION[self::CLIENTE_LOGADO]) ?
            ($reloadDB
                ? ClienteQuery::create()->findPk(unserialize($_SESSION[self::CLIENTE_LOGADO])->getId())
                : unserialize($_SESSION[self::CLIENTE_LOGADO])) :
            null;
    }

    /**
     *
     * Ao principo não vai ter cliente bloqueado. Se logo vora incluido, tirar a function
     *
     */

    public static function getBloqueado()
    {
        return false;
    }

    /**
     *
     * Faz logout do cliente na sessão
     *
     */
    public static function doLogout()
    {
        unset($_SESSION[self::CLIENTE_LOGADO]);

        global $container;
        $container->getCarrinhoProvider()->getCarrinho()->recalcularPedido();
    }

    /**
     *
     * Codifica uma string (id)
     *
     * @param string $id
     * @return string codificada
     */
    public static function codificaId($id)
    {
        return base64_encode($id);
    }

    /**
     *
     * Decodifica uma string (id)
     *
     * @param string $id
     * @return string decodificada
     */
    public static function decodificaId($id)
    {
        return base64_decode($id);
    }

    /**
     *
     * Verifica na sessão se tem reseller ativo
     *
     * @param string $id
     * @return string decodificada
     */
    public static function isResellerActived()
    {
        return isset($_SESSION['_sf2_attributes']['resellerLoggedActive'])
            || isset($_SESSION['_sf2_attributes']['resellerActive'])
                ? true
                : false;
    }

    /**
     * Grava o produto visitado pelo cliente
     */
    public static function produtoVisitado($objProduto)
    {
        $objVisitados = ProdutoVisitadoQuery::create()
                ->add(ProdutoVisitadoPeer::PRODUTO_ID, $objProduto->getId())
                ->add(ProdutoVisitadoPeer::CLIENTE_ID, ClientePeer::getClienteLogado()->getId())
                ->findOne();

        if (!$objVisitados) {
            $objVisitado = new ProdutoVisitado();
            $objVisitado->setDataVisitado(date('Y-m-d H:m:s'));
            $objVisitado->setProdutoId($objProduto->getId());
            $objVisitado->setClienteId(ClientePeer::getClienteLogado()->getId());
            if ($objVisitado->validate()) {
                $objVisitado->save();
                return true;
            } else {
                return false;
            }
        }
    }

    /**
     * Utiliza o mesmo algotirmo de setSenha() e permite comparar
     * se uma string externa é igual a senha atualmente gravada
     * no banco de dados
     *
     * @param string $senha Senha em texto normal que será utilizada para gerar o has
     * @return string Retorna uma um hash criado com base na senha informada
     *
     */
    public static function geraHashSenha($senha)
    {
        return (sha1($senha));
    }

    /**
     * Pega o CEP que deve ser setado no campo CEP existente no carrinho
     * Verifica se existe algum CEP setado na sessão, se sim, utiliza-o, senão
     * verifica se o cliente está logado e pega o CEP do endereço principal
     *
     * @return string Retorna o CEP que deve ser utilizado como CEP destinado para o carrinho
     */
    public static function getCepDefaultCarrinho()
    {
        // Verifica se existe um CEP setado na sessão
        if (isset($_SESSION['pedido_info']['cep']) && !empty($_SESSION['pedido_info']['cep'])) {
            return $_SESSION['pedido_info']['cep'];
        }
        // Se o cliente está autenticado, e não possui CEP na sessão, então pega o CEP do endereço principal
        elseif (ClientePeer::isAuthenticad()) {
            $objEnderecoPrincipal = ClientePeer::getClienteLogado()->getEnderecoPrincipal();
            
            if ($objEnderecoPrincipal) {
                return $objEnderecoPrincipal->getCEP();
            }
        }
        

        return '';
    }
    
    
    public static function getStatusList($v = null)
    {
        
        return array(
            self::STATUS_APROVADO => 'Aprovado',
            self::STATUS_REPROVADO => 'Reprovado',
            self::STATUS_PENDENTE => 'Pendente',
        );
    }

    /**
     * @param Cliente $objCliente
     * @param null $enderecoCliente
     * @return array
     * @throws PropelException
     */
    public static function formatterClienteIntegrationBling(Cliente $objCliente, $enderecoCliente = null)
    {
        /** @var  $endereco Endereco */

        $nomeFantasia = '';
        $tipo = 'F';
        $rgie = 'N/A';

        if ($objCliente->isPessoaJuridica()) :
            $nomeFantasia = $objCliente->getNomeFantasia();
            $tipo = 'J';
            $rgie = $objCliente->getInscricaoEstadual();
        endif;

        $endereco = is_null($enderecoCliente) ? $objCliente->getEnderecoPrincipal() : $enderecoCliente;

        return array(
            'nome' => resumo($objCliente->getNomeCompleto(), '120', ''),
            'fantasia' => resumo($nomeFantasia, '30', ''),
            'tipoPessoa' => $tipo,
            'cpf_cnpj' => resumo($objCliente->getCodigoFederal(), '18', ''),
            'ie_rg' => resumo($rgie, '18', ''),
            'contribuinte' => 9,
            'endereco' => resumo($endereco->getLogradouro(), '50', ''),
            'numero' => resumo($endereco->getNumero(), '10', ''),
            'complemento' => resumo($endereco->getComplemento(), '100', ''),
            'bairro' => resumo($endereco->getBairro(), '30', ''),
            'cep' => $endereco->getCepBling(),
            'cidade' => resumo($endereco->getCidade()->getNome(), '30', ''),
            'uf' => $endereco->getCidade()->getEstado()->getSigla(),
            'fone' => resumo($objCliente->getTelefone(), '40', ''),
            'email' => resumo($objCliente->getEmail(), '100', ''),

        );
    }

    /**
     * @param $clienteId
     * @param $rede
     * @param $i
     * @param $nivelMax
     * @return mixed
     * @throws PropelException
     */
    public static function getIndicadoresCliente($clienteId, &$rede, $i, $nivelMax)
    {
        $indicators = ClienteQuery::create()
            ->addAsColumn('Nome', 'NOME')
            ->addAsColumn('Id', 'ID')
            ->filterByClienteIndicadorDiretoId($clienteId)
            ->filterByVago(0);

        foreach (BasePeer::doSelect($indicators)->fetchAll() as $row) {
            if (!isset($row['Id']) || $row['Id'] == null) {
                continue;
            }

            if (isset($rede['nomes'][$i])) {
                $rede['nomes'][$i] .= '|||' . $row['Nome'];
            } else {
                $rede['nomes'][$i] = $row['Nome'];
            }

            if (isset($rede['ids'][$i])) {
                $rede['ids'][$i] .= '|||' . $row['Id'];
            } else {
                $rede['ids'][$i] = $row['Id'];
            }

            if ($i < $nivelMax) {
                ClientePeer::getIndicadoresCliente($row['Id'], $rede, $i + 1, $nivelMax);
            }
        }

        return $rede;
    }

    public static function getIndicadorCliente($clienteId)
    {
        $indicators = ClienteQuery::create()
            ->addAsColumn('Nome', 'NOME')
            ->addAsColumn('Id', 'ID')
            ->addAsColumn('status', 'STATUS')
            ->addAsColumn('compra_ultimo_mes', 'COMPRA_ULTIMO_MES')
            ->filterByClienteIndicadorDiretoId($clienteId)
            ->filterByVago(0);

        foreach (BasePeer::doSelect($indicators)->fetchAll() as $row) {
            if (!isset($row['Id']) || $row['Id'] == null) {
                continue;
            }

            if (isset($rede['nomes'])) {
                $rede['nomes'] .= '|||' . $row['Nome'];
            } else {
                $rede['nomes'] = $row['Nome'];
            }

            if (isset($rede['ids'])) {
                $rede['ids'] .= '|||' . $row['Id'];
            } else {
                $rede['ids'] = $row['Id'];
            }
        }

        return $rede;
    }

    public static function verifyLeads($arrClientes)
    {
        $clienteReturn = null;
        $countClienteReturn = 0;
        $date = strtotime(date('Y-m-d'));

        $arrClienteData = array();


        foreach ($arrClientes as $cliente) {
            /** @var Cliente $cliente */

            $dateCliente = strtotime($cliente->getDataUltimoLead('Y-m-d'));
            if (count($arrClienteData) == 0 || $dateCliente <= $date) {
                $date = $dateCliente;
                $arrClienteData[] = $cliente;
            }
        }


        if (empty($arrClienteData)) {
            return $clienteReturn;
        } elseif (count($arrClienteData) > 1) {
            foreach ($arrClienteData as $clienteData) {
                $clienteReturn = $clienteData;
                break;

//                $arrClientesAgendaCliente = ClienteDistribuidorQuery::create()
//                                                ->filterByClienteId($clienteData->getId())
//                                                ->filterByTipoLead(null, Criteria::NOT_EQUAL)
//                                                ->count();
//                if ($clienteReturn == null || $arrClientesAgendaCliente < $countClienteReturn) {
//                    $countClienteReturn = count($clienteData->getClienteDistribuidors());
//                    $clienteReturn = $clienteData;
//                }
            }

            return $clienteReturn;
        } else {
            return $arrClienteData[0];
        }
    }

    public static function getAllClientesList()
    {
        $arrClientes = ClienteQuery::create()->find();

        $arrClientesRet = array();

        if (count($arrClientes)) {
            foreach ($arrClientes as $objCliente) {
                /** @var $objCliente Cliente */
                $arrClientesRet[$objCliente->getId()] = $objCliente->getNomeCompleto();
            }
        }

        return $arrClientesRet;
    }

    static function maximizeGroupConcatLenght()
    {
        // Parabéns, uma das coisas mais ridículas que já vi, feito "sob medida"
        $gambi = "SET @@group_concat_max_len = 18446744073709551615";
        $con = Propel::getConnection();
        $stmt = $con->prepare($gambi);
        $stmt->execute();
    }

    static function getAllClientsIds()
    {
        $SQL = "SELECT ID
                  FROM qp1_cliente
                 ORDER BY ID";

        $con = Propel::getConnection();
        $stmt = $con->prepare($SQL);
        $stmt->execute();

        $queryIds = $stmt->fetchAll();

        $clientes = '';

        foreach ($queryIds as $id) {
            if ($clientes !== '') :
                $clientes .= ',';
            endif;

            $clientes .= $id[0];
        }

        return $clientes;
    }

    static function getClientsIdsByCombo($combo)
    {
        ClientePeer::maximizeGroupConcatLenght();

        $SQL = "SELECT  group_concat(ID) as CLIENTES
        FROM qp1_cliente
        WHERE PLANO_ID " . $combo . "
        ORDER BY ID";

        $con = Propel::getConnection();
        $stmt = $con->prepare($SQL);
        $stmt->execute();

        $clientesVips = $stmt->fetchObject();
        return $clientesVips;
    }

    static function getClientsIdsByAtivoMes($ativo)
    {
        ClientePeer::maximizeGroupConcatLenght();

        $SQL = "SELECT  group_concat(ID) as CLIENTES
        FROM qp1_cliente
        WHERE NAO_COMPRA = " . $ativo . "
        ORDER BY ID";

        $con = Propel::getConnection();
        $stmt = $con->prepare($SQL);
        $stmt->execute();

        $clientesVips = $stmt->fetchObject();
        return $clientesVips;
    }

    public static function getIndicadosDiretosByClienteId($clienteId)
    {
        $indicators = ClienteQuery::create()
            ->addAsColumn('nome', 'IF(qp1_cliente.CNPJ IS NOT NULL, qp1_cliente.RAZAO_SOCIAL, qp1_cliente.NOME)')
            ->addAsColumn('id', 'qp1_cliente.ID')
            ->addAsColumn('nr_patrocinador', 'qp1_cliente.CHAVE_INDICACAO')
            ->addAsColumn('email', 'qp1_cliente.EMAIL')
            ->addAsColumn('telefone', 'qp1_cliente.TELEFONE')
            ->addAsColumn('plano', 'qp1_cliente.PLANO_ID')
            ->addAsColumn('status', 'qp1_cliente.STATUS')
            ->addAsColumn('compra_ultimo_mes', 'qp1_cliente.COMPRA_ULTIMO_MES')
            ->filterByClienteIndicadorDiretoId($clienteId)
            // ->filterByVago(0)
            ->usePlanoQuery()
                ->filterByPlanoClientePreferencial(true, Criteria::NOT_EQUAL)
            ->endUse();

        $arrRetorno = array();

        $arrClientes = BasePeer::doSelect($indicators)->fetchAll();

        if (count($arrClientes)) :
            foreach ($arrClientes as $objCliente) :
                $arrRetorno[] = $objCliente;
            endforeach;
        endif;

        return $arrRetorno;
    }

    public static function getCountIndicadorCliente($clienteId)
    {
        $indicators = ClienteQuery::create()
            ->addAsColumn('Nome', 'NOME')
            ->addAsColumn('Id', 'ID')
            ->addAsColumn('status', 'STATUS')
            ->addAsColumn('compra_ultimo_mes', 'COMPRA_ULTIMO_MES')
            ->filterByClienteIndicadorDiretoId($clienteId)
            ->filterByVago(0);


        return BasePeer::doSelect($indicators)->rowCount() > 0 ? BasePeer::doSelect($indicators)->rowCount() : 0 ;
    }

    /**
     * @return array
     */
    public static function getClientesAtivosPermanente()
    {
        /*
         * GEODESIC CENTER HOUSE OF PRAYER OF SOUTH AMERICA
         * MATHEUS PEREIRA DE MORAIS
         * Reinaldo Gomes de Morais
         * Eliane Morais
         * Ana Beatriz Morais
         * Ana Carolina Morais
         * SPIGREEN INTERNACIONAL DISTRIBUIDORA DE ALIMENTOS E COSMETICOS LTDA
         * Ernani Fernandes Braga
         * Spigreen Mercosul Internacional
         * Raphael Caio Nunes de Amorim
         * Pitter Queiroz Maciel
         * DR MENTE & SAÚDE
         */
        $clientesAtivacaoPermanente = [1, 2, 3, 6, 8, 169, 213, 222, 226, 327, 1013, 4088];

        return $clientesAtivacaoPermanente;
    }

    public static function getClienteAtivoMensal($clienteId, $start = null, $end = null)
    {
        if (in_array($clienteId, self::getClientesAtivosPermanente())) :
            return true;
        endif;

        $cliente = ClienteQuery::create()
            ->filterById($clienteId)
            ->filterByVago(false)
            ->filterByStatus(1)
            ->findOne();

        if (empty($cliente)) :
            return false;
        endif;

        $plano = $cliente->getPlano();

        if (!$plano || $plano->getPlanoClientePreferencial()) :
            return true;
        endif;

        if (empty($start) && empty($end)) :
            $start = new DateTime('first day of this month');
            $start->setTime(0, 0, 0);

            $end = new DateTime('last day of this month');
            $end->setTime(23, 59, 59, 99999);
        endif;

        $ultimaCompraPlano = PedidoStatusHistoricoQuery::create()
            ->filterByPedidoStatusId(1)
            ->filterByIsConcluido(1)
            ->filterByUpdatedAt([
                'min' => $start,
                'max' => $end
            ])
            ->usePedidoQuery()
                ->filterByClienteId($clienteId)
                ->filterByStatus('CANCELADO', Criteria::NOT_EQUAL)
                ->usePedidoItemQuery()
                    ->useProdutoVariacaoQuery()
                        ->useProdutoQuery()
                            ->filterByPlanoId($plano->getId())
                        ->endUse()
                    ->endUse()
                ->endUse()
            ->endUse()
            ->findOne();

        if (!is_null($ultimaCompraPlano)):
            return true;
        endif;

        if ($plano->getId() == 9 && in_array($start->format('n'), ['8', '9']) && $start->format('Y') == '2020'):
            $startPrev = (clone $start)->modify('first day of previous month');
            $endPrev = (clone $end)->modify('last day of previous month');

            $ultimaCompraPlano = PedidoStatusHistoricoQuery::create()
                ->filterByPedidoStatusId(1)
                ->filterByIsConcluido(1)
                ->filterByUpdatedAt([
                    'min' => $startPrev,
                    'max' => $endPrev
                ])
                ->usePedidoQuery()
                    ->filterByClienteId($clienteId)
                    ->filterByStatus('CANCELADO', Criteria::NOT_EQUAL)
                    ->usePedidoItemQuery()
                        ->useProdutoVariacaoQuery()
                            ->useProdutoQuery()
                                ->filterById(149)
                                ->filterByPlanoId($plano->getId())
                            ->endUse()
                        ->endUse()
                    ->endUse()
                ->endUse()
                ->findOne();

            if (!is_null($ultimaCompraPlano)):
                return true;
            endif;
        endif;

        $valorMinimo = ConfiguracaoPontuacaoMensalPeer::getValorMinimoPontosMensal();

        $controlePontos = $cliente->getControlePontuacaoMes(
            $start->format('n'),
            $start->format('Y')
        );

        $totalPontos = $controlePontos->getPontosPessoais() ?? 0;

        return $totalPontos >= $valorMinimo;
    }

    public static function getTipoCliente($clienteId)
    {
        $tipo = '';

        $cliente = self::retrieveByPK($clienteId);

        if (!$cliente->getPlano()) :
            $tipo = 'Cliente Final';
        elseif ($cliente->getPlano()->getPlanoClientePreferencial()) :
            $tipo = 'Cliente Preferencial';
        else :
            $tipo = 'Distribuidor';
        endif;

        return $tipo;
    }

    public function getSearchedRede($nome) {
        return ClienteQuery::create()
            ->filterByNome('%' .$nome . '%')
            ->_or()
            ->filterByNomeFantasia('%' .$nome . '%')
            ->_or()
            ->filterByRazaoSocial('%' .$nome . '%')
            ->findOne();
    }

    public static function getPatrocinadorMaisProximoPorCEP($cep, $page = 1)
    {
        $clientes = [];
        $limite = $page * 3;

        $cep = preg_replace('/\D/', '', $cep);

        $consultaEndereco = CorreiosEndereco::consultaCepViaCep($cep);

        $lastMonthStart = new DateTime('first day of last month');
        $lastMonthStart->setTime(0, 0, 0);

        $lastMonthEnd = new DateTime('last day of last month');
        $lastMonthEnd->setTime(23, 59, 59, 99999);

        $clientesIgnorar = [1, 2, 3, 8, 6, 169, 213];
        
        $queryClientes = ClienteQuery::create()
            ->filterByVago(false)
            ->filterById($clientesIgnorar, Criteria::NOT_IN)
            ->groupById();

        $queryClientesMesmoEstado = (clone $queryClientes)
            ->usePlanoCarreiraHistoricoQuery()
                ->usePlanoCarreiraQuery()
                    ->filterByNivel(3, Criteria::GREATER_EQUAL)
                ->endUse()
            ->endUse()
            ->useEnderecoQuery()
                ->useCidadeQuery()
                    ->useEstadoQuery()
                        ->filterBySigla($consultaEndereco['uf'])
                    ->endUse()
                ->endUse()
            ->endUse();
        
        if ($queryClientesMesmoEstado->count() > 0) :
            foreach ($queryClientesMesmoEstado->find() as $cliente) :
                if (
                    ClientePeer::getClienteAtivoMensal($cliente->getId(), $lastMonthStart, $lastMonthEnd) ||
                    ClientePeer::getClienteAtivoMensal($cliente->getId())
                ) :
                    $clientes[] = $cliente;
                endif;
                
                if (count($clientes) > $limite) :
                    break;
                endif;
            endforeach;

            if (!empty($clientes)) :
                return [array_slice($clientes, $limite - 3, 3), count($clientes) <= $limite];
            endif;
        endif;

        $queryClientesEstadoDiferente = (clone $queryClientes)
            ->usePlanoCarreiraHistoricoQuery()
                ->usePlanoCarreiraQuery()
                    ->filterByNivel(5, Criteria::GREATER_EQUAL)
                ->endUse()
            ->endUse()
            ->useEnderecoQuery()
                ->useCidadeQuery()
                    ->useEstadoQuery()
                        ->filterBySigla($consultaEndereco['uf'], Criteria::NOT_EQUAL)
                    ->endUse()
                ->endUse()
            ->endUse();
        
        if ($queryClientesEstadoDiferente->count() > 0) :
            foreach ($queryClientesEstadoDiferente->find() as $cliente) :
                if (
                    ClientePeer::getClienteAtivoMensal($cliente->getId(), $lastMonthStart, $lastMonthEnd) ||
                    ClientePeer::getClienteAtivoMensal($cliente->getId())
                ) :
                    $clientes[] = $cliente;
                endif;
                
                if (count($clientes) > $limite) :
                    break;
                endif;
            endforeach;

            if (!empty($clientes)) :
                return [array_slice($clientes, $limite - 3, 3), count($clientes) <= $limite];
            endif;
        endif;

        // Caso não tenha achado um Supervisor ou acima no mesmo estado, ou
        // Executivo ou acima nas demais regiões, retorna o cliente Spigreen Internacional (ID 213)
        if (empty($clientes)) :
            $clientes[] = self::retrieveByPK(213);
        endif;

        return [$clientes, true];
    }

}
