<?php
    require_once '../clases/token.class.php';
    $_token = new token;
    //Fecha que usaremos como referencia para descativar los token. En este caso cogemos fecha y hora y desactivaremos todos los que tengan una fecha inferior
    $fecha = date('Y-m-d H:i');
    echo $_token->actualizarTokens($fecha);
?>