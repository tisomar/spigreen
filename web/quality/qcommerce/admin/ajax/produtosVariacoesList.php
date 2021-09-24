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

$result = $obj->listarVariacoes($_GET["prod1_cod"]);

$_SESSION["PRODUTOS"] = serialize($obj);
?>
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="tabelas strip">
<tr>
<td class="header"><?php echo utf8_encode('Variação');?></td>
<td class="header center">EA</td>
<td class="header center">EM</td>
<td class="header" colspan="2">&nbsp;</td>
</tr>
<?php
if (mysql_num_rows($result)) {
    while ($dados = mysql_fetch_array($result)) {
        ?>
        <tr>
        <td><?php echo utf8_encode($dados["VARI1_NOM"]);?></td>
        <td class="center"><?php echo utf8_encode($dados["VARI1_EST_ATU"]);?></td>
        <td class="center"><?php echo utf8_encode($dados["VARI1_EST_MIN"]);?></td>
        <td class="center"><a href="javascript:alterarVariacoes('<?php echo $dados["VARI1_COD"];?>')"><img src="../img/icones/alterar.png" width="18" height="17" alt="" /></a></td>
        <td class="center"><?php if ($dados["VARI1_AUT"] == 0) {
            ?><a href="javascript:excluirVariacoes('<?php echo $dados["VARI1_COD"];?>')"><img src="../img/icones/excluir.png" width="18" height="17" alt="" /></a><?php
                           }?></td>
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
