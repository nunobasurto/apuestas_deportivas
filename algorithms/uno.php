<?php
for ($i = 4; $i <= 10; $i++) {
    $id = 100 + $i;
    //Empiezan los Selects para extraer informacion del partido.
    $jornada = db_query('SELECT f.jornada FROM {fecha_jornada} f WHERE f.id_partido = :id', array(':id' => $id))->fetchField();
    $equipolocal  = db_query('SELECT e.nombre FROM {fecha_jornada} f, {equipos} e WHERE f.id_partido = :id AND f.equipo_local = e.id_equipo', array(':id' => $id))->fetchField();
    $equipovisitante  = db_query('SELECT e.nombre FROM {fecha_jornada} f, {equipos} e WHERE f.id_partido = :id AND f.equipo_visitante = e.id_equipo', array(':id'=>$id))->fetchField();
    //Se establece la URL donde realizar Scrapping
	$url = 'http://www.marca.com/estadisticas/futbol/primera/2016_17/jornada_'. $jornada .'/'.$equipolocal.'_'.$equipovisitante;
    $source = file_get_contents($url);
 	libxml_use_internal_errors(true);
 	libxml_clear_errors();
 	$html = new DOMDocument();
 	$html->loadHTML($source);
 	$xpath=new DOMXpath($html);
 	$local = array();
 	$visitante = array();
 	$trs = $html->getElementsByTagName("tr");
 	$h4 = $html->getElementsByTagName("h4");
 	$h5 = $html->getElementsByTagName("h5");

 	$rLocal = $h4->item(0)->nodeValue;
 	$rVisitante = $h4->item(1)->nodeValue;
 	$arbitro = $h5->item(2)->nodeValue;
 	echo $equipolocal . ' ' . $rLocal . ' '. $equipovisitante .' ' . $rVisitante . ' arbitro ' . $arbitro;
 	$flag = true;
 	foreach($trs as $tr){
 		if($flag){
 			$posesion = $tr->getElementsByTagName("td")->item(0)->nodeValue;
 			$posesion2 = $tr->getElementsByTagName("td")->item(2)->nodeValue;
 			if(!empty($posesion)){
 				array_push($local, $posesion);
 				$flag=false;
 			}
 			if(!empty($posesion2))
	 			array_push($visitante, $posesion2);
 		}
 		else
 		{
 			$stat = (integer)$tr->getElementsByTagName("span")->item(0)->nodeValue;
	 		$stat2 = (integer)$tr->getElementsByTagName("span")->item(3)->nodeValue;
                        $fl = false;
	 		if(!empty($stat)){
	 			array_push($local, $stat);
				$fl = true;
 			}if(!empty($stat2)){
	 			array_push($visitante, $stat2);
				$fl = true;
			}
			if($fl and empty($stat))
				array_push($local, $stat);
			if($fl and empty($stat2))
				array_push($visitante, $stat2);
		}
	}
	echo "<br>" . PHP_EOL;
	print_r($local);
	echo "<br>" . PHP_EOL;
	print_r($visitante);
	$insert = db_insert('partidos')
	->fields(array(
	'id_partido' => $id,
	'goles_local' => $rLocal,
	'goles_visitante' => $rVisitante,
	'arbitro' => $arbitro,
	'posesion_local' =>$local[0],
	'posesion_visitante' => $visitante[0],
	'remates_local' => $local[1],
	'remates_visitante' => $visitante[1],
	'remates3p_local' => $local[2],
	'remates3p_visitante' => $visitante[2],
	'corners_local' => $local[3],
	'corners_visitante' => $visitante[3],
	'paradas_local' => $local[4],
	'paradas_visitante' => $visitante[4],
	'perdidas_local' => $local[5],
	'perdidas_visitante' => $visitante[5],
	'recuperaciones_local' => $local[6],
	'recuperaciones_visitante' => $visitante[6],
	'faltas_local' => $local[7],
	'faltas_visitante' => $visitante[7],
	))
	->execute();
}
 
?>