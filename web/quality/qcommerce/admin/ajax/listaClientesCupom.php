<?php
include('../includes/config.inc.php');
 
if ($_POST) {
    $arrCupom = $_POST['cupom'];
    
    if (isset($arrCupom['ID'])) {
        $objCupom = CupomQuery::create()->filterById($arrCupom['ID'])->findOne();
        
        if ($objCupom instanceof Cupom) {
            $clientes = $objCupom->getClientesCupom();
            $regitrados = true;
        }
    } else {
        $clientes = Cupom::filtraClientesCupom($arrCupom);
        $regitrados = false;
    }
}
?>
    <table width="100%" cellpadding="0" cellspacing="0" border="0" class="tabelas strip">
        <tr>
            <td class="header center">ID</td>
            <td class="header center">Nome / Razão Social</td>
            <td class="header center">E-mail</td>
            <td class="header center">Data Nascimento / Fundação</td>
            <?php if ($regitrados) { ?>
            <td class="header center">Utilizado</td>
            <?php } ?>
        </tr>
<?php
if (count($clientes) > 0) {
    foreach ($clientes as $cliente) { ?>
        <tr>
            <td class="center"><?php echo $cliente->getId(); ?></td>
            <td class="center"><?php echo $cliente->getNomeCompleto(); ?></td>
            <td class="center"><?php echo $cliente->getEmail(); ?></td>
            <td class="center"><?php echo $cliente->getDataNascimentoDataFundacao('d/m/Y'); ?></td>
            <?php if ($regitrados) {
                $objCupomCliente = CupomClienteQuery::create()->filterByCupomId($arrCupom['ID'])->filterByClienteId($cliente->getId())->findOne();
                ?>
            <td class="center"><?php echo ($objCupomCliente->getUtilizado() == 1) ? '<span class="sim">SIM</span>' : '<span class="nao">Não</span>'; ?></td>
            <?php } ?>
        </tr>
        <?php
    }
} else {
    echo "<tr><td colspan='4' class='center'>Nenhum cliente!</td></tr>";
}
?>
    </table>  
