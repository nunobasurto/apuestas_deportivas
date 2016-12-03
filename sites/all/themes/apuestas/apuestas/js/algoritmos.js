(function($){
  $(document).ready(function(){
 
  	$("#preprocesamiento").change(function(){
	    var preprocesamiento = $("#preprocesamiento").val();
      $("#pre_XML").load("../leeXML.php", {_preprocesamiento:preprocesamiento});
    });

  	$("#algoritmos").change(function(){
      var algoritmos = $("#algoritmos").val();
      $("#alg_XML").load("../leeXML.php", {_algoritmos:algoritmos});
    });

   $("#archivo").change(function(){
      $("#botonBD").removeAttr("disabled");
    });

    $("#bases").change(function(){
      $("#botonBD").removeAttr("disabled");
    });

    $("#preprocesamiento").change(function(){
      $("#prep").removeAttr("disabled");
    });

    $("#algoritmos").change(function(){
      $("#alg").removeAttr("disabled");
    });

    $("#existentes").change(function(){
      $("#botonRecu").removeAttr("disabled");
    });

  });
})(jQuery);
