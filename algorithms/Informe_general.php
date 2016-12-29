<?php

define('DRUPAL_ROOT', getcwd());
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

echo '<table class="table table-hover table-responsive">';
echo  '<tr>';
echo     '<td colspan=7><h3>Jornada '.$jor_aux.'</h3></td>';
echo   '</tr>';
echo  '<tr align="right">
    <td><CENTER>Jornada</CENTER></td>
    <td><img src="images/bet365_2.png" width="60" height="20" alt="BET365"></td>
    <td><img src="images/marcaapuestas.jpeg" width="60" height="20" alt="Marca"></td>
    <td><img src="images/bwin.jpg" width="60" height="25" alt="BWIN"></td>
    <td><img src="images/888.png" width="40" height="25" alt="888"></td>
  </tr>';

$balance = db_select('balance_general','bg')
    ->fields('bg')
    ->execute();
$contador = array();
foreach ($balance as $balance_jornada) {
    $contador[0]+=$balance_jornada->BET365;
    $contador[1]+=$balance_jornada->Marca;
    $contador[2]+=$balance_jornada->BWIN;
    $contador[3]+=end($balance_jornada);
    echo '<tr align = "right">';
    echo '<td><CENTER><a href=informe-jornada?jornada='.$balance_jornada->jornada.'>'. $balance_jornada->jornada. '</a></CENTER></td>';
    if($balance_jornada->BET365>0)
	   echo '<td class="success">'.number_format($balance_jornada->BET365,2).'</td>';
    else
        echo '<td class="warning">'.number_format($balance_jornada->BET365,2).'</td>';
	if($balance_jornada->Marca>0)
        echo '<td class="success">'.number_format($balance_jornada->Marca,2).'</td>';
    else
        echo '<td class="warning">'.number_format($balance_jornada->Marca,2).'</td>';
	if ($balance_jornada->BWIN>0)
        echo '<td class="success">'.number_format($balance_jornada->BWIN,2).'</td>';
    else
        echo '<td class="warning">'.number_format($balance_jornada->BWIN,2).'</td>';
    if(end($balance_jornada)>0)
        echo '<td class="success">'.number_format(end($balance_jornada),2).'</td>';
    else
        echo '<td class="warning">'.number_format(end($balance_jornada),2).'</td>';

    echo '</tr>';
}
echo '<tr align = "right">';
echo '<td><CENTER><b>Balance final: </b></CENTER></td>';
if($contador[0]>0)
    echo '<td class="success"><b>'.number_format($contador[0],2).'</b></td>';
else
    echo '<td class="warning"><b>'.number_format($contador[0],2).'</b></td>';
if($contador[1]>0)
    echo '<td class="success"><b>'.number_format($contador[1],2).'</b></td>';
else
    echo '<td class="warning"><b>'.number_format($contador[1],2).'</b></td>';
if($contador[2]>0)
    echo '<td class="success"><b>'.number_format($contador[2],2).'</b></td>';
else
    echo '<td class="warning"><b>'.number_format($contador[2],2).'</b></td>';
if($contador[3]>0)
    echo '<td class="success"><b>'.number_format($contador[3],2).'</b></td>';
else
    echo '<td class="warning"><b>'.number_format($contador[3],2).'</b></td>';
echo '</tr>';
echo '</table>';


?>