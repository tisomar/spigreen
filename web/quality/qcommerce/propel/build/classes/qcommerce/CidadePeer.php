<?php



/**
 * Skeleton subclass for performing query and update operations on the 'QP1_CIDADE' table.
 *
 * Cidades brasileiras
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class CidadePeer extends BaseCidadePeer
{

    /**
     * Retorna o objeto Cidade através de um CEP
     * (Através do CEP será consultado o webservice para ver o nome da cidade,
     * se a cidade existir no banco de dados será retornado o objeto da cidade).
     * 
     * @param string $cep Cep que deseja-se retornar a cidade
     * @return mixed False em caso de erro ou o objeto Cidade no caso de sucesso
     */
    public static function getCidadeByCep($cep = "")
    {
        $curl = simple_curl('http://www.qapi.com.br/correios/endereco/' . $cep);

        if ($curl)
        {
            $json = json_decode($curl, true);

            if (!empty($json))
            {
                $cidade = trim($json[0]['cidade']);
                
                return CidadeQuery::create()->findOneByNome($cidade);
            }
        }
        return false;
        
    }
}
