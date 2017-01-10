#!/usr/bin/php
<?php
define('DRUPAL_ROOT', getcwd());
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);
$tiempo = getdate();
$currentTyme= $tiempo["year"] . '-' . $tiempo["mon"] . '-' . $tiempo["mday"] . ' 00:00:00';
$jornada  = db_query('SELECT f.jornada FROM {fecha_jornada} f WHERE f.fecha_despues <= :ff ORDER BY f.fecha_despues desc', array(':ff' => $currentTyme))->fetchField(); 
$url = 'http://www.resultados-futbol.com/primera/grupo1/jornada' . "$jornada";
$source = file_get_contents($url);
libxml_use_internal_errors(true);
libxml_clear_errors();
$html = new DOMDocument();
$html->loadHTML($source);
$trs = $html->getElementsByTagName("tr"); 	$flag=False;
foreach($trs as $tr){
	$posicion = (int)$tr->getElementsByTagName("th")->item(0)->nodeValue;	
	if($posicion==1){
		$flag=True;
	}
	$equipo = $tr->getElementsByTagName("td")->item(0)->nodeValue;
	if($flag==True AND !empty($equipo)){
		$puntos = (int)$tr->getElementsByTagName("td")->item(1)->nodeValue;
		$jugados = (int)$tr->getElementsByTagName("td")->item(2)->nodeValue;
		$victorias =(int) $tr->getElementsByTagName("td")->item(3)->nodeValue;
		$empates = (int)$tr->getElementsByTagName("td")->item(4)->nodeValue;
		$derrotas = (int)$tr->getElementsByTagName("td")->item(5)->nodeValue;
		$favor = (int)$tr->getElementsByTagName("td")->item(6)->nodeValue;
		$contra = (int)$tr->getElementsByTagName("td")->item(7)->nodeValue;
			
		$equipo = trim($equipo);
		$id_equipo = db_query('SELECT e.id_equipo FROM {equipos} e WHERE e.nombreCompleto = :equipo', array(':equipo' => $equipo))->fetchField();
		$control  = db_query('SELECT cj.posicion FROM {clasificacion_jornada} cj WHERE cj.id_equipo = :id AND cj.jornada = :jornada', array(':id' => $id_equipo), ':jornada' => $jornada)->fetchField();
		//Control para que no lo vuelva a ejecutar si los datos ya han sido almacenados.
		if($control==null){
			$insert = db_insert('clasificacion_jornada')
			->fields(array(
				'id_equipo' => $id_equipo,
				'jornada' => $jornada,
				'posicion' => $posicion,
				'puntos' => $puntos,
				'jugados'=> $jugados,
				'victorias'=> $victorias,
				'empates'=> $empates,
				'derrotas'=> $derrotas,
				'favor'=> $favor,
				'contra'=> $contra,
				))	
			->execute();
		}
	}
		if($posicion == 20){
			$flag=False;
			break;
		}	
	}	
?>
