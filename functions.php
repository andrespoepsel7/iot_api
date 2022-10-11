<?php
    // Función para obtener todos los datos de la base de datos
    function obtener_facturas($conn){
        $sql = "SELECT * FROM factura_venta";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $facturas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $facturas;
    }

    // Función para obtener los proyectos asignados a una factura
    function obtener_proyectos($conn){
        $sql = "SELECT * FROM proyecto";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $proyectos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $proyectos;
    }

    // Función para obtener los proyectos asignados a una factura
    function obtener_clientes($conn){
        $sql = "SELECT * FROM cliente";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $clientes = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $clientes;
    }

    function obtener_contratos($conn){
        $sql = "SELECT * FROM registro_contrato_proyecto";
        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $contratos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $contratos;
    }

    // Función para autenticar al usuario y ver si tiene los permisos correspondientes para
    // poder ver el sistema de facturació
    // Códigos de error:
    // -1 Usuario inexistente
    // -2 Clave incorrecta
    // -3 El usuario existe y puso la contraseña correcta pero no tiene los permisos necearios para ver el sistema
    function autenticar_usuario($conn){
        $user_data = json_decode(file_get_contents('php://input')); 
        $sql = "SELECT * FROM usuarios WHERE username = :usuario";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':usuario', $user_data->usuario);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if($usuario){
            // Autenticar al usuario mediante la contraseña
            if($user_data->clave === $usuario['password']){
                // Obtener los permisos
                $id_usuario = $usuario['cod_user'];
                $sql2 = "SELECT * FROM key_user_menu WHERE id_user = :id_usuario and id_menu IN (87, 110)";
                $stmt2 = $conn->prepare($sql2);
                $stmt2->bindParam(':id_usuario', $id_usuario);
                $stmt2->execute();
                $permisos = $stmt2->fetchAll(PDO::FETCH_ASSOC);
                // Si el usuario tiene permiso para acceder se devuelve su información
                if($permisos){
                    return $usuario;
                // El usuario puso los datos correctos pero no tiene permiso para acceder 
                }else{
                    return -3;
                }
            // El usuario puso la contraseña incorrecta    
            }else{
                return -2;
            }
        // El usuario no existe
        }else{
            return -1;
        }
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
    // Crear usuarios
    function crear_usuario($conn){
        $user = json_decode(file_get_contents('php://input')); 
        $sql = "INSERT INTO users(id, name, email, mobile) VALUES(null, :name, :email, :mobile)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name', $user->name);
        $stmt->bindParam(':email', $user->email);
        $stmt->bindParam(':mobile', $user->mobile);
        if($stmt->execute()){
            $response = ['status'=> 1, 'message'=>'Record created succesfully!'];
        }else{
            $response = ['status'=> 0, 'message'=>'Failed to create Record!'];
        }
        return $response;
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