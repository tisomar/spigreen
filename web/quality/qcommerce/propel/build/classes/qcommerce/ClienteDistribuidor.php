<?php



/**
 * Skeleton subclass for representing a row from the 'qp1_cliente_distribuidor' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class ClienteDistribuidor extends BaseClienteDistribuidor
{
    const TIPO_PESSOA_FISICA    = 'F';
    const TIPO_PESSOA_JURIDICA  = 'J';

    const APROVADO = 'APROVADO';
    const PENDENTE  = 'PENDENTE';

    const SEXO_MASCULINO = 'M';
    const SEXO_FEMININO  = 'F';

//    public function getNome()
//    {
//        return trim($this->getNome());
//    }
    public function getNomeCompleto()
    {
        return trim($this->getNomeRazaoSocial()) . " " . trim($this->getSobrenomeNomeFantasia());
    }

    public function isPessoaFisica()
    {
        return $this->tipo == self::TIPO_PESSOA_FISICA;
    }

    public function isPessoaJuridica()
    {
        return $this->tipo == self::TIPO_PESSOA_JURIDICA;
    }

    public function getTipoLeadDescricao()
    {
        $tipos = array(
            'P' => _trans('agenda.tipo_produto'),
            'D' => _trans('agenda.tipo_distribuidor'),
            'C' => _trans('agenda.tipo_cadastro'),

        );

        return $tipos[$this->getTipoLead()];
    }

}
