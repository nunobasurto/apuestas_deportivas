<?php
//Tomemos el Betis como ejemplo y calculemos para su partido de la jornada 12 contra Las Palmas.
$id_equipo = 4;
$id_equipo2 = 10;

$local = db_query('SELECT f.id_partido FROM {fecha_jornada} f WHERE f.equipo_local = :id OR f.equipo_visitante = :id', array(':id' => $id_equipo))->fetchField();
$visitante = db_query('SELECT f.id_partido FROM {fecha_jornada} f WHERE f.equipo_local = :id OR f.equipo_visitante = :id', array(':id' => $id_equipo2))->fetchField();
echo 'El tipo de local es: ' . $local;
echo "<br>" . PHP_EOL;
echo "<br>" . PHP_EOL;
echo 'El tipo de vis es: ' . $visitante;
echo "<br>" . PHP_EOL;
echo "<br>" . PHP_EOL;

?>