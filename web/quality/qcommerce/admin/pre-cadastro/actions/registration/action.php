<?php

if (!isset($_class)) {
    trigger_error('você deve definir a classe $_class');
}


if ($request->getMethod() == 'POST') {
    $data = trata_post_array($request->request->get('data'));

    $type = null;
    $save = $error = array();

    foreach ($data as $alias => $value) {
        if ($alias == 'precadastro.ativo') {
            $save[$alias] =  $value;
            if ($value == '0') {
                break;
            }
        }

        if ($alias == 'precadastro.tipo' && $value == 'data') {
            $save[$alias] = $value;
            $type = 'data';
        }

        if ($alias == 'precadastro.tipo' && $value == 'dias') {
            $save[$alias] = $value;
            $type = 'dias';
        }

        if ($alias == 'precadastro.data_final' && $type == 'data') {
            $now = date('d-m-Y') . ' 00:00:00';
            $val = str_replace('/', '-', $value) . ' 00:00:00';
            $dateNow = new DateTime($now);
            $dateValue = new DateTime($val);
            if ($dateValue->getTimestamp() > $dateNow->getTimestamp()) {
                $save[$alias] = $dateValue->format('Y-m-d');
            } else {
                $error[] = 'Data de finalização deve ser maior que hoje';
            }
        }

        if ($alias == 'precadastro.dias_corridos' && $type == 'dias') {
            if ($value >= 1) {
                $save[$alias] = $value;
            } else {
                $error[] = 'Quantidade de dias corridos deve ser maior ou igual a 1';
            }
        }
    }

    if (empty($error)) {
        foreach ($save as $key => $value) {
            Config::setParametro($key, $value);
        }
        Config::saveParameters();
    } else {
        foreach ($save as $value) {
            FlashMsg::erro($value);
        }
    }
}
