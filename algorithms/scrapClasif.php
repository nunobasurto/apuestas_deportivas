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
		$posicion = (int)$tr->getElementsByTagName("th")->item(0)->nodeValue;
		
		if($posicion==1){
			$flag=True;
		}
		$equipo = $tr->getElementsByTagName("td")->item(0)->nodeValue;
		if($flag==True AND !empty($equipo)){
			/*echo 'Pos ' . $posicion . ' ' . utf8_decode($equipo);
			echo "<br>" . PHP_EOL;*/
			$puntos = (int)$tr->getElementsByTagName("td")->item(1)->nodeValue;
			$jugados = (int)$tr->getElementsByTagName("td")->item(2)->nodeValue;
			$victorias =(int) $tr->getElementsByTagName("td")->item(3)->nodeValue;
			$empates = (int)$tr->getElementsByTagName("td")->item(4)->nodeValue;
			$derrotas = (int)$tr->getElementsByTagName("td")->item(5)->nodeValue;
			$favor = (int)$tr->getElementsByTagName("td")->item(6)->nodeValue;
			$contra = (int)$tr->getElementsByTagName("td")->item(7)->nodeValue;
			
			$equipo = trim($equipo);
			echo $posicion . ' ' . $equipo . ' ' . $puntos . ' ' . $jugados . ' ' . $victorias . ' ' . $empates . ' ' . $derrotas . ' ' . $favor . ' ' . $contra;
			echo "<br>" . PHP_EOL;

			/*$update = db_update('equipos')
			->fields(array(
				'posicion' => $posicion,
				'puntos' => $puntos,
				'jugados'=> $jugados,
				'victorias'=> $victorias,
				'empates'=> $empates,
				'derrotas'=> $derrotas,
				'favor'=> $favor,
				'contra'=> $contra,
				))
			->condition('nombreCompleto', $equipo ,'=')
			->execute();*/
			
			/*$args = array(':posicion' => $posicion, ':equipo' => $equipo);
			$art = db_query('UPDATE {equipos} SET posicion=:posicion  WHERE nombreCompleto = :equipo', 
				array(':posicion'=>$posicion, ':equipo'=>$equipo))->fetchField();*/
		}
		if($posicion == 20){
			$flag=False;
			break;
		}
		
	}
	

?>
