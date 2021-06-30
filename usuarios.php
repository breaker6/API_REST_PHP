<?php
require_once 'clases/respuestas.class.php';
require_once 'clases/usuarios.class.php';//Clase a la que haremos referencia

//Instanciamos la clase respuestas y la clase usuarios
$_respuestas = new respuestas;
$_usuarios = new usuarios;//Instanciaremos la clase a la que hemos hecho referencia antes

//Comprobamos el tipo de metodo que estamos recibiendo
//El metodo GET se utiliza para consultar datos guardados
if($_SERVER['REQUEST_METHOD'] == "GET"){
    //Comprobamos si hemos recibido el numero de paginas a imprimir
    if(isset($_GET["page"])){
        //Guardamos el número de paginas en $pagina
        $pagina = $_GET["page"];
        //Lanzamos la funcion, devolvemos en un json con los datos y devolvemos el codigo 200 de que todo OK
        $listausuarios = $_usuarios->listaUsuarios($pagina);
        header("Content-Type: application/json");
        echo json_encode($listausuarios);
        http_response_code(200);
    //Si no hemos recibido el numero de paginas, comprobamos si hemos recibido el id
    }else if(isset($_GET['id'])){
        //Guardamos el id en $usuarioid
        $usuarioid = $_GET['id'];
        //Lanzamos la funcón, devolvemos un json con los datos del usuario y devolvemos el codigo 200 de que todo OK
        $datosUsuario = $_usuarios->obtenerUsuario($usuarioid);
        header("Content-Type: application/json");
        echo json_encode($datosUsuario);
        http_response_code(200);
    }
    //NOTA: Si no hemos recibido ninguno de los dos parámetros, no hará nada

//El metodo POST se utiliza para escribir en la base de datos 
}else if($_SERVER['REQUEST_METHOD'] == "POST"){
    //Guardamos los datos recibidos en $postBody
    $postBody = file_get_contents("php://input");
    //Mandamos los datos a la funcion post de usuarios.class.php
    $datosArray = $_usuarios->post($postBody);
    //Devolvemos un json con la respuesta tanto si hay ido bien como si no
    header('Content-Type: application/json');
    if(isset($datosArray["result"]["error_id"])){
        $responseCode = $datosArray["result"]["error_id"];
        http_response_code($responseCode);
    }else{
        http_response_code(200);
    }
    //Imprimimos el json con los datos del usuario guardados
    echo json_encode($datosArray);

//El metodo PUT se utiliza para modificar datos
}else if($_SERVER['REQUEST_METHOD'] == "PUT"){
    //Guardamos los datos recibidos en $postBody
    $postBody = file_get_contents("php://input");
    //Mandamos los datos a la funcion put de usuarios.class.php
    $datosArray = $_usuarios->put($postBody);
    //Devolvemos un json con la respuesta tanto si hay ido bien como si no
    header('Content-Type: application/json');
    if(isset($datosArray["result"]["error_id"])){
        $responseCode = $datosArray["result"]["error_id"];
        http_response_code($responseCode);
    }else{
        http_response_code(200);
    }
    echo json_encode($datosArray);

}else if($_SERVER['REQUEST_METHOD'] == "DELETE"){

        /*$headers = getallheaders();
        if(isset($headers["token"]) && isset($headers["pacienteId"])){
            //recibimos los datos enviados por el header
            $send = [
                "token" => $headers["token"],
                "pacienteId" =>$headers["pacienteId"]
            ];
            $postBody = json_encode($send);
        }else{*/
            //Guardamos los datos recibidos en $postBody
            $postBody = file_get_contents("php://input");
        //}
        
        //Mandamos los datos a la funcion delete de usuarios.class.php
        $datosArray = $_usuarios->delete($postBody);
        //delvovemos una respuesta 
        header('Content-Type: application/json');
        if(isset($datosArray["result"]["error_id"])){
            $responseCode = $datosArray["result"]["error_id"];
            http_response_code($responseCode);
        }else{
            http_response_code(200);
        }
        echo json_encode($datosArray); 
}else{
    //Si no es ninguno de los metodos aqui contemplados, sacaremos un error
    header('Content-Type: application/json');
    $datosArray = $_respuestas->error_405();
    echo json_encode($datosArray);
}


?>