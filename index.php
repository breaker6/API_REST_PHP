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
    <h1>Instrucciones API</h1>
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
           GET  /usuarios?page=$numeroPagina
           <br>
           GET  /usuarios?id=$id
        </code>
        <code>
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
           PUT  /usuarios
           <br> 
           {
            <br> 
               "nombre" : "",               
               <br> 
               "email" : "",                      
               <br>         
               "token" : "" ,                -> REQUERIDO        
               <br>       
               "id" : ""   -> REQUERIDO
               <br>
           }
        </code>
        <code>
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