<?php
set_time_limit(0);

$con = Propel::getConnection();

//$stmt = $con->prepare('
//    SET FOREIGN_KEY_CHECKS = 0;
//    TRUNCATE `qp1_google_shopping_item`;
//    TRUNCATE `qp1_google_shopping_categoria`;
//    SET FOREIGN_KEY_CHECKS = 1;
//');
//$stmt->execute();

list($usec, $sec) = explode(' ', microtime());
$script_start = (float) $sec + (float) $usec;

$file = __DIR__ . '/taxonomy.pt-BR.txt';
$filecontents = file_get_contents($file);

$rows = explode("\n", $filecontents);
foreach ($rows as $row) {
    if (strpos($row, 'Google_Product_Taxonomy_Version') == false) {
        $gs = new CategoriaGoogleShopping();
        $gs->setNome($row);
        $gs->save($con);
    }
}

// Terminamos o "contador" e exibimos
list($usec, $sec) = explode(' ', microtime());
$script_end = (float) $sec + (float) $usec;
$elapsed_time = round($script_end - $script_start, 5);
echo 'Elapsed time: ', $elapsed_time, ' secs. Memory usage: ', round(((memory_get_peak_usage(true) / 1024) / 1024), 2), 'Mb';
