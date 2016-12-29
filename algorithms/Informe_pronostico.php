<?php
define('DRUPAL_ROOT', getcwd());
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
//Muestra el pronóstico de la siguiente jornada.

$tiempo = getdate();
$currentTyme= $tiempo["year"] . '-' . $tiempo["mon"] . '-' . $tiempo["mday"] . ' 00:00:00';
$jornada  = db_query('SELECT f.jornada FROM {fecha_jornada} f WHERE f.fecha_despues > :ff ORDER BY f.fecha_despues asc', array(':ff' => $currentTyme))->fetchField(); 

echo '<table class="table table-hover table-responsive">';
echo  '<tr>';
echo     '<td colspan=7><h3>Jornada '.$jornada.'</h3></td>';
echo   '</tr>';
echo '<tr align="right">
    <td>Partido</td>
    <td><CENTER>Pronóstico</CENTER></td>
    <td><img src="images/bet365_2.png" width="60" height="20" alt="BET365"></td>
    <td><img src="images/marcaapuestas.jpeg" width="60" height="20" alt="Marca"></td>
    <td><img src="images/bwin.jpg" width="60" height="25" alt="BWIN"></td>
    <td><img src="images/888.png" width="40" height="25" alt="888"></td>
	</tr>';
$jor_Aux = $jornada;
$jornada = $jornada*100;
$arrayCasas = array(' ','BET365','Marca', 'BWIN','888Bet');
for ($i=$jornada+1; $i <=$jornada+10; $i++) { 
	$tarifas=db_select('apuestas','a')
			->fields('a', array('BET365','Marca', 'BWIN','888Bet'))
			->condition('id_partido', $i, '=')
			->execute();

	foreach($tarifas as $tarifa){
		$cuotas = array();
		array_push($cuotas, $tarifa->BET365);
		array_push($cuotas, $tarifa->Marca);
		array_push($cuotas, $tarifa->BWIN);
		array_push($cuotas, end($tarifa));
		$cuot = array();
		$contador=0;
		foreach ($cuotas as $key=>$cuota) {
			$contador++;
			$cu=db_select('Cuotas','c')
				->fields('c', array('Local','Empate', 'Visitante'))
				->condition('id_cuota', $cuota, '=')
				->execute();
			foreach ($cu as $valor) {
				$insert = array($valor->Local, $valor->Empate, $valor->Visitante);
			}
			if($cuota!=null)
				$cuot[$cuota] = $insert;
			else
				$cuot[$i*10+$contador] = array(0,0,0);
		}
		$mostrar[$i]=$cuot;
	}

}
$contadores = array();
foreach($mostrar as $id=>$partido){
	$equipolocal  = db_query('SELECT f.equipo_local FROM {fecha_jornada} f WHERE f.id_partido = :id ', array(':id' => $id))->fetchField();
    $equipovisitante  = db_query('SELECT f.equipo_visitante FROM {fecha_jornada} f WHERE f.id_partido = :id', array(':id'=>$id))->fetchField();
    //$escudolocal = db_query('SELECT i.escudo FROM {images} i WHERE i.id_equipo = :id_equipo' array(':id_equipo' => $equipolocal))->fetchField();
    //$escudovisitante = db_query('SELECT i.escudo FROM {images} i WHERE i.id_equipo = :id_equipo' array(':id_equipo' => $equipovisitante))->fetchField();
    $equipolocal = db_query('SELECT e.nombreCompleto FROM {equipos} e WHERE e.id_equipo = :id', array(':id'=>$equipolocal))->fetchfield();
    $equipovisitante = db_query('SELECT e.nombreCompleto FROM {equipos} e WHERE e.id_equipo = :id', array(':id'=>$equipovisitante))->fetchfield();
	$pronostico = db_query('SELECT p.pronostico_estimado FROM {pronosticos} p WHERE p.id_partido = :id', array(':id'=>$id))->fetchfield();

	echo '<tr align = "right">';
	
    //echo '<td> <img src=' . $escudolocal .' width="40" height="25" alt="888">-<img src=' . $escudovisitante .' width="40" height="25" alt="888"></td>';
    echo '<td>' . $equipolocal . ' - '. $equipovisitante . '</td>';
    echo '<td><CENTER>'.$pronostico.'</CENTER></td>';
	if($pronostico=="1")
		$pronos = 0;
	else if($pronostico=="X")
		$pronos = 1;
	else
		$pronos = 2;
	foreach ($partido as $casa=>$casa_apuestas){
		foreach ($casa_apuestas as $x12=>$cuota) {
			if($x12==$pronos){
				echo '<td>'.number_format($cuota,2).'</td>';
				$contadores[$casa-($id*10)]+=$cuota;
			}
		}
	}
	echo '</tr>';
}

echo '<tr align = "right">';
echo '<td colspan="2"><CENTER><b>Ganancias Potenciales:<b></CENTER></td>';
echo '<td><b>'.number_format($contadores[1],2).'</b></td>';
echo '<td><b>'.number_format($contadores[2],2).'</b></td>';
echo '<td><b>'.number_format($contadores[3],2).'</b></td>';
echo '<td><b>'.number_format($contadores[4],2).'</b></td>';
echo '</tr>';
echo '</table>';




?>