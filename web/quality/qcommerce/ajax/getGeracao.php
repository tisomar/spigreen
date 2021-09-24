<?php
/**
 * Created by PhpStorm.
 * User: Giovan
 * Date: 25/04/2018
 * Time: 09:28
 */

if ($container->getRequest()->getMethod() == 'POST') {
//nivel=2
    $ids = $container->getRequest()->request->get('ids');
    $nivel = $container->getRequest()->request->get('nivel');

//$nivel = 2;
//$ids = '130|||131|||132|||133|||135|||141|||173|||229|||230|||265|||267|||268';

    if ($nivel < 10) {
        if (empty($ids) || is_null($ids)) {
            $return = array(
                'html'      => '',
                'retorno'   => 'error',
                'msg'       => 'Ids em branco.',
                'load'      => 'true'
            );
        } else {
            $arrIdsExploded = explode('|||', $ids);

            if (count($arrIdsExploded) > 0 && !empty($arrIdsExploded[0]) && !is_null($arrIdsExploded[0])) {
                $arrNomes = $arrIds = '';
                foreach ($arrIdsExploded as $id) {
                    $retorno = ClientePeer::getIndicadorCliente($id);

                    if (empty($retorno['nomes'])) {
                        continue;
                    }

                    if (empty($arrNomes)) {
                        $arrNomes = $retorno['nomes'];
                    } else {
                        $arrNomes .= '|||' . $retorno['nomes'];
                    }

                    if (empty($arrIds)) {
                        $arrIds = $retorno['ids'];
                    } else {
                        $arrIds .= '|||' . $retorno['ids'];
                    }
                }

                $nomesTd = '';
                $nomesExploded = explode('|||', $arrNomes);
                if (count($nomesExploded) > 0 && !empty($nomesExploded[0]) && !is_null($nomesExploded[0])) {
                    foreach ($nomesExploded as $nome) {
                        if (empty($nome)) {
                            continue;
                        }

                        $nomesTd .=  $nome . '<br>';
                    }
                    $nivelAtual = $nivel + 1;
                    if (!empty($nomesTd)) {
                        $html = '
                            <tr>
                                <td data-nivel="' . $nivelAtual . '" data-ids="' . $arrIds . '" >' . $nivelAtual . '</td>
                                <td>' . $nomesTd . '</td>
                            </tr>
                        ';



                        if ($nivelAtual >= 10) {
                            $return = array(
                                'html' => $html,
                                'retorno' => 'success',
                                'msg' => '',
                                'load' => 'false',
                            );
                        } else {
                            $return = array(
                                'html' => $html,
                                'retorno' => 'success',
                                'msg' => '',
                                'load' => 'true',
                            );
                        }
                    } else {
                        $return = array(
                            'html'      => '',
                            'retorno'   => 'error',
                            'msg'       => 'Nenhum cliente encontrado, tente novamente.',
                            'load'      => 'true'
                        );
                    }
                } else {
                    $return = array(
                        'html'      => '',
                        'retorno'   => 'error',
                        'msg'       => 'Nenhum cliente encontrado.',
                        'load'      => 'false'
                    );
                }
            } else {
                $return = array(
                    'html'      => '',
                    'retorno'   => 'error',
                    'msg'       => 'Ids em branco.',
                    'load'      => 'false'
                );
            }
        }
    } else {
        $return = array(
            'html'      => '',
            'retorno'   => 'error',
            'msg'       => 'Nível máximo atingido.',
            'load'      => 'false'
        );
    }
} else {
    $return = array(
        'html'      => '',
        'retorno'   => 'error',
        'msg'       => 'Método de pesquisa inválido.',
        'load'      => 'false'
    );
}

echo json_encode($return);
die;
