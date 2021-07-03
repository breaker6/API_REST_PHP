<?php
require_once "conexion/conexion.php";
require_once "respuestas.class.php";

class pedidos extends conexion {
    //Atributos de la clase. Corresponderan a las columnas de la tabla (o tablas) a las que referenciaremos
    private $tablepedidos = "pedidos"; //Nombre de la tabla pedidos
    private $pedidoid = "";
    private $usuarioid = "";
    private $direccion = "";
    private $fechapedido = "";
    private $observacionespedido = "";

    private $tablelineas = "pedidos_lineas"; //Nombre de la tabla donde están las lineas de los pedidos
    private $lineaid = "";
    private $productoid = "";
    private $descuento = "";
    private $observacioneslinea = "";


    private $token = ""; //Token

    //Funcion para listar productos. El numero de pagina delimitará cuantos se imprimen (de 100 en 100)
    public function listaPedidos($pagina = 1){
        $inicio  = 0 ;
        $cantidad = 100;
        //Calculamos desde donde empezaremos a imprimir
        if($pagina > 1){
            $inicio = ($cantidad * ($pagina - 1)) +1 ;
            $cantidad = $cantidad * $pagina;
        }
        //Hacemos la consulta con los delimitadores y obtenemos los datos
        $query = "SELECT * FROM " . $this->tablepedidos . " limit $inicio,$cantidad";
        $datos = parent::obtenerDatos($query);
        return ($datos);
    }
    //Funcion para listar los datos del producto con el id recibido
    public function obtenerPedido($id){
        //Consulta para revisar los datos del pedido
        $query = "SELECT * FROM " . $this->tablepedidos . " WHERE id = '$id'";
        //Consulta para recoger las lineas del pedido
        $query2 = "SELECT * FROM " . $this->tablelineas . " WHERE idpedido = '$id'";
        $pedido = [
            'numeropedido' => parent::obtenerDatos($query),
            "lineas" => array(parent::obtenerDatos($query2))
        ];
        return $pedido;
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
                //Si hemos recibido el id del pedido, tendremos que añadir linea al pedido
                if(isset($datos['idpedido'])){
                    //Comprobamos si hemos recibido los datos requeridos. Si no, daremos un error
                    if(!isset($datos['idproducto'])){
                        return $_respuestas->error_400();
                    //Si los hemos recibido, los guardaremos en los atributos de la clase
                    }else{
                        $this->pedidoid = $datos['idpedido'];
                        $this->productoid = $datos['idproducto'];
                        if(isset($datos['observaciones'])) { $this->observacioneslinea = $datos['observaciones']; }
                        if(isset($datos['descuento'])) { $this->descuento = $datos['descuento']; }
            
                        //Insertamos la linea en el pedido
                        $resp = $this->insertarLineaPedido();
                        //Si recibimos respuesta, la devolvemos (debe ser el id insertado)
                        if($resp){
                            $respuesta = $_respuestas->response;
                            $respuesta["result"] = array(
                                "lineaId" => $resp
                            );
                            return $respuesta;
                        //Si no hemos recibido respuesta, probablemente haya habido algun error al hacer el insert. Mostraremos el error de error interno
                        }else{
                            return $_respuestas->error_500();
                        }
                    }

                }
                //Si no hemos recibido el id del pedido, estaremos creando un pedido nuevo
                else{
                    //Comprobamos si hemos recibido los datos requeridos. Si no, daremos un error
                    if(!isset($datos['idusuario']) || !isset($datos['direccion']) || !isset($datos['fecha_pedido'])){
                        return $_respuestas->error_400();
                    //Si los hemos recibido, los guardaremos en los atributos de la clase
                    }else{
                        $this->idusuario = $datos['idusuario'];
                        $this->direccion = $datos['direccion'];
                        $this->fecha_pedido = $datos['fecha_pedido'];
                        if(isset($datos['observaciones'])) { $this->observacionespedido = $datos['observaciones']; }
            
                        //Insertamos el pedido
                        $resp = $this->insertarPedido();
                        //Si recibimos respuesta, la devolvemos (debe ser el id insertado)
                        if($resp){
                            $respuesta = $_respuestas->response;
                            $respuesta["result"] = array(
                                "pedidoId" => $resp
                            );
                            return $respuesta;
                        //Si no hemos recibido respuesta, probablemente haya habido algun error al hacer el insert. Mostraremos el error de error interno
                        }else{
                            return $_respuestas->error_500();
                        }
                    }
                }
            //else de if($arrayToken){
            }else{
                return $_respuestas->error_401("El Token que envio es invalido o ha caducado");
            }
        }//else de if(!isset($datos['token'])){
    }
    
    //Funcion que guardar los datos del pedido en la base de datos
    private function insertarPedido(){
        $query = "INSERT INTO " . $this->tablepedidos . " (idusuario,direccion,fecha_pedido,observaciones) values ('" . $this->idusuario ."','" . $this->direccion . "','" . $this->fecha_pedido . "','" . $this->observacionespedido . "')"; 
        $resp = parent::nonQueryId($query);
        
        if($resp){
             return $resp;
        }else{
            return 0;
        }
    }

    //Funcion que añadirá linea al pedido
    private function insertarLineaPedido(){
        $query = "INSERT INTO " . $this->tablelineas . " (idpedido,idproducto,descuento,observaciones) values ('" . $this->pedidoid ."','" . $this->productoid . "','" . $this->descuento . "','" . $this->observacioneslinea . "')"; 
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
                //Si no hemos recibido ni el id del pedido ni el id de la linea a modificar, daremos un error
                if(!isset($datos['idpedido']) && !isset($datos['idlinea'])){
                    return $_respuestas->error_400();
                //Si tenemos el id del pedido, verificaremos el resto de campos que hemos recibido
                }else if(isset($datos['idpedido'])){
                    $this->pedidoid = $datos['idpedido'];
                    if(isset($datos['idusuario'])) { $this->usuarioid = $datos['idusuario']; }
                    if(isset($datos['direccion'])) { $this->direccion = $datos['direccion']; }
                    if(isset($datos['fecha_pedido'])) { $this->fechapedido = $datos['fecha_pedido']; }
                    if(isset($datos['observaciones'])) { $this->observacionespedido = $datos['observaciones']; }
                    
                    //Ejecutamos la modificacion del pedido
                    $resp = $this->modificarPedido();
                   
                    //Si hemos recibido respuesta, es que ha ido todo bien. Devolvemos el OK
                    if(isset($resp)){
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "idpedido" => $this->pedidoid
                        );
                        return $respuesta;
                    }else{
                        return $_respuestas->error_500();
                    }
                //Si tenemos el id de la linea, verificaremos el resto de campos que hemos recibido
                }else if(isset($datos['idlinea'])){
                    $this->lineaid = $datos['idlinea'];
                    if(isset($datos['idproducto'])) { $this->productoid = $datos['idproducto']; }
                    if(isset($datos['descuento'])) { $this->descuento = $datos['descuento']; }
                    if(isset($datos['observaciones'])) { $this->observacioneslinea = $datos['observaciones']; }

                    //Ejecutamos la modificacion de la linea
                    $resp = $this->modificarLineaPedido();

                    //Si hemos recibido respuesta, es que ha ido todo bien. Devolvemos el OK
                    if(isset($resp)){
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "idlinea" => $this->lineaid
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

    //Funcion que hará la modificación del pedido y devolvera el numero de filas afectadas
    private function modificarPedido(){
        //Creamos la variable auxiliar $primeraCondicion que nos ayudara a construir la sql
        $primeraCondicion = true;
        //Este UPDATE permite modificar solo unos campos y dejar los no rellenados como están
        $query = "UPDATE " . $this->tablepedidos . " SET ";
        if ($this->usuarioid != ""){
            if (!$primeraCondicion){
                $query.=",";
            }
            else{
                $primeraCondicion = false;
            }
            $query .= "idusuario ='" . $this->usuarioid . "'";
        }
        if ($this->direccion != ""){
            if (!$primeraCondicion){
                $query.=",";
            }
            else{
                $primeraCondicion = false;
            }
            $query .= "direccion ='" . $this->direccion . "'";
        }
        if ($this->fechapedido != ""){
            if (!$primeraCondicion){
                $query.=",";
            }
            else{
                $primeraCondicion = false;
            }
            $query .= "fecha_pedido ='" . $this->fechapedido . "'";
        }
        if ($this->observacionespedido != ""){
            if (!$primeraCondicion){
                $query.=",";
            }
            else{
                $primeraCondicion = false;
            }
            $query .= "observaciones ='" . $this->observacionespedido . "'";
        }
        $query.= " WHERE id = '" . $this->pedidoid . "'"; 
        $resp = parent::nonQuery($query);

        //Devolvemos el número de filas afectadas
        if($resp >= 1){
             return $resp;
        }else{
            return 0;
        }
    }

    //Funcion que hará la modificación de la linea del pedido y devolvera el numero de filas afectadas
    private function modificarLineaPedido(){
        //Creamos la variable auxiliar $primeraCondicion que nos ayudara a construir la sql
        $primeraCondicion = true;
        //Este UPDATE permite modificar solo unos campos y dejar los no rellenados como están
        $query = "UPDATE " . $this->tablelineas . " SET ";
        if ($this->productoid != ""){
            if (!$primeraCondicion){
                $query.=",";
            }
            else{
                $primeraCondicion = false;
            }
            $query .= "idproducto ='" . $this->productoid . "'";
        }
        if ($this->descuento != ""){
            if (!$primeraCondicion){
                $query.=",";
            }
            else{
                $primeraCondicion = false;
            }
            $query .= "descuento ='" . $this->descuento . "'";
        }
        if ($this->observacioneslinea != ""){
            if (!$primeraCondicion){
                $query.=",";
            }
            else{
                $primeraCondicion = false;
            }
            $query .= "observaciones ='" . $this->observacioneslinea . "'";
        }
        $query.= " WHERE id = '" . $this->lineaid . "'"; 
        $resp = parent::nonQuery($query);

        //Devolvemos el número de filas afectadas
        if($resp >= 1){
             return $resp;
        }else{
            return 0;
        }
    }

    //Funcion para eliminar un pedido o alguna linea de un pedido de la base de datos
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
                //Si no hemos recibido ni el id del pedido ni el id de la linea a modificar, daremos un error
                if(!isset($datos['idpedido']) && !isset($datos['idlinea'])){
                    return $_respuestas->error_400();
                //Si tenemos el id del pedido, lo guardaremos en el atributo pedidoid de la clase
                }else if(isset($datos['idpedido'])){
                    $this->pedidoid = $datos['idpedido'];
                    //Ejecutamos la eliminación del pedido y todas sus lineas
                    $resp = $this->eliminarPedido();
                        
                    //Si hemos recibido respuesta, es que ha ido todo bien. Devolvemos el OK
                    if(isset($resp)){
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "pedidoId" => $this->pedidoid
                        );
                        return $respuesta;
                    }else{
                        return $_respuestas->error_500();
                    }
                //Si tenemos el id de la linea, lo guardaremos en el atributo lineaid de la clase    
                }else if(isset($datos['idlinea'])){     
                    $this->lineaid = $datos['idlinea'];
                    //Ejecutamos la eliminación de la linea del pedido
                    $resp = $this->eliminarLineaPedido();
                        
                    //Si hemos recibido respuesta, es que ha ido todo bien. Devolvemos el OK
                    if(isset($resp)){
                        $respuesta = $_respuestas->response;
                        $respuesta["result"] = array(
                            "lineaId" => $this->lineaid
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

    //Función que eliminará el pedido y todas sus lineas
    private function eliminarPedido(){
        //Eliminamos todas las lineas asociadas al pedido
        $querylineas = "DELETE FROM " . $this->tablelineas . " WHERE idpedido= '" . $this->pedidoid . "'";
        $resplineas = parent::nonQuery($querylineas);
        //Si recibimos un 0 o numero mayor, es que no ha dado error. Continuamos con el pedido
        if($resplineas >= 0 ){
            //Eliminamos el pedido
            $querypedidos = "DELETE FROM " . $this->tablepedidos . " WHERE id= '" . $this->pedidoid . "'";
            $resppedidos = parent::nonQuery($querypedidos);
            //Devolvemos el número de filas afectadas
            if($resppedidos >= 1){
                 return $resppedidos;
            }else{
                return 0;
            }
        }else{
            return 0;
        }
    }

    //Función que eliminará una linea del pedido
    private function eliminarLineaPedido(){
        $querylineas = "DELETE FROM " . $this->tablelineas . " WHERE id= '" . $this->lineaid . "'";
        $resp = parent::nonQuery($querylineas);
        //Devolvemos el numero de lineas afectadas
        if($resp >= 1){
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