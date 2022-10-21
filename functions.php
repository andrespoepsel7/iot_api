<?php
    // Funciones para el CRUD en la base de datos de IOT
    // Función para obtener todos los datos de la base de datos
    function obtener_usuarios($conn){
        $sql = "SELECT * FROM usuarios";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $usuarios;
    }

    // Función para obtener las lecturas de los sensores
    function obtener_lecturas($conn){
        $sql = "SELECT * FROM lecturas";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $lecturas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $lecturas;
    }

    // Función para obtener solo la ultima fila que indica el estado del LED
    function obtener_lectura($conn){
        $sql = "SELECT * FROM lecturas ORDER BY id DESC LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $lectura = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $lectura;
    }

    // Función para obtener solo la ultima fila que indica el estado del LED
    function obtener_estado($conn){
        $sql = "SELECT * FROM estados ORDER BY id DESC LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $estado = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $estado;
    }

    // Crear usuarios
    function crear_usuario($conn){
        $user = json_decode(file_get_contents('php://input')); 
        $sql = "INSERT INTO usuarios(id, nombre, usuario, clave, telefono, carnet, email, fecha_creacion) VALUES(null, :nombre, :usuario, :clave, :telefono, :carnet, :email, :fecha_creacion)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nombre', $user->nombre);
        $stmt->bindParam(':usuario', $user->usuario);
        $stmt->bindParam(':clave', $user->clave);
        $stmt->bindParam(':telefono', $user->telefono);
        $stmt->bindParam(':carnet', $user->carnet);
        $stmt->bindParam(':email', $user->email);
        $stmt->bindParam(':fecha_creacion', $user->fecha_creacion);
        if($stmt->execute()){
            $response = ['status'=> 1, 'message'=>'Record created succesfully!'];
        }else{
            $response = ['status'=> 0, 'message'=>'Failed to create Record!'];
        }
        return $response;
    }

    // Función para autenticar al usuario y ver si tiene los permisos correspondientes para
    // poder ver el sistema de facturació
    // Códigos de error:
    // -1 Usuario inexistente
    // -2 Clave incorrecta
    function autenticar_usuario($conn){
        $user_data = json_decode(file_get_contents('php://input')); 
        $sql = "SELECT * FROM usuarios WHERE usuario = :usuario";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':usuario', $user_data->usuario);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if($usuario){
            // Autenticar al usuario mediante la contraseña
            if($user_data->clave === $usuario['clave']){
                return $usuario;  
            // El usuario puso la contraseña incorrecta    
            }else{
                return -2;
            }
        // El usuario no existe
        }else{
            return -1;
        }
    }
    // Funcion para guardar las lecturas en la base de datos en la tabla de lecturas
    function guardar_datos($conn){
        $datos = json_decode(file_get_contents('php://input')); 
        $sql = "INSERT INTO lecturas(id, temperatura, humedad) VALUES(null, :temperatura, :humedad)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':temperatura', $datos->temperatura);
        $stmt->bindParam(':humedad', $datos->humedad);
        if($stmt->execute()){
            $response = ['status'=> 1, 'message'=>'Se guardaron los datos correctamente!'];
        }else{
            $response = ['status'=> 0, 'message'=>'No se pudieron giuardar los datos!'];
        }
        return $response;
    }
    
    // Funcion para guardar los cambios de estado que existan en los actuadores
    function guardar_estados($conn){
        $estados = json_decode(file_get_contents('php://input')); 
        $sql = "INSERT INTO estados(id, estado_led) VALUES(null, :estado_led)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':estado_led', $estados->estado_led);
        if($stmt->execute()){
            $response = ['status'=> 1, 'message'=>'Se guardaron los datos correctamente!'];
        }else{
            $response = ['status'=> 0, 'message'=>'No se pudieron giuardar los datos!'];
        }
        return $response;
    }

?>