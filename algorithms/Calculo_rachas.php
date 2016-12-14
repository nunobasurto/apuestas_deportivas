#!/usr/bin/php

<?php
define('DRUPAL_ROOT', getcwd());
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

$tiempo = getdate();
$currentTyme= $tiempo["year"] . '-' . $tiempo["mon"] . '-' . $tiempo["mday"] . ' 00:00:00';
$jornada  = db_query('SELECT f.jornada FROM {fecha_jornada} f WHERE f.fecha_despues <= :ff ORDER BY f.fecha_despues desc', array(':ff' => $currentTyme))->fetchField(); 
$jorAux=$jornada;
$jornada = $jornada*100;
for ($j=$jornada+1; $j <=$jornada+10 ; $j++) { 
	//De esta forma ya tenemos cada uno de los id correspondientes a cada partido de la jornada.
	//Ahora debemos saber cuales son los equipos que van a disputar dicho partido.
	$equipolocal  = db_query('SELECT f.equipo_local FROM {fecha_jornada} f WHERE f.id_partido = :id ', array(':id' => $j))->fetchField();
    $equipovisitante  = db_query('SELECT f.equipo_visitante FROM {fecha_jornada} f WHERE f.id_partido = :id', array(':id'=>$j))->fetchField();
   	/*echo 'El equipolocal es: ' . $equipolocal;
   	echo "<br>" . PHP_EOL;
   	echo 'El equipo visitante es: ' . $equipovisitante;
   	echo "<br>" . PHP_EOL;*/
	//calculamos las instancias para cada partido.
	$partido = array();
	array_push($partido, $equipolocal);
	array_push($partido, $equipovisitante);
	//Instancias para cada equipo.
	for ($i=0; $i < sizeof($partido); $i++) { 
		//echo "Equipo es: ", $partido[$i];

		$local = db_select('fecha_jornada','f');
		$local->join('partidos', 'p', 'f.id_partido = p.id_partido');
		$local->fields('p',array('goles_local', 'goles_visitante'))
			->fields('f', array('jornada'))
			->condition('f.equipo_local', $partido[$i], '=')
			->condition('f.jornada' , $jorAux, '<=');
		$result = $local->execute();

		$puntos = array();
		$goles_favor = array();
		$goles_contra = array();
		$jornadas = array();
		foreach ($result as $key) {
			$goles_favor [$key->jornada] = $key->goles_local;
			$goles_contra [$key->jornada] = $key->goles_visitante;
			if ($key->goles_local>$key->goles_visitante)
				$puntos [$key->jornada] = 3;
			else if ($key->goles_local==$key->goles_visitante)
				$puntos [$key->jornada] = 1;
			else
				$puntos [$key->jornada] = 0;
		}
		//Separamos en dos para obtener por separado los partidos como local y visitante.
		$visitante = db_select('fecha_jornada','f');
		$visitante->join('partidos', 'p', 'f.id_partido = p.id_partido');
		$visitante->fields('p',array('goles_local', 'goles_visitante'))
			->fields('f', array('jornada'))
			->condition('f.equipo_visitante', $partido[$i], '=')
			->condition('f.jornada' , $jorAux, '<=');
		$result = $visitante->execute();

		foreach ($result as $key) {
			$goles_favor [$key->jornada] = $key->goles_visitante;
			$goles_contra [$key->jornada] = $key->goles_local;
			if ($key->goles_local<$key->goles_visitante)
				$puntos [$key->jornada] = 3;
			else if ($key->goles_local==$key->goles_visitante)
				$puntos [$key->jornada] = 1;
			else
				$puntos [$key->jornada] = 0;
		}
		//Ordenamos por claver de mayor a menor asi accedemos mas facilmente a las ultimas jornadas.
		krsort($puntos);
		krsort($goles_favor);
		krsort($goles_contra);
		//Insertamos en la tabla equipo las rachas.
		//el equipo en cuestion es $partido[$i];
		$update = db_update('clasificacion_jornada')
			->fields(array(
				'local_visitante' => $i,
				'puntosUlt5' => array_sum(array_slice($puntos, 0,5)),
				'puntosUlt4' => array_sum(array_slice($puntos, 0,4)),
				'puntosUlt3'=> array_sum(array_slice($puntos, 0,3)),
				'puntosUlt2'=> array_sum(array_slice($puntos, 0,2)),
				'puntosUlt1'=> array_sum(array_slice($puntos, 0,1)),
				'golesFav5'=> array_sum(array_slice($goles_favor, 0,5)),
				'golesFav4'=> array_sum(array_slice($goles_favor, 0,4)),
				'golesFav3'=> array_sum(array_slice($goles_favor, 0,3)),
				'golesFav2'=> array_sum(array_slice($goles_favor, 0,2)),
				'golesFav1'=> array_sum(array_slice($goles_favor, 0,1)),
				'golesCont5'=> array_sum(array_slice($goles_contra, 0,5)),
				'golesCont4'=> array_sum(array_slice($goles_contra, 0,4)),
				'golesCont3'=> array_sum(array_slice($goles_contra, 0,3)),
				'golesCont2'=> array_sum(array_slice($goles_contra, 0,2)),
				'golesCont1'=> array_sum(array_slice($goles_contra, 0,1)),
				))
			->condition('id_equipo', $partido[$i] ,'=')
			->condition('jornada', $jorAux, '=')
			->execute();

		/*
		print_r($jornadas);
		echo "<br>" . PHP_EOL;
		echo 'Puntos: ';
		print_r($puntos);
		echo "<br>" . PHP_EOL;
		echo 'Goles a favor: ';
		print_r($goles_favor);
		echo "<br>" . PHP_EOL;
		echo 'Goles en contra: ';
		print_r($goles_contra);
		echo "<br>" . PHP_EOL;
		echo "<br>" . PHP_EOL;
		*/
	}
}

?>