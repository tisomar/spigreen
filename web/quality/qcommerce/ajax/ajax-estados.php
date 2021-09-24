<?php
/* @var $objEndereco Endereco */
include_once __DIR__ . '/../includes/include_propel.inc.php';

$defaultValueEstado = null;
if (isset($_GET['id']) && (is_numeric($_GET['id']))) {
    $defaultValueEstado = $objEndereco->getCidade()->getEstadoId();
} else {
    if (isset($objEndereco) && $objEndereco instanceof Endereco) {
        $objCidade = $objEndereco->getCidade();
        if (!is_null($objCidade)) {
            $defaultValueEstado = $objEndereco->getCidade()->getEstadoId();
        }
    }
}

$collEstados = EstadoQuery::create()->orderByNome()->find();
?>

<select class="form-control validity-state" id="address-uf" name="estadoId" required>
    <option value="" data-sigla="">Estado</option>
    <?php foreach ($collEstados as $objEstado) : /* @var $estado Estado */ ?>      
        <?php
        $selected = (isset($_POST['estadoId']) && ($_POST['estadoId'] == $objEstado->getId()) or
                (isset($_GET['sigla'])) && ($_GET['sigla'] == $objEstado->getSigla()) or
                ($defaultValueEstado == $objEstado->getId())) ? 'selected' : '';
        ?>
        <option <?php echo $selected ?> value="<?php echo $objEstado->getId(); ?>" data-sigla="<?php echo $objEstado->getSigla() ?>">
            <?php echo $objEstado->getSigla(); ?>
        </option>                
    <?php endforeach; ?>             
</select> 
