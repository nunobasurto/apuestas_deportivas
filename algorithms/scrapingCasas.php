<?php
$url = 'http://www.resultados-futbol.com/primera/grupo1/jornada10';
    $source = file_get_contents($url);
 	libxml_use_internal_errors(true);
 	libxml_clear_errors();
 	$html = new DOMDocument();
 	$html->loadHTML($source);
 	$xpath=new DOMXpath($html);

 	$trs = $html->getElementsByTagName("tr");

 	foreach($trs as $tr){
 		$nameStat = $tr->getElementsByTagName("div");
 		$flag=true;
 		
 		$eLocal = $tr->getElementsByTagName("td")->item(2)->nodeValue;
 		$eVisitante = $tr->getElementsByTagName("td")->item(4)->nodeValue;
 		echo 'Local ' . $eLocal , ' Visitante ' . $eVisitante;
 		echo "<br>" . PHP_EOL;
		foreach($nameStat as $stat){
			$var=$stat->getElementsByTagName("a")->item(0)->nodeValue;
			if ($var =='Bet365' AND $flag = true){
				$Local=substr($stat->getElementsByTagName("a")->item(1)->nodeValue,2,4);
				$Empate=substr($stat->getElementsByTagName("a")->item(2)->nodeValue,2,4);
				$Visitante=substr($stat->getElementsByTagName("a")->item(3)->nodeValue,2,4);
				utf8_decode($Local);
				echo 'BET365; Los valores de 1 X 2 son '. $Local .' '. $Empate .' '. $Visitante;
				echo "<br>" . PHP_EOL;
				$flag = false;
			}
			if ($var =='Marca Apuestas' AND $flag = true){
				$Local=substr($stat->getElementsByTagName("a")->item(1)->nodeValue,2,4);
				$Empate=substr($stat->getElementsByTagName("a")->item(2)->nodeValue,2,4);
				$Visitante=substr($stat->getElementsByTagName("a")->item(3)->nodeValue,2,4);
				echo 'MARCA: Los valores de 1 X 2 son '. $Local .' '. $Empate .' '. $Visitante;
				echo "<br>" . PHP_EOL;
				$flag = false;
			}
			if ($var =='bwin' AND $flag = true){
				$Local=substr($stat->getElementsByTagName("a")->item(1)->nodeValue,2,4);
				$Empate=substr($stat->getElementsByTagName("a")->item(2)->nodeValue,2,4);
				$Visitante=substr($stat->getElementsByTagName("a")->item(3)->nodeValue,2,4);
				echo 'BWIN: Los valores de 1 X 2 son '. $Local .' '. $Empate .' '. $Visitante;
				echo "<br>" . PHP_EOL;
				$flag = false;
			}
		}
	}
?>