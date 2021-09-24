<?php
/* @var $objEndereco Endereco */
include_once __DIR__ .  '/../includes/include_propel.inc.php';

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: text/html; charset=UTF-8');
}

$nomeCidade = $idCidade = null;

$queryCidades = CidadeQuery::create();

if (isset($_GET['estadoId']) && (is_numeric($_GET['estadoId']))) {
    $queryCidades->filterByEstadoId($_GET['estadoId']);
} elseif (isset($_GET['id']) && (is_numeric($_GET['id']))) {
    $queryCidades->filterByEstadoId($objEndereco->getCidade()->getEstadoId());
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && (isset($_POST['cadastro']['CIDADE_ID']))) {
    $idCidade = $_POST['cadastro']['CIDADE_ID'];
}

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id']) && (is_numeric($_GET['id']))) {
        $idCidade = $objEndereco->getCidadeId();
    }

    if (isset($_GET['cidade'])) {
        $nomeCidade = trim($_GET['cidade']);
    }
}



if ($idCidade || $nomeCidade || isset($_GET['estadoId']) || isset($_GET['id'])) {
    $objCidades = $queryCidades->orderByNome()->find();
}

?>

    <div class="select" id="cidades">
        <label><?php echo escape(_trans('forms.endereco.label.cidade')) ?> <span class="asterisk">*</span></label>
        <select class="form-control" name="cadastro[CIDADE_ID]" title="Cidade" id="sel-cidades" required>
            <option value=""></option>
            <?php if (isset($objCidades)) :?>
                <?php foreach ($objCidades as $cidade) : /* @var $cidade Cidade */ ?>
                    <?php $selected =   ($nomeCidade == (trim($cidade->getNome())))
                                        || ($idCidade == $cidade->getId())
                                    ? 'selected="selected"'
                                    : '';
                    ?>
                    <option <?php echo $selected ?> value="<?php echo $cidade->getId(); ?>">
                        <?php echo $cidade->getNome(); ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select> 
    </div><!-- select -->

