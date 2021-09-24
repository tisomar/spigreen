<?php
$cronStopTime = time();
$cronTotalTime = $cronStopTime - $cronStartTime;

echo PHP_EOL;
echo "**************************************************" . PHP_EOL;
echo " - Finalizado em: " . date('d/m/Y H:i:s') . PHP_EOL;
echo " - Tempo Total: " . $cronTotalTime . " segundos." . PHP_EOL;
echo " - " . $cronFile . PHP_EOL;
echo "**************************************************" . PHP_EOL;
