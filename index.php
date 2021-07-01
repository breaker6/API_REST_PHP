<?php
//Añadimos la clase conexion.php
require_once "clases/conexion/conexion.php";

//Declaramos la variable conexión de la clase para comprobar que funciona
$conexion = new conexion;

//$query = "INSERT INTO productos (descripcion,precio)VALUE('Anillo compromiso',219.0)";

//print_r($conexion->nonQueryId($query));

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API - Prubebas</title>
    <link rel="stylesheet" href="assets/estilo.css" type="text/css">
</head>
<body>
<div  class="container">
    <h1>API Pedidos</h1>
    <div class="divbody">
        <h3>Introducción</h3>
        <code>
        Esta API de pedidos es una API de ejemplo de como se podría gestionar una base de datos de pedidos. Abajo están listadas todas las funciones que tiene, el tipo de llamada que se ejecuta y los campos que serán requeridos en el json que enviaremos. Sus funciones son:<br>
        - <b>/auth</b>: Permite hacer login en la aplicación<br>
        - <b>/usuarios</b>: Permite gestionar los usuarios que pueden acceder a la aplicación y sus datos<br>
        
        </code>
    </div> 
    <div class="divbody">
        <h3>Auth - login</h3>
        <code>
           POST  /auth
           <br>
           {
               <br>
               "email" :"",  -> REQUERIDO
               <br>
               "password": "" -> REQUERIDO
               <br>
            }
        
        </code>
    </div>      
    <div class="divbody">   
        <h3>Usuarios</h3>
        <code>
           <b>Listar usuarios</b><br><br>
           GET  /usuarios?page=$numeroPagina
           <br>
           GET  /usuarios?id=$id
        </code>
        <code>
           <b>Crear usuarios</b><br><br>
           POST  /usuarios
           <br> 
           {
            <br> 
               "nombre" : "",               -> REQUERIDO
               <br> 
               "email" : "",                  -> REQUERIDO    
               <br>         
               "token" : ""                 -> REQUERIDO        
               <br>       
           }
        </code>
        <code>
           <b>Editar usuarios</b><br><br>
           PUT  /usuarios
           <br> 
           {
            <br> 
               "id" : ""   -> REQUERIDO
               <br>
               "nombre" : "",               
               <br> 
               "email" : "",
               <br> 
               "estado" : "",                         
               <br>         
               "token" : "" ,                -> REQUERIDO        
               <br>         
           }
        </code>
        <code>
           <b>Eliminar usuarios</b><br><br>
           DELETE  /usuarios
           <br> 
           {   
               <br>    
               "token" : "",                -> REQUERIDO        
               <br>       
               "id" : ""   -> REQUERIDO
               <br>
           }
        </code>
    </div>
</div>
    
</body>
</html>