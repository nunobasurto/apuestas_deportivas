<?php
define('DRUPAL_ROOT', getcwd());
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

$tiempo = getdate();
$currentTyme= $tiempo["year"] . '-' . $tiempo["mon"] . '-' . $tiempo["mday"] . ' 00:00:00';

$get = $_GET["jornada"];

if ($get == null)
	$jornada  = db_query('SELECT f.jornada FROM {fecha_jornada} f WHERE f.fecha_despues <= :ff ORDER BY f.fecha_despues desc', array(':ff' => $currentTyme))->fetchField();
else
	$jornada=$get;

$jor_aux=$jornada*100;
$arrayCasas = array(' ','BET365','Marca', 'BWIN','888Bet');
for ($i=$jor_aux+1; $i <=$jor_aux+10; $i++) { 
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
			$cu=db_select('cuotas','c')
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
$sumatorio = 0;

echo '<table class="table table-hover table-responsive">';
echo  '<tr>';
echo     '<td colspan=7><h3>Jornada '.$jornada.'</h3></td>';
echo   '</tr>';
echo  '<tr align = "right">
    <td>Partido</td>
    <td>Resultado</td>
    <td><CENTER>Pronóstico</CENTER></td>
    <td><img src="images/bet365_2.png" width="60" height="20" alt="BET365"></td>
    <td><img src="images/marcaapuestas.jpeg" width="60" height="20" alt="Marca"></td>
    <td><img src="images/bwin.jpg" width="60" height="20" alt="BWIN"></td>
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

    if($pronostico=="1")
		$pronos = 0;
	else if($pronostico=="X")
		$pronos = 1;
	else
		$pronos = 2;
	$resolucion = compara_resultados($goleslocal, $golesvisitante, $pronostico);

    echo '<tr align="right">';
    echo '<td>'.$equipolocal.'-'.$equipovisitante.'</td>';
    echo '<td>'.$goleslocal.'-'.$golesvisitante.'</td>';
    if($resolucion=="ACIERTO")
    	echo '<td><CENTER><b>'.$pronostico.'<b><CENTER</td>';
    else
    	echo '<td><CENTER>'.$pronostico.'<CENTER</td>';
	
	foreach ($partido as $casa=>$casa_apuestas){
		if ($casa!=null){
			foreach ($casa_apuestas as $x12=>$cuota) {
				if($x12==$pronos AND $resolucion=="ACIERTO"){
					echo '<td>'.number_format($cuota,2).'</td>';
					$contadores[$casa-($id*10)]+=($cuota-1);

				}else if($x12==$pronos AND $resolucion=="ERROR"){
					echo '<td>-1.00</td>';
					$contadores[$casa-($id*10)]-=1;
				}
			}
		}
		else
			echo '<td>'.number_format(0,2).'</td>';
	}
	echo '</tr>';
}
$balance_general = db_query('SELECT bg.BET365 FROM {balance_general} bg WHERE bg.jornada = :jornada', array(':jornada' => $jornada))->fetchfield();
if ($balance_general == null){
	$ins=db_insert('balance_general')
			->fields(array(
			'jornada' => $jornada,
			'BET365'=>($contadores[1]),
			'Marca'=>($contadores[2]),
			'BWIN'=>($contadores[3]),
			'888Bet'=>($contadores[4]),
			))
			->execute();
}
echo '<tr align = "right">';
echo '<td colspan="3"><CENTER><b>Balance</b></CENTER></td>';
if($contadores[1]>0)
	echo '<td class="success"><b>'.number_format($contadores[1],2).'</b></td>';
else
	echo '<td class="warning"><b>'.number_format($contadores[1],2).'</b></td>';
if($contadores[2]>0)
	echo '<td class="success"><b>'.number_format($contadores[2],2).'</b></td>';
else
	echo '<td class="warning"><b>'.number_format($contadores[2],2).'</b></td>';
if($contadores[3]>0)
	echo '<td class="success"><b>'.number_format($contadores[3],2).'</b></td>';
else
	echo '<td class="warning"><b>'.number_format($contadores[3],2).'</b></td>';
if($contadores[4]>0)
	echo '<td class="success"><b>'.number_format($contadores[4],2).'</b></td>';
else
	echo '<td class="warning"><b>'.number_format($contadores[4],2).'</b></td>';
echo '</tr>';
echo '</table>';


/*
* Esta función compara los goles y el pronóstico, devolviendo acierto o error.
*/
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