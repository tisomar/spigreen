<?php

/**
 * Criando função de atalho para exibir um conteúdo
 *
 * @param int $conteudoId Id do conteúdo que deseja-se retornar
 * @return string Retorna a descrição do conteúdo em caso de sucesso ou uma mensagem de conteúdo não encontrado
 */
function _mostrarConteudoDescricao($conteudoId)
{
    $objConteudo = ConteudoQuery::create()->findOneById($conteudoId);

    return ($objConteudo->getDescricao()) ? $objConteudo->getDescricao() : 'Conteúdo não encontrado.';
}

/**
 * Criando função de atalho para pegar um parâmetro<br />
 * Atalho de: ParametroPeer::getParametro($parametro)
 *
 * @author Felipe Corrêa
 * @since  07/03/2013
 *
 * @param  String $parametro    Nome do parâmetro
 * @param  bool   $obrigatorio  Caso o parâmetro seja obrigatório e não for encontrado, então lançará uma excessão
 * @return mixed  Retorna false caso o parâmetro não existir
 */
function _parametro($parametro, $obrigatorio = false)
{
    return Config::get($parametro, $obrigatorio);
}
function _trans($id, $parameters = array(), $domain = null, $locale = null)
{
    return QPTranslator::trans($id, $parameters, $domain, $locale);
}
