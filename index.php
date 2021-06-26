<?php
//Añadimos la clase conexion.php
require_once "clases/conexion/conexion.php";

//Declaramos la variable conexión de la clase para comprobar que funciona
$conexion = new conexion;

$query = "INSERT INTO productos (descripcion,precio)VALUE('Anillo compromiso',219.0)";

print_r($conexion->nonQueryId($query));

?>