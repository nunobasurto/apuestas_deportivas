<?php
define('DRUPAL_ROOT', getcwd());
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
//Obetenemos la jornada para que el demonio ejecute el algoritmo.
/*
$tiempo = getdate();
$currentTyme= $tiempo["year"] . '-' . $tiempo["mon"] . '-' . $tiempo["mday"] . ' 00:00:00';
$jornada  = db_query('SELECT f.jornada FROM {fecha_jornada} f WHERE f.fecha_despues = :ff', array(':ff' => $currentTyme))->fetchField();
*/
$jornada = 13;
$jornada = $jornada*100;

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
foreach($mostrar as $id=>$partido){
	echo "<br>" . PHP_EOL;
	$equipolocal  = db_query('SELECT f.equipo_local FROM {fecha_jornada} f WHERE f.id_partido = :id ', array(':id' => $id))->fetchField();
    $equipovisitante  = db_query('SELECT f.equipo_visitante FROM {fecha_jornada} f WHERE f.id_partido = :id', array(':id'=>$id))->fetchField();

    $equipolocal = db_query('SELECT e.nombreCompleto FROM {equipos} e WHERE e.id_equipo = :id', array(':id'=>$equipolocal))->fetchfield();
    $equipovisitante = db_query('SELECT e.nombreCompleto FROM {equipos} e WHERE e.id_equipo = :id', array(':id'=>$equipovisitante))->fetchfield();

    $goleslocal = db_query('SELECT p.goles_local FROM {partidos} p WHERE p.id_partido = :id', array(':id'=>$id))->fetchfield();
    $golesvisitante = db_query('SELECT p.goles_visitante FROM {partidos} p WHERE p.id_partido = :id', array(':id'=>$id))->fetchfield();

    $pronostico = db_query('SELECT p.pronostico_estimado FROM {pronosticos} p WHERE p.id_partido = :id', array(':id'=>$id))->fetchfield();

	echo $equipolocal . ' ' . $goleslocal .' vs ' . $golesvisitante . ' ' . $equipovisitante;
	echo ' Pronostico: ' . $pronostico;
	$resolucion = compara_resultados($goleslocal, $golesvisitante, $pronostico);
	echo ' ' . $resolucion;
	echo "<br>" . PHP_EOL;
	if ($resolucion=="ACIERTO"){
		$mejor = extraer_mejor_cuota($pronostico, $partido);
		echo 'Mejor: ' . $mejor;
		echo "<br>" . PHP_EOL;
		$sumatorio += $mejor;
	}
	else
		$sumatorio -= 1;
	
	foreach ($partido as $casa_apuestas){
		foreach ($casa_apuestas as $casa=>$cuota) {

			echo $cuota . ' ';
		}
		echo "<br>" . PHP_EOL;
	}
}
echo "<br>" . PHP_EOL;
echo 'EL BALANCE ES: ' . $sumatorio;
echo "<br>" . PHP_EOL;

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

function extraer_mejor_cuota($pronostico, $cuotas){
	if ($pronostico == "1")
		$columna=0;
	else if($pronostico == "X")
		$columna=1;
	else
		$columna=2;
	$max=0;
	foreach ($cuotas as $cuota) {
		if ($cuota[$columna]>$max){
			$max = $cuota[$columna];
		}
	}
	return $max;
}

?>