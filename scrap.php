<?php

scrapping();


function scrapping(){
 	$source = file_get_contents('http://www.marca.com/estadisticas/futbol/primera/2016_17/jornada_7/celta_barcelona');
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

 	$rLocal = (integer)$h4->item(0)->nodeValue;
 	$rVisitante = (integer)$h4->item(1)->nodeValue;
 	$arbitro = $h5->item(2)->nodeValue;

 	var_dump($rLocal);
	$hola = is_string($rLocal);

	echo 'Es o no ' . $hola;
	echo "<br>" . PHP_EOL;
 	echo 'Local ' . $rLocal . ' visitante ' . $rVisitante . ' arbitro ' . $arbitro;
 	$flag = true;
 	foreach($trs as $tr){
 //Cada TD  
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
 			$title = (integer)$tr->getElementsByTagName("span")->item(0)->nodeValue;
 		//$value = $tr->getElementsByTagName("td")->item(1)->nodeValue;
	 		$title2 = (integer)$tr->getElementsByTagName("span")->item(3)->nodeValue;
 		//$value2 = $tr->getElementsByTagName("td")->item(3)->nodeValue;
	 		if(!empty($title))
	 			array_push($local, $title);
 			if(!empty($title2))
	 			array_push($visitante, $title2);
		}
	}
	echo "<br>" . PHP_EOL;
	print_r($local);
	echo "<br>" . PHP_EOL;
	print_r($visitante);
	
	//nsert = db_query("INSERT INTO {partidos} (id_partido, goles_local, goles_visitante)
	//ALUES (%d, %d, %d)", 101, $rLocal, $rVisitante);
}

 
?>
