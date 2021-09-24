<?php
$query_builder = $request->request->get('object') . 'Query';

$object = $query_builder::create()
            ->findOneById($request->request->get('id'));

$method = 'set' . $request->request->get('method');

$value = $request->request->get('value') == "true";

$object->$method($value);
$object->save();
