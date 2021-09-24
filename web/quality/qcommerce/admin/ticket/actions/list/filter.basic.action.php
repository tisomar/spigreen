<?php

if (!isset($preQuery)) {
    $preQuery = null;
}

$query = $_classQuery::create(null, $preQuery);

if ($request->query->get('is_filter') == 'true') {
    foreach ($request->query->get('filter') as $phpName => $value) {
        $value = trim($value);
        $methodName = 'filterBy' . $phpName;

        if ($value === '' || $value === null || !method_exists($classQueryName, $methodName)) {
            continue;
        }

        $query->$methodName($value);
    }
}
