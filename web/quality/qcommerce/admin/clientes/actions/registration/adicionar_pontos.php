<?php
$erros = array();
if ($request->request->has('transferencia_puntos')) {
    $gerenciador = new GerenciadorPontos($con = Propel::getConnection(), $logger);

    $data = $request->request->get('transferencia_puntos');


    $pontos = $data["quantidade_puntos"];
    $tipoMovimento = $data["tipo_movimento"];
    $destinatario = ClienteQuery::create()->findById($data["id_cliente"]);

    if (count($destinatario) > 0) {
        $destinatario = $destinatario[0];
        if ($pontos > 0) {
            if ($tipoMovimento == "adicionar") {
                $gerenciador->adicionarPontos($destinatario, $pontos);
                die('Pontos adicionados com sucesso.');
            }
            if ($tipoMovimento == "diminuir") {
                $gerenciador->diminuirPontos($destinatario, $pontos);
                die('Pontos diminuidos com sucesso.');
            }
            die("Tipo movimento não permitido.");
        }
        die("O minimo de pontos é <strong>0</strong>.");
    } else {
        die("Franqueado não encontrado.");
    }

    die("Error ao adicionar ponto.");
}

die("Error.");
