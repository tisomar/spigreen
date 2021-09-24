<?php
include("../includes/config.inc.php");
?>
<table width="100%" cellpadding="0" cellspacing="0" border="0" class="tabelas">
<tr>
<td class="header">Visitas</td>
<td class="header">&nbsp;</td>
</tr>
<tr> 
<td class="center nopadd" colspan="2">

<div id="abaMes" class="aba_on"><a href="javascript:carregaGraficoAnalytics('mes');"><?php echo 'Mês';?></a></div>
<div id="abaDia" class="aba_off"><a href="javascript:carregaGraficoAnalytics('dia');">Dia</a></div>

</td>
</tr>
<tr>
<td class="center" colspan="2">
<?php
$objAnalytics = new Analytics($analyticsLogin, $analyticsPassword, $analyticsId);
$data = $objAnalytics->graficoVisitasMes();
echo $data;
?>
</td>
</tr>
</table>
