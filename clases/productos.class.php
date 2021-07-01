<?php
require_once "conexion/conexion.php";
require_once "respuestas.class.php";

class productos extends conexion {
    //Atributos de la clase. Corresponderan a las columnas de la tabla (o tablas) a las que referenciaremos
    private $table = "productos"; //Nombre de la tabla
    private $productoid = "";
    private $descripcion = "";
    private $precio = "";
    private $token = ""; //Token

    //Funcion para listar productos. El numero de pagina delimitará cuantos se imprimen (de 100 en 100)
    public function listaProductos($pagina = 1){
        $inicio  = 0 ;
        $cantidad = 100;
        //Calculamos desde donde empezaremos a imprimir
        if($pagina > 1){
            $inicio = ($cantidad * ($pagina - 1)) +1 ;
            $cantidad = $cantidad * $pagina;
        }
        //Hacemos la consulta con los delimitadores y obtenemos los datos
        $query = "SELECT * FROM " . $this->table . " limit $inicio,$cantidad";
        $datos = parent::obtenerDatos($query);
        return ($datos);
    }
    //Funcion para listar los datos del producto con el id recibido
    public function obtenerProducto($id){
        $query = "SELECT * FROM " . $this->table . " WHERE id = '$id'";
        return parent::obtenerDatos($query);
    }

    //Funcion que gestiona la inserción en base de datos
    public function post($json){
        //Instanciamos la clase respuestas
        $_respuestas = new respuestas;
        //Convertimos los datos recibidos en un array asociativo
        $datos = json_decode($json,true);

        //Si no hemos recibido el token, daremos un error
        //NOTA: Podriamos omitir la comprobación del token. Para ello, habría que comentar desde la siguiente linea hasta if($arrayToken){ y sus cierres mas abajo
        if(!isset($datos['token'])){
            return $_respuestas->error_401();
        }else{
            $this->token = $datos['token'];
            $arrayToken =   $this->buscarToken();
            if($arrayToken){
                //Comprobamos si hemos recibido los datos requeridos. Si no, daremos un error
                if(!isset($datos['descripcion']) || !isset($datos['precio'])){
                    return $_respuestas->error_400();
                //Si los hemos recibido, los guardaremos en los atributos de la clase
                }else{
                    $this->descripcion = $datos['descripcion'];
                    $this->precio = $datos['precio'];
                    //if(isset($datos['telefono'])) { $this->telefono = $datos['telefono']; }
                    //if(isset($datos['direccion'])) { $this->direccion = $datos['direccion']; }
                    //if(isset($datos['codigoPostal'])) { $this->codigoPostal = $datos['codigoPostal']; }
                    //if(isset($datos['genero'])) { $this->genero = $datos['genero']; }
                    //if(isset($datos['fechaNacimiento'])) { $this->fechaNacimiento = $datos['fechaNacimiento']; }
                    //Insertamos el producto
                    $resp = $this->insertarProducto();
                    //Si recibimos respuesta, la devolvemos (debe ser el id insertado)
                    if($resp){
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "productoId" => $resp
                        );
                        return $respuesta;
                    //Si no hemos recibido respuesta, probablemente haya habido algun error al hacer el insert. Mostraremos el error de error interno
                    }else{
                        return $_respuestas->error_500();
                    }
                }
            //else de if($arrayToken){
            }else{
                return $_respuestas->error_401("El Token que envio es invalido o ha caducado");
            }
        }//else de if(!isset($datos['token'])){
    }
    
    //Funcion que hará la inserción del producto y devolvera el id del producto insertado
    private function insertarProducto(){
        $query = "INSERT INTO " . $this->table . " (descripcion,precio) values ('" . $this->descripcion ."','" . $this->precio . "')"; 
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

        //Si no hemos recibido el token, daremos un error
        //NOTA: Podriamos omitir la comprobación del token. Para ello, habría que comentar desde la siguiente linea hasta if($arrayToken){ y sus cierres mas abajo
        if(!isset($datos['token'])){
            return $_respuestas->error_401();
        }else{
            //Si hemos recibido el token, lo buscaremos con la función buscarToken
            $this->token = $datos['token'];
            $arrayToken =   $this->buscarToken();
            //Si el token existe en la tabla, seguimos
            if($arrayToken){
                //Si no hemos recibido el id, daremos un error
                if(!isset($datos['id'])){
                    return $_respuestas->error_400();
                //Si tenemos el id, verificaremos el resto de campos que hemos recibido
                }else{
                    $this->productoid = $datos['id'];
                    if(isset($datos['descripcion'])) { $this->descripcion = $datos['descripcion']; }
                    if(isset($datos['precio'])) { $this->precio = $datos['precio']; }
                    //Todos los campos comentados son campos de ejemplo
                    //if(isset($datos['dni'])) { $this->dni = $datos['dni']; }
                    //if(isset($datos['telefono'])) { $this->telefono = $datos['telefono']; }
                    //if(isset($datos['direccion'])) { $this->direccion = $datos['direccion']; }
                    //if(isset($datos['codigoPostal'])) { $this->codigoPostal = $datos['codigoPostal']; }
                    //if(isset($datos['genero'])) { $this->genero = $datos['genero']; }
                    //if(isset($datos['fechaNacimiento'])) { $this->fechaNacimiento = $datos['fechaNacimiento']; }
                    
                    //Ejecutamos la modificacion del producto
                    $resp = $this->modificarProducto();
                   
                    //Si hemos recibido respuesta, es que ha ido todo bien. Devolvemos el OK
                    if(isset($resp)){
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "id" => $this->productoid
                        );
                        return $respuesta;
                    }else{
                        return $_respuestas->error_500();
                    }
                }
            //else de if($arrayToken){
            }else{
                return $_respuestas->error_401("El Token que envio es invalido o ha caducado");
            }
        }//else de if(!isset($datos['token'])){
    }

    //Funcion que hará la modificación del producto y devolvera el numero de filas afectadas
    private function modificarProducto(){
        //Creamos la variable auxiliar $primeraCondicion que nos ayudara a construir la sql
        $primeraCondicion = true;
        //Este UPDATE comentado funcionaría si tenemos en cuenta que todos los campos son obligatorios
        //$query = "UPDATE " . $this->table . " SET nombre ='" . $this->nombre . "', email = '" . $this->email . "', estado = '" . $this->estado .
        //Este UPDATE es el que permite modificar solo unos campos y dejar los no rellenados como están
        $query = "UPDATE " . $this->table . " SET ";
        if ($this->descripcion != ""){
            if (!$primeraCondicion){
                $query.=",";
            }
            else{
                $primeraCondicion = false;
            }
            $query .= "descripcion ='" . $this->descripcion . "'";
        }
        if ($this->precio != ""){
            if (!$primeraCondicion){
                $query.=",";
            }
            else{
                $primeraCondicion = false;
            }
            $query .= "precio ='" . $this->precio . "'";
        }
        $query.= " WHERE id = '" . $this->productoid . "'"; 
        $resp = parent::nonQuery($query);

        //Devolvemos el número de filas afectadas
        if($resp >= 1){
             return $resp;
        }else{
            return 0;
        }
    }

    //Funcion para eliminar un producto de la base de datos
    public function delete($json){
        //Instanciamos la clase respuestas
        $_respuestas = new respuestas;
        //Convertimos los datos recibidos en un array asociativo
        $datos = json_decode($json,true);
        
        //Si no hemos recibido el token, daremos un error
        //NOTA: Podriamos omitir la comprobación del token. Para ello, habría que comentar desde la siguiente linea hasta if($arrayToken){ y sus cierres mas abajo
        if(!isset($datos['token'])){
            return $_respuestas->error_401();
        }else{
            $this->token = $datos['token'];
            $arrayToken =   $this->buscarToken();
            if($arrayToken){

                //Si no hemos recibido el id, daremos un error
                if(!isset($datos['id'])){
                    return $_respuestas->error_400();
                //Si tenemos el id, verificaremos el resto de campos que hemos recibido 
                }else{
                    $this->productoid = $datos['id'];
                    //Ejecutamos la eliminación del producto
                    $resp = $this->eliminarProducto();
                    
                    //Si hemos recibido respuesta, es que ha ido todo bien. Devolvemos el OK
                    if(isset($resp)){
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "productoId" => $this->productoid
                        );
                        return $respuesta;
                    }else{
                        return $_respuestas->error_500();
                    }
                }
            //else de if($arrayToken){
            }else{
                return $_respuestas->error_401("El Token que envio es invalido o ha caducado");
            }
        }//else de if(!isset($datos['token'])){ 
    }

    //Función que eliminará al producto y devolverá su id
    private function eliminarProducto(){
        $query = "DELETE FROM " . $this->table . " WHERE id= '" . $this->productoid . "'";
        $resp = parent::nonQuery($query);
        if($resp >= 1 ){
            return $resp;
        }else{
            return 0;
        }
    }

    //Funcion que permite buscar el token recibido para ver si existe o esta inactivo
    private function buscarToken(){
        $query = "SELECT  id,usuarioId,estado from usuarios_token WHERE token = '" . $this->token . "' AND Estado = 'Activo'";
        $resp = parent::obtenerDatos($query);
        if($resp){
            return $resp;
        }else{
            return 0;
        }
    }

    //Funcion que permite actualizar el token
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