<?php
define('DRUPAL_ROOT', getcwd());
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
//Muestra el pronóstico de la siguiente jornada.

$tiempo = getdate();
$currentTyme= $tiempo["year"] . '-' . $tiempo["mon"] . '-' . $tiempo["mday"] . ' 00:00:00';
$jornada  = db_query('SELECT f.jornada FROM {fecha_jornada} f WHERE f.fecha_despues > :ff ORDER BY f.fecha_despues asc', array(':ff' => $currentTyme))->fetchField(); 

echo "Jornada " . $jornada;

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

foreach($mostrar as $id=>$partido){
	echo "<br>" . PHP_EOL;
	$equipolocal  = db_query('SELECT f.equipo_local FROM {fecha_jornada} f WHERE f.id_partido = :id ', array(':id' => $id))->fetchField();
    $equipovisitante  = db_query('SELECT f.equipo_visitante FROM {fecha_jornada} f WHERE f.id_partido = :id', array(':id'=>$id))->fetchField();

    $equipolocal = db_query('SELECT e.nombreCompleto FROM {equipos} e WHERE e.id_equipo = :id', array(':id'=>$equipolocal))->fetchfield();
    $equipovisitante = db_query('SELECT e.nombreCompleto FROM {equipos} e WHERE e.id_equipo = :id', array(':id'=>$equipovisitante))->fetchfield();
	$pronostico = db_query('SELECT p.pronostico_estimado FROM {pronosticos} p WHERE p.id_partido = :id', array(':id'=>$id))->fetchfield();

	echo $equipolocal . ' vs ' . $equipovisitante;
	echo ' Pronostico: ' . $pronostico . "<br>";
	$mejor = extraer_mejor_cuota($pronostico, $partido);
	echo 'Mejor: ' . $mejor . "<br>";
	if($pronostico=="1")
		$pronos = 0;
	else if($pronostico=="x")
		$pronos = 1;
	else
		$pronos = 2;

	foreach ($partido as $casa=>$casa_apuestas){
		echo $arrayCasas[$casa-($id*10)];
		foreach ($casa_apuestas as $casa=>$cuota) {
			if($casa==$pronos)
				echo "\t" . $cuota . ' ';
		}
		echo "<br>" . PHP_EOL;
	}

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