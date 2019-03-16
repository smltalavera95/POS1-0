<?php 

//Para requerirlo una vez y si ya esta agregado no lo volvera a pedir
require_once "global.php";

$conexion= new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);


//Consulta a la bases de datos para indicar el conjunto de caracteres
mysqli_query ($conexion, 'SET NAMES "'.DB_ENCODE.'"');

//Revisar si hay un error en la conexion
if (mysqli_connect_errno()){
	printf("Fallo conexion a la bases de datos: %s\n", mysqli_connect_error());
	exit();
}

//Crea la funcion para ejecutar las consultas

if (!function_exists('ejecutarConsulta')){
	function ejecutarConsulta($sql){
		global $conexion;
		$query = $conexion->query($sql);
		return $query;
	}
	//Para retornar una consulta por fila
	function ejecutarConsultaSimpleFila($sql){
		global $conexion;
		$query = $conexion->query($sql);
		$row= $query->fetch_assoc();
		return $row;
	}
	//Devuelve la llave primaria del registro insertado
	function ejecutarConsulta_retornarID($sql){
		global $conexion;
		$query= $conexion->query($sql);
		return $conexion->insert_id;
	}
	//Escapar los caracteres especiales de una cadena para utilizarlo en una sentencia sql
	function limpiarCadena($str){
		global $conexion;
		$str = mysqli_real_escape_string($conexion, trim($str));
		return htmlspecialchars($str);
	}
}
?>