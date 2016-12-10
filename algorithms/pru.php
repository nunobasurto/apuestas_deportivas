<?php

$tiempo = getdate();
$currentTyme= $tiempo["year"] . '-' . $tiempo["mon"] . '-' . $tiempo["mday"] . ' 00:00:00';
echo $currentTyme;


$fechaFiciticia  = "2016-12-09 00:00:00";

if ($fechaFiciticia == $fecha){
	echo "eSto";
	$jornada  = db_query('SELECT f.jornada FROM {fecha_jornada} f WHERE f.fecha_antes = :ff', array(':ff' => $fechaFiciticia))->fetchField();
	echo 'La jornada es: ' . $jornada;
}


?>