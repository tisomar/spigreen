<?php
/* @var $objEndereco Endereco */
include_once __DIR__ . '/../includes/include_propel.inc.php';

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: text/html; charset=UTF-8');
}

$nomeCidade = $idCidade = null;
$objCidades = array();

$queryCidades = CidadeQuery::create()
        ->orderByNome();


// Quando for uma chamada por ajax, deve-se passar a variável [estadoId]
if (isset($_GET['estadoId']) && (is_numeric($_GET['estadoId']))) {
    $queryCidades->filterByEstadoId($_GET['estadoId']);
}
// Se for um include, verifica se tem o endereço instanciado para carregar as cidades
// pelo estado do endereço
elseif (isset($_GET['id']) && (is_numeric($_GET['id']))) {
//elseif (isset($objEndereco) && $objEndereco->getCidade() instanceof Cidade)
    $queryCidades->filterByEstadoId($objEndereco->getCidade()->getEstadoId());
}


// Quando for um post, obtém a cidade selecionada para retornar em caso de erro
if ($_SERVER['REQUEST_METHOD'] == 'POST' && (isset($_POST['e']['CIDADE_ID']))) {
    $idCidade = $_POST['e']['CIDADE_ID'];
}

// Se for um get, verifica se tem cidade cadastrada no endereço para traze-la
// selecionada no campo ou quando for seleção automática por busca de CEP
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Atualiza conforme o endereço.
    if (isset($_GET['id']) && (is_numeric($_GET['id']))) {
        $idCidade = $objEndereco->getCidadeId();
    }

    // Atualiza a cidade para quando buscamos um CEP na qual passamos o nome
    // da cidade como parâmetro.
    if (isset($_GET['cidade'])) {
        $nomeCidade = trim($_GET['cidade']);
    }
}

//if ($idCidade || $nomeCidade)
if ($idCidade || $nomeCidade || isset($_GET['estadoId']) || isset($_GET['id'])) {
    $objCidades = $queryCidades->find();
}
?>
<select class="form-control validity-city" id="register-city" name='e[CIDADE_ID]' required>
    <option value="">Cidade</option>
    <?php foreach ($objCidades as $cidade) : /* @var $cidade Cidade */ ?>
        <?php
        $selected = ($nomeCidade == (trim($cidade->getNome()))) || ($idCidade == $cidade->getId()) ? 'selected="selected"' : '';
        ?>
        <option <?php echo $selected ?> value="<?php echo $cidade->getId(); ?>">
            <?php echo $cidade->getNome(); ?>
        </option>
    <?php endforeach; ?>   
</select> 
