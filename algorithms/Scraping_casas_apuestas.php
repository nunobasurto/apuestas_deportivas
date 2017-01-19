#!/usr/bin/php
<?php
define('DRUPAL_ROOT', getcwd());
require_once DRUPAL_ROOT . '/includes/bootstrap.inc';
drupal_bootstrap(DRUPAL_BOOTSTRAP_FULL);

$tiempo = getdate();
$currentTyme= $tiempo["year"] . '-' . $tiempo["mon"] . '-' . $tiempo["mday"] . ' 00:00:00';
$jornada  = db_query('SELECT f.jornada FROM {fecha_jornada} f WHERE f.fecha_antes > :ff ORDER BY f.fecha_antes asc', array(':ff' => $currentTyme))->fetchField(); 
$jor_aux = $jornada*100;

$id_prim = $jornada*100 + 1;
$control = db_query('SELECT a.BET365 FROM {apuestas} a WHERE a.id_partido = :id', array(':id' => $id_prim))->fetchField();

if($control == null){
for ($i = 1; $i <= 10; $i++) {
    $id = $jor_aux + $i;
    //Extreaemos los equipos local y visitante para utilizarlos en la url.
    $equipolocal  = db_query('SELECT e.nombre FROM {fecha_jornada} f, {equipos} e WHERE f.id_partido = :id AND f.equipo_local = e.id_equipo', array(':id' => $id))->fetchField();
    $equipovisitante  = db_query('SELECT e.nombre FROM {fecha_jornada} f, {equipos} e WHERE f.id_partido = :id AND f.equipo_visitante = e.id_equipo', array(':id'=>$id))->fetchField();
    //Generamos dos arrays vacíos donde iremos metiendo los datos.
    //Establecemos la url como un String.
	$url = 'http://www.resultados-futbol.com/partido/'. $equipolocal .'/'.$equipovisitante;
	echo $equipolocal . ' - ' . $equipovisitante;
	echo '</br>' . PHP_EOL;
    $source = file_get_contents($url);
 	libxml_use_internal_errors(true);
 	libxml_clear_errors();
 	$html = new DOMDocument();
 	$html->loadHTML($source);
 	$xpath=new DOMXpath($html);
 	$divs = $html->getElementsByTagName("div");

 	$id_1 = $id*10+1;
 	$id_2 = $id*10+2;
 	$id_3 = $id*10+3;
 	$id_4 = $id*10+4;

 	$ins=db_insert('apuestas')
		->fields(array(
		'id_partido' => $id,
		'BET365'=>null,
		'Marca'=>null,
		'BWIN'=>null,
		'888Bet'=>null,
		))
		->execute();
	
 		$flag1 = True;
 		$flag2 = True;
 		$flag3 = True;
 		$flag4 = True;
 		
		foreach($divs as $stat){
			$var=$stat->getElementsByTagName("a")->item(0)->nodeValue;
			
			if ($var =='Bet365'AND $flag1 ==true){
				$Local=substr($stat->getElementsByTagName("a")->item(1)->nodeValue,2,4);
				$Empate=substr($stat->getElementsByTagName("a")->item(2)->nodeValue,2,4);
				$Visitante=substr($stat->getElementsByTagName("a")->item(3)->nodeValue,2,4);
				echo 'Bet365 ';
				echo $Local . ' - ' . $Empate . ' - ' . $Visitante;
				echo '</br>' . PHP_EOL;
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
				->condition('id_partido', $id, '=')
				->execute();
				
			}
			elseif ($var =='Marca Apuestas'AND $flag2 ==true){
				$Local=substr($stat->getElementsByTagName("a")->item(1)->nodeValue,2,4);
				$Empate=substr($stat->getElementsByTagName("a")->item(2)->nodeValue,2,4);
				$Visitante=substr($stat->getElementsByTagName("a")->item(3)->nodeValue,2,4);
				echo 'Marca ';
				echo $Local . ' - ' . $Empate . ' - ' . $Visitante;
				echo '</br>' . PHP_EOL;
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
				->condition('id_partido', $id, '=')
				->execute();
			}
			elseif ($var =='bwin'AND $flag3 ==true){
				$Local=substr($stat->getElementsByTagName("a")->item(1)->nodeValue,2,4);
				$Empate=substr($stat->getElementsByTagName("a")->item(2)->nodeValue,2,4);
				$Visitante=substr($stat->getElementsByTagName("a")->item(3)->nodeValue,2,4);
				echo 'BWIN ';
				echo $Local . ' - ' . $Empate . ' - ' . $Visitante;
				echo '</br>' . PHP_EOL;
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
				->condition('id_partido', $id, '=')
				->execute();
				
			}
			elseif ($var =='888 sport'AND $flag4 ==true){
				$Local=substr($stat->getElementsByTagName("a")->item(1)->nodeValue,2,4);
				$Empate=substr($stat->getElementsByTagName("a")->item(2)->nodeValue,2,4);
				$Visitante=substr($stat->getElementsByTagName("a")->item(3)->nodeValue,2,4);
				echo '888 sports ';
				echo $Local . ' - ' . $Empate . ' - ' . $Visitante;
				echo '</br>' . PHP_EOL;
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
				->condition('id_partido', $id, '=')
				->execute();
				
			}
		}
	}
}
header("Location:/informe-pronostico");
?>
