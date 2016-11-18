<?php
//Tomemos el Betis como ejemplo y calculemos para su partido de la jornada 12 contra Las Palmas.
define('DRUPAL_ROOT', getcwd());
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

$id_equipo = 4;
$id_equipo2 = 10;
$jornada = 12;
//De esta forma obtenemos la clave de la jornada para comparar con el id_partido.
$j_aux = $jornada * 100;

echo 'La jornada auxiliar es: ' . $j_aux;

//$local = db_query('SELECT f.id_partido FROM {fecha_jornada} f WHERE f.equipo_local = :id OR f.equipo_visitante = :id', array(':id' => $id_equipo))->fetchAllAssoc('id_partido')->fetchField();
//$visitante = db_query('SELECT f.id_partido FROM {fecha_jornada} f WHERE f.equipo_local = :id OR f.equipo_visitante = :id', array(':id' => $id_equipo2))->fetchField();*/

$local = db_select('fecha_jornada','f')
	->fields('f',array('id_partido'))
	->condition('equipo_local', $id_equipo, '=')
	->execute()
	->fetchAllAssoc('id_partido');

$visitante = db_select('fecha_jornada','f')
	->fields('f',array('id_partido'))
	->condition('equipo_visitante', $id_equipo, '=')
	->execute()
	->fetchAllAssoc('id_partido');

$l = array();
$v = array();
//Obtengo los valores del array y los filtro para obtener úncamente jornadas pasadas.
foreach( $local  as $r){
	if($r->id_partido < $j_aux)
   		array_push($l, $r->id_partido);
}
foreach( $visitante  as $r){
	if($r->id_partido < $j_aux)
   		array_push($v,$r->id_partido);
}

//Vamos a obtener los resutados del equipo como local:
//Lo primero serán los puntos obtenidos en las últimas jornadas ara ellos partiremos de la jornada actual a calcular que en este caso es la 12 y tendremos las rachas de los ultimos 1,2,3,4,5 partidos.
$puntos = array();
for ($i=0; $i < sizeof($l); $i++) { 
	$partidos_local = db_select('partidos' , 'p')
		->fields('p', array('goles_local', 'goles_visitante'))
		->condition('id_partido', $l[$i], '=')
		->execute()
		->fetchAllAssoc('id_partido');
	echo "<br>" . PHP_EOL;
	foreach( $partidos_local  as $r){
		$a =  $r->goles_local;
		$b = $r->goles_visitante;
		if ($a>$b)
			array_push($puntos,3);
		else if ($a==$b)
			array_push($puntos,1);
		else
			array_push($puntos,0);
	}
}
echo "Puntos del Betis : ";
print_r($puntos);
echo "<br>" . PHP_EOL;
echo "<br>" . PHP_EOL;

print_r($partidos_local);


echo "<br>" . PHP_EOL;

print_r($l);
print_r($v);

?>