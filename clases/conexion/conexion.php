<?php

class conexion {
	//Los atributos de la clase serán los recibidos de config
    private $server;
    private $user;
    private $password;
    private $database;
    private $port;
    //En el atributo conexión guardaremos los datos para conectar. Variaran en función del tipo de base de datos
    private $conexion;

    //Este es el consturctor de la clase
    function __construct(){
    	//Obtenemos los datos con la función datosConexion declarada mas abajo
        $listadatos = $this->datosConexion();
        //Recorremos el array recibido y guardamos los datos en los atributos de esta clase
        foreach ($listadatos as $key => $value) {
            $this->server = $value['server'];
            $this->user = $value['user'];
            $this->password = $value['password'];
            $this->database = $value['database'];
            $this->port = $value['port'];
        }
        //En el atributo conexión instanciaremos la clase mysqli que nos permitirá conectar con la base de datos. Si la base de datos es de tipo distinto a mysql, habría que cambiar la linea
        $this->conexion = new mysqli($this->server,$this->user,$this->password,$this->database,$this->port);
        //Si la conexión da error, sacaremos un mensaje y detendremos la ejecución
        if($this->conexion->connect_errno){
            echo "algo va mal con la conexion";
            die();
        }

    }

    //Función que nos permitirá leer los datos de la conexión
    private function datosConexion(){
    	//Almacenamos la dirección de este archivo en $direccion
        $direccion = dirname(__FILE__);
        //la función file_get_contents abre un archivo, guardar todo su contenido y devolverlo. Lo que le pasamos es la ubicación de config
        $jsondata = file_get_contents($direccion . "/" . "config");
        //json_decode convierte el un json en un array. Cnvertimos el json y lo devolvemos
        return json_decode($jsondata, true);
    }

    //Función que nos permite convertir los textos que recibamos a utf8 para evitar problemas
    private function convertirUTF8($array){
        //Recorremos el array recibido y convertimos los datos a utf8
        array_walk_recursive($array,function(&$item,$key){
            if(!mb_detect_encoding($item,'utf-8',true)){
                $item = utf8_encode($item);
            }
        });
        //Devolvemos el array
        return $array;
    }

    //Funcion para obtener los datos
    public function obtenerDatos($sqlstring){
        //Ejecutamos la consulta que hemos recibido
        $results = $this->conexion->query($sqlstring);
        $resultArray = array();
        //Introducimos los resultados en $resultArray
        foreach ($results as $key) {
            $resultArray[] = $key;
        }
        //Devolvemos el array convertido a utf8
        return $this->convertirUTF8($resultArray);
    }

    //Funcion para ejecutar una consulta a la base de datos (puede ser guardar, actualizar o borrar datos)
    public function nonQuery($sqlstring){
        $results = $this->conexion->query($sqlstring);
        return $this->conexion->affected_rows;
    }

    //Funcion para ejecutar consulta y devolver el id de la fila afectada. Si no ha hecho cambios, devuelve 0
    public function nonQueryId($sqlstr){
        $results = $this->conexion->query($sqlstr);
         $filas = $this->conexion->affected_rows;
         if($filas >= 1){
            return $this->conexion->insert_id;
         }else{
             return 0;
         }
    }

}

?>
