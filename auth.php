<?php 
require_once 'clases/auth.class.php';
require_once 'clases/respuestas.class.php';

//Instanciamos las clases auth y respuestas que están dentro de clases
$_auth = new auth;
$_respuestas = new respuestas;

//Si el método por el que intentamos entrar aquí es POST, accedemos
if($_SERVER['REQUEST_METHOD'] == "POST"){

	//Recogemos los datos que recibimos
    $postBody = file_get_contents("php://input");
    //Enviamos los datos al manejador
    $datosArray = $_auth->login($postBody);
    //Devolvemos una respuesta. Le decimos primero el tipo de respuesta que será (un json)
    header('Content-Type: application/json');
    //Comprobamos si hay un error
    if(isset($datosArray["result"]["error_id"])){
    	//Si lo hay, devolvemos la respuesta
        $responseCode = $datosArray["result"]["error_id"];
        http_response_code($responseCode);
    }else{
    	//Si no hay error, devolveremos el codigo 200
        http_response_code(200);
    }
    //Imprimimos el json
    echo json_encode($datosArray);

//Si no, da error
}else{
	//Le decimos que la respuesta será un json
    header('Content-Type: application/json');
    //Escribiremos en $datosArray el error
    $datosArray = $_respuestas->error_405();
    //Lo imprimimos
    echo json_encode($datosArray);

    echo " metodo no permitido";
}


?>