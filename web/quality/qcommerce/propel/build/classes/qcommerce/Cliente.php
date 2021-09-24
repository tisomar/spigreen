<?php

/**
 * Skeleton subclass for representing a row from the 'QP1_CLIENTE' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class Cliente extends BaseCliente
{

    const TAMANHO_CHAVE_INDICACAO = 11;

    const LADO_AUTOMATICO = 'AUTOMATICO';
    const LADO_ESQUERDO = 'ESQUERDO';
    const LADO_DIREITO = 'DIREITO';

    const PONTOS_INDIVIDUAIS = 'INDIVIDUAL';

    public function preInsert(\PropelPDO $con = null)
    {
        if (!$this->getChaveIndicacao()) {
            $this->gerarChaveIndicacao();
        }

        return parent::preInsert($con);
    }

    /**
     * Valida as entradas dos dados no objeto com as validações
     * definidas neste método e no schema.xml do ORM.
     *
     * @param array $erros
     * @param mixed $columns
     * @return boolean TRUE se não encontrou nenhum erro.
     */
    public function myValidate(&$erros, $columns = null)
    {
        /**
         * Valida se o CPF é válido.
         */
        if ($this->getCpf() != '' && false == isValidCpf($this->getCpf())) {
            $erros[] = "Você deve informar um CPF válido.";
        } else {
            /**
             * Verifica se o CPF informado já está cadastrado no sistema.
             *
             * RETIRADO VALIDAÇAO NO TICKET 2652
             * https://qualitypress1.freshdesk.com/helpdesk/tickets/2652
             *
             */
            /*

            if (ClienteQuery::create()->filterByCpf($this->getCpf())->filterById($this->getId(), Criteria::NOT_EQUAL)->count() > 0) {
                $erros[] = "Este CPF já está cadastrado em nosso sistema.";
            }

            */
        }

        if ($this->isPessoaJuridica()) {
            /**
             * Valida se o CPF é válido.
             */
            if ($this->getCnpj() != '' && false == isValidCnpj($this->getCnpj())) {
                $erros[] = "Você deve informar um CNPJ válido.";
            } else {
                /**
                 * Verifica se o CPF informado já está cadastrado no sistema.
                 */
                if (ClienteQuery::create()->filterByCnpj($this->getCnpj())->filterById($this->getId(), Criteria::NOT_EQUAL)->count() > 0) {
                    $erros[] = "Este CNPJ já está cadastrado em nosso sistema.";
                }
            }
        }

        return parent::myValidate($erros, $columns);
    }

    public function setDataNascimento($v)
    {
        if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $v)) {
            $v = data_mysql($v);
        }

        parent::setDataNascimento($v);
    }

    /**
     * Retorna o nome completo do cliente
     *
     * @return string
     */
    public function getNomeCompleto()
    {
        return $this->isPessoaFisica() || $this->getVago() ? trim($this->getNome()) : trim($this->getRazaoSocial());
    }

    public function getStatusDescricao($addIcon = false)
    {

        $template = '<h4><span class="label label-%s label-lg">%s</span></h4>';

        $label = '';
        $content = '';

        $statusList = ClientePeer::getStatusList();
        if (isset($statusList[$this->getStatus()])) {
            if ($addIcon) {
                switch ($this->getStatus()) {
                    case ClientePeer::STATUS_APROVADO:
                        $content .= '<span class="icon-ok"></span> ';
                        $label = 'success';
                        break;
                    case ClientePeer::STATUS_PENDENTE:
                        $content .= '<span class="icon-time"></span> ';
                        $label = 'warning';
                        break;
                    case ClientePeer::STATUS_REPROVADO:
                        $content .= '<span class="icon-remove"></span> ';
                        $label = 'danger';
                        break;
                }
            }
            $content .= $statusList[$this->getStatus()];
        }

        if (!isset($label)) {
            $label = 'label-default';
        }

        return sprintf($template, $label, $content);
    }

    public function getStatusLabel()
    {

        $options = array(
            ClientePeer::STATUS_PENDENTE => array(
                'label' => 'warning',
                'icon' => 'icon-time',
                'title' => 'Pendente'
            ),
            ClientePeer::STATUS_APROVADO => array(
                'label' => 'success',
                'icon' => 'icon-ok',
                'title' => 'Aprovado'
            ),
            ClientePeer::STATUS_REPROVADO => array(
                'label' => 'danger',
                'icon' => 'icon-ban-circle',
                'title' => 'Reprovado'
            ),
        );

        $title = $label = $icon = null;

        extract($options[$this->getStatus()]);


        return label($title, $label, $icon);
    }


    /**
     *
     * Seta a senha do usuario com a criptografia md5();
     *
     * @param string $v A senha a ser definida na instancia do usuario
     * @return Cliente
     */
    public function setSenha($s)
    {
        if ($s != '') {
            parent::setSenha(sha1($s));
        }

        return $this;
    }

    /**
     * @return Endereco|null
     * @throws PropelException
     */
    public function getEnderecoPrincipal()
    {

        $retorno = null;
        $endereco = EnderecoQuery::create()
            ->filterByClienteId($this->getId())
            ->filterByEnderecoPrincipal(true, Criteria::EQUAL)
            ->findOne();

        if (is_null($endereco)) {
            $endereco = EnderecoQuery::create()
                ->filterByClienteId($this->getId())
                ->findOne();

            if (!is_null($endereco)) {
                $endereco->setEnderecoPrincipal(true);
                $endereco->save();
            } else {
                $retorno = $endereco;
            }
        } else {
            $retorno = $endereco;
        }

        return $retorno;
    }

    /**
     * Envia email com link para o cliente criar uma nova senha
     *
     * @return boolean True se enviou o email false senao
     */
    public function recoveryPassword()
    {
        // Gera um token com validade para a recuperacao da nova senha
        $this->gerarTokenRecuperacaoSenha();
        \QPress\Mailing\Mailing::clienteRecuperacaoSenha($this, get_url_site() . "/login/recuperacao-de-senha/");
    }

    /**
     * Metodo para gerar o token de recuperacao de senha já com uma data de validade
     * O cliente é atualizado com estas informacoes
     *
     * @return null
     * @throws PropelException
     * @author Felipe Corrêa
     * @since 2013-01-31 10:16
     *
     */
    public function gerarTokenRecuperacaoSenha()
    {

        // Gerando um token a partir do id do usuário e do tempo atual para criar
        // um identificar único para a recuperação de senha
        $token = md5($this->getId() . microtime());

        // Data maxima em que o token podera ser utilizado
        $dataExpiracao = date('Y-m-d 23:59:59', strtotime('+2 day'));

        $this->setRecuperacaoSenhaToken($token);
        $this->setRecuperacaoSenhaData($dataExpiracao);

        $this->save();
    }

    /**
     * Verifica se a string do token é valida e se está dentro do prazo de validade
     *
     * @return bool True se o token é válido
     */
    public function isTokenRecuperacaoSenhaValido()
    {
        return !is_null($this->getRecuperacaoSenhaToken()) && strtotime('now') <= strtotime($this->getRecuperacaoSenhaData('Y-m-d'));
    }

    /**
     * Invalida token de recuperação de token (limpa o token)
     *
     * @return void
     * @since 13/02/2013
     * @author Felipe Corrêa
     */
    public function invalidaTokenRecuperacaoSenha()
    {

        $this->setRecuperacaoSenhaToken(null);
        $this->setRecuperacaoSenhaData(null);
        $this->save();
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
     * getUltimoPedidoRealizado()   - Localiza qual foi o último pedido realizado
     *                              pelo cliente em questão
     */
    public function getUltimoPedidoRealizado()
    {
        return PedidoQuery::create()->filterByCliente($this)->orderById(Criteria::DESC)->findOne();
    }

    public function getUltimoPedidoConfirmado()
    {
        return PedidoQuery::create()
            ->filterByClienteId($this->getId())
            ->filterByPagamentoConfirmado(false)
            ->orderById(Criteria::DESC)
            ->findOne();
    }

    /**
     * Efeuta o login do Cliente
     */
    public function efetuaLogin()
    {
        ClientePeer::setClienteLogado($this);
    }

    public function isPessoaJuridica()
    {
        return !empty($this->getCnpj());
    }

    public function isPessoaFisica()
    {
        return $this->isPessoaJuridica() == false;
    }

    public function getPrimeiroNome()
    {
        $parts = explode(' ', trim($this->getNomeCompleto()));
        return array_shift($parts);
    }

    public function getLastNome()
    {
        $parts = trim(str_replace(trim($this->getPrimeiroNome()), '', trim($this->getNomeCompleto())));
        return $parts;
    }

    /**
     *
     * @return string
     */
    public function getTelefoneDDD()
    {
        $tel = parent::getTelefone();
        if ($tel) {
            $arr = explode(' ', $tel);
            if (count($arr) > 0) {
                return $arr[0];
            }
        }
        return '';
    }

    /**
     *
     * @return string
     */
    public function getTelefoneSemDDD()
    {
        $tel = parent::getTelefone();
        if ($tel) {
            $arr = explode(' ', $tel);
            if (count($arr) > 1) {
                return $arr[1];
            }
        }
        return '';
    }

    public function getCodigoFederal()
    {
        return $this->isPessoaFisica() ?
            $this->getCpf() :
            $this->getCnpj();
    }


    public function validateOnDelete()
    {
        $failures = array();

        // Não permite excluir clientes vinculados à pedidos criados
        if (PedidoQuery::create()->filterByCliente($this)->count() > 0) {
            $failures[] = 'Este cliente não pode ser excluído porque há pedidos vinculados à ele.';
        }

        return $failures;
    }


    /**
     *
     * @return string Chave gerada.
     */
    public function gerarChaveIndicacao()
    {
        if ($this->getChaveIndicacao()) {
            throw new LogicException('Cliente já possui uma chave.');
        }

        $tamanho = self::TAMANHO_CHAVE_INDICACAO;
        $alfabeto = '0123456789';
        $min = 0;
        $max = strlen($alfabeto) - 1;
        $chave = '';
        for ($i = 0; $i < $tamanho; $i++) {
            $rand = mt_rand($min, $max);
            $chave .= $alfabeto[$rand];
        }

        //garante que geramos um chave unica
        $query = ClienteQuery::create()
            ->filterByChaveIndicacao($chave);

        if ($query->count() > 0) {
            //Chave ja existe. Tenta novamente.
            return $this->gerarChaveIndicacao();
        }

        $this->setChaveIndicacao($chave);

        return $chave;
    }


    /**
     *
     * @param Cliente $parent
     * @return Cliente
     * @throws PropelException
     */
    public function insertAsFirstChildOf($parent)
    {
        $ret = parent::insertAsFirstChildOf($parent);

        $this->setClienteRelatedByClienteIndicadorId($parent);

        return $ret;
    }

    /**
     *
     * @param Cliente $parent
     * @return Cliente
     * @throws PropelException
     */
    public function insertAsLastChildOf($parent)
    {
        $ret = parent::insertAsLastChildOf($parent);

        $this->setClienteRelatedByClienteIndicadorId($parent);

        return $ret;
    }


    /**
     *
     * @param Cliente $sibling
     * @return Cliente
     * @throws PropelException
     */
    public function insertAsNextSiblingOf($sibling)
    {
        $ret = parent::insertAsNextSiblingOf($sibling);

        $this->setClienteRelatedByClienteIndicadorId($sibling->getParent());

        return $ret;
    }


    /**
     *
     * @param Cliente $sibling
     * @return Cliente
     * @throws PropelException
     */
    public function insertAsPrevSiblingOf($sibling)
    {

        $ret = parent::insertAsPrevSiblingOf($sibling);

        $this->setClienteRelatedByClienteIndicadorId($sibling->getParent());

        return $ret;
    }

    /**
     *
     * @param PropelPDO $con
     * @return Cliente|null
     * @throws PropelException
     */
    public function getPatrocinador(PropelPDO $con = null)
    {
        return $this->getClienteRelatedByClienteIndicadorId($con);
    }


    /**
     * Retorna o patrocinador direto do cliente (o patrocinador que o cliente informou na finalização do primeiro pedido).
     * Atenção: este patrocinador pode ser diferente do que o pai na arvore binaria (retornado por getPatrocinador/getParent).
     * Isso pode acontecer quando o cliente for inserido abaixo de um patrocinador diferente do que solicitou (devido as regras de rede binaria).
     *
     * @param PropelPDO $con
     * @return Cliente|null
     * @throws PropelException
     */
    public function getPatrocinadorDireto(PropelPDO $con = null)
    {
        return $this->getClienteRelatedByClienteIndicadorDiretoId($con);
    }

    public function setLadoInsercaoCadastrados($v)
    {
        if (!in_array($v, array(self::LADO_AUTOMATICO, self::LADO_ESQUERDO, self::LADO_DIREITO))) {
            throw new InvalidArgumentException('Lado inválido.');
        }

        return parent::setLadoInsercaoCadastrados($v);
    }

    /**
     *
     * @param PropelPDO $con
     * @throws PropelException
     */
    public function renovaMensalidade(PropelPDO $con = null)
    {
        if ($dtVencimento = $this->getVencimentoMensalidade(null)) {
            $timestamp = get_x_months_to_the_future($dtVencimento->getTimestamp(), 1);
        } else {
            $timestamp = get_x_months_to_the_future(time(), 1);
        }
        $this->setVencimentoMensalidade(new Datetime("@$timestamp"));

        $this->save($con);
    }

    /**
     *
     * @return bool
     */

    public function isLivreMensalidade()
    {
        return (bool)$this->getLivreMensalidade();
    }

    /**
     * @param DateTime $now
     * @return bool
     * @throws PropelException
     */
    public function isMensalidadeEmDia(DateTime $now = null)
    {
//        if (null === $now) {
//            $now = new DateTime('now');
//        }
//
//        /**
//         * Retirado a validação da mensalidade.
//         */
//        return true;

        return $this->isLivreMensalidade()
            || ($this->getVencimentoMensalidade() &&
                $this->getVencimentoMensalidade(null)->getTimestamp() >= $now->getTimestamp());
    }

    public function getTotalParticipantesRede($minDate = null, $maxData = null)
    {
        $query = ClienteQuery::create()
            ->addAsColumn('total', "COUNT(qp1_cliente.ID)")
            ->join('Plano')
            ->addJoinCondition('Plano', 'Plano.PlanoClientePreferencial <> ?', '1')
            ->filterByTreeRight($this->getTreeRight(), Criteria::LESS_EQUAL)
            ->filterByTreeLeft($this->getTreeLeft(), Criteria::GREATER_EQUAL)
            ->filterByVago(0, Criteria::EQUAL);

        if($minDate !== null && $maxData !== null) :
            $query->filterByCreatedAt(['min' => $minDate, 'max' => $maxData]);
        endif;

        if ($row = BasePeer::doSelect($query)->fetch()) {
            return $row['total'];
        }

        return (($this->getTreeRight() - $this->getTreeLeft()) + 1) / 2;
    }

    /**
     * @param $count
     * @return bool
     *
     */
    public function zerarCadastro($count)
    {
        //seta dados do cliente como vago
        try {
            $clienteInativo = ClienteInativadoQuery::create()->filterByClienteId($this->getId())->findOneOrCreate();
            $actuallyClient = $this->copy();
            $clienteInativo->setByArray($actuallyClient->toArray());
            $clienteInativo->setChaveIndicacao($this->getChaveIndicacao());
            $clienteInativo->setPlanoId($this->getPlanoId());
            $clienteInativo->setRazaoSocial($this->getRazaoSocial());
            $clienteInativo->setNomeFantasia($this->getNomeFantasia());
            $clienteInativo->setInscricaoEstadual($this->getInscricaoEstadual());
            $clienteInativo->setCnpj($this->getCnpj());
            $clienteInativo->setLadoInsercaoCadastrados($this->getLadoInsercaoCadastrados());
            $clienteInativo->setTreeLeft($this->getTreeLeft());
            $clienteInativo->setTreeRight($this->getTreeRight());
            $clienteInativo->setTreeLevel($this->getTreeLevel());
            $clienteInativo->setDataNascimento($this->getDataNascimento());
            $clienteInativo->setIndicadorId($this->getClienteIndicadorId());
            $clienteInativo->setIndicadorDiretoId($this->getClienteIndicadorDiretoId());

            $this->setNome('Cadastro Vago ' . $count);
            $this->setCpf($this->getCpfZerado());
            $this->setEmail('vago' . $count . '@spigreen.com.br');
            $this->setDataNascimento('01/01/1960');
            $this->setSenha('vago' . $count);
            $this->setLadoInsercaoCadastrados('AUTOMATICO');
            $this->setVago(1);

            //seta dados do precadastro
            $precadastro = PreCadastroClienteQuery::create()->findOneByClienteId($this->getId());
            if ($precadastro instanceof PreCadastroCliente) {
                $precadastro->setConcluido(true);
            }

            //zera extrato
            $extratoPositivo = ExtratoQuery::create()
                ->filterByOperacao('+', Criteria::EQUAL)
                ->filterByClienteId($this->getId())
                ->groupByClienteId()
                ->select(array('total_pontos'))
                ->withColumn(' coalesce(sum(PONTOS),0) ', 'total_pontos')
                ->findOne();

            $extratoNegativo = ExtratoQuery::create()
                ->filterByOperacao('-', Criteria::EQUAL)
                ->filterByCliente($this)
                ->groupByClienteId()
                ->select(array('total_pontos'))
                ->withColumn(' coalesce(sum(PONTOS),0) ', 'total_pontos')
                ->findOne();

            if (is_null($extratoPositivo)) {
                $extratoPositivo = 0;
            }

            if (is_null($extratoNegativo)) {
                $extratoNegativo = 0;
            }

            $extrato = $extratoPositivo - $extratoNegativo;
            $operacao = '-';

            if ($extrato < 0) {
                $operacao = '+';
                $extrato = abs($extrato);
            }

            if ($extrato < 0 || $extrato > 0) {
                $objExtrato = new Extrato();
                $objExtrato->setClienteId($this->getId());
                $objExtrato->setTipo(Extrato::TIPO_SISTEMA);
                $objExtrato->setPontos($extrato);
                $objExtrato->setOperacao($operacao);
                $objExtrato->setData(date('Y-m-d'));
                $objExtrato->setObservacao('Pré Cadastro inativado.');
                $objExtrato->save();
            }

            $clienteInativo->save();

            if ($precadastro instanceof PreCadastroCliente) {
                $precadastro->save();
            }

            $this->save();

            return true;
        } catch (Exception $e) {
            return false;
        }

        return false;
    }


    public function getCpfZerado()
    {

        $cpf = $this->cpfRandom(1);

        return $cpf;
    }

    public function cpfRandom($mascara = "1")
    {
        $n1 = rand(0, 9);
        $n2 = rand(0, 9);
        $n3 = rand(0, 9);
        $n4 = rand(0, 9);
        $n5 = rand(0, 9);
        $n6 = rand(0, 9);
        $n7 = rand(0, 9);
        $n8 = rand(0, 9);
        $n9 = rand(0, 9);
        $d1 = $n9 * 2 + $n8 * 3 + $n7 * 4 + $n6 * 5 + $n5 * 6 + $n4 * 7 + $n3 * 8 + $n2 * 9 + $n1 * 10;
        $d1 = 11 - (self::mod($d1, 11));
        if ($d1 >= 10) {
            $d1 = 0;
        }
        $d2 = $d1 * 2 + $n9 * 3 + $n8 * 4 + $n7 * 5 + $n6 * 6 + $n5 * 7 + $n4 * 8 + $n3 * 9 + $n2 * 10 + $n1 * 11;
        $d2 = 11 - (self::mod($d2, 11));
        if ($d2 >= 10) {
            $d2 = 0;
        }
        $retorno = '';
        if ($mascara == 1) {
            $retorno = '' . $n1 . $n2 . $n3 . "." . $n4 . $n5 . $n6 . "." . $n7 . $n8 . $n9 . "-" . $d1 . $d2;
        } else {
            $retorno = '' . $n1 . $n2 . $n3 . $n4 . $n5 . $n6 . $n7 . $n8 . $n9 . $d1 . $d2;
        }
        return $retorno;
    }

    /**
     * @param type $dividendo
     * @param type $divisor
     * @return type
     */
    public function mod($dividendo, $divisor)
    {
        return round($dividendo - (floor($dividendo / $divisor) * $divisor));
    }

    /**
     * @param int $nivelMax
     * @return array
     */
    public function getRedeIndicacaoDiretaCliente($nivelMax = 10)
    {
        $rede = array();
        ClientePeer::getIndicadoresCliente($this->getId(), $rede, 1, $nivelMax);
        return $rede;
        /** @todo Implementar processo separação da rede; */
    }

    public function getIndicacaoDiretaCliente($arrClienteId)
    {

        $rede = array();
        if (count($arrClienteId) > 0) {
            foreach ($arrClienteId as $clienteId) {
                ClientePeer::getIndicadorCliente($clienteId, $rede);
            }
        }
        return $rede;
    }

    /*
    * Retorna a quantidade de pontos que um cliente ganhou em um determinado mês.
    * Não leva em consideração os gastos.
    *
    * tipoPontos
    * Os pontos podem ser INDIVIDUAIS que é exatamente o que está no extrato e equivale a 12% dos pontos gerados pelo pedido
    * Ou GERAIS que é 88% dos pontos que foram gerados pelo pedido (este precisa ser calculado);
    *
    */
    public function getTotalPontosMes($mes = null, $ano = null, $tipoPontos = self::PONTOS_INDIVIDUAIS, $dataAberta = null)
    {

        if (is_array($dataAberta)) {
            $arrDatas = array(format_data($dataAberta[0], UsuarioPeer::LINGUAGEM_INGLES), format_data($dataAberta[1], UsuarioPeer::LINGUAGEM_INGLES));
        } else {
            $arrDatas = get_datas_limite_mes($mes, $ano);
        }

        $c = new Criteria();
        $c->add(ExtratoPeer::CLIENTE_ID, $this->getId());
        $c->add(ExtratoPeer::DATA, $arrDatas[0], Criteria::GREATER_EQUAL);
        $c->addAnd(ExtratoPeer::DATA, $arrDatas[1], Criteria::LESS_EQUAL);
        $c->add(ExtratoPeer::OPERACAO, ExtratoPeer::POSITIVO);
        $c->add(ExtratoPeer::TIPO, ExtratoPeer::PEDIDO);
        $c->add(ExtratoPeer::BLOQUEADO, 0);
//        $c->add(ExtratoPeer::CANCELAMENTO, 0);

        $arrExtrato = ExtratoPeer::doSelect($c);
        $total = 0;
        foreach ($arrExtrato as $objExtrato) {
            /* @var $objExtrato Extrato */
            if ($tipoPontos == self::PONTOS_INDIVIDUAIS) {
                $total += $objExtrato->getPontos();
            } else {
                $pontos_12porc = $objExtrato->getPontos();
                $pontos_100porc = ($pontos_12porc * 100) / 12;
                $pontos_88porc = ($pontos_100porc * 88) / 100;

                $total += round($pontos_88porc, 2, PHP_ROUND_HALF_DOWN);
            }
        }

        return $total;
    }

    /*
         * Retorna o saldo de pontos disponível na conta do cliente
         *
         */
    public function getSaldoPontos($dataAte = null)
    {

        $objResult = ExtratoQuery::create()
            ->withColumn('SUM(' . ExtratoPeer::PONTOS . ')', 'Total')
            ->filterByClienteId($this->getId())
            ->filterByOperacao(ExtratoPeer::POSITIVO)
            ->filterByBloqueado(0)
            ->filterByData((!$dataAte) ? '2299-01-01' : $dataAte, Criteria::LESS_THAN)
            ->find();

        $positivos = $objResult[0]->getTotal();

        $objResult = ExtratoQuery::create()
            ->withColumn('SUM(' . ExtratoPeer::PONTOS . ')', 'Total')
            ->filterByClienteId($this->getId())
            ->filterByOperacao(ExtratoPeer::NEGATIVO)
            ->filterByBloqueado(0)
            ->filterByData((!$dataAte) ? '2299-01-01' : $dataAte, Criteria::LESS_THAN)
            ->find();
        $negativos = $objResult[0]->getTotal();

        return $positivos - $negativos;
    }

    public function getSaldoPontosBloqueados()
    {
        $objResult = ExtratoQuery::create()
            ->withColumn('SUM(' . ExtratoPeer::PONTOS . ')', 'Total')
            ->filterByClienteId($this->getId())
            ->filterByOperacao(ExtratoPeer::POSITIVO)
            ->filterByBloqueado(1)
            ->find();
        $positivos = $objResult[0]->getTotal();
        return $positivos;
    }

//    public function getTotalPontosMesRedeFromCache()
//    {
//        $objPontosRede = PontosRedePeer::retrieveByPK($this->getId());
//        if ($objPontosRede instanceof PontosRede) {
//            return array($objPontosRede->getPontos(), $objPontosRede->getDataAtualizacao('d/m/Y H:i'));
//        } else {
//            return array(0, date('d/m/Y 00:00'));
//        }
//    }
    /**
     * @param bool $endereco
     * @param Endereco|null $objEndereco
     * @return null
     */
    public function insertClienteDistribuidor($endereco = false, Endereco $objEndereco = null)
    {

        try {
            $objClienteDistribuidor = new ClienteDistribuidor();

            $objClienteDistribuidor->setClienteId($this->getClienteIndicadorId());
            $objClienteDistribuidor->setClienteRedefacilId($this->getId());
            $objClienteDistribuidor->setEmail($this->getEmail());
            $objClienteDistribuidor->setTipoLead('C');
            $objClienteDistribuidor->setStatus('PENDENTE');
            $objClienteDistribuidor->setLead(true);
            $objClienteDistribuidor->setTipo($this->isPessoaFisica() ? 'F' : 'J');
            $objClienteDistribuidor->setTelefoneCelular($this->getTelefone());
            $objClienteDistribuidor->setNomeRazaoSocial($this->getPrimeiroNome() . ' ' . $this->getRazaoSocial());
            $objClienteDistribuidor->setSobrenomeNomeFantasia($this->getLastNome() . ' ' . $this->getNomeFantasia());
            $objClienteDistribuidor->setCpfCnpj($this->getCodigoFederal());
            $objClienteDistribuidor->setNotificacaoAlerta(0);
            $objClienteDistribuidor->setAlertaAvisoMudancaPatrocinador(0);

            if ($endereco) {
                $objClienteDistribuidor->setCep($objEndereco->getCep());
                $objClienteDistribuidor->setEndereco($objEndereco->getLogradouro());
                $objClienteDistribuidor->setNumero($objEndereco->getNumero());
                $objClienteDistribuidor->setComplemento($objEndereco->getComplemento());
                $objClienteDistribuidor->setBairro($objEndereco->getBairro());
                $objClienteDistribuidor->setCidade($objEndereco->getCidadeId());
                $objClienteDistribuidor->setEstado($objEndereco->getCidade()->getEstadoId());
            }

            $objClienteDistribuidor->save();
        } catch (Exception $e) {
            return null;
        }
    }


    public function isConsumidorFinal()
    {

        return $this->getTipoConsumidor() == 0 ? true : false;
    }

    public function quantidadeMensagensPendentes()
    {
        $birthday = $this->getDataNascimento('Y-m-d 00:00:00');
        $birthdayDateTime = new DateTime($birthday);

        $now = new DateTime('now');

        $birthdayThisYear = date_create_from_format('Y-m-d H:i:s', $now->format('Y') . '-' . $birthdayDateTime->format('m') . '-' . $birthdayDateTime->format('d') . " 00:00:00");
        $birthdayLastYear = date_create_from_format('Y-m-d H:i:s', $now->format('Y') . '-' . $birthdayDateTime->format('m') . '-' . $birthdayDateTime->format('d') . " 00:00:00")->modify('-1 year');

        $interval = $now->diff($birthdayThisYear);
        $diff = $interval->format('%r%a');
        //Verifique o aniversário
        if ($diff <= 0) {
            $lastBirthday = $birthdayThisYear;
        } else {
            $lastBirthday = $birthdayLastYear;
        }
        $SQL = "SELECT COUNT(da.ID)
        FROM qp1_documento_alerta da
          LEFT JOIN qp1_documento_alerta_clientes dsc on (da.ID = dsc.DOCUMENTO_ALERTA_ID AND dsc.CLIENTE_ID = " . $this->getId() . " )

        WHERE
        da.ID_CLIENTES_STR LIKE \"%," . $this->getId() . ",%\"
        AND da.DATA_ENVIO <='" . $now->format('Y-m-d') . "'
        AND (dsc.CLIENTE_ID is NULL OR dsc.DATA_LIDO is NULL )
        AND da.TIPO_MENSAGEM != 'aniversariantes'
        AND da.TITULO != ''
        AND da.CORPO != ''
        AND da.CANCELADA = false";
        $con = Propel::getConnection();
        $stmt = $con->prepare($SQL);
        $stmt->execute();
        $mensagens = $stmt->fetchAll(PDO::FETCH_COLUMN);


        $SQLAniv = "SELECT
                    COUNT(dsc.ID) AS total_feitos,
                        (SELECT ID
                        FROM qp1_documento_alerta da
                        WHERE da.ID_CLIENTES_STR LIKE \"%," . $this->getId() . ",%\" 
                        AND da.TIPO_MENSAGEM = 'aniversariantes' 
                        AND da.DATA_ENVIO <= '" . $lastBirthday->format('Y-m-d') . "'
                        ORDER BY ORDEM LIMIT 1) AS alerta_cadastrada
                    FROM qp1_documento_alerta_clientes dsc
                    WHERE dsc.CLIENTE_ID = " . $this->getId() . "
                        AND dsc.DOCUMENTO_ALERTA_ID IN
                                (SELECT ID
                                FROM qp1_documento_alerta da
                                WHERE da.ID_CLIENTES_STR LIKE \"%," . $this->getId() . ",%\" 
                                    AND da.TIPO_MENSAGEM = 'aniversariantes' 
                                    AND da.DATA_ENVIO <= '" . $lastBirthday->format('Y-m-d') . "'
          )";
        $con = Propel::getConnection();
        $stmt = $con->prepare($SQLAniv);
        $stmt->execute();
        $mensagensAniv = $stmt->fetchObject();
        if ($mensagensAniv->alerta_cadastrada != null && $mensagensAniv->total_feitos == 0) {
            return ($mensagens[0] + 1);
        } else {
            return $mensagens[0];
        }
    }

    public function mensagensPendentes()
    {
        $birthday = $this->getDataNascimento('Y-m-d 00:00:00');

        $birthdayDateTime = new DateTime($birthday);
        $now = new DateTime('now');
        $birthdayThisYear = date_create_from_format('Y-m-d H:i:s', $now->format('Y') . '-' . $birthdayDateTime->format('m') . '-' . $birthdayDateTime->format('d') . " 00:00:00");
        $birthdayLastYear = date_create_from_format('Y-m-d H:i:s', $now->format('Y') . '-' . $birthdayDateTime->format('m') . '-' . $birthdayDateTime->format('d') . " 00:00:00")->modify('-1 year');

        $interval = $now->diff($birthdayThisYear);
        $diff = $interval->format('%r%a');

        //Verifique o aniversário
        if ($diff <= 0) :
            $lastBirthday = $birthdayThisYear;
        else :
            $lastBirthday = $birthdayLastYear;
        endif;

        $SQL = "SELECT 
                    da.TIPO_DEST, 
                    da.TIPO_MENSAGEM, 
                    da.ID, 
                    da.SOMENTE_LEITURA, 
                    da.DATA_ENVIO, dsc.DATA_CRIACAO, dsc.DATA_LIDO, da.TITULO, da.CORPO
        FROM qp1_documento_alerta da
          LEFT JOIN qp1_documento_alerta_clientes dsc on (da.ID = dsc.DOCUMENTO_ALERTA_ID AND dsc.CLIENTE_ID = " . $this->getId() . " )
        WHERE da.DATA_ENVIO <='" . $now->format('Y-m-d') . "'
        AND (dsc.CLIENTE_ID is NULL OR dsc.DATA_LIDO is NULL)
        AND (da.TIPO_MENSAGEM != 'aniversariantes' OR da.DATA_ENVIO <= '" . $lastBirthday->format('Y-m-d') . "')
        AND da.CANCELADA = false
        AND da.TITULO != ''
        AND da.CORPO != ''
        ORDER BY da.SOMENTE_LEITURA, da.ORDEM ASC";

        $con = Propel::getConnection();
        $stmt = $con->prepare($SQL);
        $stmt->execute();

        $alertas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if ($alertas != false) :
            foreach ($alertas as $key => $alerta) :
                if ($alerta['TIPO_MENSAGEM'] != 'aniversariantes') :
                    $exist = DocumentoAlertaClientesQuery::create()
                        ->filterByDocumentoAlertaId($alerta['ID'])
                        ->filterByCliente($this)
                        ->findOne();

                    /**
                     * @var $alerta DocumentoAlerta
                     */
                    $auxAlerta = DocumentoAlertaQuery::create()
                        ->filterById($alerta['ID'])
                        ->findOne();

                    if (!$exist && $auxAlerta->mostraMensagem($this)) :
                        $documentoAlertaClientes = new DocumentoAlertaClientes();
                        $documentoAlertaClientes->setCliente($this);
                        $documentoAlertaClientes->setDocumentoAlertaId($alerta['ID']);
                        $documentoAlertaClientes->save();
                    endif;
                else :
                    $anterior = DocumentoAlertaClientesQuery::create()
                        ->filterByCliente($this)
                        ->filterByDataCriacao($now->format('Y-m-d 23:59:59'), Criteria::LESS_EQUAL)
                        ->filterByDataCriacao($lastBirthday, Criteria::GREATER_EQUAL)
                        ->useDocumentoAlertaQuery()
                        ->filterByTipoMensagem('aniversariantes')
                        ->endUse()
                        ->findOne();

                    if ($anterior != null) :
                        unset($alertas[$key]);
                    endif;
                endif;
            endforeach;

            if (count($alertas) > 0) :
                $pdfs = null;

                foreach ($alertas as $alerta) :
                    /**
                     * @var $alerta DocumentoAlerta
                     */
                    $auxAlerta = DocumentoAlertaQuery::create()
                        ->filterById($alerta['ID'])
                        ->findOne();

                    if ($auxAlerta->mostraMensagem($this)) :
                        // Aniversariantes não tem PDFs
                        if ($alerta['TIPO_MENSAGEM'] != 'aniversariantes') :
                            $pdfs = DocumentoAlertaPdfQuery::create()
                                ->filterByDocumentoAlertaId($alerta['ID'])
                                ->find();
                        endif;

                        $arrReplaces = $this->getReplaces($alerta);

                        $alerta['TIPO_MENSAGEM'] = DocumentoAlertaPeer::getTipoDesc($alerta['TIPO_MENSAGEM']);
                        $alerta['CORPO'] = str_replace($arrReplaces['keys'], $arrReplaces['values'], $alerta['CORPO']);
                        $alerta['TITULO'] = str_replace($arrReplaces['keys'], $arrReplaces['values'], $alerta['TITULO']);

                        $alertaDocumento = array('alerta' => $alerta, 'pdfs' => $pdfs, 'quantidadeMensagens' => $this->quantidadeMensagensPendentes());

                        return $alertaDocumento;
                    endif;
                endforeach;
            endif;
        endif;
    }

    function getReplaces($alerta)
    {
        $birthday = $this->getDataNascimento('Y-m-d');
        $birthdayDateTime = new DateTime($birthday);
        $now = new DateTime('now');
        $birthdayThisYear = date_create_from_format('Y-m-d', $now->format('Y') . '-' . $birthdayDateTime->format('m') . '-' . $birthdayDateTime->format('d'));
        if (is_array($alerta)) {
            $data = new DateTime($alerta['DATA_ENVIO']);
        } else {
            $data = new DateTime($alerta->DATA);
        }

        $arrReplaces = array(
            '__nome_cliente__' => $this->getNomeCompleto(),
            '__aniversario__' => $birthdayThisYear->format('d/m/Y'),
            '__data__' => $data->format('d/m/Y')
        );

        return array('keys' => array_keys($arrReplaces), 'values' => array_values($arrReplaces));
    }

    public function getMessagemById($id)
    {
        $SQL = "SELECT da.TIPO_DEST, da.TIPO_MENSAGEM, da.ID, da.SOMENTE_LEITURA, da.DATA_ENVIO, dsc.DATA_CRIACAO, dsc.DATA_LIDO, da.TITULO, da.CORPO
        FROM qp1_documento_alerta da
          LEFT JOIN qp1_documento_alerta_clientes dsc on (da.ID = dsc.DOCUMENTO_ALERTA_ID AND dsc.CLIENTE_ID = " . $this->getId() . " )
        WHERE dsc.ID = " . $id . " LIMIT 1";

        $con = Propel::getConnection();
        $stmt = $con->prepare($SQL);
        $stmt->execute();

        $alerta = $stmt->fetch(PDO::FETCH_ASSOC);

        $pdfs = DocumentoAlertaPdfQuery::create()
            ->filterByDocumentoAlertaId($alerta['ID'])
            ->find();

        $arrReplaces = $this->getReplaces($alerta);

        $alerta['TIPO_MENSAGEM'] = DocumentoAlertaPeer::getTipoDesc($alerta['TIPO_MENSAGEM']);
        $alerta['TITULO'] = str_replace($arrReplaces['keys'], $arrReplaces['values'], $alerta['TITULO']);
        $alerta['CORPO'] = str_replace($arrReplaces['keys'], $arrReplaces['values'], $alerta['CORPO']);
        $alertaDocumento = array('alerta' => $alerta, 'pdfs' => $pdfs);

        return $alertaDocumento;
    }

    public function getClienteDataCadastro()
    {
        return date('d/m/Y H:i:s', strtotime($this->getCreatedAt()));
    }

    public function getClienteDataAtivacao($clienteId = 0)
    {
        $clienteDataAtivacao = PedidoQuery::create()
            ->select(['dataAtivacao'])
            ->withColumn('qp1_pedido_status_historico.updated_at', 'dataAtivacao')
            ->filterByClienteId($clienteId)
            ->usePedidoItemQuery()
                ->useProdutoVariacaoQuery()
                    ->useProdutoQuery()
                        ->filterByPlanoId(null, Criteria::ISNOTNULL)
                    ->endUse()
                ->endUse()
            ->endUse()
            ->usePedidoStatusHistoricoQuery()
                ->filterByPedidoStatusId(1)
                ->filterByIsConcluido(1)
            ->endUse()
            ->filterByStatus(Pedido::CANCELADO, Criteria::NOT_EQUAL)
            ->findOne();
            
        if ($clienteDataAtivacao == null) :
            return null;
        endif;
        
        return (new Datetime($clienteDataAtivacao))->format('d/m/Y H:i:s');
    }

    public function getPlanoCarreira($mes, $ano)
    {
        $plano = PlanoCarreiraHistoricoQuery::create()
            ->filterByMes($mes)
            ->filterByAno($ano)
            ->filterByClienteId($this->getId())
            ->findOne();

        return $plano;
    }

    public function isClientePreferencial()
    {
        return $this->getPlano() && $this->getPlano()->getPlanoClientePreferencial();
    }

    public function isClienteDistribuidor()
    {
        return $this->getPlano() && !$this->getPlano()->getPlanoClientePreferencial();
    }

    public function isClienteComPlano()
    {
        return $this->getPlano() ;
    }

    public function isClienteFinal()
    {
        return !$this->getPlano();
    }

    /**
     * @return array
     * @throws PropelException
     */
    public function getClientesRede($incluirRoot = false)
    {
        $query = ClienteQuery::create()
            ->join('Plano')
            ->addJoinCondition('Plano', 'Plano.PlanoClientePreferencial <> ?', '1')
            ->_if(!$incluirRoot)
                ->filterByTreeLeft($this->getTreeLeft(), Criteria::GREATER_THAN)
                ->filterByTreeRight($this->getTreeRight(), Criteria::LESS_THAN)
            ->_else()
                ->filterByTreeLeft($this->getTreeLeft(), Criteria::GREATER_EQUAL)
                ->filterByTreeRight($this->getTreeRight(), Criteria::LESS_EQUAL)
            ->_endif()
            ->filterByVago(0, Criteria::EQUAL)
            ->find();

        $clientes = [];

        foreach ($query as $cliente) :
            $clientes[] = $cliente->getId();
        endforeach;

        return $clientes;
    }

    public function getControlePontuacaoMes($mes, $ano)
    {
        $pontos = ControlePontuacaoClienteQuery::create()
            ->filterByClienteId($this->getId())
            ->filterByMes($mes)
            ->filterByAno($ano)
            ->findOneOrCreate();

        return $pontos;
    }

    public function getRankLideresMaiorRede($minDate = null, $maxDate = null, $limit = null) {

        $minDate = $minDate->format('Y-m-d H:i:s');
        $maxDate = $maxDate->format('Y-m-d H:i:s');
        $indicador = $this->getId();

        $SQL = "
            SELECT 
                c1.ID CLIENTE_ID,
                c1.NOME NOME,
                (
                    SELECT COUNT(c2.ID) 
                    FROM qp1_cliente c2 
                    INNER JOIN `qp1_plano` 
                    ON (c2.PLANO_ID=qp1_plano.ID 
                    AND qp1_plano.PLANO_CLIENTE_PREFERENCIAL <> 1) 
                    WHERE c2.TREE_RIGHT<=c1.TREE_RIGHT AND c2.TREE_LEFT>=c1.TREE_LEFT 
                    AND c2.VAGO=0 
                ) as TOTAL_REDE
            FROM qp1_cliente c1
                INNER JOIN `qp1_plano` 
            ON (c1.PLANO_ID=qp1_plano.ID AND qp1_plano.PLANO_CLIENTE_PREFERENCIAL <> 1) 
            WHERE c1.INDICADOR_DIRETO_ID = $indicador 
            AND c1.VAGO=0 
            ORDER BY TOTAL_REDE DESC
            LIMIT $limit";

        $con = Propel::getConnection();
        $stmt = $con->prepare($SQL);
        $stmt->execute();

        $lideres = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $lideres;
    }

    public function getRankLideresBonusRecompra($minDate = null, $maxDate = null, $limit = null) {

        $minDate = $minDate->format('Y-m-d H:i:s');
        $maxDate = $maxDate->format('Y-m-d H:i:s');
        $indicador = $this->getId();

        $SQL = "
            SELECT 
            c3.ID CLIENTE_ID,
            c3.NOME NOME ,
            (
                SELECT SUM(pi.VALOR_PONTOS_UNITARIO * pi.QUANTIDADE) TOTAL_PONTOS 
                    FROM qp1_pedido p, qp1_cliente c1, qp1_cliente c2, qp1_pedido_status_historico ph, qp1_pedido_item pi, qp1_produto_variacao pv, qp1_produto pr 
                WHERE p.STATUS <> 'CANCELADO' 
                AND p.CLIENTE_ID = c2.ID 
                AND c1.tree_left <= c2.tree_left 
                AND c1.tree_right >= c2.tree_right 
                AND c1.ID = c3.ID 
                AND ph.PEDIDO_ID = p.ID 
                AND ph.PEDIDO_STATUS_ID = 1 
                AND ph.IS_CONCLUIDO = 1 
                AND pi.PEDIDO_ID = p.ID 
                AND pi.PLANO_ID IS NULL 
                AND pi.PRODUTO_VARIACAO_ID = pv.ID 
                AND pv.PRODUTO_ID = pr.ID 
                AND pr.PLANO_ID IS NULL 
                AND c2.ID <> c1.ID 
                AND (p.HOTSITE_CLIENTE_ID IS NULL OR p.HOTSITE_CLIENTE_ID <> c1.ID) 
                AND (NOT EXISTS ( SELECT 1 FROM qp1_extrato_cliente_preferencial e WHERE e.PEDIDO_ID = p.ID ) OR c2.INDICADOR_ID <> c1.ID)
            ) as TOTAL_BONUS_RECOMPRA
        FROM qp1_cliente c3
            INNER JOIN `qp1_plano` 
        ON (c3.PLANO_ID=qp1_plano.ID AND qp1_plano.PLANO_CLIENTE_PREFERENCIAL <> 1) 
        WHERE c3.INDICADOR_DIRETO_ID = $indicador 
        AND c3.VAGO=0 
        ORDER BY TOTAL_BONUS_RECOMPRA DESC
        LIMIT $limit";

        $con = Propel::getConnection();
        $stmt = $con->prepare($SQL);
        $stmt->execute();

        $lideres = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $lideres;
    }

    public function getParticipantesRede($minDate = null, $maxDate = null, $cliente = null)
    {
        $query = ClienteQuery::create()
            ->usePlanoQuery()
                ->filterByPlanoClientePreferencial(false)
            ->endUse()
            ->filterByClienteIndicadorDiretoId($this->getId())
            ->filterByVago(0, Criteria::EQUAL);

        if($minDate !== null && $maxDate !== null) :
            $query->filterByCreatedAt(['min' => $minDate, 'max' => $maxDate]);
        endif;

        if($cliente !== null) :
           $query->filterByNome('%'. $cliente  . '%', Criteria::LIKE); 
        endif;

        return $query->find();
    }

    public function getRankPontosAdesao($minDate = null, $maxDate = null, $limit = null) {
        $minDate = $minDate->format('Y-m-d H:i:s');
        $maxDate = $maxDate->format('Y-m-d H:i:s');
        $indicador = $this->getId();

        $SQL = "
        SELECT 
            c1.ID CLIENTE_ID,
            c1.NOME NOME,
            (
                SELECT IFNULL(SUM(qp1_pedido_item.VALOR_PONTOS_UNITARIO * qp1_pedido_item.QUANTIDADE), 0) AS valorTotalPontos 
                    FROM `qp1_pedido` 
                LEFT JOIN qp1_cliente c2
                    ON (qp1_pedido.CLIENTE_ID=c2.ID) 
                INNER JOIN `qp1_pedido_item` 
                    ON (qp1_pedido.ID=qp1_pedido_item.PEDIDO_ID) 
                LEFT JOIN `qp1_produto_variacao` 
                    ON (qp1_pedido_item.PRODUTO_VARIACAO_ID=qp1_produto_variacao.ID) 
                INNER JOIN `qp1_produto` 
                    ON (qp1_produto_variacao.PRODUTO_ID=qp1_produto.ID) 
                INNER JOIN `qp1_pedido_status_historico` 
                    ON (qp1_pedido.ID=qp1_pedido_status_historico.PEDIDO_ID) 
                    WHERE c2.TREE_RIGHT<c1.tree_right 
                    AND c2.TREE_LEFT>c1.tree_left
                    AND qp1_produto.PLANO_ID IS NOT NULL 
                    AND qp1_pedido.STATUS<>'CANCELADO' 
                    AND qp1_pedido_status_historico.PEDIDO_STATUS_ID=1 
                    AND qp1_pedido_status_historico.IS_CONCLUIDO=1
            ) as TOTAL_ADESAO
        FROM qp1_cliente c1
            INNER JOIN `qp1_plano` 
        ON (c1.PLANO_ID=qp1_plano.ID AND qp1_plano.PLANO_CLIENTE_PREFERENCIAL <> 1) 
        WHERE c1.INDICADOR_DIRETO_ID = $indicador 
        AND c1.VAGO=0 
        ORDER BY TOTAL_ADESAO DESC
        LIMIT $limit";

        $con = Propel::getConnection();
        $stmt = $con->prepare($SQL);
        $stmt->execute();

        $lideres = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $lideres;
    }
}
