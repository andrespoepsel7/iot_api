<?php

    // utilización de las funciones
    require "functions.php";
    // Reportar los errores que ocurren
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    // Headers para poder hacer requests
    header("Access-Control-Allow-Origin: *");
    header("Access-Control-Allow-Headers: *");
    header("Access-Control-Allow-Methods: *");
    // Se importa y se crea el objeto para conectar con la base de datos
    include 'DbConnect.php';
    $objDb = new DbConnect;
    // Se conecta con la base de datos
    $conn = $objDb->connect();
    
    // Se verifica cual es el metodo de request de HTTP
    $method = $_SERVER['REQUEST_METHOD'];

    // Se hace un switch dependiendo de el método que está siendo solicitado
    switch($method){
        case "GET":
            $path = explode('/', $_SERVER['REQUEST_URI']);

            // Si el método Get contiene como argumento los usuarios
            if(isset($path[2]) && $path[2] === 'usuarios'){
                $facturas = obtener_usuarios($conn);
                echo json_encode($facturas);
            // Si existe otro error no se devuelve nada
            }else{
                echo null;
            } 
            break;

        case "POST":
            $path = explode('/', $_SERVER['REQUEST_URI']);
            if(isset($path[2]) && $path[2] === 'autenticar_usuario'){
                $usuario = autenticar_usuario($conn);
                echo json_encode($usuario);
            }else if(isset($path[2]) && $path[2] === 'crear_usuario'){
                $response = crear_usuario($conn);
                echo json_encode($response);
            }
            // $response = crear_usuario($conn);
            // echo json_encode($response);
            break;
        case "PUT":
            // $response = editar_usuario($conn);
            // echo json_encode($response);
            break;
        case "DELETE":
            // $response = eliminar_usuario($conn);
            // echo json_encode($response);
            break;
    }

?>
