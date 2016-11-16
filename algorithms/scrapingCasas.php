<?php
	$url = 'http://www.resultados-futbol.com/primera/grupo1/jornada12';
	$source = file_get_contents($url);
 	libxml_use_internal_errors(true);
 	libxml_clear_errors();
 	$html = new DOMDocument();
 	$html->loadHTML($source);
 	$xpath=new DOMXpath($html);
 	$trs = $html->getElementsByTagName("tr");
 	$flag=true;

 	foreach($trs as $tr){
 		//echo 'Baaaaaa';
 		$nameStat = $tr->getElementsByTagName("div"); 		
 		$eLocal = $tr->getElementsByTagName("td")->item(2)->nodeValue;
 		$eVisitante = $tr->getElementsByTagName("td")->item(4)->nodeValue;
 		$flag1 = True;
 		$flag2 = True;
 		$flag3 = True;
 		$flag4 = True;
 		$id_partido;
 		$id_1;
 		$id_2;
 		$id_3;
 		$id_4;

 		if(!empty($eLocal)AND!empty(trim($eVisitante))AND strlen($eVisitante)>3 AND $eVisitante !='Posición'){
 			$eLocal = trim($eLocal);
 			$eVisitante = trim($eVisitante);

			$id_local = db_query('SELECT e.id_equipo FROM {equipos} e WHERE e.nombreCompleto = :eLocal', array(':eLocal' => $eLocal))->fetchField();
			$id_visitante = db_query('SELECT e.id_equipo FROM {equipos} e WHERE e.nombreCompleto = :eVisitante', array(':eVisitante' => $eVisitante))->fetchField();
			$id_partido = db_query('SELECT f.id_partido FROM {fecha_jornada} f WHERE f.equipo_local = :id_local AND f.equipo_visitante = :id_visitante', array(':id_local' => $id_local, ':id_visitante' =>$id_visitante))->fetchField();

			echo 'El id local es: ' . $id_local . ' y el visitante: ' . $id_visitante;
			echo "<br>" . PHP_EOL;
			echo 'El id del partido es: ' . $id_partido;
			echo "<br>" . PHP_EOL;

 			echo 'Local ' . $eLocal , ' Visitante ' . $eVisitante;
 			echo "<br>" . PHP_EOL;

 			$id_par =  "$id_partido";
 			$id_1 = (int)($id_par . '1');
 			$id_2 = (int)($id_par . '2');
 			$id_3 = (int)($id_par . '3');
 			$id_4 = (int)($id_par . '4');

 			$insert2 = db_insert('apuestas')
				->fields(array(
				'id_partido' => $id_partido,
				))
				->execute();
 		}
		foreach($nameStat as $stat){
			$var=$stat->getElementsByTagName("a")->item(0)->nodeValue;
			if ($var =='Bet365' AND $flag1 == True){
				$Local=substr($stat->getElementsByTagName("a")->item(1)->nodeValue,2,4);
				$Empate=substr($stat->getElementsByTagName("a")->item(2)->nodeValue,2,4);
				$Visitante=substr($stat->getElementsByTagName("a")->item(3)->nodeValue,2,4);
				$Local = str_replace(',', '.' , $Local);
				$Empate = str_replace(',', '.' , $Empate);
				$Visitante = str_replace(',', '.' , $Visitante);
				echo 'BET365: Los valores de 1 X 2 son '. $Local .' '. $Empate .' '. $Visitante;
				echo "<br>" . PHP_EOL;
				$flag1 = False;
				$insert = db_insert('Cuotas')
					->fields(array(
					'id_cuota' => $id_1,
					'Local' => (float)$Local,
					'Empate' => (float)$Empate,
					'Visitante' => (float)$Visitante,
				))
				->execute();

				$insert2 = db_update('apuestas')
				->fields(array(
				'BET365' => $id_1,
				))
				->condition('id_partido', $id_partido, '=')
				->execute();
			}
			elseif ($var =='Marca Apuestas' AND $flag2 ==True){
				$Local=substr($stat->getElementsByTagName("a")->item(1)->nodeValue,2,4);
				$Empate=substr($stat->getElementsByTagName("a")->item(2)->nodeValue,2,4);
				$Visitante=substr($stat->getElementsByTagName("a")->item(3)->nodeValue,2,4);
				$Local = str_replace(',', '.' , $Local);
				$Empate = str_replace(',', '.' , $Empate);
				$Visitante = str_replace(',', '.' , $Visitante);
				echo 'MARCA: Los valores de 1 X 2 son '. $Local .' '. $Empate .' '. $Visitante;
				echo "<br>" . PHP_EOL;
				$flag2 = False;
				$insert = db_insert('Cuotas')
					->fields(array(
					'id_cuota' => $id_2,
					'Local' => (float)$Local,
					'Empate' => (float)$Empate,
					'Visitante' => (float)$Visitante,
				))
				->execute();

				$insert2 = db_update('apuestas')
				->fields(array(
				'Marca' => $id_2,
				))
				->condition('id_partido', $id_partido, '=')
				->execute();
			}
			elseif ($var =='bwin' AND $flag3 == true){
				$Local=substr($stat->getElementsByTagName("a")->item(1)->nodeValue,2,4);
				$Empate=substr($stat->getElementsByTagName("a")->item(2)->nodeValue,2,4);
				$Visitante=substr($stat->getElementsByTagName("a")->item(3)->nodeValue,2,4);
				$Local = str_replace(',', '.' , $Local);
				$Empate = str_replace(',', '.' , $Empate);
				$Visitante = str_replace(',', '.' , $Visitante);
				echo 'BWIN: Los valores de 1 X 2 son '. $Local .' '. $Empate .' '. $Visitante;
				echo "<br>" . PHP_EOL;
				$flag3 = false;
				$insert = db_insert('Cuotas')
					->fields(array(
					'id_cuota' => $id_3,
					'Local' => (float)$Local,
					'Empate' => (float)$Empate,
					'Visitante' => (float)$Visitante,
				))
				->execute();

				$insert2 = db_update('apuestas')
				->fields(array(
				'BWIN' => $id_3,
				))
				->condition('id_partido', $id_partido, '=')
				->execute();
			}
			elseif ($var =='888 sport' AND $flag4 == true){
				$Local=substr($stat->getElementsByTagName("a")->item(1)->nodeValue,2,4);
				$Empate=substr($stat->getElementsByTagName("a")->item(2)->nodeValue,2,4);
				$Visitante=substr($stat->getElementsByTagName("a")->item(3)->nodeValue,2,4);
				$Local = str_replace(',', '.' , $Local);
				$Empate = str_replace(',', '.' , $Empate);
				$Visitante = str_replace(',', '.' , $Visitante);
				echo '888: Los valores de 1 X 2 son '. $Local .' '. $Empate .' '. $Visitante;
				echo "<br>" . PHP_EOL;
				$flag4 = false;
				$insert = db_insert('Cuotas')
					->fields(array(
					'id_cuota' => $id_4,
					'Local' => (float)$Local,
					'Empate' => (float)$Empate,
					'Visitante' => (float)$Visitante,
				))
				->execute();

				$insert2 = db_update('apuestas')
				->fields(array(
				'888Bet' => $id_4,
				))
				->condition('id_partido', $id_partido, '=')
				->execute();
				
			}
		}
	}
?>
