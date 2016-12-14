<?php
define('DRUPAL_ROOT', getcwd());
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
//Obetenemos la jornada para que el demonio ejecute el algoritmo.
$tiempo = getdate();
$currentTyme= $tiempo["year"] . '-' . $tiempo["mon"] . '-' . $tiempo["mday"] . ' 00:00:00';
$jornada  = db_query('SELECT f.jornada FROM {fecha_jornada} f WHERE f.fecha_despues <= :ff ORDER BY f.fecha_despues desc', array(':ff' => $currentTyme))->fetchField(); 
$jornada=12;
$jor_aux=$jornada;
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
		foreach ($cuotas as $cuota) {
			$cu=db_select('Cuotas','c')
				->fields('c', array('Local','Empate', 'Visitante'))
				->condition('id_cuota', $cuota, '=')
				->execute();
			foreach ($cu as $valor) {
				$insert = array($valor->Local, $valor->Empate, $valor->Visitante);
			}
			$cuot[$cuota] = $insert;
			//$cuot = array($cuota=>$insert);
		}
		$mostrar[$i]=$cuot;
	}


}
$sumatorio = 0;

echo '<table class="table table-hover table-responsive">';
echo  '<tr>';
echo     '<td colspan=7><h3>Jornada '.$jor_aux.'</h3></td>';
echo   '</tr>';
echo  '<tr>
    <td>Partido</td>
    <td>Resultado</td>
    <td>Pronóstico</td>
    <td><img src="images/bet365.png" width="60" height="25" alt="BET365"></td>
    <td><img src="images/marcaapuestas.jpeg" width="60" height="20" alt="Marca"></td>
    <td><img src="images/bwin.png" width="60" height="25" alt="BWIN"></td>
    <td><img src="images/888.png" width="40" height="25" alt="888"></td>
  </tr>';

$contadores  = array();

foreach($mostrar as $id=>$partido){
	$equipolocal  = db_query('SELECT f.equipo_local FROM {fecha_jornada} f WHERE f.id_partido = :id ', array(':id' => $id))->fetchField();
    $equipovisitante  = db_query('SELECT f.equipo_visitante FROM {fecha_jornada} f WHERE f.id_partido = :id', array(':id'=>$id))->fetchField();

    $equipolocal = db_query('SELECT e.nombreCompleto FROM {equipos} e WHERE e.id_equipo = :id', array(':id'=>$equipolocal))->fetchfield();
    $equipovisitante = db_query('SELECT e.nombreCompleto FROM {equipos} e WHERE e.id_equipo = :id', array(':id'=>$equipovisitante))->fetchfield();

    $goleslocal = db_query('SELECT p.goles_local FROM {partidos} p WHERE p.id_partido = :id', array(':id'=>$id))->fetchfield();
    $golesvisitante = db_query('SELECT p.goles_visitante FROM {partidos} p WHERE p.id_partido = :id', array(':id'=>$id))->fetchfield();

    $pronostico = db_query('SELECT p.pronostico_estimado FROM {pronosticos} p WHERE p.id_partido = :id', array(':id'=>$id))->fetchfield();

    echo '<tr>';
    echo '<td>'.$equipolocal.'-'.$equipovisitante.'</td>';
    echo '<td>'.$goleslocal.'-'.$golesvisitante.'</td>';
    echo '<td>'.$pronostico.'</td>';
  

	//echo $equipolocal . ' ' . $goleslocal .' vs ' . $golesvisitante . ' ' . $equipovisitante;
	//echo ' Pronostico: ' . $pronostico;
	if($pronostico=="1")
		$pronos = 0;
	else if($pronostico=="X")
		$pronos = 1;
	else
		$pronos = 2;
	$resolucion = compara_resultados($goleslocal, $golesvisitante, $pronostico);
	foreach ($partido as $casa=>$casa_apuestas){
		foreach ($casa_apuestas as $x12=>$cuota) {
			if($x12==$pronos AND $resolucion=="ACIERTO"){
				echo '<td>'.$cuota.'</td>';
				$contadores[$casa-($id*10)]+=($cuota-1);
			}else if($x12==$pronos AND $resolucion=="ERROR"){
				echo '<td>-1</td>';
				$contadores[$casa-($id*10)]-=1;
			}
		}
	}
	echo '</tr>';
}
$balance_general = db_query('SELECT bg.BET365 FROM {balance_general} bg WHERE bg.jornada = :jornada', array(':jornada' => $jor_aux))->fetchfield();
if ($balance_general == null){
	$ins=db_insert('balance_general')
			->fields(array(
			'jornada' => $jor_aux,
			'BET365'=>($contadores[1]),
			'Marca'=>($contadores[2]),
			'BWIN'=>($contadores[3]),
			'888Bet'=>($contadores[4]),
			))
			->execute();
}
echo '<tr>';
echo '<td> BALANCE </td>';
echo '<td> </td>';
echo '<td> </td>';
echo '<td>'.($contadores[1]).'</td>';
echo '<td>'.($contadores[2]).'</td>';
echo '<td>'.($contadores[3]).'</td>';
echo '<td>'.($contadores[4]).'</td>';
echo '</tr>';
echo '</table>';


//echo 'EL BALANCE ES: ' . $sumatorio;
//echo "<br>" . PHP_EOL;

function compara_resultados($goleslocal, $golesvisitante, $pronostico)
{
	$val;
	if($goleslocal>$golesvisitante)
		$val = "1";
	else if($goleslocal==$golesvisitante)
		$val = "X";
	else
		$val = "2";

	if($val == $pronostico)
		$retorno = "ACIERTO";
	else
		$retorno = "ERROR";
	return $retorno;
}


?>