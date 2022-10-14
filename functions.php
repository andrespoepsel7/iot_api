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
        $sql = "INSERT INTO lecturas(id, fecha_lectura, temperatura, humedad) VALUES(null, :fecha_lectura, :temperatura, :humedad)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':fecha_lectura', $datos->fecha_lectura);
        $stmt->bindParam(':temperatura', $datos->temperatura);
        $stmt->bindParam(':humedad', $datos->humedad);
        if($stmt->execute()){
            $response = ['status'=> 1, 'message'=>'Record created succesfully!'];
        }else{
            $response = ['status'=> 0, 'message'=>'Failed to create Record!'];
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
?>