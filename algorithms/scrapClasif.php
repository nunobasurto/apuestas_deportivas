<?php
	$url = 'http://www.resultados-futbol.com/primera/grupo1/jornada11';
	$source = file_get_contents($url);
 	libxml_use_internal_errors(true);
 	libxml_clear_errors();
 	$html = new DOMDocument();
 	$html->loadHTML($source);
 	$xpath=new DOMXpath($html);
 	$trs = $html->getElementsByTagName("tr");
 	$flag=False;
	foreach($trs as $tr){
		$posicion = $tr->getElementsByTagName("th")->item(0)->nodeValue;
		
		if($posicion=='1'){
			$flag=True;
		}
		$equipo = $tr->getElementsByTagName("td")->item(0)->nodeValue;
		if($flag==True AND !empty($equipo)){
			echo 'Pos ' . $posicion . ' ' . utf8_decode($equipo);
			echo "<br>" . PHP_EOL;
		}
		if($posicion == '20'){
			$flag=False;
			break;
		}
		
	}
	

?>
