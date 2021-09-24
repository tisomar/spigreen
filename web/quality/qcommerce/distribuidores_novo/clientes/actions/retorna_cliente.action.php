<?php

    $objClienteDistribuidor = ClienteDistribuidorQuery::create()->findPk($_GET['id']);
    
    $cliente['nome'] = escape($objClienteDistribuidor->getNomeCompleto());
    $cliente['email'] = escape($objClienteDistribuidor->getEmail());
    $cliente['tipo'] = escape($objClienteDistribuidor->getTipo());
    $cliente['celular'] = escape($objClienteDistribuidor->getTelefoneCelular());
    $cliente['whatsapp'] = escape($objClienteDistribuidor->getWhatsApp());
    
    $cliente['cep'] = escape($objClienteDistribuidor->getCep());
    $cliente['endereco'] = escape($objClienteDistribuidor->getEndereco());
    $cliente['numero'] = escape($objClienteDistribuidor->getNumero());
    $cliente['bairro'] = escape($objClienteDistribuidor->getBairro());
    $cliente['complemento'] = escape($objClienteDistribuidor->getComplemento());
    $cliente['cidade'] = escape($objClienteDistribuidor->getCidade());
    $cliente['estado'] = escape($objClienteDistribuidor->getEstado());
    
    
    $cliente['telefone'] = escape($objClienteDistribuidor->getTelefone());
    $cliente['cpfcnpj'] = escape($objClienteDistribuidor->getCpfCnpj());
    $cliente['rgie'] = escape($objClienteDistribuidor->getRgIe());
    $cliente['data'] = escape($objClienteDistribuidor->getDataNascimentoDataFundacao('d/m/Y'));
    $cliente['sexo'] = escape($objClienteDistribuidor->getSexo());
            
    echo json_encode($cliente);
