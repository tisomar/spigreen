<?php

### Filtros habilitados
if ($request->query->get('is_filter') == 'true') {
    $fieldListSpecific  = $object_peer::getFieldNames(BasePeer::TYPE_RAW_COLNAME);

    ### Percorrer filtros
    foreach ($request->query->get('filter') as $field => $value) {
        $value = trim($value);
        $field = strtoupper($field);
        
        if ($value === '' || $value === null || !in_array($field, $fieldListSpecific)) {
            continue;
        }
        
        $object_peer_selected = $object_peer;
        
        ### Especifico o tipo de filtro
        $column = $object_peer_selected::translateFieldName($field, BasePeer::TYPE_RAW_COLNAME, BasePeer::TYPE_COLNAME);
        
        $criteria = null;
        switch ($object_peer_selected::getTableMap()->getColumn($column)->getType()) {
            case PropelColumnTypes::BOOLEAN:
                $criteria = Criteria::EQUAL;
                break;
        }
        
        if (is_null($criteria)) {
            $criteria = ($object_peer_selected::getTableMap()->getColumn($column)->isNumeric()
                    ? Criteria::EQUAL
                    : Criteria::LIKE
                );
        }
        $value = $criteria == Criteria::EQUAL ? $value : '%' . $value . '%';
        
        ### Adiciono o filtro
        $constante = strtoupper($field);
        if (defined($object_peer_selected . '::' . $constante)) {
            $constante = constant($object_peer_selected . '::' . $constante);
            $query_builder->addAnd($constante, $value, $criteria);
        }
    }
}
