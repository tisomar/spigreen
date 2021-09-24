<?php


class BonificacaoExpansao extends GerenciadorBonificacao implements BonificacaoPedidoInterface
{

    /**
     * BonificacaoExpansao constructor.
     * @param PropelPDO|null $con
     */
    public function __construct(PropelPDO $con = null)
    {
        parent::__construct($con);
    }

    public function distribuirBonus(Pedido $pedido)
    {
        // Não cria extrato se já existe para o mesmo pedido e tipo, ou se o pedido não tem plano
        if ($this->existeExtrato(parent::TIPO_EXPANSAO, $pedido) || !$pedido->getPlano()) :
            return;
        endif;

        // **************** Será aplicado apenas para DIS e CP

        $totalPontos = $this->getTotalPontosPedido(parent::TIPO_EXPANSAO, $pedido);

        $patrocinador = $pedido->getCliente()->getPatrocinador($this->con);

        $nivel = 1;
        $percentualDistribuir = $this->getPercentualBonusPlano($pedido->getPlano(), parent::TIPO_EXPANSAO, $nivel);

        $data = new Datetime();

        while ($patrocinador && $percentualDistribuir) :
            $bonusDistribuir = 0;

            // Não bonifica o cliente se o cadastro dele for vago,
            // ou se o plano dele não permite bonificação de recompra
            if ($patrocinador->getVago() || !$patrocinador->getPlano() ||
                !$patrocinador->getPlano()->getParticipaExpansao()) :
                $patrocinador = $patrocinador->getPatrocinador($this->con);
                continue;
            endif;

            $bonusDistribuir = $totalPontos * $percentualDistribuir;

            // Se o bônus está zerado provavelmente não existe configuração de percentual/nivel para o plano do cliente,
            // Ou o pedido não possui itens que geram a bonificação de expansão.
            // Portanto, não será criado extrato com valor zerado
            if ($bonusDistribuir <= 0) :
                $nivel++;
                continue;
            endif;

            $tipoExtrato = '';
            $observacaoExtrato = '';
            $bloqueado = false;

            // Gera extrato de tipo direta para o nivel 1, para os demais gera indireta
            if ($nivel == 1) :
                $tipoExtrato = Extrato::TIPO_INDICACAO_DIRETA;
                $observacaoExtrato = sprintf('Bônus de Equipe Direta. Pedido %d - Cliente ' .
                                             $pedido->getCliente()->getNomeCompleto(),
                                             $pedido->getId());

                // Se o cliente direto não estiver ativo, o extrato de expansão direta ficará bloqueado
                if (!ClientePeer::getClienteAtivoMensal($patrocinador->getId())) :
                    $bloqueado = true;
                endif;
            else :
                $tipoExtrato = Extrato::TIPO_INDICACAO_INDIRETA;
                $observacaoExtrato = sprintf('Bônus de Equipe Indireta. Pedido %d - Cliente ' .
                    $pedido->getCliente()->getNomeCompleto(),
                    $pedido->getId());

                $bloqueado = true;
            endif;

            $this->criaExtratoBonificacao(
                $tipoExtrato,
                '+',
                $bonusDistribuir,
                $patrocinador,
                $data,
                $observacaoExtrato,
                $bloqueado,
                ['PEDIDO_ID' => $pedido->getId()]
            );

            $nivel++;
            $patrocinador = $patrocinador->getPatrocinador($this->con);
            $percentualDistribuir = $this->getPercentualBonusPlano($pedido->getPlano(), parent::TIPO_EXPANSAO, $nivel);
        endwhile;
    }

    public function executarCompressaoDinamica()
    {
        // TODO: Implement executarCompressaoDinamica() method.
    }

}