<?php
//Añadimos la clase conexion.php
require_once "clases/conexion/conexion.php";

//Declaramos la variable conexión de la clase para comprobar que funciona
$conexion = new conexion;

$query = "SELECT * from users";

print_r($conexion->obtenerDatos($query));

?>