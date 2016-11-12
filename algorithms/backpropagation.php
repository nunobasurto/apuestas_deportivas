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
    function __construct($num_inputs, $num_hidden, $num_outputs, $hidden_layer_weights, $hidden_layer_bias, $output_layer_weights, $output_layer_bias)
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
                    array_push($this->hidden_layer->neurons[$h]->weights, (float)rand()/(float)getrandmax());
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
                if(empty($output_layer_weights))
                     array_push($this->output_layer->neurons[$h]->weights, (float)rand()/(float)getrandmax());
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
    function train($training_inputs, $training_outputs)
    {
        $var= array();
        $this->feed_forward($training_inputs);
        # 1. Output neuron deltass
        $pd_errors_wrt_output_neuron_total_net_input = array();
        for ($i=0; $i < count($this->output_layer->neurons); $i++) { 
            array_push($pd_errors_wrt_output_neuron_total_net_input, 0);
        }
        for ($o=0; $o < count($this->output_layer->neurons); $o++) 
        { 
            $pd_errors_wrt_output_neuron_total_net_input[$o] = $this->output_layer->neurons[$o]->calculate_pd_error_wrt_total_net_input($training_outputs[$o]);
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
                $total_error += $this->output_layer->neurons[$o]->calculate_error($training_outputs[$o]);
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
            array_push($this->neurons, new Neuron($bias));
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
    function calculate_error($target_output)
    {
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
$nn = new NeuralNetwork(2,2,2, [0.15,0.2,0.25,0.3], 0.35, [0.4,0.45,0.5,0.55], 0.6);
for ($i=0; $i <10000; $i++) { 
    $nn->train([0.05, 0.1], [0.01, 0.99]);
    echo $i . ' ' . round($nn->calculate_total_error([[[0.05,0.1], [0.01,0.99]]]),9);
    echo "<br>" . PHP_EOL;

}
?>
