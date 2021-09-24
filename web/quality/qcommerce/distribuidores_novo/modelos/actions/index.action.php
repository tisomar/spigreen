<?php

$query = DistribuidorTemplateQuery::create()
                    ->filterByCliente(ClientePeer::getClienteLogado())
                    ->_or()
                    ->filterByClienteId(null, Criteria::ISNULL)

                    ->filterByTipo($tipoTemplate);

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$pager = new QPropelPager($query, 'DistribuidorTemplatePeer', 'doSelect', $page);


$breadcrumb = array('Templates ' . $tipoTemplate => '');
