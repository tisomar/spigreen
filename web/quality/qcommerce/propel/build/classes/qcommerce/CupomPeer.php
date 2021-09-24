<?php

/**
 * Skeleton subclass for performing query and update operations on the 'QP1_CUPOM' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class CupomPeer extends BaseCupomPeer
{
    const TIPO_DESCONTO_REAIS = 'REAIS';
    const TIPO_DESCONTO_PORCENTAGEM = 'PORCENTAGEM';
    const CUPOM_MODEL_ALL = 'todos';
    const CUPOM_MODEL_FINAL_COSTUMER = 'consumidor_final'; // COSTUMER WITHOUT COMBO/PLAN
    const CUPOM_MODEL_DISTRIBUTOR = 'distribuidor'; // COSTUMER WITH COMBO/PLAN
    const CUPOM_MODEL_COSTUMERS = 'clientes'; //COSTUMER(S) SELECTEDS
    const CUPOM_MODEL_COSTUMERS_CHILDRENS = 'clientes_filhos'; //CHILDRENS OF COSTUMER(S) SELECTEDS
    const CUPOM_MODEL_COSTUMERS_CHILDRENS_FINAL_COSTUMER = 'clientes_filhos_final'; //CHILDRENS OF COSTUMER(S) SELECTEDS
    const CUPOM_MODEL_COSTUMERS_CHILDRENS_DISTRIBUTOR =
        'clientes_filhos_distribuidor'; //CHILDRENS OF COSTUMER(S) SELECTEDS
    const CUPOM_MODEL_COSTUMERS_AND_CHILDRENS = 'clientes_e_filhos'; //COSTUMER(S) SELECTEDS AND YOURS CHILDRENS
    const CUPOM_MODEL_COSTUMERS_AND_CHILDRENS_FINAL_COSTUMER =
        'clientes_e_filhos_final'; //COSTUMER(S) SELECTEDS AND YOURS CHILDRENS
    const CUPOM_MODEL_COSTUMERS_AND_CHILDRENS_DISTRIBUTOR =
        'clientes_e_filhos_distribuidor'; //COSTUMER(S) SELECTEDS AND YOURS CHILDRENS

    /**
     * @return array
     */
    public static function getTipoDescontoList()
    {
        return array(
            self::TIPO_DESCONTO_PORCENTAGEM => 'Por porcentagem',
            self::TIPO_DESCONTO_REAIS => 'Em reais',
        );
    }

    /**
     * @return array
     */
    public static function getModeloUsoCupomList()
    {
        return array(
            self::CUPOM_MODEL_ALL => 'Todos',
            self::CUPOM_MODEL_FINAL_COSTUMER => 'Consumidor Final',
            self::CUPOM_MODEL_DISTRIBUTOR => 'Distribuidores',
            self::CUPOM_MODEL_COSTUMERS => 'Cliente(s)',
            self::CUPOM_MODEL_COSTUMERS_CHILDRENS => 'Filhos dos Cliente(s)',
            self::CUPOM_MODEL_COSTUMERS_CHILDRENS_FINAL_COSTUMER => 'Filhos dos Cliente(s) sem plano',
            self::CUPOM_MODEL_COSTUMERS_CHILDRENS_DISTRIBUTOR => 'Filhos dos Cliente(s) com plano',
            self::CUPOM_MODEL_COSTUMERS_AND_CHILDRENS => 'Cliente(s) e seus Filhos',
            self::CUPOM_MODEL_COSTUMERS_AND_CHILDRENS_FINAL_COSTUMER => 'Cliente(s) e seus Filhos sem plano',
            self::CUPOM_MODEL_COSTUMERS_AND_CHILDRENS_DISTRIBUTOR => 'Cliente(s) e seus Filhos com plano',
        );
    }

    /**
     * @param $cupom
     * @param $carrinho
     * @return bool
     */
    public static function isValid($cupom, $carrinho)
    {

        $objCupom = CupomQuery::create()->findOneByCupom($cupom);

        if (is_null($objCupom) || ($objCupom->getCupom() != $cupom)) {
            FlashMsg::danger('O cupom informado é inválido.');
            return false;
        } elseif ($objCupom->isUsedClienteId(ClientePeer::getClienteLogado()->getId())) {
            FlashMsg::danger('O cupom informado já foi utilizado.');
            return false;
        } elseif ($objCupom->isExpired()) {
            FlashMsg::danger('A data de validade deste cupom expirou.');
            return false;
        } elseif ($objCupom->isActive() == false) {
            FlashMsg::danger('Este cupom não está ativo.');
            return false;
        } elseif ($carrinho->getValorItens() < $objCupom->getValorMinimoCarrinho()) {
            FlashMsg::danger(sprintf(
                'A soma valor mínimo de itens no carrinho (subtotal) deve ser de '
                . '<b>R$ %s</b> para que você possa utilizar este cupom.<br />'
                . '<a href="' . get_url_site() . '/carrinho/">Clique aqui</a> para voltar ao carrinho e adicionar mais produtos.',
                format_money($objCupom->getValorMinimoCarrinho())
            ));
            return false;
        } else {
            if ($objCupom->getModeloClienteUso() == CupomPeer::CUPOM_MODEL_DISTRIBUTOR
                && ClientePeer::getClienteLogado()->isConsumidorFinal()) {
                FlashMsg::danger('Este cupom é exclusivo para consumidores com plano ativo.');
                return false;
            } elseif ($objCupom->getModeloClienteUso() == CupomPeer::CUPOM_MODEL_FINAL_COSTUMER
                && !ClientePeer::getClienteLogado()->isConsumidorFinal()) {
                FlashMsg::danger('Este cupom é exclusivo para consumidores sem plano ativo.');
                return false;
            } elseif ($objCupom->getModeloClienteUso() == CupomPeer::CUPOM_MODEL_COSTUMERS) {
                $clientesPermission = explode(',', $objCupom->getClientes());
                if (!in_array(ClientePeer::getClienteLogado()->getId(), $clientesPermission)) {
                    FlashMsg::danger('Cliente não autorizado para utilizar este cupom.');
                    return false;
                }
            } elseif ($objCupom->getModeloClienteUso() == CupomPeer::CUPOM_MODEL_COSTUMERS_CHILDRENS) {
                $clientesPermission = explode(',', $objCupom->getClientes());
                $childrensIds = [];
                foreach ($clientesPermission as $clientesId) {
                    $objCliente = ClientePeer::retrieveByPK($clientesId);
                    if ($objCliente instanceof Cliente) {
                        $childrens = $objCliente->getDescendants();
                        if (count($childrens) > 0) {
                            foreach ($childrens as $children) {
                                /** @var $children Cliente */
                                $childrensIds[] = $children->getId();
                            }
                        }
                    }
                }

                if (!in_array(ClientePeer::getClienteLogado()->getId(), $childrensIds)) {
                    FlashMsg::danger('Cliente não autorizado para utilizar este cupom.');
                    return false;
                }
            } elseif ($objCupom->getModeloClienteUso() == CupomPeer::CUPOM_MODEL_COSTUMERS_CHILDRENS_FINAL_COSTUMER) {
                $clientesPermission = explode(',', $objCupom->getClientes());
                $childrensIds = [];
                foreach ($clientesPermission as $clientesId) {
                    $objCliente = ClientePeer::retrieveByPK($clientesId);
                    if ($objCliente instanceof Cliente) {
                        $childrens = $objCliente->getDescendants();
                        if (count($childrens) > 0) {
                            foreach ($childrens as $children) {
                                /** @var $children Cliente */
                                if ($children->isConsumidorFinal()) {
                                    $childrensIds[] = $children->getId();
                                }
                            }
                        }
                    }
                }

                if (!in_array(ClientePeer::getClienteLogado()->getId(), $childrensIds)) {
                    FlashMsg::danger('Cliente não autorizado para utilizar este cupom.');
                    return false;
                }
            } elseif ($objCupom->getModeloClienteUso() == CupomPeer::CUPOM_MODEL_COSTUMERS_CHILDRENS_DISTRIBUTOR) {
                $clientesPermission = explode(',', $objCupom->getClientes());
                $childrensIds = [];
                foreach ($clientesPermission as $clientesId) {
                    $objCliente = ClientePeer::retrieveByPK($clientesId);
                    if ($objCliente instanceof Cliente) {
                        $childrens = $objCliente->getDescendants();
                        if (count($childrens) > 0) {
                            foreach ($childrens as $children) {
                                /** @var $children Cliente */
                                if (!$children->isConsumidorFinal()) {
                                    $childrensIds[] = $children->getId();
                                }
                            }
                        }
                    }
                }

                if (!in_array(ClientePeer::getClienteLogado()->getId(), $childrensIds)) {
                    FlashMsg::danger('Cliente não autorizado para utilizar este cupom.');
                    return false;
                }
            } elseif ($objCupom->getModeloClienteUso() == CupomPeer::CUPOM_MODEL_COSTUMERS_AND_CHILDRENS) {
                $clientesPermission = explode(',', $objCupom->getClientes());

                if (!in_array(ClientePeer::getClienteLogado()->getId(), $clientesPermission)) {
                    FlashMsg::danger('Cliente não autorizado para utilizar este cupom.');
                    return false;
                }

                $childrensIds = [];

                foreach ($clientesPermission as $clientesId) {
                    $objCliente = ClientePeer::retrieveByPK($clientesId);
                    if ($objCliente instanceof Cliente) {
                        $childrens = $objCliente->getDescendants();
                        if (count($childrens) > 0) {
                            foreach ($childrens as $children) {
                                /** @var $children Cliente */
                                $childrensIds[] = $children->getId();
                            }
                        }
                    }
                }
                if (!in_array(ClientePeer::getClienteLogado()->getId(), $childrensIds)) {
                    FlashMsg::danger('Cliente não autorizado para utilizar este cupom.');
                    return false;
                }
            } elseif ($objCupom->getModeloClienteUso() ==
                CupomPeer::CUPOM_MODEL_COSTUMERS_AND_CHILDRENS_FINAL_COSTUMER) {
                $clientesPermission = explode(',', $objCupom->getClientes());

                if (!in_array(ClientePeer::getClienteLogado()->getId(), $clientesPermission)) {
                    FlashMsg::danger('Cliente não autorizado para utilizar este cupom.');
                    return false;
                }

                $childrensIds = [];

                foreach ($clientesPermission as $clientesId) {
                    $objCliente = ClientePeer::retrieveByPK($clientesId);
                    if ($objCliente instanceof Cliente) {
                        $childrens = $objCliente->getDescendants();
                        if (count($childrens) > 0) {
                            foreach ($childrens as $children) {
                                /** @var $children Cliente */
                                if ($children->isConsumidorFinal()) {
                                    $childrensIds[] = $children->getId();
                                }
                            }
                        }
                    }
                }
                if (!in_array(ClientePeer::getClienteLogado()->getId(), $childrensIds)) {
                    FlashMsg::danger('Cliente não autorizado para utilizar este cupom.');
                    return false;
                }
            } elseif ($objCupom->getModeloClienteUso() == CupomPeer::CUPOM_MODEL_COSTUMERS_AND_CHILDRENS_DISTRIBUTOR) {
                $clientesPermission = explode(',', $objCupom->getClientes());

                if (!in_array(ClientePeer::getClienteLogado()->getId(), $clientesPermission)) {
                    FlashMsg::danger('Cliente não autorizado para utilizar este cupom.');
                    return false;
                }

                $childrensIds = [];

                foreach ($clientesPermission as $clientesId) {
                    $objCliente = ClientePeer::retrieveByPK($clientesId);
                    if ($objCliente instanceof Cliente) {
                        $childrens = $objCliente->getDescendants();
                        if (count($childrens) > 0) {
                            foreach ($childrens as $children) {
                                /** @var $children Cliente */
                                if (!$children->isConsumidorFinal()) {
                                    $childrensIds[] = $children->getId();
                                }
                            }
                        }
                    }
                }
                if (!in_array(ClientePeer::getClienteLogado()->getId(), $childrensIds)) {
                    FlashMsg::danger('Cliente não autorizado para utilizar este cupom.');
                    return false;
                }
            }
        }

        return true;
    }
}
