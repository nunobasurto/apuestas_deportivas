<?php
//Implementación del algoritmo de backpropagation.
    $learning_rate = 0,5;
    function main($num_inputs, $num_hidden, $num_outputs)
    {

    }
    function inicializarPesosCapasOcultas($pesosCapasOcultas)
    {
        foreach ($neuronasCapasOcultas as $neurona) {
            foreach($num_inputs as $input){
                if(empty($pesosCapasOcultas))
                    $neuronasCapasOcultas[$neurona].pesos.add(random())
                else
                    $neuronasCapasOcultas[$neurona].pesos.add($pesosCapasOcultas[$cont])
                $cont++;
            }
        }
    }
    
    function inicializarPesosCapasOcultas($pesosCapasOcultas)
    {
        foreach ($neuronasCapasOcultas as $neurona) {
            foreach($num_inputs as $input){
                if(empty($pesosCapasOcultas))
                    $neuronasCapasOcultas[$neurona].pesos.add(random())
                else
                    $neuronasCapasOcultas[$neurona].pesos.add($pesosCapasOcultas[$cont])
                $cont++;
            }
        }
    }

?>