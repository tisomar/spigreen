<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$cliente = ClientePeer::getClienteLogado(true);
$clienteId = $cliente->getId();

$gerenciador = new GerenciadorPontos($con = Propel::getConnection(), $logger);

$totalPontosDisponiveis = $gerenciador->getTotalPontosDisponiveisParaResgate($cliente, null, null, 'INDICACAO_DIRETA');
$totalPontosReservados = $gerenciador->getTotalPontosReservadosComResgate($cliente);

$totalPontosDisponiveis = $totalPontosDisponiveis - $totalPontosReservados;
$pontosDisponiveis = $totalPontosDisponiveis < 0 ? 0 : $totalPontosDisponiveis;

$pontosMinimo = (int)Config::get('resgate.valor_minimo');
$pontosMinFormatMoney = formata_pontos($pontosMinimo);

$taxaresgate = (int)Config::get('resgate.taxa');
$resgateDesabilitado = (bool) Config::get('resgate.desabilitado');

$inicioMesAnterior = new Datetime('first day of last month');
$inicioMesAnterior->setTime(0, 0);

$fimMesAnterior = new Datetime('last day of last month');
$fimMesAnterior->setTime(23, 59, 59);

$bloqueiaResgate = !(
    ClientePeer::getClienteAtivoMensal($cliente->getId()) ||
    ClientePeer::getClienteAtivoMensal($cliente->getId(), $inicioMesAnterior, $fimMesAnterior)
);

$erros = array();
$errosCadastroBanco = array();

$BancosCadastrados = BancoCadastroClienteQuery::create()
->filterByCliente($cliente)
->find();

if ($request->request->has('cadastroBanco')) :
    $arrResgate = $request->request->get('cadastroBanco');
    
    if ( $request->request->get('cadastroBanco')['ID'] != null):
        $bancoUpdate = BancoCadastroClienteQuery::create()->findOneById($arrResgate['ID']);
        $bancoUpdate->setBanco($arrResgate['BANCO']);
        $bancoUpdate->setAgencia($arrResgate['AGENCIA']);
        $bancoUpdate->setConta($arrResgate['CONTA']);
        $bancoUpdate->setTipoConta($arrResgate['TIPO_CONTA']);
        $bancoUpdate->setPisPasep($arrResgate['PIS_PASEP']);
        $bancoUpdate->setNomeCorrentista($arrResgate['NOME_CORRENTISTA']);
        $bancoUpdate->setCpf($arrResgate['CPF_CORRENTISTA']);
        $bancoUpdate->setCnpj($arrResgate['CNPJ_CORRENTISTA']);
        $bancoUpdate->save();

        FlashMsg::success('Banco alterado com sucesso.');
        redirect('/minha-conta/resgate');
    endif; 

    if ($BancosCadastrados->count() >= 4):
        $errosCadastroBanco[] = "Não é permitido o cadastro de mais que 4 bancos por cliente";

        FlashMsg::warning('Não é permitido o cadastro de mais que 4 bancos por cliente');
        redirect('/minha-conta/resgate');
    else:
        $BancoCadastroCliente = new BancoCadastroCliente();
        $BancoCadastroCliente->setByArray($arrResgate);
        $BancoCadastroCliente->setCliente($cliente);

        if (!$errosCadastroBanco) :
            $BancoCadastroCliente->save($con);

            FlashMsg::success('Banco cadastrado com sucesso.');
            redirect('/minha-conta/resgate');
        endif;
    endif;
endif;

if ($request->request->has('resgate')) :
    $resgatesPendentes = ResgateQuery::create()
        ->filterBySituacao('PENDENTE')
        ->filterByCliente($cliente)
        ->find();

     if ($resgatesPendentes->count() > 0) :
        $erros[] = "Não é permitido solicitar resgate com outra solicitação pendente.";
    endif;

    $arrRequest = $request->request->get('resgate');

    $bancoSelected = BancoCadastroClienteQuery::create()->findOneById($arrRequest['ID']);
    
    $arrResgate['BANCO'] = $bancoSelected->getBanco();
    $arrResgate['AGENCIA'] = $bancoSelected->getAgencia();
    $arrResgate['CONTA'] = $bancoSelected->getConta();
    $arrResgate['TIPO_CONTA'] = $bancoSelected->getTipoConta();
    $arrResgate['PIS_PASEP'] = $bancoSelected->getPisPasep();
    $arrResgate['NOME_CORRENTISTA'] = $bancoSelected->getNomeCorrentista();
    $arrResgate['CPF_CORRENTISTA'] = $bancoSelected->getCpf();
    $arrResgate['CNPJ_CORRENTISTA'] = $bancoSelected->getCnpj();
    $arrResgate['VALOR'] = str_replace(['R', '$', ' ', '.'], '', $arrRequest['VALOR']);
    $arrResgate['VALOR'] = str_replace(',', '.', $arrResgate['VALOR']);
    $taxa = 0;
   
    $resgate = new Resgate();
    $resgate->setByArray($arrResgate);
    $resgate->setCliente($cliente);

    if ($resgate->getValor() > $pontosDisponiveis) :
        $valorFormatado = formata_pontos($totalPontosDisponiveis);
        $erros[] = "O limite de bônus disponíveis é R$ <strong>$valorFormatado</strong>.";
    endif;
 
    $pis = $resgate->getPisPasep();
    $cnpj = $resgate->getCnpjCorrentista();

    if($clienteId != 8) : 
        if ((is_null($pis) || empty($pis)) && (is_null($cnpj) || empty($cnpj))) :
            $erros[] = "O campo Pis/Pasep não pode ficar em branco.";
        endif;
    endif;
 
    if ($resgate->getValor() < $pontosMinimo) :
        $valorFormatado = formata_pontos($pontosMinimo);
        $erros[] = "Solicitação não atingiu o mínimo para resgate que é de R$ <strong>$valorFormatado</strong>.";
    endif;

    if ($taxaresgate > 0) :
        $taxa = $taxaresgate;
    endif;

    $resgate->setValorTaxa($taxa);
    $resgate->setValorDepositar($resgate->getValor() - $taxa);

    if ($resgate->myValidate($erros) && !$erros) :
        $resgate->save($con);

        try {
            QPress\Mailing\Mailing::avisoResgateEfetuadoAdmin($resgate);
        } catch (Exception $e) {
        }

        FlashMsg::success('Resgate solicitado com sucesso.');

        redirect('/minha-conta/meu-plano');
    endif;
else :
    $arrResgate = array(
        'VALOR' => 'R$ 0,00',
        'BANCO' => '',
        'AGENCIA' => '',
        'CONTA' => '',
        'TIPO_CONTA' => Resgate::CONTA_CORRENTE,
        'NOME_CORRENTISTA' => '',
        'CPF_CORRENTISTA' => '',
        'CNPJ_CORRENTISTA' => '',
        'PIS_PASEP' => ''
    );
endif;

foreach ($erros as $erro) :
    FlashMsg::danger($erro);
endforeach;
