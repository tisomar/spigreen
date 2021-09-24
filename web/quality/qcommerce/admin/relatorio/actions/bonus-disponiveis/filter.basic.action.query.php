<?php

if ($request->query->get('is_filter') == 'true') {
    if($request->query->get('filter')['DataDe'] != '' && $request->query->get('filter')['DataDe'] != '') :
        $dataDe = date_create_from_format('d/m/Y', $_GET['filter']['DataDe']);
        $dataAte = date_create_from_format('d/m/Y', $_GET['filter']['DataAte']);
        // $preQuery->filterByData(['min' => $dataDe , 'max' => $dataAte]);

        $dataFiltro = ['min' => $dataDe , 'max' => $dataAte];
    endif;

    if(!empty($request->query->get('filter')['ClienteId']) != '') :
        $preQuery->filterById($request->query->get('filter')['ClienteId']);
    endif;
}
