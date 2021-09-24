<?php

// Armazenando o contato em um objeto genérico para facilitar a utilização
$contato = new Generic();

$objClienteDistribuidor = new ClienteDistribuidor();
if (isset($_POST['cliente_distribuidor']) && !empty($_POST['cliente_distribuidor'])) {
    $arrClienteDistribuidor = array_map('trim', $_POST['cliente_distribuidor']);

    $clienteLead = ClienteDistribuidorQuery::create()->findOneByEmail($arrClienteDistribuidor['EMAIL']);

    if (!$clienteLead instanceof ClienteDistribuidor) {
        if (strlen(preg_replace("/[^0-9]/", "", $arrClienteDistribuidor["TELEFONE_CELULAR"])) >= 10) {
            if (strlen(preg_replace("/[^0-9]/", "", $arrClienteDistribuidor["CEP"])) == 8) {
                $objClienteDistribuidor->setByArray($arrClienteDistribuidor, BasePeer::TYPE_FIELDNAME, isset($erros) ? $erros : '');

                $objClienteDistribuidor->setStatus(ClienteDistribuidor::PENDENTE);
                $objClienteDistribuidor->setTipo('F');
                $objClienteDistribuidor->setTipoLead('P');
                $objClienteDistribuidor->setLead(true);

                $clienteSite = ClienteQuery::create()->findOneByEmail($arrClienteDistribuidor['EMAIL']);


                if ($clienteSite instanceof Cliente) {
                    $distribuidor = ClienteQuery::create()->findOneByClienteIndicadorId($clienteSite->getClienteIndicadorId());

                    $distribuidorId = $distribuidor->getId();
                } else {
                    $distribuidor = ClienteDistribuidorPeer::getDistribuidorMaisPertoProduto($objClienteDistribuidor->getCep());

                    if (!$distribuidor) {
                        FlashMsg::erro('Não tem nenhum distribuidor que possa te antender. Entre em contato com suporte');
                        redirect('/leads');
                    }
                    $distribuidorId = $distribuidor->getId();
                }
                $objClienteDistribuidor->setClienteId($distribuidorId);


                if ($objClienteDistribuidor->myValidate($erros) && !$erros) {
                    $con = Propel::getConnection();
                    $con->beginTransaction();

                    $objClienteDistribuidor->save();


                    if (!$erros) {
                        ClienteDistribuidorPeer::notificarAoDistribuidorDoClientePeloNovoCadastroProduto($objClienteDistribuidor);

                        $distribuidor->setDataUltimoLead(date('Y-m-d'));
                        $distribuidor->save();

                        $con->commit();
                        $dados = array(
                            "__nome_distribuidor__" => $distribuidor->getNomeCompleto(),
                            "__email_distribuidor__" => $distribuidor->getEmail(),
                            "__cidade_distribuidor__" => $distribuidor->getEnderecoPrincipal()->getCidade()->getNome(),
                            "__estado_distribuidor__" => $distribuidor->getEnderecoPrincipal()->getCidade()->getEstado()->getNome(),
                        );
                        $conteudo = _mostrarConteudoDescricao('21', $dados);
                        $conteudo .= "<br>";
                        $conteudo .= "<b>" . _trans('leads.nome_distribuidor') . ":</b> " . $distribuidor->getNomeCompleto() . "<br>";
                        $conteudo .= "<b>" . _trans('leads.email_distribuidor') . ":</b> " . $distribuidor->getEmail() . "<br>";
                        $conteudo .= "<b>" . _trans('leads.cidade_distribuidor') . ":</b> " . $distribuidor->getEnderecoPrincipal()->getCidade()->getNome() . "<br>";
                        $conteudo .= "<b>" . _trans('leads.estado_distribuidor') . ":</b> " . $distribuidor->getEnderecoPrincipal()->getCidade()->getEstado()->getNome() . "<br>";

                        //FlashMsg::sucesso($conteudo);
                        $_SESSION['modal-leads'] = 'sim';
                        redirect('/leads/produto');
                    } else {
                        $con->rollBack();
                    }
                }
            } else {
                $erros[] = _trans('leads.cep_obrigatorio');
            }
        } else {
            $erros[] = _trans('leads.telefone_obrigatorio');
        }
    } else {
        $erros[] = _trans('leads.lead_ja_cadastrado');
    }

    foreach ($erros as $erro) {
        FlashMsg::erro($erro);
    }
} else {
    $arrClienteDistribuidor = $objClienteDistribuidor->toArray(BasePeer::TYPE_FIELDNAME);
    if (empty($arrClienteDistribuidor['TIPO'])) {
        $arrClienteDistribuidor['TIPO'] = ClienteDistribuidor::TIPO_PESSOA_FISICA;
    }
}
