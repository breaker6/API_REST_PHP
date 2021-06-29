<?php
require_once 'conexion/conexion.php';
require_once 'respuestas.class.php';

//Creamos la clase auth heredando todos los metodos de la clase conexión
class auth extends conexion{

    //Función de login que recibirá el json enviado por auth.php
    public function login($json){
        //Instanciamos la clase respuestas que nos mostrará los posibles errores
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
            //La contraseña en la base de datos está encriptada. Para comprobar que son iguales, tendremos
            //que encriptar la que hemos recibido. Usaremos el metodo encriptar que está en
            //clases/conexion.php
            //$password = parent::encriptar($password);
            //Enviamos el usuario al metodo obtenerDatosUsuario para comprobar que está en la base de datos
            $datos = $this->obtenerDatosUsuario($usuario);
            if($datos){
                //si existe el usuario
                //Comprobamos si la contraseña recibida es igual a la de la base de datos
                if($password == $datos[0]['password']){
                    //Verificamos si el estado del usuario es Activo. El estado es una columna en la tabla
                    if($datos[0]['estado'] == "Activo"){
                        //Generamos el token con la función que hemos creado en esta misma clase
                        $verificar  = $this->insertarToken($datos[0]['id']);
                        if($verificar){
                            //Si el token se ha creado y guardado correctamente
                            //Igualamos $result a la respuesta recibida (ok)
                            $result = $_respuestas->response;
                            //Le añadimos el token en el campo result
                            $result["result"] = array(
                            "token" => $verificar
                            );
                            return $result;
                        }else{
                            //error al guardar
                            return $_respuestas->error_500("Error interno, No hemos podido guardar");
                        }
                    }else{
                        //el usuario esta inactivo
                        return $_respuestas->error_200("El usuario esta inactivo");
                    }
                }else{
                    //la contraseña no es igual
                    return $_respuestas->error_200("El password es invalido");
                }
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

    //Función para generar e insertar el token en la base de datos
    private function insertarToken($usuarioid){
        $val = true;
        //Generamos el token
        $token = bin2hex(openssl_random_pseudo_bytes(16,$val));
        //Guardamos en $date la fecha del día. Podría usarse otro formato
        $date = date("Y-m-d H:i");
        //Marcamos el estado del token como Activo
        $estado = "Activo";
        //Hacemos el INSERT en la tabla de los tokens con los datos
        $query = "INSERT INTO usuarios_token (usuarioId,token,estado,fecha)VALUES('$usuarioid','$token','$estado','$date')";
        $verifica = parent::nonQuery($query);
        //Si ha funcionado, devolvemos el token. Si no, devolvemos 0
        if($verifica){
            return $token;
        }else{
            return 0;
        }
    }

}

?>