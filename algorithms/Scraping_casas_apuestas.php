#!/usr/bin/php
<?php
define('DRUPAL_ROOT', getcwd());
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

$tiempo = getdate();
$currentTyme= $tiempo["year"] . '-' . $tiempo["mon"] . '-' . $tiempo["mday"] . ' 00:00:00';
$jornada  = db_query('SELECT f.jornada FROM {fecha_jornada} f WHERE f.fecha_antes > :ff ORDER BY f.fecha_antes asc', array(':ff' => $currentTyme))->fetchField(); 
$url = 'http://www.resultados-futbol.com/primera/grupo1/jornada' . "$jornada";
echo $jornada;
$source = file_get_contents($url);
libxml_use_internal_errors(true);
libxml_clear_errors();
$html = new DOMDocument();
$html->loadHTML($source);
$xpath=new DOMXpath($html);
$trs = $html->getElementsByTagName("tr");
$flag=true;

$id_prim = $jornada*100 + 1;
$control = db_query('SELECT a.BET365 FROM {apuestas} a WHERE a.id_partido = :id', array(':id' => $id_prim))->fetchField();

//Control para que no lo vuelva a ejecutar si los datos ya han sido almacenados.
if ($control == null){
foreach($trs as $tr){
 	$nameStat = $tr->getElementsByTagName("div"); 		
 	$eLocal = $tr->getElementsByTagName("td")->item(2)->nodeValue;
 	$eVisitante = $tr->getElementsByTagName("td")->item(4)->nodeValue;
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

 		$id_par =  "$id_partido";
 		$id_1 = (int)($id_par . '1');
 		$id_2 = (int)($id_par . '2');
 		$id_3 = (int)($id_par . '3');
 		$id_4 = (int)($id_par . '4');


 		$ins=db_insert('apuestas')
			->fields(array(
			'id_partido' => $id_partido,
			'BET365'=>null,
			'Marca'=>null,
			'BWIN'=>null,
			'888Bet'=>null,
			))
			->execute();

 		$flag1 = True;
- 		$flag2 = True;
- 		$flag3 = True;
- 		$flag4 = True;
 		}
		foreach($nameStat as $stat){
			$var=$stat->getElementsByTagName("a")->item(0)->nodeValue;
			
			if ($var =='Bet365'AND $flag1 ==true){
				$Local=substr($stat->getElementsByTagName("a")->item(1)->nodeValue,2,4);
				$Empate=substr($stat->getElementsByTagName("a")->item(2)->nodeValue,2,4);
				$Visitante=substr($stat->getElementsByTagName("a")->item(3)->nodeValue,2,4);
				$Local = str_replace(',', '.' , $Local);
				$Empate = str_replace(',', '.' , $Empate);
				$Visitante = str_replace(',', '.' , $Visitante);
				$insert = db_insert('cuotas')
					->fields(array(
					'id_cuota' => $id_1,
					'Local' => (float)$Local,
					'Empate' => (float)$Empate,
					'Visitante' => (float)$Visitante,
				))
				->execute();
				$flag1=false;
				$insert2 = db_update('apuestas')
				->fields(array(
				'BET365' => $id_1,
				))
				->condition('id_partido', $id_partido, '=')
				->execute();
				
			}
			elseif ($var =='Marca Apuestas'AND $flag2 ==true){
				$Local=substr($stat->getElementsByTagName("a")->item(1)->nodeValue,2,4);
				$Empate=substr($stat->getElementsByTagName("a")->item(2)->nodeValue,2,4);
				$Visitante=substr($stat->getElementsByTagName("a")->item(3)->nodeValue,2,4);
				$Local = str_replace(',', '.' , $Local);
				$Empate = str_replace(',', '.' , $Empate);
				$Visitante = str_replace(',', '.' , $Visitante);
				$insert = db_insert('cuotas')
					->fields(array(
					'id_cuota' => $id_2,
					'Local' => (float)$Local,
					'Empate' => (float)$Empate,
					'Visitante' => (float)$Visitante,
				))
				->execute();
				$flag2=false;
				$insert2 = db_update('apuestas')
				->fields(array(
				'Marca' => $id_2,
				))
				->condition('id_partido', $id_partido, '=')
				->execute();
				$flag2=false;
			}
			elseif ($var =='bwin'AND $flag3 ==true){
				$Local=substr($stat->getElementsByTagName("a")->item(1)->nodeValue,2,4);
				$Empate=substr($stat->getElementsByTagName("a")->item(2)->nodeValue,2,4);
				$Visitante=substr($stat->getElementsByTagName("a")->item(3)->nodeValue,2,4);
				$Local = str_replace(',', '.' , $Local);
				$Empate = str_replace(',', '.' , $Empate);
				$Visitante = str_replace(',', '.' , $Visitante);
				$insert = db_insert('cuotas')
					->fields(array(
					'id_cuota' => $id_3,
					'Local' => (float)$Local,
					'Empate' => (float)$Empate,
					'Visitante' => (float)$Visitante,
				))
				->execute();
				$flag3=false;
				$insert2 = db_update('apuestas')
				->fields(array(
				'BWIN' => $id_3,
				))
				->condition('id_partido', $id_partido, '=')
				->execute();
				
			}
			elseif ($var =='888 sport'AND $flag4 ==true){
				$Local=substr($stat->getElementsByTagName("a")->item(1)->nodeValue,2,4);
				$Empate=substr($stat->getElementsByTagName("a")->item(2)->nodeValue,2,4);
				$Visitante=substr($stat->getElementsByTagName("a")->item(3)->nodeValue,2,4);
				$Local = str_replace(',', '.' , $Local);
				$Empate = str_replace(',', '.' , $Empate);
				$Visitante = str_replace(',', '.' , $Visitante);
				$insert = db_insert('cuotas')
					->fields(array(
					'id_cuota' => $id_4,
					'Local' => (float)$Local,
					'Empate' => (float)$Empate,
					'Visitante' => (float)$Visitante,
				))
				->execute();
				$flag4=false;
				$insert2 = db_update('apuestas')
				->fields(array(
				'888Bet' => $id_4,
				))
				->condition('id_partido', $id_partido, '=')
				->execute();
				
			}
		}
	}
}
	header("Location:/informe-pronostico");
?>
