<?php
    $url = file_get_contents("http://www.marca.com/estadisticas/futbol/primera/2016_17/jornada_7/realmadrid_eibar");
        if (preg_match('|<span>(.*?)</span>|is',$url,$cap)){
            echo "Result: ",$cap[1];
        }
    
?>
