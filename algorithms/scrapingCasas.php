<?php
	$url = 'http://www.resultados-futbol.com/primera/grupo1/jornada10';
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
 		if(!empty($eLocal)AND!empty($eVisitante)AND strlen($eVisitante)>3 AND $eVisitante !='Posición'){

 			echo 'Local ' . utf8_decode($eLocal) , ' Visitante ' .utf8_decode($eVisitante);
 			echo "<br>" . PHP_EOL;
 		}
		foreach($nameStat as $stat){
			$var=$stat->getElementsByTagName("a")->item(0)->nodeValue;
			if ($var =='Bet365' AND $flag1 == True){
				$Local=substr($stat->getElementsByTagName("a")->item(1)->nodeValue,2,4);
				$Empate=substr($stat->getElementsByTagName("a")->item(2)->nodeValue,2,4);
				$Visitante=substr($stat->getElementsByTagName("a")->item(3)->nodeValue,2,4);
				utf8_decode($Local);
				echo 'BET365: Los valores de 1 X 2 son '. $Local .' '. $Empate .' '. $Visitante;
				echo "<br>" . PHP_EOL;
				$flag1 = False;
			}
			elseif ($var =='Marca Apuestas' AND $flag2 ==True){
				$Local=substr($stat->getElementsByTagName("a")->item(1)->nodeValue,2,4);
				$Empate=substr($stat->getElementsByTagName("a")->item(2)->nodeValue,2,4);
				$Visitante=substr($stat->getElementsByTagName("a")->item(3)->nodeValue,2,4);
				echo 'MARCA: Los valores de 1 X 2 son '. $Local .' '. $Empate .' '. $Visitante;
				echo "<br>" . PHP_EOL;
				$flag2 = False;
			}
			elseif ($var =='bwin' AND $flag3 == true){
				$Local=substr($stat->getElementsByTagName("a")->item(1)->nodeValue,2,4);
				$Empate=substr($stat->getElementsByTagName("a")->item(2)->nodeValue,2,4);
				$Visitante=substr($stat->getElementsByTagName("a")->item(3)->nodeValue,2,4);
				echo 'BWIN: Los valores de 1 X 2 son '. $Local .' '. $Empate .' '. $Visitante;
				echo "<br>" . PHP_EOL;
				$flag3 = false;
			}
			elseif ($var =='888 sport' AND $flag4 == true){
				$Local=substr($stat->getElementsByTagName("a")->item(1)->nodeValue,2,4);
				$Empate=substr($stat->getElementsByTagName("a")->item(2)->nodeValue,2,4);
				$Visitante=substr($stat->getElementsByTagName("a")->item(3)->nodeValue,2,4);
				echo '888: Los valores de 1 X 2 son '. $Local .' '. $Empate .' '. $Visitante;
				echo "<br>" . PHP_EOL;
				$flag4 = false;
			}
		}
	}
?>
