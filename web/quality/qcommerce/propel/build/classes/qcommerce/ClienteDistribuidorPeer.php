<?php


/**
 * Skeleton subclass for performing query and update operations on the 'qp1_cliente_distribuidor' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class ClienteDistribuidorPeer extends BaseClienteDistribuidorPeer
{

    /**
     * @return Cliente
     */
    public static function getDistribuidorMaisPerto($cep)
    {
        include_once QCOMMERCE_DIR . "/classes/CorreiosEndereco.php";

        /** @var Cliente $clienteReturn */
        $clienteReturn = null;
        $countClienteReturn = 0;

        $return = \CorreiosEndereco::consultaCepViaCep($cep);

        if(!is_null($return)) {
            $clientes = ClienteQuery::create()
                ->filterByTreeLeft(0, Criteria::GREATER_THAN)
//                ->filterByVip(true)
//                ->filterByPontoApoioCertificado(true)
                ->useEnderecoQuery()
//                ->filterByTipo('PRINCIPAL', Criteria::EQUAL)
                ->useCidadeQuery()
                ->filterByNome($return["cidade"])
                ->useEstadoQuery()
                ->filterBySigla($return["uf"])
                ->endUse()
                ->endUse()
                ->endUse()
                ->groupById()
                ->find();


            $clienteReturn = ClientePeer::verifyLeads($clientes);

            if ($clienteReturn != null) {
                return $clienteReturn;
            }

            $clientes = ClienteQuery::create()
                ->filterByTreeLeft(0, Criteria::GREATER_THAN)
//                ->filterByVip(true)
//                ->filterByPontoApoioCertificado(true)
                ->useEnderecoQuery()
//                ->filterByTipo('PRINCIPAL', Criteria::EQUAL)
                ->useCidadeQuery()
                ->useEstadoQuery()
                ->filterBySigla($return["uf"])
                ->endUse()
                ->endUse()
                ->endUse()
                ->groupById()
                ->find();

            $clienteReturn = ClientePeer::verifyLeads($clientes);

            if ($clienteReturn != null) {
                return $clienteReturn;
            }

        }

        $clientes = ClienteQuery::create()
            //->filterByPontoApoio(true)
            ->filterByTreeLeft(0, Criteria::GREATER_THAN)
//            ->filterByVip(true)
//            ->filterByPontoApoioCertificado(true)
            ->groupById()
            ->find();


        $clienteReturn = ClientePeer::verifyLeads($clientes);


        if ($clienteReturn != null) {
            return $clienteReturn;
        }

        /*$clientes = ClienteQuery::create()
            ->filterByVip(true)
            ->filterByPontoApoioCertificado(true)
            ->groupById()
            ->find();


        $clienteReturn = ClientePeer::verifyLeads($clientes);

        if ($clienteReturn != null) {
            return $clienteReturn;
        }*/


        return null;
    }

    /**
     * @return Cliente
     */
    public static function getDistribuidorMaisPertoCron($cep)
    {
        /** @var Cliente $clienteReturn */
        $clienteReturn = null;
        $countClienteReturn = 0;

        $return = \CorreiosEndereco::consultaCepViaCep($cep);

        if(!is_null($return)) {
            $clientes = ClienteQuery::create()
                //->filterByPontoApoio(true)
                ->filterByTreeLeft(0, Criteria::GREATER_THAN)
//                ->filterByVip(true)
//                ->filterByPontoApoioCertificado(true)
                ->useEnderecoQuery()
//                ->filterByTipo('PRINCIPAL', Criteria::EQUAL)
                ->useCidadeQuery()
                ->filterByNome($return["cidade"])
                ->useEstadoQuery()
                ->filterBySigla($return["uf"])
                ->endUse()
                ->endUse()
                ->endUse()
                ->groupById()
                ->find();


            $clienteReturn = ClientePeer::verifyLeads($clientes);

            if ($clienteReturn != null) {
                return $clienteReturn;
            }

            $clientes = ClienteQuery::create()
                //->filterByPontoApoio(true)
                ->filterByTreeLeft(0, Criteria::GREATER_THAN)
//                ->filterByVip(true)
//                ->filterByPontoApoioCertificado(true)
                ->useEnderecoQuery()
//                ->filterByTipo('PRINCIPAL', Criteria::EQUAL)
                ->useCidadeQuery()
                ->useEstadoQuery()
                ->filterBySigla($return["uf"])
                ->endUse()
                ->endUse()
                ->endUse()
                ->groupById()
                ->find();

            $clienteReturn = ClientePeer::verifyLeads($clientes);

            if ($clienteReturn != null) {
                return $clienteReturn;
            }

        }

        $clientes = ClienteQuery::create()
            //->filterByPontoApoio(true)
            ->filterByTreeLeft(0, Criteria::GREATER_THAN)
//            ->filterByVip(true)
//            ->filterByPontoApoioCertificado(true)
            ->groupById()
            ->find();


        $clienteReturn = ClientePeer::verifyLeads($clientes);


        if ($clienteReturn != null) {
            return $clienteReturn;
        }

        /*$clientes = ClienteQuery::create()
            ->filterByVip(true)
            ->filterByPontoApoioCertificado(true)
            ->groupById()
            ->find();


        $clienteReturn = ClientePeer::verifyLeads($clientes);

        if ($clienteReturn != null) {
            return $clienteReturn;
        }*/


        return null;
    }

    /**
     *
     * @param String Cep
     * @return Cliente|null
     * @throws PropelException
     */
    public static function getDistribuidorMaisPertoProduto($cep)
    {
        include_once QCOMMERCE_DIR . "/classes/CorreiosEndereco.php";
        /** @var Cliente $clienteReturn */
        $clienteReturn = null;
        $countClienteReturn = 0;
        $compraDias = _parametro('leads.produtos.dias_ultima_compra');
        $compravalor = _parametro('leads.produtos.valor_ultima_compra');

        if(!is_numeric($compraDias))
            $compraDias = 100;

        if(!is_numeric($compravalor))
            $compravalor = ConfiguracaoPeer::retrieveByPK(1)->getResgateValorCompra();

        $return = \CorreiosEndereco::consultaCepViaCep($cep);

        $data = new DateTime(date( 'Y-m-d'));
        $data->sub(new DateInterval('P'.$compraDias.'D'));

        if(!is_null($return)){

            $data = new DateTime(date( 'Y-m-d'));
            $data->sub(new DateInterval('P'.$compraDias.'D'));
            //$data->format('Y-m-d');

            $clientes = ClienteQuery::create()
                ->filterByTreeLeft(0, Criteria::GREATER_THAN)
//                ->filterByVip(true)
//                ->filterByPontoApoioCertificado(true)
                ->withColumn(' sum(qp1_pedido.VALOR_ITENS) ',  'soma_valor')
                ->having(' soma_valor >= "'.$compravalor.'" ')
                ->usePedidoQuery()
                    ->filterByCreatedAt($data->format('Y-m-d'), Criteria::GREATER_EQUAL)
                    ->filterByStatus(Pedido::FINALIZADO, Criteria::EQUAL)
                ->endUse()
                ->useDistribuidorConfiguracaoQuery()
                    ->filterByChaveApiMailforweb(null, Criteria::NOT_EQUAL)
                ->endUse()
                ->useEnderecoQuery()
                    ->filterByTipo('PRINCIPAL', Criteria::EQUAL)
                    ->useCidadeQuery()
                        ->filterByNome($return["cidade"])
                        ->useEstadoQuery()
                            ->filterBySigla($return["uf"])
                        ->endUse()
                    ->endUse()
                ->endUse()
                ->groupById()
                ->find();

            $clienteReturn = ClientePeer::verifyLeads($clientes);

            if ($clienteReturn != null) {
                return $clienteReturn;
            }

            $clientes = ClienteQuery::create()
                ->filterByTreeLeft(0, Criteria::GREATER_THAN)
//                ->filterByVip(true)
//                ->filterByPontoApoioCertificado(true)
                ->withColumn(' sum(qp1_pedido.VALOR_ITENS) ',  'soma_valor')
                ->having(' soma_valor >= "'.$compravalor.'"')
                ->usePedidoQuery()
                    ->filterByCreatedAt($data->format('Y-m-d'), Criteria::GREATER_EQUAL)
                    ->filterByStatus(Pedido::FINALIZADO, Criteria::EQUAL)
                ->endUse()
                ->useDistribuidorConfiguracaoQuery()
                    ->filterByChaveApiMailforweb(null, Criteria::NOT_EQUAL)
                ->endUse()
                ->useEnderecoQuery()
                    ->filterByTipo('PRINCIPAL', Criteria::EQUAL)
                    ->useCidadeQuery()
                        ->useEstadoQuery()
                            ->filterBySigla($return["uf"])
                        ->endUse()
                    ->endUse()
                ->endUse()
                ->groupById()
                ->find();


            $clienteReturn = ClientePeer::verifyLeads($clientes);

            if ($clienteReturn != null) {
                return $clienteReturn;
            }

        }

        $clientes = ClienteQuery::create()
            ->filterByTreeLeft(0, Criteria::GREATER_THAN)
//            ->filterByVip(true)
//            ->filterByPontoApoioCertificado(true)
            ->withColumn(' sum(qp1_pedido.VALOR_ITENS) ',  'soma_valor')
            ->usePedidoQuery()
                ->filterByCreatedAt($data->format('Y-m-d'), Criteria::GREATER_EQUAL)
                ->filterByStatus(Pedido::FINALIZADO, Criteria::EQUAL)
            ->endUse()
            ->useDistribuidorConfiguracaoQuery()
                ->filterByChaveApiMailforweb(null, Criteria::NOT_EQUAL)
            ->endUse()
            ->groupById()
            ->having(' soma_valor >= "'.$compravalor.'"')
            ->find();


        $clienteReturn = ClientePeer::verifyLeads($clientes);

        if ($clienteReturn != null) {
            return $clienteReturn;
        }


        return null;
    }

    /**
     *
     * @param String Cep
     * @return Cliente|null
     * @throws PropelException
     */
    public static function getDistribuidorMaisPertoProdutoCron($cep)
    {

        /** @var Cliente $clienteReturn */
        $clienteReturn = null;
        $countClienteReturn = 0;

        $compraDias = _parametro('leads.produtos.dias_ultima_compra');
        $compravalor = _parametro('leads.produtos.valor_ultima_compra');

        if(!is_numeric($compraDias))
            $compraDias = 100;

        if(!is_numeric($compravalor))
            $compravalor = ConfiguracaoPeer::retrieveByPK(1)->getResgateValorCompra();

        $return = \CorreiosEndereco::consultaCepViaCep($cep);

        $data = new DateTime(date( 'Y-m-d'));
        $data->sub(new DateInterval('P'.$compraDias.'D'));
        //$data->format('Y-m-d');

        if(!is_null($return)){



            $clientes = ClienteQuery::create()
                ->filterByTreeLeft(0, Criteria::GREATER_THAN)
//                ->filterByVip(true)
//                ->filterByPontoApoioCertificado(true)
                ->withColumn(' sum(qp1_pedido.VALOR_ITENS) ',  'soma_valor')
                ->having(' soma_valor >= "'.$compravalor.'" ')
                ->usePedidoQuery()
                ->filterByCreatedAt($data->format('Y-m-d'), Criteria::GREATER_EQUAL)
                ->filterByStatus(Pedido::FINALIZADO, Criteria::EQUAL)
                ->endUse()
                ->useDistribuidorConfiguracaoQuery()
                ->filterByChaveApiMailforweb(null, Criteria::NOT_EQUAL)
                ->endUse()
                ->useEnderecoQuery()
                ->filterByTipo('PRINCIPAL', Criteria::EQUAL)
                ->useCidadeQuery()
                ->filterByNome($return["cidade"])
                ->useEstadoQuery()
                ->filterBySigla($return["uf"])
                ->endUse()
                ->endUse()
                ->endUse()
                ->groupById()
                ->find();

            $clienteReturn = ClientePeer::verifyLeads($clientes);

            if ($clienteReturn != null) {
                return $clienteReturn;
            }

            $clientes = ClienteQuery::create()
                ->filterByTreeLeft(0, Criteria::GREATER_THAN)
//                ->filterByVip(true)
//                ->filterByPontoApoioCertificado(true)
                ->withColumn(' sum(qp1_pedido.VALOR_ITENS) ',  'soma_valor')
                ->having(' soma_valor >= "'.$compravalor.'"')
                ->usePedidoQuery()
                ->filterByCreatedAt($data->format('Y-m-d'), Criteria::GREATER_EQUAL)
                ->filterByStatus(Pedido::FINALIZADO, Criteria::EQUAL)
                ->endUse()
                ->useDistribuidorConfiguracaoQuery()
                ->filterByChaveApiMailforweb(null, Criteria::NOT_EQUAL)
                ->endUse()
                ->useEnderecoQuery()
                ->filterByTipo('PRINCIPAL', Criteria::EQUAL)
                ->useCidadeQuery()
                ->useEstadoQuery()
                ->filterBySigla($return["uf"])
                ->endUse()
                ->endUse()
                ->endUse()
                ->groupById()
                ->find();


            $clienteReturn = ClientePeer::verifyLeads($clientes);

            if ($clienteReturn != null) {
                return $clienteReturn;
            }

        }

        $clientes = ClienteQuery::create()
            ->filterByTreeLeft(0, Criteria::GREATER_THAN)
//            ->filterByVip(true)
//            ->filterByPontoApoioCertificado(true)
            ->withColumn(' sum(qp1_pedido.VALOR_ITENS) ',  'soma_valor')
            ->usePedidoQuery()
            ->filterByCreatedAt($data->format('Y-m-d'), Criteria::GREATER_EQUAL)
            ->filterByStatus(Pedido::FINALIZADO, Criteria::EQUAL)
            ->endUse()
            ->useDistribuidorConfiguracaoQuery()
            ->filterByChaveApiMailforweb(null, Criteria::NOT_EQUAL)
            ->endUse()
            ->groupById()
            ->having(' soma_valor >= "'.$compravalor.'"')
            ->find();


        $clienteReturn = ClientePeer::verifyLeads($clientes);

        if ($clienteReturn != null) {
            return $clienteReturn;
        }


        return null;
    }

    /**
     * @param $clienteDistribuidor ClienteDistribuidor
     * @return bool
     */
    public static function notificarAoDistribuidorDoClientePeloNovoCadastro($clienteDistribuidor)
    {
        include_once QCOMMERCE_DIR . "/classes/IntegracaoMailforweb.php";

        $objCliente = $clienteDistribuidor->getCliente();
        $mensagem = "Novo contato cadastrado em sua Agenda do Distribuidor interessado no Negócio da Spigreen. Acesse sua agenda e faça o contato em no máximo 24h!";
        $mensagemEmail = Mensagem::getCabecalho();
        $mensagemEmail .=
            "<tr>
			<td class='titulo'><b>Novo Contato na Agenda</b></td>
            </tr>
            <tr>
			<td id='conteudo-email'>
				<br>
				<p><b>Ol�, <span class='rosa'>" . $objCliente->getNomeCompleto() . "</span></b></p>
				<br>
				<p>Novo contato agenda do distribuidor!</p>
				    <p>
				    Novo contato cadastrado em sua Agenda do Distribuidor interessado no Negócio da Spigreen. 
				    <br><br>Acesse sua agenda e faça o contato em no máximo 24h!
					<br /><br />
					
					Atenciosamente.
				</p>
				<br />
				&nbsp;
			</td>
		</tr>";

        $mensagemEmail .= Mensagem::getRodape();
        try {
            /* @var $objConfiguracao DistribuidorConfiguracao */
            $objConfiguracao = $objCliente->getDistribuidorConfiguracaos()->getFirst();

            if (!is_null($objConfiguracao)) {
                $integracaoMfw = new IntegracaoMailforweb($objConfiguracao->getChaveApiMailforweb());
                $result = $integracaoMfw->enviaSMS($objConfiguracao->getTelefoneNotificacao(), $mensagem);
                Qmail::enviaMensagem($objCliente->getEmail(), "Novo Contato na Agenda :: ", $mensagemEmail);
                //$result = $integracaoMfw->enviaEmailTransacional(, , , Mensagem::getEmpresa()->getEmail(), Mensagem::getEmpresa()->getNome());
            }
        } catch (Exception $ex) {
            error_log($ex->getMessage());
        }

        return true;
    }

    /**
     * @param $clienteDistribuidor ClienteDistribuidor
     * @return bool
     */
    public static function notificarAoDistribuidorDoClientePeloNovoCadastroProduto($clienteDistribuidor)
    {

        include_once QCOMMERCE_DIR . "/classes/IntegracaoMailforweb.php";

        $objCliente = $clienteDistribuidor->getCliente();
        $mensagem = "Você recebeu um novo contato interessado em produtos na sua Agenda do Distribuidor da Spigreen.";
        $mensagemEmail = Mensagem::getCabecalho();
        $mensagemEmail .=
            "<tr>
			<td class='titulo'><b>Novo Contato na Agenda</b></td>
            </tr>
            <tr>
			<td id='conteudo-email'>
				<br>
				<p><b>Olá, <span class='rosa'>" . $objCliente->getNomeCompleto() . "</span></b></p>
				<br>
                <p>
                Você recebeu um novo contato interessado em produtos na sua Agenda do Distribuidor da Spigreen.
                <br /><br />
					
					Atenciosamente.
				</p>
				<br />
				&nbsp;
			</td>
		</tr>";
        $mensagemEmail .= Mensagem::getRodape();

        try {
            /* @var $objConfiguracao DistribuidorConfiguracao */
            $objConfiguracao = $objCliente->getDistribuidorConfiguracaos()->getFirst();

            if (!is_null($objConfiguracao)) {
                $integracaoMfw = new IntegracaoMailforweb($objConfiguracao->getChaveApiMailforweb());
                $result = $integracaoMfw->enviaSMS($objConfiguracao->getTelefoneNotificacao(), $mensagem);
                Qmail::enviaMensagem($objCliente->getEmail(), "Novo Contato na Agenda :: ", $mensagemEmail);
                //$result = $integracaoMfw->enviaEmailTransacional($objCliente->getEmail(), "Novo Contato na Agenda :: " . Mensagem::getEmpresa()->getNome(), $mensagemEmail, Mensagem::getEmpresa()->getEmail(), Mensagem::getEmpresa()->getNome());
            }
        } catch (Exception $ex) {
            error_log($ex->getMessage());
        }

        return true;
    }

    /**
     * @param $clienteDistribuidor ClienteDistribuidor
     * @return bool
     */
    public static function notificarAoDistribuidorDoClienteAntesVencimentoDaData($clienteDistribuidor)
    {

        include_once QCOMMERCE_DIR . "/classes/IntegracaoMailforweb.php";


        $objCliente = $clienteDistribuidor->getCliente();
        $mensagem = "Novo Contato na Agenda perto da data de vencimento";
        $mensagemEmail = Mensagem::getCabecalho();
        $mensagemEmail .=
            "<tr>
			<td class='titulo'><b>Novo Contato na Agenda perto da data de vencimento</b></td>
            </tr>
            <tr>
			<td id='conteudo-email'>
				<br>
				<p><b>Olá, <span class='rosa'>" . $objCliente->getNomeCompleto() . "</span></b></p>
				<br>
				<p>Um de seus contatos recebidos na agenda do distribuidor está próximo de expirar. 
				Faça contato o quanto antes para não perder o negócio.
				
				</p>
				<p>
					<br /><br />
					
					Atenciosamente.
				</p>
				<br />
				&nbsp;
			</td>
		</tr>";
        $mensagemEmail .= Mensagem::getRodape();


        try {
            /* @var $objConfiguracao DistribuidorConfiguracao */
            $objConfiguracao = $objCliente->getDistribuidorConfiguracaos()->getFirst();

            if (!is_null($objConfiguracao)) {
                $integracaoMfw = new IntegracaoMailforweb($objConfiguracao->getChaveApiMailforweb());
                $resultSMS = $integracaoMfw->enviaSMS($objConfiguracao->getTelefoneNotificacao(), $mensagem);
                //$resultEMail = $integracaoMfw->enviaEmailTransacional($objCliente->getEmail(), "Cliente próximo da data de expiração :: " . Mensagem::getEmpresa()->getNome(), $mensagemEmail, Mensagem::getEmpresa()->getEmail(), Mensagem::getEmpresa()->getNome());
                Qmail::enviaMensagem($objCliente->getEmail(), "Cliente próximo da data de expiração :: ", $mensagemEmail);
            }
        } catch (Exception $ex) {
            error_log($ex->getMessage());
        }

        return true;
    }

    /**
     * @param $clienteDistribuidor ClienteDistribuidor
     * @return bool
     */
    public static function notificarAoDistribuidorDoClienteAntesVencimentoDaDataCron($clienteDistribuidor)
    {

        $objCliente = $clienteDistribuidor->getCliente();
        $mensagem = "Novo Contato na Agenda perto da data de vencimento";
        $mensagemEmail = Mensagem::getCabecalho();
        $mensagemEmail .=
            "<tr>
			<td class='titulo'><b>Novo Contato na Agenda perto da data de vencimento</b></td>
            </tr>
            <tr>
			<td id='conteudo-email'>
				<br>
				<p><b>Olá, <span class='rosa'>" . $objCliente->getNomeCompleto() . "</span></b></p>
				<br>
				<p>Um de seus contatos recebidos na agenda do distribuidor está próximo de expirar. 
				Faça contato o quanto antes para não perder o negócio.
				
				</p>
				<p>
					<br /><br />
					
					Atenciosamente.
				</p>
				<br />
				&nbsp;
			</td>
		</tr>";
        $mensagemEmail .= Mensagem::getRodape();


        try {
            /* @var $objConfiguracao DistribuidorConfiguracao */
            $objConfiguracao = $objCliente->getDistribuidorConfiguracaos()->getFirst();

            if (!is_null($objConfiguracao)) {
                $integracaoMfw = new IntegracaoMailforweb($objConfiguracao->getChaveApiMailforweb());
                $resultSMS = $integracaoMfw->enviaSMS($objConfiguracao->getTelefoneNotificacao(), $mensagem);
                //$resultEMail = $integracaoMfw->enviaEmailTransacional($objCliente->getEmail(), "Cliente próximo da data de expiração :: " . Mensagem::getEmpresa()->getNome(), $mensagemEmail, Mensagem::getEmpresa()->getEmail(), Mensagem::getEmpresa()->getNome());
                Qmail::enviaMensagem($objCliente->getEmail(), "Cliente próximo da data de expiração :: ", $mensagemEmail);
            }
        } catch (Exception $ex) {
            error_log($ex->getMessage());
        }

        return true;
    }


    /**
     * @param $clienteDistribuidor ClienteDistribuidor
     * @param $oldDistribuidor Cliente
     * @return bool
     */
    public static function notificarAoDistribuidorDoClienteAposVencimentoDaData($clienteDistribuidor,$oldDistribuidor)
    {

        include_once QCOMMERCE_DIR . "/classes/IntegracaoMailforweb.php";


        $mensagem = "Novo Contato foi enviado para outro distribuidor, fique atento para não perder o próximo.";
        $mensagemEmail = Mensagem::getCabecalho();
        $mensagemEmail .=
            "<tr>
			<td class='titulo'><b>Você perdeu um contato da agenda.</b></td>
            </tr>
            <tr>
			<td id='conteudo-email'>
				<br>
				<p><b>Olá, <span class='rosa'>" . $oldDistribuidor->getNomeCompleto() . "</span></b></p>
				<br>
				<p>Novo Contato foi enviado para outro distribuidor, fique atento para não perder o próximo.
				</p>
				<p>
					<br /><br />    
					
					Atenciosamente.
				</p>
				<br />
				&nbsp;
			</td>
		</tr>";
        $mensagemEmail .= Mensagem::getRodape();


        try {
            /* @var $objConfiguracao DistribuidorConfiguracao */
            $objConfiguracao = $oldDistribuidor->getDistribuidorConfiguracaos()->getFirst();

            if (!is_null($objConfiguracao)) {
                $integracaoMfw = new IntegracaoMailforweb($objConfiguracao->getChaveApiMailforweb());
                $resultSMS = $integracaoMfw->enviaSMS($objConfiguracao->getTelefoneNotificacao(), $mensagem);
                //$resultEMail = $integracaoMfw->enviaEmailTransacional($oldDistribuidor->getEmail(), "Você perdeu um contato da agenda : " . Mensagem::getEmpresa()->getNome(), $mensagemEmail, Mensagem::getEmpresa()->getEmail(), Mensagem::getEmpresa()->getNome());
                Qmail::enviaMensagem($oldDistribuidor->getEmail(), "Você perdeu um contato da agenda : ", $mensagemEmail);
            }
        } catch (Exception $ex) {
            error_log($ex->getMessage());
        }

        return true;
    }

    /**
     * @param $clienteDistribuidor ClienteDistribuidor
     * @param $oldDistribuidor Cliente
     * @return bool
     */
    public static function notificarAoDistribuidorDoClienteAposVencimentoDaDataCron($clienteDistribuidor,$oldDistribuidor)
    {
        $mensagem = "Novo Contato na Agenda perto da data de vencimento";
        $mensagemEmail = Mensagem::getCabecalho();
        $mensagemEmail .=
            "<tr>
			<td class='titulo'><b>Você perdeu um contato da agenda.</b></td>
            </tr>
            <tr>
			<td id='conteudo-email'>
				<br>
				<p><b>Olá, <span class='rosa'>" . $oldDistribuidor->getNomeCompleto() . "</span></b></p>
				<br>
				<p>Novo Contato foi enviado para outro distribuidor, fique atento para não perder o próximo.
				</p>
				<p>
					<br /><br />    
					
					Atenciosamente.
				</p>
				<br />
				&nbsp;
			</td>
		</tr>";
        $mensagemEmail .= Mensagem::getRodape();


        try {
            /* @var $objConfiguracao DistribuidorConfiguracao */
            $objConfiguracao = $oldDistribuidor->getDistribuidorConfiguracaos()->getFirst();

            if (!is_null($objConfiguracao)) {
                $integracaoMfw = new IntegracaoMailforweb($objConfiguracao->getChaveApiMailforweb());
                $resultSMS = $integracaoMfw->enviaSMS($objConfiguracao->getTelefoneNotificacao(), $mensagem);
                //$resultEMail = $integracaoMfw->enviaEmailTransacional($oldDistribuidor->getEmail(), "Você perdeu um contato da agenda :: " . Mensagem::getEmpresa()->getNome(), $mensagemEmail, Mensagem::getEmpresa()->getEmail(), Mensagem::getEmpresa()->getNome());
                Qmail::enviaMensagem($oldDistribuidor->getEmail(), "Você perdeu um contato da agenda : ", $mensagemEmail);
            }
        } catch (Exception $ex) {
            error_log($ex->getMessage());
        }

        return true;
    }


    /**
     * @param $clienteDistribuidor ClienteDistribuidor
     * @param $novoDistribuidor Cliente
     * @return bool
     */
    public static function notificarAoClienteDoNovoDistribuidorAposVencimentoDaData($clienteDistribuidor,$novoDistribuidor)
    {

        include_once QCOMMERCE_DIR . "/classes/IntegracaoMailforweb.php";


        $objCliente = $clienteDistribuidor->getCliente();
        $mensagem = "O Distribuidor ao qual você foi direcionado não conseguiu lhe atender. Você será encaminhado para outro.";
        $mensagemEmail = Mensagem::getCabecalho();
        $mensagemEmail .=
            "<tr>
			<td class='titulo'><b>Você foi redirecionado para outro distribuidor.</b></td>
            </tr>
            <tr>
			<td id='conteudo-email'>
				<br>
				<p><b>Olá, <span class='rosa'>" . $clienteDistribuidor->getNomeCompleto() . "</span></b></p>
				<br>
				<p> 
                    O Distribuidor ao qual você foi direcionado não conseguiu lhe atender. Você será encaminhado para outro. 
                    Ele fará contato o mais breve possível. Abaixo os dados do distribuidor:
				</p>
				<b> Nome:</b> " . $novoDistribuidor->getNomeCompleto()."<br>
				<b> E-Mail:</b> " . $novoDistribuidor->getEmail()."<br>
				<b> Telefone:</b> " . $novoDistribuidor->getEnderecoPrincipal()->getTelefone1()."<br>
				<b> Cidade:</b> " . $novoDistribuidor->getEnderecoPrincipal()->getCidade()->getNome()."<br>
				<b> Estado:</b> " . $novoDistribuidor->getEnderecoPrincipal()->getCidade()->getEstado()->getNome()."<br>"
            ."
				<br />
				&nbsp;
			</td>
		</tr>";
        $mensagemEmail .= Mensagem::getRodape();


        try {
            /* @var $objConfiguracao DistribuidorConfiguracao */
            $objConfiguracao = $objCliente->getDistribuidorConfiguracaos()->getFirst();

            if (!is_null($objConfiguracao)) {
                $integracaoMfw = new IntegracaoMailforweb($objConfiguracao->getChaveApiMailforweb());
                $resultSMS = $integracaoMfw->enviaSMS($clienteDistribuidor->getTelefone(), $mensagem);
                //$resultEMail = $integracaoMfw->enviaEmailTransacional($clienteDistribuidor->getEmail(), "Novo Contato de Distribuidor :: " . Mensagem::getEmpresa()->getNome(), $mensagemEmail, Mensagem::getEmpresa()->getEmail(), Mensagem::getEmpresa()->getNome());
                Qmail::enviaMensagem($clienteDistribuidor->getEmail(), "Novo Contato de Distribuidor :: ", $mensagemEmail);
            }
        } catch (Exception $ex) {
            error_log($ex->getMessage());
        }

        return true;
    }

    /**
     * @param $clienteDistribuidor ClienteDistribuidor
     * @param $novoDistribuidor Cliente
     * @return bool
     */
    public static function notificarAoClienteDoNovoDistribuidorAposVencimentoDaDataCron($clienteDistribuidor,$novoDistribuidor)
    {


        $objCliente = $clienteDistribuidor->getCliente();
        $mensagem = "O Distribuidor ao qual você foi direcionado não conseguiu lhe atender. Você será encaminhado para outro.";
        $mensagemEmail = Mensagem::getCabecalho();
        $mensagemEmail .=
            "<tr>
			<td class='titulo'><b>Você foi redirecionado para outro distribuidor.</b></td>
            </tr>
            <tr>
			<td id='conteudo-email'>
				<br>
				<p><b>Olá, <span class='rosa'>" . $clienteDistribuidor->getNomeCompleto() . "</span></b></p>
				<br>
				<p> 
                    O Distribuidor ao qual você foi direcionado não conseguiu lhe atender. Você será encaminhado para outro. 
                    Ele fará contato o mais breve possível. Abaixo os dados do distribuidor:
				</p>
				<b> Nome:</b> " . $novoDistribuidor->getNomeCompleto()."<br>
				<b> E-Mail:</b> " . $novoDistribuidor->getEmail()."<br>
				<b> Telefone:</b> " . $novoDistribuidor->getEnderecoPrincipal()->getTelefone1()."<br>
				<b> Cidade:</b> " . $novoDistribuidor->getEnderecoPrincipal()->getCidade()->getNome()."<br>
				<b> Estado:</b> " . $novoDistribuidor->getEnderecoPrincipal()->getCidade()->getEstado()->getNome()."<br>"
            ."
				<br />
				&nbsp;
			</td>
		</tr>";
        $mensagemEmail .= Mensagem::getRodape();


        try {
            /* @var $objConfiguracao DistribuidorConfiguracao */
            $objConfiguracao = $objCliente->getDistribuidorConfiguracaos()->getFirst();

            if (!is_null($objConfiguracao)) {
                $integracaoMfw = new IntegracaoMailforweb($objConfiguracao->getChaveApiMailforweb());
                $resultSMS = $integracaoMfw->enviaSMS($clienteDistribuidor->getTelefone(), $mensagem);
                //$resultEMail = $integracaoMfw->enviaEmailTransacional($clienteDistribuidor->getEmail(), "Novo Contato de Distribuidor :: " . Mensagem::getEmpresa()->getNome(), $mensagemEmail, Mensagem::getEmpresa()->getEmail(), Mensagem::getEmpresa()->getNome());
                Qmail::enviaMensagem($clienteDistribuidor->getEmail(), "Novo Contato de Distribuidor :: ", $mensagemEmail);
            }
        } catch (Exception $ex) {
            error_log($ex->getMessage());
        }

        return true;
    }

    /**
     * @param $novoDistribuidor Cliente
     * @return bool
     */
    public static function notificarAoDistribuidorDoNovoClienteProdutoCron($novoDistribuidor)
    {


        $mensagem = "Você recebeu um novo contato interessado em produtos na sua Agenda do Distribuidor da Spigreen.";
        $mensagemEmail = Mensagem::getCabecalho();
        $mensagemEmail .=
            "<tr>
			<td class='titulo'><b>Novo Contato na Agenda</b></td>
            </tr>
            <tr>
			<td id='conteudo-email'>
				<br>
				<p><b>Olá, <span class='rosa'>" . $novoDistribuidor->getNomeCompleto() . "</span></b></p>
				<br>
                <p>
                Você recebeu um novo contato interessado em produtos na sua Agenda do Distribuidor da Spigreen.
                <br /><br />
					
					Atenciosamente.
				</p>
				<br />
				&nbsp;
			</td>
		</tr>";
        $mensagemEmail .= Mensagem::getRodape();

        try {
            /* @var $objConfiguracao DistribuidorConfiguracao */
            $objConfiguracao = $novoDistribuidor->getDistribuidorConfiguracaos()->getFirst();

            if (!is_null($objConfiguracao)) {
                $integracaoMfw = new IntegracaoMailforweb($objConfiguracao->getChaveApiMailforweb());
                $result = $integracaoMfw->enviaSMS($objConfiguracao->getTelefoneNotificacao(), $mensagem);
                Qmail::enviaMensagem($novoDistribuidor->getEmail(), "Novo Contato na Agenda :: ", $mensagemEmail);
                //$result = $integracaoMfw->enviaEmailTransacional($objCliente->getEmail(), "Novo Contato na Agenda :: " . Mensagem::getEmpresa()->getNome(), $mensagemEmail, Mensagem::getEmpresa()->getEmail(), Mensagem::getEmpresa()->getNome());
            }
        } catch (Exception $ex) {
            error_log($ex->getMessage());
        }

        return true;
    }

    /**
     * @param $clienteDistribuidor ClienteDistribuidor
     * @return bool
     */
    public static function notificarAoDistribuidorDoNovoClienteDistribuidorCron($novoDistribuidor)
    {

        $mensagem = "Novo contato cadastrado em sua Agenda do Distribuidor interessado no Negócio da Spigreen. Acesse sua agenda e faça o contato em no máximo 24h!";
        $mensagemEmail = Mensagem::getCabecalho();
        $mensagemEmail .=
            "<tr>
			<td class='titulo'><b>Novo Contato na Agenda</b></td>
            </tr>
            <tr>
			<td id='conteudo-email'>
				<br>
				<p><b>Olá, <span class='rosa'>" . $novoDistribuidor->getNomeCompleto() . "</span></b></p>
				<br>
				<p>Novo contato agenda do distribuidor!</p>
				    <p>
				    Novo contato cadastrado em sua Agenda do Distribuidor interessado no Negócio da Spigreen. 
				    <br><br>Acesse sua agenda e faça o contato em no máximo 24h!
					<br /><br />
					
					Atenciosamente.
				</p>
				<br />
				&nbsp;
			</td>
		</tr>";
        $mensagemEmail .= Mensagem::getRodape();

        try {
            /* @var $objConfiguracao DistribuidorConfiguracao */
            $objConfiguracao = $novoDistribuidor->getDistribuidorConfiguracaos()->getFirst();

            if (!is_null($objConfiguracao)) {
                $integracaoMfw = new IntegracaoMailforweb($objConfiguracao->getChaveApiMailforweb());
                $result = $integracaoMfw->enviaSMS($objConfiguracao->getTelefoneNotificacao(), $mensagem);
                Qmail::enviaMensagem($novoDistribuidor->getEmail(), "Novo Contato na Agenda :: " , $mensagemEmail);
                //$result = $integracaoMfw->enviaEmailTransacional(, , , Mensagem::getEmpresa()->getEmail(), Mensagem::getEmpresa()->getNome());
            }
        } catch (Exception $ex) {
            error_log($ex->getMessage());
        }

        return true;
    }

}
