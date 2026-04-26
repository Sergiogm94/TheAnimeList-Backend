<?php
require_once "bdDatos.php";

function conBD() {
		$conexion =	new mysqli(HOST,USER,PASSWORD,DATABASE);
		if ($conexion -> connect_error){
			die("<br>Error de conexión con la base de datos: " . $conexion -> connect_error);
        }
     // echo "Conexión exitosa";
     return $conexion;
}

?>