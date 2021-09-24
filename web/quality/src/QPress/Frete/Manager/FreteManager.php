<?php

/*
 * This file is part of the Duo Criativa software.
 * Este arquivo é parte do software da Duo Criativa.
 *
 * (c) Paulo Ribeiro <paulo@duocriativa.com.br>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace QPress\Frete\Manager;

use QPress\Frete\FreteInterface;
use QPress\Frete\Package\Package;
use CalculoCorreiosDefault;

class FreteManager {

    protected $modalidades;

    function __construct() {

    }

    public function addModalidade(FreteInterface $frete) {
        if (!isset($this->modalidades[$frete->getNome()])) {
            $this->modalidades[$frete->getNome()] = $frete;
        }
    }

    public function consultar($modalidade, Package $package) {
        $modalidade = $this->getModalidade($modalidade);
        return $modalidade->consultar($package);
    }

    public function getModalidade($modalidade) {
        if (is_string($modalidade) && isset($this->modalidades[$modalidade])) {
            $modalidade = $this->modalidades[$modalidade];
        }
        if (!$modalidade instanceof FreteInterface) {

            $modalidade = end($this->modalidades);

            if (!$modalidade instanceof FreteInterface) {
                throw new \Exception(sprintf('Tipo de objeto %s inválido', $modalidade));
            }

        }
        return $modalidade;
    }

    public function getModalidades() {
        return $this->modalidades;
    }

    public static function calcularFrete($package, $cepFrom) {

        global $container;

        $frete = null;

        # Define se há frete de entrega disponível
        $hasModalidadeDisponivel = false;

        /**
         * Consulta os valores nas modalidades de frete disponíveis.
         * Normalmente são:
         * - Frete Grátis por Região;
         * - Correios PAC
         * - Correios SEDEX
         * - Correios E-Sedex
         * - Transportadora
         */
        $modalidades = $container->getFreteManager()->getModalidades();
        foreach ($modalidades as $modalidade) { /* @var $modalidade \QPress\Frete\FreteInterface */
            /**
             * Se a modalidade for retirada em loja e ela estiver disponível,
             * no final das consultas, caso não haja nenhuma modalidade de entrega disponível,
             * apresenta-se ao cliente que o produto está disponível apenas para retirada em loja.
             */
            if ($modalidade->getNome() == 'retirada_loja') {
                $frete = $modalidade->consultar($package);
                if ($frete->isDisponivel()) {
                    $retiradaEmLoja = $frete;
                }
                continue;
            }

            /**
             * Efetua a consulta e define o valor e prazo na primeira modalidade que encontrar disponível.
             * Para alterar a ordem das modalidades, deve-se alterar a inicialização das mesmas no arquivo 'Container.php'
             */
            /* @var $frete \QPress\Frete\DataResponse\DataResponseFreteInterface  */
            $frete = $modalidade->consultar($package);
            if ($frete->isDisponivel()) {
                $hasModalidadeDisponivel = true;
                break;
            }
        }

        # Se não conseguiu calcular um frete, informa o cliente.
        if ($hasModalidadeDisponivel == false) {
            if (isset($retiradaEmLoja)) {
                $frete = $retiradaEmLoja;
                \FlashMsg::add('info', 'Produto disponível apenas para retirada em loja.');
            } else {
                \FlashMsg::add('danger', 'Nenhum meio de entrega disponível para este produto.');
            }
        }

        return $frete;

    }

    /**
     * Calculates freight for QPress Package Class.
     *
     * @param \QPress\Frete\Package\Package $package Package created with QPress Package Class.
     * @return array
     */
    public static function calcularFreteCompleto(\QPress\Frete\Package\Package $package) {
        global $container;
        $all = [];
        $modalities = $container->getFreteManager()->getModalidades();

        foreach ($modalities as $modality):/* @var $modality \QPress\Frete\FreteInterface */
            $query = $modality->consultar($package);

            if ($query->isDisponivel()):
                $all[] = [
                    'modality' => $modality,
                    'query' => $query,
                ];
            endif;
        endforeach;

        return $all;
    }
}
