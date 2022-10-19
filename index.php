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
            }else if(isset($path[2]) && $path[2] === 'lecturas'){
                $lecturas = obtener_lecturas($conn);
                echo json_encode($lecturas);
            }else if(isset($path[2]) && $path[2] === 'lectura'){
                $lectura = obtener_lectura($conn);
                echo json_encode($lectura);
            }else if(isset($path[2]) && $path[2] === 'estado'){
                $estado = obtener_estado($conn);
                echo json_encode($estado);
            }else{
                echo null;
            } 
            break;

        case "POST":
            $uri = $_SERVER['REQUEST_URI'];
            $path = explode('/', $uri);
            if(isset($path[2]) && $path[2] === 'autenticar_usuario'){
                $usuario = autenticar_usuario($conn);
                echo json_encode($usuario);
            }else if(isset($path[2]) && $path[2] === 'crear_usuario'){
                $response = crear_usuario($conn);
                echo json_encode($response);
            }else if(isset($path[2]) && $path[2] === 'guardar_datos'){
                $datos = guardar_datos($conn);
                echo json_encode($datos);
            }else if(isset($path[2]) && $path[2] === 'guardar_estados'){
                $estados = guardar_estados($conn);
                echo json_encode($estados);
            }else{
                echo null;
            }
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
