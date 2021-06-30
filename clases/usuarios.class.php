<?php
require_once "conexion/conexion.php";
require_once "respuestas.class.php";

class usuarios extends conexion {
    //Atributos de la clase. Corresponderan a las columnas de la tabla (o tablas) a las que referenciaremos
    private $table = "users"; //Nombre de la tabla
    private $usuarioid = "";
    private $nombre = "";
    private $email = "";
    private $estado = "";
    private $token = "";

    //Funcion para listar usuarios. El numero de pagina delimitará cuantos se imprimen (de 100 en 100)
    public function listaUsuarios($pagina = 1){
        $inicio  = 0 ;
        $cantidad = 100;
        //Calculamos desde donde empezaremos a imprimir
        if($pagina > 1){
            $inicio = ($cantidad * ($pagina - 1)) +1 ;
            $cantidad = $cantidad * $pagina;
        }
        //Hacemos la consulta con los delimitadores y obtenemos los datos
        $query = "SELECT id,nombre,email,estado FROM " . $this->table . " limit $inicio,$cantidad";
        $datos = parent::obtenerDatos($query);
        return ($datos);
    }
    //Funcion para listar los datos del usuario con el id recibido
    public function obtenerUsuario($id){
        $query = "SELECT id,nombre,email,estado FROM " . $this->table . " WHERE id = '$id'";
        return parent::obtenerDatos($query);
    }

    //Funcion que gestiona la inserción en base de datos
    public function post($json){
        //Instanciamos la clase respuestas
        $_respuestas = new respuestas;
        //Convertimos los datos recibidos en un array asociativo
        $datos = json_decode($json,true);

        //Si no hemos recibido el token, daremos un error
        /*if(!isset($datos['token'])){
                return $_respuestas->error_401();
        }else{
            $this->token = $datos['token'];
            $arrayToken =   $this->buscarToken();
            if($arrayToken){*/
                //Comprobamos si hemos recibido los datos requeridos. Si no, daremos un error
                if(!isset($datos['nombre']) || !isset($datos['email'])){
                    return $_respuestas->error_400();
                //Si los hemos recibido, los guardaremos en los atributos de la clase
                }else{
                    $this->nombre = $datos['nombre'];
                    $this->email = $datos['email'];
                    //if(isset($datos['telefono'])) { $this->telefono = $datos['telefono']; }
                    //if(isset($datos['direccion'])) { $this->direccion = $datos['direccion']; }
                    //if(isset($datos['codigoPostal'])) { $this->codigoPostal = $datos['codigoPostal']; }
                    //if(isset($datos['genero'])) { $this->genero = $datos['genero']; }
                    //if(isset($datos['fechaNacimiento'])) { $this->fechaNacimiento = $datos['fechaNacimiento']; }
                    //Insertamos el usuario
                    $resp = $this->insertarUsuario();
                    //Si recibimos respuesta, la devolvemos (debe ser el id insertado)
                    if($resp){
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "usuarioId" => $resp
                        );
                        return $respuesta;
                    //Si no hemos recibido respuesta, probablemente haya habido algun error al hacer el insert. Mostraremos el error de error interno
                    }else{
                        return $_respuestas->error_500();
                    }
                }

            /*}else{
                return $_respuestas->error_401("El Token que envio es invalido o ha caducado");
            }
        } */
    }
    
    //Funcion que hará la inserción del usuario y devolvera el id del usuario insertado
    private function insertarUsuario(){
        $query = "INSERT INTO " . $this->table . " (nombre,email) values ('" . $this->nombre ."','" . $this->email . "')"; 
        $resp = parent::nonQueryId($query);
        
        if($resp){
             return $resp;
        }else{
            return 0;
        }
    }
    
    //Funcion que gestiona las modificaciones en base de datos
    public function put($json){
        //Instanciamos la clase respuestas
        $_respuestas = new respuestas;
        //Convertimos los datos recibidos en un array asociativo
        $datos = json_decode($json,true);

        /*if(!isset($datos['token'])){
            return $_respuestas->error_401();
        }else{
            //Si hemos recibido el token, lo buscaremos con la función buscarToken
            $this->token = $datos['token'];
            $arrayToken =   $this->buscarToken();
            //Si el token existe en la tabla, seguimos
            if($arrayToken){*/
                //Si no hemos recibido el id, daremos un error
                if(!isset($datos['id'])){
                    return $_respuestas->error_400();
                //Si tenemos el id, verificaremos el resto de campos que hemos recibido
                }else{
                    $this->usuarioid = $datos['id'];
                    if(isset($datos['nombre'])) { $this->nombre = $datos['nombre']; }
                    if(isset($datos['email'])) { $this->email = $datos['email']; }
                    if(isset($datos['estado'])) { $this->estado = $datos['estado']; }
                    //Todos los campos comentados son campos de ejemplo
                    //if(isset($datos['dni'])) { $this->dni = $datos['dni']; }
                    //if(isset($datos['telefono'])) { $this->telefono = $datos['telefono']; }
                    //if(isset($datos['direccion'])) { $this->direccion = $datos['direccion']; }
                    //if(isset($datos['codigoPostal'])) { $this->codigoPostal = $datos['codigoPostal']; }
                    //if(isset($datos['genero'])) { $this->genero = $datos['genero']; }
                    //if(isset($datos['fechaNacimiento'])) { $this->fechaNacimiento = $datos['fechaNacimiento']; }
                    
                    //Ejecutamos la modificacion del usuario
                    $resp = $this->modificarUsuario();
                    
                    //Si hemos recibido respuesta, es que ha ido todo bien. Devolvemos el OK
                    if(isset($resp)){
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "id" => $this->usuarioid
                        );
                        return $respuesta;
                    }else{
                        return $_respuestas->error_500();
                    }
                }

            /*}else{
                return $_respuestas->error_401("El Token que envio es invalido o ha caducado");
            }
        }*/
    }

    //Funcion que hará la modificación del usuario y devolvera el numero de filas afectadas
    private function modificarUsuario(){
        //Este UPDATE comentado funcionaría si tenemos en cuenta que todos los campos son obligatorios
        //$query = "UPDATE " . $this->table . " SET nombre ='" . $this->nombre . "', email = '" . $this->email . "', estado = '" . $this->estado .
        //Este UPDATE es el que permite modificar solo unos campos y dejar los no rellenados como están
        $query = "UPDATE " . $this->table . " SET ";
        if ($this->nombre != ""){
            $query .= "nombre ='" . $this->nombre . "'";
        }
        if ($this->email != ""){
            $query .= ", email ='" . $this->email . "'";
        }
        if ($this->estado != ""){
            $query .= ", estado ='" . $this->estado . "'";
        }
        $query.= " WHERE id = '" . $this->usuarioid . "'"; 
        $resp = parent::nonQuery($query);

        //Devolvemos el número de filas afectadas
        if($resp >= 1){
             return $resp;
        }else{
            return 0;
        }
    }

    public function delete($json){
        $_respuestas = new respuestas;
        $datos = json_decode($json,true);

        if(!isset($datos['token'])){
            return $_respuestas->error_401();
        }else{
            $this->token = $datos['token'];
            $arrayToken =   $this->buscarToken();
            if($arrayToken){

                if(!isset($datos['id'])){
                    return $_respuestas->error_400();
                }else{
                    $this->pacienteid = $datos['id'];
                    $resp = $this->eliminarPaciente();
                    if($resp){
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "usuarioId" => $this->usuarioid
                        );
                        return $respuesta;
                    }else{
                        return $_respuestas->error_500();
                    }
                }

            }else{
                return $_respuestas->error_401("El Token que envio es invalido o ha caducado");
            }
        }  
    }

    private function eliminarPaciente(){
        $query = "DELETE FROM " . $this->table . " WHERE id= '" . $this->usuarioid . "'";
        $resp = parent::nonQuery($query);
        if($resp >= 1 ){
            return $resp;
        }else{
            return 0;
        }
    }

    private function buscarToken(){
        $query = "SELECT  id,usuarioId,estado from usuarios_token WHERE token = '" . $this->token . "' AND Estado = 'Activo'";
        $resp = parent::obtenerDatos($query);
        if($resp){
            return $resp;
        }else{
            return 0;
        }
    }

    private function actualizarToken($tokenid){
        $date = date("Y-m-d H:i");
        $query = "UPDATE usuarios_token SET fecha = '$date' WHERE id = '$tokenid' ";
        $resp = parent::nonQuery($query);
        if($resp >= 1){
            return $resp;
        }else{
            return 0;
        }
    }

}