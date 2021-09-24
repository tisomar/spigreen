<?php

if ($request->query->get('is_filter') == 'true') {
    if($request->query->get('filter')['DataDe'] != '' && $request->query->get('filter')['DataDe'] != '') :
        $dataDe = date_create_from_format('d/m/Y', $_GET['filter']['DataDe']);
        $dataAte = date_create_from_format('d/m/Y', $_GET['filter']['DataAte']);
        $preQuery->filterByData(['min' => $dataDe , 'max' => $dataAte]);
    endif;

    if(!empty($request->query->get('filter')['Situacao']) != '') :
        $preQuery->filterBySituacao($request->query->get('filter')['Situacao']);
    endif;

    if(!empty($request->query->get('filter')['Banco']) != '') :
        $preQuery->filterByBanco($request->query->get('filter')['Banco']);
    endif;
}
