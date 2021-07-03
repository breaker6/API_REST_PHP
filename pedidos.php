<?php
require_once 'clases/respuestas.class.php';
require_once 'clases/pedidos.class.php';//Clase a la que haremos referencia

//Instanciamos la clase respuestas y la clase usuarios
$_respuestas = new respuestas;
$_pedidos = new pedidos;//Instanciaremos la clase a la que hemos hecho referencia antes

//Comprobamos el tipo de metodo que estamos recibiendo
//El metodo GET se utiliza para consultar datos guardados
if($_SERVER['REQUEST_METHOD'] == "GET"){
    //Comprobamos si hemos recibido el numero de paginas a imprimir
    if(isset($_GET["page"])){
        //Guardamos el número de paginas en $pagina
        $pagina = $_GET["page"];
        //Lanzamos la funcion, devolvemos en un json con los datos y devolvemos el codigo 200 de que todo OK
        $listapedidos = $_pedidos->listaPedidos($pagina);
        header("Content-Type: application/json");
        echo json_encode($listapedidos);
        http_response_code(200);
    //Si no hemos recibido el numero de paginas, comprobamos si hemos recibido el id
    }else if(isset($_GET['id'])){
        //Guardamos el id en $usuarioid
        $pedidoid = $_GET['id'];
        //Lanzamos la funcón, devolvemos un json con los datos del producto y devolvemos el codigo 200 de que todo OK
        $datosPedido = $_pedidos->obtenerPedido($pedidoid);
        header("Content-Type: application/json");
        echo json_encode($datosPedido);
        http_response_code(200);
    }
    //NOTA: Si no hemos recibido ninguno de los dos parámetros, no hará nada

//El metodo POST se utiliza para escribir en la base de datos 
}else if($_SERVER['REQUEST_METHOD'] == "POST"){
    //Guardamos los datos recibidos en $postBody
    $postBody = file_get_contents("php://input");
    //Mandamos los datos a la funcion post de productos.class.php
    $datosArray = $_pedidos->post($postBody);
    //Devolvemos un json con la respuesta tanto si hay ido bien como si no
    header('Content-Type: application/json');
    if(isset($datosArray["result"]["error_id"])){
        $responseCode = $datosArray["result"]["error_id"];
        http_response_code($responseCode);
    }else{
        http_response_code(200);
    }
    //Imprimimos el json con los datos del producto guardados
    echo json_encode($datosArray);

//El metodo PUT se utiliza para modificar datos
}else if($_SERVER['REQUEST_METHOD'] == "PUT"){
    //Guardamos los datos recibidos en $postBody
    $postBody = file_get_contents("php://input");
    //Mandamos los datos a la funcion put de productos.class.php
    $datosArray = $_pedidos->put($postBody);
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
        //Recogemos los headers que pueden haber sido enviados
        $headers = getallheaders();
        //En caso de haberlos, recogeremos los datos de ellos
        if(isset($headers["token"])){
        	if (isset($headers["idpedido"])){
	            //Metemos los datos recibidos en un array y los convertimos a un json
	            $send = [
	                "token" => $headers["token"],
	                "idpedido" =>$headers["idpedido"]
	            ];
	        }
	        if (isset($headers["idlinea"])){
	            //Metemos los datos recibidos en un array y los convertimos a un json
	            $send = [
	                "token" => $headers["token"],
	                "idlinea" =>$headers["idlinea"]
	            ];
	        }
            $postBody = json_encode($send);
        //Si no hay headers, los cogeremos desde el body
        }else{
            //Guardamos los datos recibidos en $postBody
            $postBody = file_get_contents("php://input");
        }
        
        //Mandamos los datos a la funcion delete de productos.class.php
        $datosArray = $_pedidos->delete($postBody);
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