<?php

/**
 * Skeleton subclass for performing query and update operations on the 'QP1_PRODUTO_COMENTARIO' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    propel.generator.qcommerce
 */
class ProdutoComentarioPeer extends BaseProdutoComentarioPeer
{

    public static function getNotas()
    {
        return array(
            5 => 'five',
            4 => 'four',
            3 => 'three',
            2 => 'two',
            1 => 'one',
        );
    }

    public static function getNotasDescricao()
    {
        return self::$notasDescricao;
    }

    public static function getStatusList()
    {
        return array(
            ProdutoComentario::STATUS_APROVADO => 'Aprovado',
            ProdutoComentario::STATUS_PENDENTE => 'Pendente',
        );
    }

    /**
     * Estabelece um intervalo mínimo entre um comentário e outro que o cliente 
     * pode fazer
     * OBS.: O valor deve ser definido em minutos
     */
    const COMENTARIO_TEMPO_MINIMO_CADASTRO = 1;

    public static $notasDescricao = array(
        5 => 'Achei ótimo!',
        4 => 'Achei bom!',
        3 => 'Achei regular!',
        2 => 'Achei ruim!',
        1 => 'Achei péssimo!',
    );

    /**
     * Retorna criteria para filtrar pelos comentários com status ativo
     * 
     * @author Felipe Corrêa
     * @since 22/02/2013
     * @return ProdutoComentarioQuery
     */
    public static function filtroAtivos()
    {
        return ProdutoComentarioQuery::create()->filterByStatus(ProdutoComentario::STATUS_APROVADO);
    }

    /**
     * Retorna o nome da nota<br />
     * Se a nota para o comentário foi 3, retorna o valor 'Regular'
     * 
     * @author Felipe Corrêa
     * @since 22/02/2013
     * 
     * @param int    $nota Nota que deseja-se pegar a descrição<br />
     *                     OBS.: Caso for vazia então pega a nota do comentário
     * 
     * @return mixed Retorna false caso não encontrar
     */
    public static function getNotaDescricao($nota = '')
    {
        if (array_key_exists((int) $nota, self::$notasDescricao))
        {
            return self::$notasDescricao[$nota];
        }

        return false;
    }

    /**
     * Calcula o percentual que aquela nota representa na media final
     * Ex.: Nota 1 representa 50%
     * 
     * @author Felipe Corrêa
     * @since 22/02/2013
     * 
     * @param $qtdComentarios    int  Quantidade total de comentários que estão ativos para o produto
     * @param $qtdAvaliacoesNota int  Total de comentários que utilizacao a nota que deseja-se saber o percentual de utilização
     * 
     * @return int Percentual de utilização da nota no produto 
     */
    public static function calculaNotaPorcentagemUtilizacao($qtdComentarios, $qtdAvaliacoesNota)
    {
        $percentualUtilizacao = 0;

        if ($qtdAvaliacoesNota > 0 && $qtdComentarios > 0)
        {
            // Encontrando a porcentagem de utilização da nota
            $percentualUtilizacao = $qtdAvaliacoesNota / $qtdComentarios;

            // Arredondando e convertendo para número inteiro
            $percentualUtilizacao = round($percentualUtilizacao * 10);
        }

        return $percentualUtilizacao;
    }

    /**
     * Função destinada a fazer verificações de segurança antes de postar um 
     * comentário de um cliente, evitando ataques automatizados de comentários
     * 
     * @author Felipe Corrêa
     * @since 25/02/2013
     * 
     * @param $objCliente Cliente Objeto do cliente que está logado
     * @return array Array de erros de segurança que foram encontrados 
     */
    public static function hasPermissaoCadastrar(Cliente $objCliente)
    {

        $erros = array();

        // Buscando último comentário deste cliente
        $objUltimoComentario = ProdutoComentarioQuery::create()
                ->filterByCliente($objCliente)
                ->orderById(Criteria::DESC)
                ->findOne();

        // Caso tenha um último comentário
        if (!is_null($objUltimoComentario))
        {
            // Intervalo mínimo antes de poder cadastrar um novo comentário
            $tempoIntervalo = (60 * self::COMENTARIO_TEMPO_MINIMO_CADASTRO);

            // Se a data atual é menor ou igual ao tempo mínimo de intervalo 
            // do último comentário, então exibe erro
            if (strtotime('now') <= ( strtotime($objUltimoComentario->getData()) + $tempoIntervalo ))
            {
                $erros[] = 'Você não pode enviar comentários tão rapidamente. Por questões de segurança nós pedimos que aguarde mais alguns minutos antes de enviar seu próximo comentário.';
            }
        }

        return $erros;
    }

}
