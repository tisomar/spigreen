<?php



/**
 * Skeleton subclass for representing a row from the 'qp1_distribuicao_preview' table.
 *
 * Tabela onde serão gravados as distribuicoes mensais de pontos para as redes
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class DistribuicaoPreview extends BaseDistribuicaoPreview
{
    public function getTotalPontosADistribuir()
    {

        /*
         * A rotina de distribuição mudou e tivemos que fazer esta regra abaixo.
         * Antes o total de pontos a distribuir era o total de pontos + pontos extras.
         * Agora o total de pontos já está calculado com os pontos extras.
         */

        if ($this->getDistribuicaoId() > 120){
            return $this->getPontosTotal();
        }
        else {
            return $this->getPontosTotal() + $this->getPontosExtras();
        }

    }
}
