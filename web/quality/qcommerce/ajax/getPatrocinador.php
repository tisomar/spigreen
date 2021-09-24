<?php
/**
 * Created by PhpStorm.
 * User: Giovan
 * Date: 25/04/2018
 * Time: 09:28
 */

if ($container->getRequest()->getMethod() == 'POST') :
    $codigoPatrocinador = $container->getRequest()->request->get('patrocinador');

    if ($codigoPatrocinador) :
        //Se for informado um valor numerico, assume que foi informado o codigo do patrocinador, senão busca pelo e-mail.
        if (ctype_digit($codigoPatrocinador)) :
            $objPatrocinador = ClienteQuery::create()->findOneByChaveIndicacao($codigoPatrocinador);
        else :
            $objPatrocinador = ClienteQuery::create()->findOneByEmail($codigoPatrocinador);
        endif;

        if ($objPatrocinador instanceof Cliente && $objPatrocinador->isInTree() && $objPatrocinador->isClienteDistribuidor()) :
            $html = '<br>
                    <div class="panel">
                        <div class="panel-body bg-default">
                            <p><h3>' . $objPatrocinador->getNomeCompleto() . ' - ' . $objPatrocinador->getChaveIndicacao() . '</h3></p>
                        </div>
                    </div>';
            $return = array(
                'html'      => $html,
                'retorno'   => 'success',
                'msg'       => 'Patrocinador confirmado.',
                'id'        => $objPatrocinador->getId(),
                'nome'      => $objPatrocinador->getNomeCompleto()
            );
        else :
            $return = array(
                'html'      => '',
                'retorno'   => 'error',
                'msg'       => 'Patrocinador não encontrado.',
                'id'        => '',
                'nome'      => ''
            );
        endif;
    else :
        $return = array(
            'html'      => '',
            'retorno'   => 'error',
            'msg'       => 'Dados para consulta do patrocinador não informados.',
            'id'        => '',
            'nome'      => ''
        );
    endif;
else :
    $return = array(
        'html'      => '',
        'retorno'   => 'error',
        'msg'       => 'Método de pesquisa inválido.',
        'id'        => '',
        'nome'      => ''
    );
endif;

echo json_encode($return);
die;
