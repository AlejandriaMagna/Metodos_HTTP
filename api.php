<?php
header('Content-Type: application/json');
include 'db.php'; // Incluye el archivo de conexión a la base de datos


$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo == 'GET') {
    $sql = "SELECT * FROM proyectos";
    $resultado = $conn->query($sql);
    $proyectos = [];

    if ($resultado->num_rows > 0) {
        while($fila = $resultado->fetch_assoc()) {
            $proyectos[] = $fila;
        }
    }
    echo json_encode($proyectos);

} elseif ($metodo == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $nombre = $data['nombre'];
    $imagen = $data['imagen'];
    $descripcion = $data['descripcion'];
    $url_github = $data['url_github'];
    $url_produccion = $data['url_produccion'];

    $sql = "INSERT INTO proyectos (nombre, imagen, descripcion, url_github, url_produccion)
            VALUES ('$nombre', '$imagen', '$descripcion', '$url_github', '$url_produccion')";

    if ($conn->query($sql) === TRUE) {
        echo json_encode(["mensaje" => "Proyecto creado correctamente"]);
    } else {
        echo json_encode(["error" => "Error al crear el proyecto"]);
    }
}



elseif ($metodo == 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (
        isset($data['id']) && isset($data['nombre']) && isset($data['imagen']) &&
        isset($data['descripcion']) && isset($data['url_github']) && isset($data['url_produccion'])
    ) {
        $id = $data['id'];
        $nombre = $data['nombre'];
        $imagen = $data['imagen'];
        $descripcion = $data['descripcion'];
        $url_github = $data['url_github'];
        $url_produccion = $data['url_produccion'];

        $sql = "UPDATE proyectos SET nombre='$nombre', imagen='$imagen', descripcion='$descripcion', 
                url_github='$url_github', url_produccion='$url_produccion' WHERE id=$id";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["mensaje" => "Proyecto actualizado correctamente"]);
        } else {
            echo json_encode(["error" => "Error al actualizar el proyecto"]);
        }
    } else {
        echo json_encode(["error" => "Faltan datos para actualizar el proyecto"]);
    }

} elseif ($metodo == 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['id'])) {
        $id = $data['id'];

        $sql = "DELETE FROM proyectos WHERE id=$id";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(["mensaje" => "Proyecto eliminado correctamente"]);
        } else {
            echo json_encode(["error" => "Error al eliminar el proyecto"]);
        }
    } else {
        echo json_encode(["error" => "Falta el id para eliminar el proyecto"]);
    }
}
 

elseif ($metodo == 'PATCH') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['id'])) {
        $id = $data['id'];
        $campos = [];

        // Solo agrega los campos que vienen en la petición
        if (isset($data['nombre'])) $campos[] = "nombre='" . $data['nombre'] . "'";
        if (isset($data['imagen'])) $campos[] = "imagen='" . $data['imagen'] . "'";
        if (isset($data['descripcion'])) $campos[] = "descripcion='" . $data['descripcion'] . "'";
        if (isset($data['url_github'])) $campos[] = "url_github='" . $data['url_github'] . "'";
        if (isset($data['url_produccion'])) $campos[] = "url_produccion='" . $data['url_produccion'] . "'";

        if (count($campos) > 0) {
            $sql = "UPDATE proyectos SET " . implode(', ', $campos) . " WHERE id=$id";
            if ($conn->query($sql) === TRUE) {
                echo json_encode(["mensaje" => "Proyecto actualizado parcialmente"]);
            } else {
                echo json_encode(["error" => "Error al actualizar el proyecto"]);
            }
        } else {
            echo json_encode(["error" => "No se enviaron campos para actualizar"]);
        }
    } else {
        echo json_encode(["error" => "Falta el id para actualizar el proyecto"]);
    }
}

elseif ($metodo == 'OPTIONS') {
    header('Allow: GET, POST, PUT, PATCH, DELETE, OPTIONS, HEAD');
    header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS, HEAD');
    header('Access-Control-Allow-Headers: Content-Type');
    http_response_code(204); // Sin contenido
    exit;
}


elseif ($metodo == 'HEAD') {
    header('Content-Type: application/json');
    http_response_code(200); // OK
    exit; // No envía cuerpo, solo los headers
}