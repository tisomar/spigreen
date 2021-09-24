<?php

include __DIR__ . '/../includes/include_config.inc.php';

$propelConnection = Propel::getConfiguration();

$arrDns = explode(';', $propelConnection['datasources']["qcommerce"]["connection"]["dsn"]);

$servername = str_replace('mysql:host=', '', $arrDns[0]);
$username = $propelConnection['datasources']["qcommerce"]["connection"]["user"];
$password = $propelConnection['datasources']["qcommerce"]["connection"]["password"];
$dbname = str_replace('dbname=', '', $arrDns[1]);
;


ini_set('max_execution_time', -1);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$timeStart = new DateTime();
echo "\n\n\n\nTime Start: " . $timeStart->format("Y-m-d H-i-s") . " <br>\n";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT 
            qp1_cliente.ID, 
            qp1_cliente.INDICADOR_ID,
            qp1_cliente.INDICADOR_DIRETO_ID
        FROM qp1_cliente 
        WHERE 
            INDICADOR_ID is not null
        ORDER BY tree_left";


/** @var mysqli_result $result */
$result = $conn->query($sql);

if (!$result) {
    echo("Error description: " . mysqli_error($conn));
    die;
}

if ($result->num_rows > 0) {
    $redeCliente = array();
    $arrayCliente = $allClientes =  $arrRetirados = $arrPreCadastro = array();

//    while($row = $result->fetch_assoc()){
//        $arrPreCadastro[] = $row["ID"];
//        $allClientes[$row["ID"]] = $row["INDICADOR_DIRETO_ID"];
//
//        if(($row["CONCLUIDO"] === null || $row["CONCLUIDO"] === '1')){
//
//            if(isset($arrayCliente[$row["INDICADOR_ID"]]) || $row["INDICADOR_ID"] == 273){
//                $arrayCliente[$row["ID"]] = $row["INDICADOR_ID"];
//            } elseif(isset($arrRetirados[$row["INDICADOR_ID"]])){
//                $pai = getPaiValido($arrayCliente, $arrRetirados, $row["INDICADOR_ID"]);
//                $arrayCliente[$row["ID"]] = $pai;
//            }
//
//
//        } else {
//            $arrRetirados[$row["ID"]] = $row["INDICADOR_ID"];
//        }
//
//    }

    while ($row = $result->fetch_assoc()) {
        $arrayCliente[$row["ID"]] = $row["INDICADOR_ID"];
    }

    $left = 1;
    $right = 2;
    $level = 1;

    getConcertarRedeClientes($redeCliente, $arrayCliente, "273", $left, $right, $level);

    $redeCliente[] = array(
        "ID" => '273',
        "INDICADOR_ID" => '',
        "NR_LEVEL" => '0',
        "NR_LEFT" => '1',
        "NR_RIGHT" => $left + 1

    );


    //mostarRedeClientes($redeCliente);



    atualizarClientes($redeCliente, $conn);

    //removerClientesRede($arrRetirados, $conn);

    //updatePreCadastroClienteAdjust($arrPreCadastro, $conn);

    die("ok");


//    while($row = $result->fetch_assoc()) {
//        echo "id: " . $row["id"]. " - Name: " . $row["firstname"]. " " . $row["lastname"]. "<br>";
//    }
} else {
    echo "0 results";
}
$conn->close();

$timeEnd = new DateTime();
echo "Time Start: " . $timeStart->format("Y-m-d H-i-s") . " <br>\n";
echo "Time End: " . $timeEnd->format("Y-m-d H-i-s") . " <br>\n";
die("1 \n\n\n\n\n");


function atualizarClientes($arrayCliente, $conn)
{

    $querysPendentes = "";
    $querysPendentesCount = 0;
    $querysPendentesCountTotal = 0;
    foreach ($arrayCliente as $cliente) {
        $querysPendentes = "UPDATE 
                                qp1_cliente 
                            SET 
                                tree_left='" . $cliente["NR_LEFT"] . "', 
                                tree_right='" . $cliente["NR_RIGHT"] . "', 
                                tree_level='" . $cliente["NR_LEVEL"] . "' 
                            WHERE ID='" . $cliente["ID"] . "' ;";


        $querysPendentesCount++;

        if ($conn->query($querysPendentes) === true) {
            if ($querysPendentesCount % 10 == 0) {
                $porcento = number_format(($querysPendentesCount * 100) / count($arrayCliente));
                echo 'update table:' . $querysPendentesCount . " ---> " . count($arrayCliente) . " (" . $porcento . "%)" . " <br>\n ";
            }
        } else {
            echo "Error updating record: " . $conn->error . "<br>\n ";
        }
    }
}

function removerClientesRede($arrayClienteRetirados, $conn)
{

    $querysPendentes = "";
    $querysPendentesCount = 0;
    $querysPendentesCountTotal = 0;

    foreach ($arrayClienteRetirados as $clienteID => $indicador_Id) {
        /*$indicador = null;

        if(isset($arrayClienteRetirados[$indicador_Id])){
            $pai = getPaiValido($arrClientes, $arrayClienteRetirados, $indicador_Id);
            $indicador = $pai;
        } else {
            $indicador = $indicador_Id;
        }*/

        $querysPendentes = "UPDATE 
                                qp1_cliente 
                            SET 
                                tree_left = null, 
                                tree_right = null, 
                                tree_level = NULL,
                                INDICADOR_DIRETO_ID = null,
                                INDICADOR_ID = null
                            WHERE 
                                ID= '" . $clienteID . "' ;";
        $querysPendentesCount++;

        if ($conn->query($querysPendentes) === true) {
            if ($querysPendentesCount % 10 == 0) {
                $porcento = number_format(($querysPendentesCount * 100) / count($arrayClienteRetirados));
                echo 'update table:' . $querysPendentesCount . " ---> " . count($arrayClienteRetirados) . " (" . $porcento . "%)" . " <br>\n ";
            }
        } else {
            echo "Error updating record: " . $conn->error . "<br>\n ";
        }
    }
}

function updatePreCadastroClienteAdjust($arrayCliente, $conn)
{

    $querysPendentes = "";
    $querysPendentesCount = 0;
    $querysPendentesCountTotal = 0;

    $arrChunkedClients = array_chunk($arrayCliente, 1000);

    foreach ($arrChunkedClients as $arrClientes) {
        $querysPendentes = "UPDATE 
                                qp1_pre_cadastro_cliente 
                            SET 
                                CONCLUIDO = 1
                            WHERE 
                                CLIENTE_ID in ('" . join("','", $arrClientes) . "') ;";
        $querysPendentesCount++;

        if ($conn->query($querysPendentes) === true) {
            if ($querysPendentesCount % 10 == 0) {
                $porcento = number_format(($querysPendentesCount * 100) / count($arrayCliente));
                echo 'update table:' . $querysPendentesCount . " ---> " . count($arrayCliente) . " (" . $porcento . "%)" . " <br>\n ";
            }
        } else {
            echo "Error updating record: " . $conn->error . "<br>\n ";
        }
    }
}


function mostarRedeClientes($arrayCliente)
{
    $narrayCliente = array_sort($arrayCliente, 'NR_LEFT');

    foreach ($narrayCliente as $cliente) {
        for ($i = 0; $i < $cliente["NR_LEVEL"]; $i++) {
            echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;|";
        }
        if ($cliente["NR_LEVEL"] > 0) {
            echo "  ---> ";
        }
        echo "<b>" . $cliente["ID"] . ' -- ' . $cliente["NR_LEVEL"] . "</b> (" . $cliente["NR_LEFT"] . " - " . $cliente["NR_RIGHT"] . ") <br>\n";
    }
}

function getConcertarRedeClientes(&$redeCliente, $arrayCliente, $pai, &$left, &$right, $level)
{

    $filhos = getFilhos($arrayCliente, $pai);

    foreach ($filhos as $id) {
        $leftInit = $left;

        $newCliente = array(
            "ID" => $id,
            "INDICADOR_ID" => $pai,
            "NR_LEVEL" => $level,
            "NR_LEFT" => $leftInit + 1,
        );

        $left++;
        getConcertarRedeClientes($redeCliente, $arrayCliente, $id, $left, $right, $level + 1);
        $left++;

        $newCliente["NR_RIGHT"] = $left;
        $redeCliente[] = $newCliente;

        if (count($redeCliente) % 1000 == 0) {
            $porcento = number_format((count($redeCliente) * 100) / count($arrayCliente));
            echo count($redeCliente) . " ---> " . count($arrayCliente) . " (" . $porcento . "%)" . " <br>\n ";
        }
    }
}

function getFilhos($arrayCliente, $pai)
{
    $clienteFilhos = array();
    foreach ($arrayCliente as $id => $paiCliente) {
        if ($paiCliente == $pai) {
            $clienteFilhos[] = $id;
        }
    }
    return $clienteFilhos;
}

function array_sort($array, $on, $order = 'SORT_ASC')
{
    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case 'SORT_ASC':
                asort($sortable_array);
                break;
            case 'SORT_DESC':
                arsort($sortable_array);
                break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
}


function getPaiValido($arrValidos, $arrRetirados, $paiAtual)
{

    $pai = $arrRetirados[$paiAtual];

    if (isset($arrValidos[$pai])) {
        return $pai;
    } elseif ($arrRetirados[$pai]) {
        getPaiValido($arrValidos, $arrRetirados, $pai);
    }

    return null;
}
