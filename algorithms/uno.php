<?php
/*    $id = 100 + $i;
    //Empiezan los Selects para extraer informacion del partido.
    $jornada = db_query('SELECT f.jornada FROM {fecha_jornada} f WHERE f.id_partido = :id', array(':id' => $id))->fetchField();
    $equipolocal  = db_query('SELECT e.nombre FROM {fecha_jornada} f, {equipos} e WHERE f.id_partido = :id AND f.equipo_local = e.id_equipo', array(':id' => $id))->fetchField();
    $equipovisitante  = db_query('SELECT e.nombre FROM {fecha_jornada} f, {equipos} e WHERE f.id_partido = :id AND f.equipo_visitante = e.id_equipo', array(':id'=>$id))->fetchField();
    */
    //Se establece la URL donde realizar Scrapping
    //$url = 'http://www.marca.com/estadisticas/futbol/primera/2016_17/jornada_'. $jornada .'/'.$equipolocal.'_'.$equipovisitante;
	$url = 'http://www.resultados-futbol.com/partido/Ud-Palmas/Granada';
    $source = file_get_contents($url);
 	libxml_use_internal_errors(true);
 	libxml_clear_errors();
 	$html = new DOMDocument();
 	$html->loadHTML($source);
 	$xpath=new DOMXpath($html);
 	$local = array();
 	$visitante = array();
 	$trs = $html->getElementsByTagName("tr");
 	$ul = $html->getElementsByTagName("ul");

 	$arbitro = $html->getElementsByTagName("span")->item(45)->nodeValue;
 	echo ' arbitro ' . $arbitro;
 	$flag = true;
 	foreach($trs as $tr){
 		$nameStat = $tr->getElementsByTagName("h6")->item(0)->nodeValue;
 		if(!empty($nameStat) AND $nameStat!='Lesiones'){
 			echo 'El nombre de la estadistica es:' . $nameStat;
 			$stat = $tr->getElementsByTagName("td")->item(0)->nodeValue;
	 		$stat2 = $tr->getElementsByTagName("td")->item(2)->nodeValue;
	 		array_push($local, $stat);
	 		array_push($visitante, $stat2);
		}
	}
	echo "<br>" . PHP_EOL;
	print_r($local);
	echo "<br>" . PHP_EOL;
	print_r($visitante);
	/*
	$insert = db_insert('partidos')
	->fields(array(
	'id_partido' => $id,
	'arbitro' => $arbitro,
	'posesion_local' =>$local[0],
	'posesion_visitante' => $visitante[0],
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
	'asistencias_local' => $local[10],
	'asistencias_visitante' => $visitante[10],
	'sustituciones_local' => $local[11],
	'sustituciones_visitante' => $visitante[11],
	'faltas_local' => $local[12],
	'faltas_visitante' => $visitante[12],
	))
	->execute();
	} */
?>