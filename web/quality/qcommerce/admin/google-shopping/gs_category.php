<?php

$results = GoogleShoppingCategoriaQuery::create()
    ->select(array('id', 'text'))
    ->withColumn('Id', 'id')
    ->withColumn('Nome', 'text')
    ->filterByNome('%' . $_GET['q'] . '%')
    ->orderByNome()
    ->find()
    ->toArray();

echo json_encode($results);
