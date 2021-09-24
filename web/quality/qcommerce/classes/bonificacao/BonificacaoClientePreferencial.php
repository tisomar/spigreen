<?php


class BonificacaoClientePreferencial extends GerenciadorBonificacao implements BonificacaoPedidoInterface
{

    /**
     * BonificacaoClientePreferencial constructor.
     * @param PropelPDO|null $con
     */
    public function __construct(PropelPDO $con = null)
    {
        parent::__construct($con);
    }

    public function distribuirBonus(Pedido $pedido)
    {
        // Não cria extrato se já existe para o mesmo pedido e tipo
        if ($this->existeExtrato(parent::TIPO_CLIENTE_PREFERENCIAL, $pedido)) :
            return;
        endif;

        // Gera bonificação apenas de pedidos feitos por CP
        if (!$pedido->getCliente()->isClientePreferencial()) :
            return;
        endif;

        $totalPontos = $this->getTotalPontosPedido(parent::TIPO_CLIENTE_PREFERENCIAL, $pedido);

        $patrocinador = $pedido->getCliente()->getPatrocinador();
        $nivel = 1;

        while ($patrocinador && $nivel <= 1) :
            // Não bonifica o cliente se o plano dele não permite bonificação de cliente preferencial, ou se o cadastro for vago
            if (!$patrocinador->isClienteDistribuidor() || !$patrocinador->getPlano()->getParticipaProdutividade() || $patrocinador->getVago()) :
                $patrocinador = $patrocinador->getPatrocinador();
                continue;
            endif;

            $bonusDistribuir = 0;

            $query = PlanoCarreiraHistoricoQuery::create()
                ->filterByCliente($patrocinador)
                ->filterByMes(date('n'))
                ->filterByAno(date('Y'))
                ->findOne();

            $patrocinadorQualificacao = $query ? $query->getPlanoCarreira()->getNivel() : 0;

            $percs = [
                1 => 75
            ];
            $bonusDistribuir = $totalPontos * $percs[$nivel] / 100;

            $gerenciador = new GerenciadorPlanoCarreira(Propel::getConnection(), $patrocinador);

            // Se estiver ativo, o cliente já tem o extrato desbloqueado,
            // somente até o nível 3, pois não tem tem regra de qualificação)
            // if ($nivel <= 3) :
            //     $bloqueado = !$gerenciador->getStatusAtivacao();
            // else :
                $bloqueado = false;
            // endif;

            $this->criaExtratoBonificacao(
                Extrato::TIPO_CLIENTE_PREFERENCIAL,
                '+',
                $bonusDistribuir,
                $patrocinador,
                new DateTime(),
                sprintf('Bônus de cliente preferencial. Pedido %d - Cliente ' . $pedido->getCliente()->getNomeCompleto(), $pedido->getId()),
                $bloqueado,
                ['PEDIDO_ID' => $pedido->getId()]
            );

            $nivel++;
            $patrocinador = $patrocinador->getPatrocinador();
        endwhile;
    }

}