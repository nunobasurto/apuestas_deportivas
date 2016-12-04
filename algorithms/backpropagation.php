<?php
//Implementación del algoritmo de backpropagation.
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
    public $learning_rate = 0.5;
    public $hidden_layer;
    public $output_layer;
    //, $hidden_layer_weights = null, $hidden_layer_bias = null, $output_layer_weights = null, $output_layer_bias = null)
    function __construct($num_inputs, $num_hidden, $num_outputs, $hidden_layer_bias = null, $hidden_layer_weights = null, $output_layer_bias = null, $output_layer_weights = null)
    {
        $this->num_inputs=$num_inputs;
        $this->hidden_layer = new NeuronLayer($num_hidden, $hidden_layer_bias);
        $this->output_layer = new NeuronLayer($num_outputs, $output_layer_bias);
        $this->init_weights_hidden($hidden_layer_weights);
        //echo 'Hidde: ' . $hidden_layer_weights;
        $this->init_weights_output($output_layer_weights);
        //echo 'Hidde: ' . $output_layer_weights;
    }
    function init_weights_hidden($hidden_layer_weights)
    {
        $cont = 0;
        for ($h=0; $h<count($this->hidden_layer->neurons); $h++) { 
            for ($i=0; $i<$this->num_inputs; $i++) {
                if(empty($hidden_layer_weights))
                {
                    array_push($this->hidden_layer->neurons[$h]->weights, (float)rand()/(float)getrandmax());
                    //print_r($this->hidden_layer->neurons[$h]->weights);
                    //echo "<br>" . PHP_EOL;
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
                     array_push($this->output_layer->neurons[$h]->weights, (float)rand()/(float)getrandmax());
                    //print_r($this->output_layer->neurons[$h]->weights);
                    //echo "<br>" . PHP_EOL;
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
        /*echo ' Training Inputs';
        print_r($training_inputs);
        echo "<br>" . PHP_EOL;
        echo ' Training Outputs';
        print_r($training_outputs);
        echo "<br>" . PHP_EOL;*/
        $var= array();
        $this->feed_forward($training_inputs);
        # 1. Output neuron deltass
        $pd_errors_wrt_output_neuron_total_net_input = array();
        for ($i=0; $i < count($this->output_layer->neurons); $i++) { 
            array_push($pd_errors_wrt_output_neuron_total_net_input, 0);
        }
        for ($o=0; $o < count($this->output_layer->neurons); $o++) 
        {
            $pd_errors_wrt_output_neuron_total_net_input[$o] = $this->output_layer->neurons[$o]->calculate_pd_error_wrt_total_net_input($training_outputs);
        }

        # 2. Hidden neuron deltas
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
        # 3. Update output neuron weights
        for ($o=0; $o < count($this->output_layer->neurons); $o++) { 
            for ($w_ho=0; $w_ho < count($this->output_layer->neurons[$o]->weights) ; $w_ho++) { 
                $pd_error_wrt_weight = $pd_errors_wrt_output_neuron_total_net_input[$o] * $this->output_layer->neurons[$o]->calculate_pd_total_net_input_wrt_weight($w_ho);
                $this->output_layer->neurons[$o]->weights[$w_ho] -= $this->learning_rate * $pd_error_wrt_weight;
            }
        }
        # 4. Update hidden neuron weights
        for ($h=0; $h < count($this->hidden_layer->neurons); $h++) { 
            for ($w_ih=0; $w_ih < count($this->hidden_layer->neurons[$h]->weights); $w_ih++) { 
                $pd_error_wrt_weight = $pd_errors_wrt_hidden_neuron_total_net_input[$h] * $this->hidden_layer->neurons[$h]->calculate_pd_total_net_input_wrt_weight($w_ih);
                $this->hidden_layer->neurons[$h]->weights[$w_ih] -= $this->learning_rate * $pd_error_wrt_weight;
            }
        }
        
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
* 
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
* 
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
    }
    function calculate_pd_error_wrt_total_net_input($target_output)
    {
        
        return $this->calculate_pd_error_wrt_output($target_output) * $this->calculate_pd_total_net_input_wrt_input();
    }
    function calculate_error($target_output ,$cont)
    {
        echo 'Target ' . $target_output;
        echo "<br>" . PHP_EOL;
        echo 'Output: ' . $this->outputs;
        echo "<br>" . PHP_EOL;
        $red=0;
        if ($this->outputs<=0.33)
            $red = 0;
        else if ($this->outputs<=0.66)
            $red = 0.5;
        else
            $red = 1;

        /*$update = db_insert('pronosticos')
            ->fields(array(
                'id_partido' => ($jornada*100)+$cont,
                'pronostico' => $this->outputs,
                'pronostico_estimado' => $red,
                ))
            ->execute();*/
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

/**
Se encarga de generar instancias para el training set.
*/


$training_set = array();
$test_set = array();
$jornada = 12;
$jornada = $jornada*100;
$jor_Aux = 12;
for ($j=$jornada+1; $j <=$jornada+1; $j++) {
    //De esta forma ya tenemos cada uno de los id correspondientes a cada partido de la jornada.
    //Ahora debemos saber cuales son los equipos que van a disputar dicho partido.
    $local  = db_query('SELECT f.equipo_local FROM {fecha_jornada} f WHERE f.id_partido = :id ', array(':id' => $j))->fetchField();
    $visitante  = db_query('SELECT f.equipo_visitante FROM {fecha_jornada} f WHERE f.id_partido = :id', array(':id'=>$j))->fetchField();

    $partido = array($local, $visitante );

    $arrayRachaLocal = array();
    $arrayRachaVisitante = array();

    foreach ($partido as $clave => $equipo) {
        //Introducimos las instancias de cada equipo

        $arrayInstancia = array();
        for ($i=1; $i < $jor_Aux ; $i++) {
            $arrayInput = array();
            //Primero debemos saber si el equipo esa jornada es local o visitante.
            $local_visitante = db_query('SELECT cj.local_visitante FROM {clasificacion_jornada} cj WHERE cj.jornada = :jornada AND cj.id_equipo = :equipo', array(':jornada'=>$i, ':equipo'=>$equipo))->fetchField();

            if($local_visitante==0)
                $rival  = db_query('SELECT f.equipo_visitante FROM {fecha_jornada} f WHERE f.equipo_local = :equipo AND f.jornada = :jornada', array(':equipo'=>$equipo, ':jornada'=>$i))->fetchField();
            else
                $rival  = db_query('SELECT f.equipo_local FROM {fecha_jornada} f WHERE f.equipo_visitante = :equipo AND f.jornada = :jornada', array(':equipo'=>$equipo, ':jornada'=>$i))->fetchField();


            $equ = db_select('fecha_jornada','f');
            $equ->join('partidos', 'p', 'f.id_partido = p.id_partido');
            $equ->fields('p');
            $db_or = db_or();
            $db_or->condition('f.equipo_local', $equipo, '=')
                ->condition('f.equipo_visitante', $equipo, '=');
            $equ->condition($db_or)
                ->condition('f.jornada', $i, '=');
            $result = $equ->execute();

            //Hayamos el resultado del partido con los goles a favor y en contra.
            //echo "<br>" . PHP_EOL;
            $goles_fav = 0;
            $goles_cont = 0;
            $cont = 0;
            foreach ($result as $key) {
                $goles_fav = $key->goles_local;
                $goles_cont = $key->goles_visitante;
                foreach($key as $k){
                    if($key->id_partido != $k){
                        array_push($arrayInput, $k);
                        $cont++;
                    }
                }
            }

            //Rachas de ambos equipos:
            $rachasEquipo = db_select('clasificacion_jornada','cj');
            $rachasEquipo->fields('cj')
                ->condition('cj.id_equipo', $equipo, '=')
                ->condition('cj.jornada' , $i, '=');
            $resultEquipo = $rachasEquipo->execute();

            $rachasRival = db_select('clasificacion_jornada','cj');
            $rachasRival->fields('cj')
                ->condition('cj.id_equipo', $equipo, '=')
                ->condition('cj.jornada' , $i, '=');
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
    array_push($test_set, $training_set[sizeof($training_set)/2-2]);
    array_push($test_set, $training_set[sizeof($training_set)-2]);

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

    echo 'Training set: ';
    print_r($training_set);
    /*echo '[[ ';
    for ($pr=0; $pr < sizeof($training_set); $pr++) {
        echo '[';
        for ($w=0; $w < sizeof($training_set[0][0]); $w++) { 
            echo $training_set[$pr][0][$w]. ', ';
        } 
        echo '] , [' . $training_set[$pr][1] . ']] ,';
        echo "<br>" . PHP_EOL;
    }
    echo ']';*/

    $nn = new NeuralNetwork(sizeof($training_set[0][0]), 39 , sizeof($training_set[0][1]));
    for ($i=0; $i <1000 ; $i++) {
        $random = rand(0, sizeof($training_set));
        $training_inputs = $training_set[$random][0];
        $training_outputs = $training_set[$random][1];
        $nn->train($training_inputs,$training_outputs);
    }
    echo $i . ' ' . $nn->calculate_total_error($training_set);
    echo "<br>" . PHP_EOL;
    echo "<br>" . PHP_EOL;
    echo "<br>" . PHP_EOL;
    /*for ($i = 0; $i <2 ;i++){
        $nn->train($test_set[$i][0], $test_set[$i][1]);
    }*/
}
/*
$training_set = array(
    array(array(0,0),0),
    array(array(0,1),1),
    array(array(1,0),1),
    array(array(1,1),0)
    );

$nn = new NeuralNetwork(sizeof($training_set[0][0]), 5, sizeof($training_set[0][1]));
for ($i=0; $i <10000; $i++) {
    $random = rand(0, sizeof($training_set));
    $training_inputs = $training_set[$random][0];
    $training_outputs = $training_set[$random][1];
    $nn->train($training_inputs,$training_outputs);
    echo $i . ' ' . $nn->calculate_total_error($training_set);
    echo "<br>" . PHP_EOL;
}
*/
?>
