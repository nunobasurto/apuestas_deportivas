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
    public $learning_rate = 0,5;
    public $hidden_layer;
    public $output_layer;
    function __construct($num_inputs, $num_hidden, $num_outputs, $hidden_layer_weights, $hidden_layer_bias, $output_layer_weights, $output_layer_bias)
    {
        this->num_inputs=$num_inputs;
        this->hidden_layer = NeuronLayer(num_hidden, hidden_layer_bias);
        this->output_layer = NeuronLayer(num_outputs, output_layer_bias);

        this->init_weights_hidden(hidden_layer_weights);
        this->init_weights_output(output_layer_weights);

    }


    function init_weights_hidden($hidden_layer_weights)
    {
        $cont = 0;
        for ($h=0; $h<sizeof($hidden_layer.neurons); $h++) { 
            for ($i=0; $i<$num_inputs; $i++) {
                if(empty($hidden_layer_weights))
                    //Atentos al add
                    $hidden_layer.neurons[$h].weights.add((float)rand()/(float)getrandmax());
                else
                    $hidden_layer.neurons[$h].weights.add(hidden_layer_weights[$cont]);
                $cont++;
            }
        }
    }
    
    function init_weights_output($output_layer_weights)
    {
        $cont = 0;
        for ($h=0; $h<sizeof($output_layer.neurons); $h++) { 
            for ($i=0; $i<sizeof($hidden_layer.neurons); $i++) {
                if(empty($output_layer_weights))
                    //Atentos al add
                    $output_layer.neurons[$h].weights.add((float)rand()/(float)getrandmax());
                else
                    $output_layer.neurons[$h].weights.add(output_layer_weights[$cont]);
                $cont++;
            }
        }
    }

    function feed_forward($inputs)
    {
        $hidden_layer_outputs = $hidden_layer.feed_forward($inputs);
        return $output_layer.feed_forward($hidden_layer_outputs);
    }
    function train($training_inputs, $training_outputs)
    {
        feed_forward($training_inputs)

        # 1. Output neuron deltas
        $pd_errors_wrt_output_neuron_total_net_input = [0] * len($output_layer.neurons)
        for ($o=0; $o < sizeof($output_layer.neurons); $o++) 
        { 
            $pd_errors_wrt_output_neuron_total_net_input[$o] = $output_layer.neurons[$o].calculate_pd_error_wrt_total_net_input(training_outputs[$o])
        }

        # 2. Hidden neuron deltas
        $pd_errors_wrt_hidden_neuron_total_net_input = [0] * len($hidden_layer.neurons);
        for ($h=0; $h < sizeof($hidden_layer.neurons); $h++) 
        { 
            $d_error_wrt_hidden_neuron_output = 0;
            for ($o=0; $o < sizeof($output_layer.neurons); $o++) { 
                $d_error_wrt_hidden_neuron_output += $pd_errors_wrt_output_neuron_total_net_input[$o] * $output_layer.neurons[$o].weights[$h];
            }
            $pd_errors_wrt_hidden_neuron_total_net_input[$h] = $d_error_wrt_hidden_neuron_output * $hidden_layer.neurons[$h].calculate_pd_total_net_input_wrt_input()
        }
        # 3. Update output neuron weights
        for ($o=0; $o < sizeof($output_layer.neurons); $o++) { 
            for ($w_ho=0; $w_ho < sizeof($output_layer.neurons[$o].weights) ; $w_ho++) { 
                $pd_error_wrt_weight = $pd_errors_wrt_output_neuron_total_net_input[$o] * $output_layer.neurons[$o].calculate_pd_total_net_input_wrt_weight($w_ho);
                $output_layer.neurons[$o].weights[$w_ho] -= $LEARNING_RATE * $pd_error_wrt_weight;
            }
        }
        # 4. Update hidden neuron weights

        for ($h=0; $h < sizeof($hidden_layer.neurons); $h++) { 
            for ($w_ih=0; $w_ih < sizeof($hidden_layer.neurons[$h].weights); $w_ih++) { 
                $pd_error_wrt_weight = $pd_errors_wrt_hidden_neuron_total_net_input[$h] * $hidden_layer.neurons[$h].calculate_pd_total_net_input_wrt_weight($w_ih);
                $hidden_layer.neurons[$h].weights[$w_ih] -= $LEARNING_RATE * $pd_error_wrt_weight;
            }
        }
        
    }
    function calculate_total_error($training_sets)
    {
        $total_error = 0;
        for ($t=0; $t < sizeof($training_sets); $t++) { 
            $training_inputs, $training_outputs = $training_sets[$t]:
            feed_forward(training_inputs);
            for ($o=0; $o < sizeof($training_outputs); $o++) { 
                $total_error += $output_layer.neurons[$o].calculate_error($training_outputs[$o]);
            }
        }
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
            $this->neurons.add(Neuron($bias));
        }
    }

    function feed_forward($inputs)
    {
        $outputs = array();
        foreach ($neurons as $neuron) {
            $outputs.add($neuron.calculate_output($inputs));
        }
        return $outputs;
    }

    function get_outputs()
    {
        $outputs = array();
        foreach ($neurons as $neuron) {
            $outputs.add($neuron.output)
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
    function __construct($bias)
    {
        $this->bias = $bias;
        $this->weights = array();
    }
    function calculate_output($inputs) 
    {
        $this->inputs = $inputs;
        $this->outputs = squash(calculate_total_net_input())
        reutrn $this->outputs;
    }
    function calculate_total_net_input()
    {
        $total = 0;
        for ($i=0; $i < $inputs; $i++) { 
            $total = $inputs[$i]*$weights[$i];
        }
        return $toal + $bias;
    }

    function squash($total_net_input)
    {
        return 1/(1+ exp(-$total_net_input));
    }

    function calculate_pd_error_wrt_total_net_input($target_output)
    {
        return calculate_pd_error_wrt_output(target_output) * calculate_pd_total_net_input_wrt_input();
    }

    function calculate_error($target_output)
    {
        return 0.5 * ($target_output - $outputs) ** 2;
    }

    function calculate_pd_error_wrt_output($target_output)
    {
        return -($target_output - $outputs);
    }

    function calculate_pd_total_net_input_wrt_input()
    {
        return $outputs * (1 - $outputs);
    }

    function calculate_pd_total_net_input_wrt_weight($index)
    {
        return $inputs[$index];
    }
}

?>