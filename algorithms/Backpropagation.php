#!/usr/bin/php
<?php
//Implementación del algoritmo de backpropagation y la inserción de los datos resultantes en la base de datos.

/**
* Esta clase implementa una red neuronal.
*/
class NeuralNetwork
{
    public $num_inputs = 0;
    public $num_hidden= 0;
    public $num_outputs=0;
    public $hidden_layer_weights=array();
    public $hidden_layer_bias=array();
    public $output_layer_weights=array();
    public $output_layer_bias=array();
    public $learning_rate = 0.15;
    public $hidden_layer;
    public $output_layer;
    function __construct($num_inputs, $num_hidden, $num_outputs, $hidden_layer_bias = null, $hidden_layer_weights = null, $output_layer_bias = null, $output_layer_weights = null)
    {
        $this->num_inputs=$num_inputs;
        $this->hidden_layer = new NeuronLayer($num_hidden, $hidden_layer_bias);
        $this->output_layer = new NeuronLayer($num_outputs, $output_layer_bias);
        $this->init_weights_hidden($hidden_layer_weights);
        $this->init_weights_output($output_layer_weights);
    }
    function init_weights_hidden($hidden_layer_weights)
    {
        $cont = 0;
        for ($h=0; $h<count($this->hidden_layer->neurons); $h++) { 
            for ($i=0; $i<$this->num_inputs; $i++) {
                if(empty($hidden_layer_weights))
                {
                    $min = -0.35;
                    $max = 0.35;
                    array_push($this->hidden_layer->neurons[$h]->weights, $min + lcg_value() * abs($max - $min));
                }
                else
                {

                    array_push($this->hidden_layer->neurons[$h]->weights,$hidden_layer_weights[$cont]);
                }
                $cont++;
            }
        }
    }
    
    function init_weights_output($output_layer_weights)
    {
        $cont = 0;
        for ($h=0; $h<count($this->output_layer->neurons); $h++) { 
            for ($i=0; $i<count($this->hidden_layer->neurons); $i++)
            {
                if(empty($output_layer_weights)){
                    $min = -0.35;
                    $max = 0.35;
                    array_push($this->output_layer->neurons[$h]->weights, $min + lcg_value() * abs($max - $min));
                 }
                else
                    array_push($this->output_layer->neurons[$h]->weights,$output_layer_weights[$cont]);
                $cont++;
            }
        }
    }
    function feed_forward($inputs)
    {
        $hidden_layer_outputs = $this->hidden_layer->feed_forward2($inputs);

        return $this->output_layer->feed_forward2($hidden_layer_outputs);
    }
    function train($training_inputs, $training_outputs=null)
    {
        $var= array();
        $this->feed_forward($training_inputs);

        # 1. Salida del delta de la neuronas.
        $pd_errors_wrt_output_neuron_total_net_input = array();
        for ($i=0; $i < count($this->output_layer->neurons); $i++) { 
            array_push($pd_errors_wrt_output_neuron_total_net_input, 0);
        }
        for ($o=0; $o < count($this->output_layer->neurons); $o++) 
        {
            $pd_errors_wrt_output_neuron_total_net_input[$o] = $this->output_layer->neurons[$o]->calculate_pd_error_wrt_total_net_input($training_outputs);
        }

        # 2. Deltas de las neuronas ocultas.
        $pd_errors_wrt_hidden_neuron_total_net_input = array();
        for ($i=0; $i <  count($this->hidden_layer->neurons); $i++) { 
            array_push($pd_errors_wrt_hidden_neuron_total_net_input, 0);
        }
        for ($h=0; $h < count($this->hidden_layer->neurons); $h++) 
        { 
            $d_error_wrt_hidden_neuron_output = 0;
            for ($o=0; $o < count($this->output_layer->neurons); $o++) 
            {
                $d_error_wrt_hidden_neuron_output += $pd_errors_wrt_output_neuron_total_net_input[$o] * $this->output_layer->neurons[$o]->weights[$h];
            }
            $pd_errors_wrt_hidden_neuron_total_net_input[$h] = $d_error_wrt_hidden_neuron_output * $this->hidden_layer->neurons[$h]->calculate_pd_total_net_input_wrt_input();
        }
        # 3. Actualizacion de los pesos de salida de las neuronas
        for ($o=0; $o < count($this->output_layer->neurons); $o++) { 
            for ($w_ho=0; $w_ho < count($this->output_layer->neurons[$o]->weights) ; $w_ho++) { 
                $pd_error_wrt_weight = $pd_errors_wrt_output_neuron_total_net_input[$o] * $this->output_layer->neurons[$o]->calculate_pd_total_net_input_wrt_weight($w_ho);
                $this->output_layer->neurons[$o]->weights[$w_ho] -= $this->learning_rate * $pd_error_wrt_weight;
            }
        }
        # 4. Actualización de los pesos de salida de las neuronas ocultas.
        for ($h=0; $h < count($this->hidden_layer->neurons); $h++) { 
            for ($w_ih=0; $w_ih < count($this->hidden_layer->neurons[$h]->weights); $w_ih++) { 
                $pd_error_wrt_weight = $pd_errors_wrt_hidden_neuron_total_net_input[$h] * $this->hidden_layer->neurons[$h]->calculate_pd_total_net_input_wrt_weight($w_ih);
                $this->hidden_layer->neurons[$h]->weights[$w_ih] -= $this->learning_rate * $pd_error_wrt_weight;
            }
        }
        
    }
    function test($inputs)
    {
        $val = $this->feed_forward($inputs);

        return $val;
    }
    function calculate_total_error($training_sets)
    {
        $total_error = 0;
        for ($t=0; $t < count($training_sets); $t++) { 
            $training_inputs = $training_sets[$t][0];
            $training_outputs = $training_sets[$t][1];
            $this->feed_forward($training_inputs);
            for ($o=0; $o < count($training_outputs); $o++) { 
                $total_error += $this->output_layer->neurons[$o]->calculate_error($training_outputs, $o);
            }
        }
        return $total_error;
    }
}
/**
* Esta clase implementa una neurona oculta.
*/
class NeuronLayer
{
    public $num_neurons=0;
    public $bias = 0;
    public $neurons;
    function __construct($num_neurons, $bias)
    {
        if (!empty($bias))
            $this->bias = $bias;
        else
            $this->bias = (float)rand()/(float)getrandmax();
        $this->neurons = array();
        for ($i=0; $i < $num_neurons; $i++) { 
            array_push($this->neurons, new Neuron($this->bias));
        }
    }
    function feed_forward2($inputs)
    {
        $outputs = array();
        foreach ($this->neurons as $neuron) {
            array_push($outputs, $neuron->calculate_output($inputs));
        }
        return $outputs;
    }
    function get_outputs()
    {
        $outputs = array();
        foreach ($neurons as $neuron) {
             array_push($outputs, $neuron->output);
        }
        return $outputs;
    }
}
/**
* Esta clase implmenta una neurona.
*/
class Neuron
{
    public $bias = 0;
    public $weights;
    public $inputs;
    public $outputs;
    function __construct($bias)
    {

        $this->bias = $bias;
        $this->weights = array();
    }
    function calculate_total_net_input()
    {
        $total = 0;
        for ($i=0; $i < count($this->inputs); $i++) { 
            $total += $this->inputs[$i]*$this->weights[$i];
        }
        return $total + $this->bias;
    }
    function calculate_output($inputs) 
    {
        $this->inputs = $inputs;
        $this->outputs = $this->squash($this->calculate_total_net_input());
        return $this->outputs;
    }
    function squash($total_net_input)
    {
        return 1/(1+ exp(-$total_net_input));
        //return tanh($total_net_input);
    }
    function calculate_pd_error_wrt_total_net_input($target_output)
    {
        
        return $this->calculate_pd_error_wrt_output($target_output) * $this->calculate_pd_total_net_input_wrt_input();
    }
    function calculate_error($target_output ,$cont)
    {
        $red=0;
        if ($this->outputs<=0.33)
            $red = 0;
        else if ($this->outputs<=0.66)
            $red = 0.5;
        else
            $red = 1;
        return 0.5 * pow(($target_output - $this->outputs), 2);
    }
    function calculate_pd_error_wrt_output($target_output)
    {
        return -($target_output - $this->outputs);
    }
    function calculate_pd_total_net_input_wrt_input()
    {
        return $this->outputs * (1 - $this->outputs);
    }
    function calculate_pd_total_net_input_wrt_weight($index)
    {
        return $this->inputs[$index];
    }
}

$training_set = array();
$test_set = array();
$tiempo = getdate();
$currentTyme= $tiempo["year"] . '-' . $tiempo["mon"] . '-' . $tiempo["mday"] . ' 00:00:00';
$jornada  = db_query('SELECT f.jornada FROM {fecha_jornada} f WHERE f.fecha_antes > :ff ORDER BY f.fecha_antes asc', array(':ff' => $currentTyme))->fetchField();
$jor_aux = $jornada*100;

$id_prue = $jor_aux + 1;
$control = db_query('SELECT p.pronostico_local FROM {pronosticos} p WHERE p.id_partido = :id ', array(':id' => $id_prue))->fetchField();
//Control para que no lo vuelva a ejecutar si los datos ya han sido almacenados.
if ($control == null){

for ($j=$jor_aux + 1; $j <= $jor_aux + 10; $j++) {

    //De esta forma ya tenemos cada uno de los id correspondientes a cada partido de la jornada.
    //Ahora debemos saber cuales son los equipos que van a disputar dicho partido.
    $local  = db_query('SELECT f.equipo_local FROM {fecha_jornada} f WHERE f.id_partido = :id ', array(':id' => $j))->fetchField();
    $visitante  = db_query('SELECT f.equipo_visitante FROM {fecha_jornada} f WHERE f.id_partido = :id', array(':id'=>$j))->fetchField();

    $partido = array($local, $visitante );

    $arrayRachaLocal = array();
    $arrayRachaVisitante = array();
    echo "<br>" . PHP_EOL;
    foreach ($partido as $clave => $equipo) {
        $nn=null;
        //Introducimos las instancias de cada equipo
        //Ahora lo primero es obtener las jornadas del equipo como local
        $jornadas = db_select('clasificacion_jornada','cj')
            ->fields('cj',array('jornada'))
            ->condition('cj.id_equipo', $equipo, '=')
            ->condition('cj.local_visitante', $clave, '=');
        $jor = $jornadas->execute();
        $jornadas=array();
        foreach ($jor as $key) {
            array_push($jornadas, $key->jornada);
        }

        $arrayInstancia = array();
        foreach ($jornadas as $jor) {
        
            $arrayInput = array();
            //Primero debemos saber si el equipo esa jornada es local o visitante.
            $local_visitante = $clave;
            $rival = 0;
            $result = array();
            if($local_visitante==0){
                $rival  = db_query('SELECT f.equipo_visitante FROM {fecha_jornada} f WHERE f.equipo_local = :equipo AND f.jornada = :jornada', array(':equipo'=>$equipo, ':jornada'=>$jor))->fetchField();

                $equ = db_select('fecha_jornada','f');
                $equ->join('partidos', 'p', 'f.id_partido = p.id_partido');
                $equ->fields('p')
                    ->condition('f.equipo_local', $equipo, '=')
                    ->condition('f.jornada', $jor, '=');
                $result = $equ->execute();
            }
            else{
                $rival  = db_query('SELECT f.equipo_local FROM {fecha_jornada} f WHERE f.equipo_visitante = :equipo AND f.jornada = :jornada', array(':equipo'=>$equipo, ':jornada'=>$jor))->fetchField();

                $equ = db_select('fecha_jornada','f');
                $equ->join('partidos', 'p', 'f.id_partido = p.id_partido');
                $equ->fields('p')
                    ->condition('f.equipo_visitante', $equipo, '=')
                    ->condition('f.jornada', $jor, '=');
                $result = $equ->execute();
            }
            $goles_fav = 0;
            $goles_cont = 0;
            foreach ($result as $key) {
                $goles_fav = $key->goles_local;
                $goles_cont = $key->goles_visitante;
                foreach($key as $k){
                    if($key->id_partido != $k)
                        array_push($arrayInput, $k);
                }
            }
            //Rachas de ambos equipos:
            $rachasEquipo = db_select('clasificacion_jornada','cj');
            $rachasEquipo->fields('cj')
                ->condition('cj.id_equipo', $equipo, '=')
                ->condition('cj.jornada' , $jor, '=');
            $resultEquipo = $rachasEquipo->execute();

            $rachasRival = db_select('clasificacion_jornada','cj');
            $rachasRival->fields('cj')
                ->condition('cj.id_equipo', $rival, '=')
                ->condition('cj.jornada' , $jor, '=');
            $resultRival= $rachasRival->execute();

            if ($local_visitante == 0){
                foreach ($resultEquipo as $key) {
                    foreach($key as $k => $valor){
                        if($k!='id_equipo' AND $k != 'jornada')
                            array_push($arrayInput, $valor);
                    }
                }
                foreach ($resultRival as $key) {
                    foreach($key as $k => $valor){
                        if($k!='id_equipo' AND $k != 'jornada')
                            array_push($arrayInput, $valor);
                    }
                }

            }else{
                foreach ($resultRival as $key) {
                    foreach($key as $k => $valor){
                        if($k!='id_equipo' AND $k != 'jornada')
                            array_push($arrayInput, $valor);
                    }
                }
                foreach ($resultEquipo as $key) {
                    foreach($key as $k => $valor){
                        if($k!='id_equipo' AND $k != 'jornada')
                            array_push($arrayInput, $valor);
                    }
                }
            }
            //Ahora ponemos el ouput, el cual depende del resultado del partido.
            $output = 0;
            if ($goles_fav>$goles_cont)
                $output = 0;
            else if ($goles_fav==$goles_cont)
                $output = 0.5;
            else
                $output = 1;

            //En este punto debemos normalizar el array Input, antes de introducir la instancia.

            $arrayAux = array();
            array_push($arrayAux, $arrayInput);
            array_push($arrayAux, $output);

            //Finalmente creamos la instancia con el arrayAux.
            array_push($arrayInstancia, $arrayAux);
        }
        //Introducimos la instancia del equipo en el training.
        foreach ($arrayInstancia as $instancia) {
            array_push($training_set, $instancia);
        } 
    }
    //La normalizacion debemos empezarla aquí ya que este es el punto en el cual tenemos todas las instancias juntas.
    for ($c=0; $c < sizeof($training_set[0][0]); $c++) { 
        $valMax = 0;
        $valMin = 50;
        for ($f=0; $f < sizeof($training_set); $f++) { 
            $valMax=max($training_set[$f][0][$c], $valMax);
            $valMin=min($training_set[$f][0][$c], $valMin);
        }

        for ($f=0; $f < sizeof($training_set); $f++) {
            if($valMax!=0) 
                $training_set[$f][0][$c]=round(($training_set[$f][0][$c]-$valMin)/($valMax-$valMin), 3);
            else
                $training_set[$f][0][$c]= 0;
        }
    }
    //Extreamos la penultima jornada como local/visitante de cada equipo
    array_push($test_set, $training_set[sizeof($training_set)/2-1]);
    array_push($test_set, $training_set[sizeof($training_set)-1]);

    //Sacamos para no tenerlas en cuenta en el training.
    array_splice($training_set,sizeof($training_set)-2,1);
    array_splice($training_set,sizeof($training_set)/2-2,1);

    //Extraemos las rachas para su posterior inserción.
    for ($v=30; $v <54 ; $v++) { 
        $test_set[0][0][$v] = $training_set[sizeof($training_set)/2-1][0][$v];
        $test_set[1][0][$v] = $training_set[sizeof($training_set)/2-1][0][$v];
    }
    for ($v=54; $v <78 ; $v++) { 
        $test_set[0][0][$v] = $training_set[sizeof($training_set)-1][0][$v];
        $test_set[1][0][$v] = $training_set[sizeof($training_set)-1][0][$v];
    }
    //Quitamos los posibles resultados de los partidos ya que son los valores a predecir.
    unset($test_set[0][1]);
    unset($test_set[1][1]);

    $training_inputs = array();
    $training_outputs = array();

    //Hacemos tres iteraciones para extraer el valor medio que finalmente obtenemos
    $valorMedLoc = array();
    $valorMedVis = array();
    for ($rep=0; $rep < 3; $rep++) { 
    
        $nn = new NeuralNetwork(sizeof($training_set[0][0]),15, sizeof($training_set[0][1]));
        for ($i=0; $i <4000; $i++) {
            $random = rand(0, sizeof($training_set));
            $training_inputs = $training_set[$random][0];
            $training_outputs = $training_set[$random][1];
            $nn->train($training_inputs,$training_outputs);
        }
        echo "<br>" . PHP_EOL;
        $valorPredicho = $nn->test($test_set[0][0]);
        print_r($valorPredicho);
        array_push($valorMedLoc, $valorPredicho[0]);
        $valorPredicho2 = $nn->test($test_set[1][0]);
        print_r($valorPredicho2);
        array_push($valorMedVis, $valorPredicho2[0]);
    }
    $medLoc = ($valorMedLoc[0] + $valorMedLoc[1] + $valorMedLoc[2]) /3;
    $medVis = ($valorMedVis[0] + $valorMedVis[1] + $valorMedVis[2]) /3;
    echo "<br>" . PHP_EOL;
    echo 'El valor es: ' . ($medLoc+$medVis)/2;
    echo "<br>" . PHP_EOL;
    //Dependiendo el valor obtenido los colocamos en tres sectores diferentes
    //[0, 0.33) -> 1 ; [0.33 , 0,66) -> X ; [0.66 , 1] -> 2
    $estimLoc;
    $estimVis;
    $estimFinal;
    if ($medLoc<0.33)
        $estimLoc = "1";
    else if ($medLoc<0.66)
        $estimLoc = "X";
    else
        $estimLoc = "2";

    if ($medVis<0.33)
        $estimVis = "1";
    else if ($medVis<0.66)
        $estimVis = "X";
    else
        $estimVis = "2";
    if ($estimLoc == $estimVis)
        $estimFinal = $estimLoc;
    else{
        if (($estimLoc == "1" AND $estimVis == "2") OR ($estimLoc == "2" AND $estimVis == "1"))
            $estimFinal = "X";
        else{
            $media = ($medLoc+$medVis)/2;
            if ($media<0.33)
                $estimFinal = "1";
            else if ($media<0.66)
                $estimFinal = "X";
            else
                $estimFinal = "2";
        }
    }
    echo "<br>" . PHP_EOL;
    echo 'Estimacion Final ' . $estimFinal;
    
    $insert = db_insert('pronosticos')
            ->fields(array(
                'id_partido' => $j,
                'pronostico_local' => $medLoc,
                'pronostico_visitante' => $medVis,
                'pronostico_estimado'=> $estimFinal,
                ))
            ->execute();
            
}
}
header("Location:/informe-pronostico");
?>
