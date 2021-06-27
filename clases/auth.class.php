<?php
require_once 'conexion/conexion.php';
require_once 'respuestas.class.php';

//Creamos la clase auth heredando todos los metodos de la clase conexión
class auth extends conexion{

    //Función de login que recibirá el json enviado por auth.php
    public function login($json){
        //Instanciamos la clase respuestas
        $_respuestas = new respuestas;
        //Convertimos el json recibido en un array
        $datos = json_decode($json,true);
        //Verificamos si hemos recibido el campo usuario y password en el json
        if(!isset($datos['usuario']) || !isset($datos["password"])){
            //error con los campos
            return $_respuestas->error_400();
        }else{
            //todo esta bien. Guardamos los datos recibidos
            $usuario = $datos['usuario'];
            $password = $datos['password'];
            //Enviamos el usuario al metodo obtenerDatosUsuario para comprobar que está en la base de datos
            $datos = $this->obtenerDatosUsuario($usuario);
            if($datos){
                //si existe el usuario
            }else{
                //no existe el usuario
                return $_respuestas->error_200("El usuaro $usuario  no existe ");
            }
        }
    }

    private function obtenerDatosUsuario($email){
        //Creamos la consulta que nos permitirá comprobar si el usuario está en la base de datos
        $query = "SELECT * FROM users WHERE email = '$email'";
        //Usamos el metodo obtenerDatos de la clase que hereda esta, que es conexion.php
        $datos = parent::obtenerDatos($query);
        //Comprobamos si en el array recibido de obtener datos, en la fila 0 hay un campo id
        if(isset($datos[0]["id"])){
            //Si es así, devolvemos los datos
            return $datos;
        }else{
            return 0;
        }
    }

}

?>