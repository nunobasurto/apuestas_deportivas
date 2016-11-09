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
        //Implementar lo último.
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

?>