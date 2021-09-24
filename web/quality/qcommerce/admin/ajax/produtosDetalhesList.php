<?php
include("../includes/config.inc.php");
include("../includes/security.inc.php");
include("../includes/funcoes.inc.php");

// CLASSES DESTA SEÇÃO
include("../classes/qpress_produto.class.php");

// VERIFICA SE A CLASSE ESTÁ CRIADA
if ($_SESSION["PRODUTOS"]) {
    $obj = unserialize($_SESSION["PRODUTOS"]);
} else {
    $obj = new Produto();
}

$result = $obj->listarDescricoes($_GET["prod1_cod"]);

$_SESSION["PRODUTOS"] = serialize($obj);
?>
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="tabelas strip">
<tr>
<td class="header"><?php echo utf8_encode('Título');?></td>
<td class="header" colspan="2">&nbsp;</td>
</tr>
<?php
if (mysql_num_rows($result)) {
    while ($dados = mysql_fetch_array($result)) {
        ?>
        <tr>
        <td><?php echo utf8_encode($dados["DESC1_NOM"]);?></td>
        <td class="center"><a href="javascript:alterarDescricoes('<?php echo $dados["DESC1_COD"];?>')"><img src="../img/icones/alterar.png" width="18" height="17" alt="" /></a></td>
        <td class="center"><a href="javascript:excluirDescricoes('<?php echo $dados["DESC1_COD"];?>')"><img src="../img/icones/excluir.png" width="18" height="17" alt="" /></a></td>
        </tr>
        <?php
    }
} else {
    ?>
    <tr>
    <td colspan="3"><?php echo utf8_encode('Nenhum registro disponível');?></td>
    </tr>
    <?php
}
?>
</table>
