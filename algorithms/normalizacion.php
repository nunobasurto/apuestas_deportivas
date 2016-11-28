<?php

define('DRUPAL_ROOT', getcwd());
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

$jornada = 11;
$select = db_select('clasificacion_jornada','cf');
		$select->fields('cf')
			->condition('cf.jornada' , $jornada, '=');
		$result = $select->execute();

$columnas = array('posicion', 'puntos', 'jugados', 'victorias', 'empates', 'derrotas', 'favor', 'contra', 'local_visitante', 'puntosUlt5', 'puntosUlt4', 'puntosUlt3', 'puntosUlt2', 'puntosUlt1', 'golesFav5', 'golesFav4', 'golesFav3', 'golesFav2', 'golesFav1', 'golesCont5', 'golesCont4', 'golesCont3', 'golesCont2', 'golesCont1');

foreach ($columnas as $columna) {
	echo "<br>" . PHP_EOL;
	echo 'Columna: ' . $columna;
	$suma = 0;
	foreach ($result as $fila) {
		echo $fila->$columna . ' ';
		$suma += $fila->$columna;
	}
	echo 'Suma: ' . $suma;
	$result = $select->execute();

	foreach ($result as $fila) {
		echo 'El id equipo es: ' . $fila->id_equipo;
		echo "<br>" . PHP_EOL;
		echo 'La jornada es ' . $fila->jornada;
		echo "<br>" . PHP_EOL;
		echo 'El valor normalizado: ' . bcdiv(($fila->$columna),$suma, 3);
		echo "<br>" . PHP_EOL;
		$update = db_update('clasificacion_jornada')
			->fields(array(
				$columna => bcdiv(($fila->$columna),$suma, 3),
				))
			->condition('id_equipo', $fila->id_equipo ,'=')
			->condition('jornada', $jornada, '=')
			->execute();
	}
	$result = $select->execute();
}

/*
foreach ($result as $fila) {
	foreach ($columnas as $columna) {
		echo $fila->$columna . ' ';
	}
}
*/

?>
