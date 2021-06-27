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
    $datosArray = $_auth->login($postBody);
    print_r(json_encode($datosArray));

//Si no, da error
}else{
    echo " metodo no permitido";
}


?>