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

print_r($result);
foreach ($columnas as $columna) {
	foreach ($result as $value) {
		echo $value->$columna . '  ';
		echo $value->golesCont2 . ' ';
		echo "<br>" . PHP_EOL;
	}
}

?>
