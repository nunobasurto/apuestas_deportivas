<?php

define('DRUPAL_ROOT', getcwd());
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);


for($k =1400; $k<=1400; $k+=100){
for ($i = 1; $i <= 10; $i++) {
    $id = $k + $i;
    //Empiezan los Selects para extraer informacion del partido.
    $jornada = db_query('SELECT f.jornada FROM {fecha_jornada} f WHERE f.id_partido = :id', array(':id' => $id))->fetchField();
    $equipolocal  = db_query('SELECT e.nombre FROM {fecha_jornada} f, {equipos} e WHERE f.id_partido = :id AND f.equipo_local = e.id_equipo', array(':id' => $id))->fetchField();
    $equipovisitante  = db_query('SELECT e.nombre FROM {fecha_jornada} f, {equipos} e WHERE f.id_partido = :id AND f.equipo_visitante = e.id_equipo', array(':id'=>$id))->fetchField();
    
    $local= array();
    $visitante = array();
    for ($j=0; $j<=15; $j++){
        $local[$j]=0;
        $visitante[$j] = 0;
    }
	$url = 'http://www.resultados-futbol.com/partido/'. $equipolocal .'/'.$equipovisitante;
    $source = file_get_contents($url);
 	libxml_use_internal_errors(true);
 	libxml_clear_errors();
 	$html = new DOMDocument();
 	$html->loadHTML($source);
 	$xpath=new DOMXpath($html);
 	$trs = $html->getElementsByTagName("tr");
 	$ul = $html->getElementsByTagName("ul");

 	$arbitro = $html->getElementsByTagName("span")->item(45)->nodeValue;
 	echo ' arbitro ' . $arbitro;
 	$flag = true;
 	foreach($trs as $tr){
 		$nameStat = $tr->getElementsByTagName("h6")->item(0)->nodeValue;
 		if(!empty($nameStat)) {
 			if ($nameStat == 'Posesión del balón'){
 				$local[0]=$tr->getElementsByTagName("td")->item(0)->nodeValue;
 				$visitante[0]=$tr->getElementsByTagName("td")->item(2)->nodeValue;
 			}
 			if ($nameStat == 'Goles'){
 				$local[1]=$tr->getElementsByTagName("td")->item(0)->nodeValue;
 				$visitante[1]=$tr->getElementsByTagName("td")->item(2)->nodeValue;
 			}
 			if ($nameStat == 'Tiros a puerta'){
 				$local[2]=$tr->getElementsByTagName("td")->item(0)->nodeValue;
 				$visitante[2]=$tr->getElementsByTagName("td")->item(2)->nodeValue;
 			}
 			if ($nameStat == 'Tiros fuera'){
 				$local[3]=$tr->getElementsByTagName("td")->item(0)->nodeValue;
 				$visitante[3]=$tr->getElementsByTagName("td")->item(2)->nodeValue;
 			}
 			if ($nameStat == 'Total tiros'){
 				$local[4]=$tr->getElementsByTagName("td")->item(0)->nodeValue;
 				$visitante[4]=$tr->getElementsByTagName("td")->item(2)->nodeValue;
 			}
 			if ($nameStat == 'Paradas del portero'){
 				$local[5]=$tr->getElementsByTagName("td")->item(0)->nodeValue;
 				$visitante[5]=$tr->getElementsByTagName("td")->item(2)->nodeValue;
 			}
 			if ($nameStat == 'Saques de esquina'){
 				$local[6]=$tr->getElementsByTagName("td")->item(0)->nodeValue;
 				$visitante[6]=$tr->getElementsByTagName("td")->item(2)->nodeValue;
 			}
 			if ($nameStat == 'Fueras de juego'){
 				$local[7]=$tr->getElementsByTagName("td")->item(0)->nodeValue;
 				$visitante[7]=$tr->getElementsByTagName("td")->item(2)->nodeValue;
 			}
 			if ($nameStat == 'Tarjetas Amarillas'){
 				$local[8]=$tr->getElementsByTagName("td")->item(0)->nodeValue;
 				$visitante[8]=$tr->getElementsByTagName("td")->item(2)->nodeValue;
 			}
 			if ($nameStat == 'Tarjetas Rojas'){
 				$local[9]=$tr->getElementsByTagName("td")->item(0)->nodeValue;
 				$visitante[9]=$tr->getElementsByTagName("td")->item(2)->nodeValue;
 			}
 			if ($nameStat == 'Asistencias'){
 				$local[10]=$tr->getElementsByTagName("td")->item(0)->nodeValue;
 				$visitante[10]=$tr->getElementsByTagName("td")->item(2)->nodeValue;
 			}
 			if ($nameStat == 'Tiros al palo'){
 				$local[11]=$tr->getElementsByTagName("td")->item(0)->nodeValue;
 				$visitante[11]=$tr->getElementsByTagName("td")->item(2)->nodeValue;
 			}
 			if ($nameStat == 'Lesiones'){
 				$local[12]=$tr->getElementsByTagName("td")->item(0)->nodeValue;
 				$visitante[12]=$tr->getElementsByTagName("td")->item(2)->nodeValue;
 			}
 			if ($nameStat == 'Sustituciones'){
 				$local[13]=$tr->getElementsByTagName("td")->item(0)->nodeValue;
 				$visitante[13]=$tr->getElementsByTagName("td")->item(2)->nodeValue;
 			}
 			if ($nameStat == 'Faltas'){
 				$local[14]=$tr->getElementsByTagName("td")->item(0)->nodeValue;
 				$visitante[14]=$tr->getElementsByTagName("td")->item(2)->nodeValue;
 			}
		}
	}
	echo "<br>" . PHP_EOL;
	print_r($local);
	echo "<br>" . PHP_EOL;
	print_r($visitante);
	
	$insert = db_insert('partidos')
	->fields(array(
	'id_partido' => $id,
	'posesion_local' =>substr($local[0], 0, 2)/100,
	'posesion_visitante' => substr($visitante[0], 0, 2)/100,
	'goles_local' => $local[1],
	'goles_visitante' => $visitante[1],
	'remates3p_local' => $local[2],
	'remates3p_visitante' => $visitante[2],
	'rematesfuera_local' => $local[3],
	'rematesfuera_visitante' => $visitante[3],
	'remates_local' => $local[4],
	'remates_visitante' => $visitante[4],
	'paradas_local' => $local[5],
	'paradas_visitante' => $visitante[5],
	'corners_local' => $local[6],
	'corners_visitante' => $visitante[6],
	'outsides_local' => $local[7],
	'outsides_visitante' => $visitante[7],
	'amarillas_local' => $local[8],
	'amarillas_visitante' => $visitante[8],
	'rojas_local' => $local[9],
	'rojas_visitante' => $visitante[9],
	'asistencias_local'=> $local[10],
	'asistencias_visitante'=> $visitante[10],
	'palos_local'=> $local[11],
	'palos_visitante'=> $visitante[11],
	'lesiones_local'=> $local[12],
	'lesiones_visitante'=> $visitante[12],
	'sustituciones_local' => $local[13],
	'sustituciones_visitante' => $visitante[13],
	'faltas_local' => $local[14],
	'faltas_visitante' => $visitante[14],
	))
	->execute();
	} 
}
?>