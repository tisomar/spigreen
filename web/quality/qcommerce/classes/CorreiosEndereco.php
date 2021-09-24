<?php

class CorreiosEndereco
{

    static $url = 'http://www.buscacep.correios.com.br/sistemas/buscacep/resultadoBuscaCepEndereco.cfm';

    public static function consultaEndereco($cep)
    {

        $cep = preg_replace('/[^0-9]/', '', $cep);

        $cURL = curl_init(self::$url);
        curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($cURL, CURLOPT_HEADER, false);
        curl_setopt($cURL, CURLOPT_POST, true);
        curl_setopt($cURL, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($cURL, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($cURL, CURLOPT_TIMEOUT, 5);
        curl_setopt($cURL, CURLOPT_POSTFIELDS, sprintf("relaxation=%s&tipoCEP=ALL&semelhante=N", $cep));
        $html = curl_exec($cURL);
        curl_close($cURL);

        $html = str_replace('&nbsp;', ' ', $html);

        preg_match_all('/<td(.*)<\/td>/U', utf8_encode($html), $campoTabela);

        if (isset($campoTabela[0][0])) {
            $campos = $campoTabela[0];

            $campos = array_map('strip_tags', $campos);
            $campos = array_map('html_entity_decode', $campos);
            $campos = array_map('trim', $campos);

            # Inverte o array para pegar na seguinte ordem: CEP, UF, CIDADE, BAIRRO, LOGRADOURO
            $endereco = array_reverse($campos);

            list($cidade, $uf) = (!empty($endereco[1]) ? explode('/', $endereco[1])  : array('',''));
            $cep        = (!empty($endereco[0]) ? $endereco[0] : '');
            ;
            $bairro     = (!empty($endereco[2]) ? $endereco[2] : '');
            $logradouro = (!empty($endereco[3]) ? $endereco[3] : '');

            $dados = array(
                'cep'        => $cep,
                'uf'         => $uf,
                'cidade'     => $cidade,
                'bairro'     => $bairro,
                'logradouro' => $logradouro,
            );

            $response[] = $dados;
        }

        if (isset($response[0])) {
            $content = $response[0];
        } else {
            $content = null;
        }
        if (!empty($content)) {
            //$content = array_map('utf8_encode', $content);
        }
        return $content;
    }

    public function consultaCepViaCep($cep) {

        $url = "https://viacep.com.br/ws/$cep/json/";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json'
        ));
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

        try {
            //execute post
            $result = curl_exec($ch);
            //close connection
            curl_close($ch);

            $arrResult = json_decode($result, true);

            $dados = array(
                'cep'           => $arrResult['cep'],
                'uf'            => $arrResult['uf'],
                'cidade'        => $arrResult['localidade'],
                'bairro'        => $arrResult['bairro'],
                'logradouro'    => $arrResult['logradouro'],
            );
            $content = $dados;

            return $content;
        } catch (\Throwable $th) {
            return null;
        }
    }
}


/*static public function consultaEndereco($cep) {

    $cep = preg_replace('/[^0-9]/', '', $cep);

    $postCorreios = "CEP=".$cep."&Metodo=listaLogradouro&TipoConsulta=cep";
    $cURL = curl_init("http://www.buscacep.correios.com.br/servicos/dnec/consultaLogradouroAction.do");
    curl_setopt($cURL, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($cURL, CURLOPT_HEADER, false);
    curl_setopt($cURL, CURLOPT_POST, true);
    curl_setopt($cURL, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($cURL, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($cURL, CURLOPT_POSTFIELDS, $postCorreios);
    $html = curl_exec($cURL);
    curl_close($cURL);// encerra e retorna os dados
    $saida = utf8_encode($html); // codifica conteudo para utf-8
    $campoTabela = "";

    preg_match_all('@<td(.*?)<\/td>@i', $saida, $campoTabela);

    if (isset($campoTabela[0][0])) {
        # Define a ordem das informa��es
        $labels = array('cep', 'uf', 'cidade', 'bairro', 'logradouro');

        # Inverte o array para pegar na seguinte ordem: CEP, UF, CIDADE, BAIRRO, LOGRADOURO
        $endereco = array_reverse($campoTabela[0]);

        $dados = array();
        foreach ($labels as $i => $label) {
            if (isset($endereco[$i])) {
                $dados[$labels[$i]] = strip_tags($endereco[$i]);
            }
        }

        $response[] = $dados;
    }

    if (isset($response[0])) {
        $content = $response[0];
    } else {
        $content = null;
    }

    return $content;

}

}*/
