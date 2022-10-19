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
    






    // Función para obtener información del usuasrio a editar de la base de datos
    function get_user_to_edit($conn, $path){
        $sql = "SELECT * FROM users WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":id", $path[3]);
        $stmt->execute();
        $users = $stmt->fetch(PDO::FETCH_ASSOC);
        return $users;
    }
    

    // Editar un usuario
    function editar_usuario($conn){
        $user = json_decode(file_get_contents('php://input')); 
        $sql = "UPDATE users SET name = :name, email = :email, mobile=:mobile WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $user->id);
        $stmt->bindParam(':name', $user->name);
        $stmt->bindParam(':email', $user->email);
        $stmt->bindParam(':mobile', $user->mobile);
        if($stmt->execute()){
            $response = ['status'=> 1, 'message'=>'Record updated succesfully!'];
        }else{
            $response = ['status'=> 0, 'message'=>'Failed to update Record!'];
        }
        return $response;
    }
    // Eliminar un usuario
    function eliminar_usuario($conn){
        $sql = "DELETE FROM users WHERE id = :id";
        $path = explode('/', $_SERVER['REQUEST_URI']);
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":id", $path[3]);
        if($stmt->execute()){
            $response = ['status'=> 1, 'message'=>'Record deleted succesfully!'];
        }else{
            $response = ['status'=> 0, 'message'=>'Failed to delete Record!'];
        }
        return $response;
    }


    // Función para calcular el ángulo de inclinación de la linea
    /*
        INPUTS: 
        percentage = el porcentaje
        x0 = coordenada inicial en eje x
        y0 = coordenada inicial en eje y
        l = el largo de la linea
        a0 = el angulo de corte del semicírculo respecto al eje x

        OUTPUTS 
        [x1, y1]
        x1 = coordenada final de la linea en x
        y1 = coordenada final de la linea en y
    */
    // function calcular_coordenadas($percentage, $x0, $y0, $l, $a0){
        
    //     $angulo = calcular_angulo($percentage, $a0);
    //     $xl = round(abs(cos(deg2rad($angulo)))*$l, 0);
    //     $yl = round(abs(sin(deg2rad($angulo)))*$l, 0);
    //     if($angulo < 90){
    //         $x1 = $x0 - $xl;
    //         $y1 = $y0 - $yl;
    //     }else if($angulo > 90){
    //         $x1 = $x0 + $xl;
    //         $y1 = $y0 + $yl;
    //     }else{
    //         $x1 = $x0;
    //         $y1 = $y0 + $l;
    //     }

    //     return [$x1, $y1];
    // }

    // function calcular_angulo($percentage, $angulo_umbral){
    //     $umbral_disponible =round(180-(2*$angulo_umbral), 0);
    //     $angulo = round($percentage*($umbral_disponible/100)) + $angulo_umbral;
    //     return $angulo;
    // }
?>