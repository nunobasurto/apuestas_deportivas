<?php

define('DRUPAL_ROOT', getcwd());
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

echo '<table class="table table-hover table-responsive">';
echo  '<tr>';
echo     '<td colspan=7><h3>Jornada '.$jor_aux.'</h3></td>';
echo   '</tr>';
echo  '<tr>
    <td>Jornada</td>
    <td><img src="images/bet365.png" width="60" height="25" alt="BET365"></td>
    <td><img src="images/marcaapuestas.jpeg" width="60" height="20" alt="Marca"></td>
    <td><img src="images/bwin.png" width="60" height="25" alt="BWIN"></td>
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
    echo '<tr>';
    echo '<td>'.$balance_jornada->jornada.'</td>';
	echo '<td>'.$balance_jornada->BET365.'</td>';
	echo '<td>'.$balance_jornada->Marca.'</td>';
	echo '<td>'.$balance_jornada->BWIN.'</td>';
    echo '<td>'.end($balance_jornada).'</td>';
    echo '</tr>';
}
echo '<tr>';
    echo '<td> SUMATORIO: </td>';
    echo '<td>'.$contador[0].'</td>';
    echo '<td>'.$contador[1].'</td>';
    echo '<td>'.$contador[2].'</td>';
    echo '<td>'.$contador[3].'</td>';
    echo '</tr>';
echo '</table>';


?>