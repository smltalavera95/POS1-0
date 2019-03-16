<?php 

//Incluir la conexion
require "../config/conexion.php";

Class Persona {
	//Constructor
	public function _construct(){

	}

	//Insertar registro
	public function insertar($tipo_persona, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email){
		$sql= "INSERT INTO persona(tipo_persona,nombre,tipo_documento, num_documento, direccion, telefono, email) VALUES ('$tipo_persona', '$nombre', '$tipo_documento','$num_documento', '$direccion', '$telefono', '$email')";//Los atributos van comillas simples
		return ejecutarConsulta($sql);

	}

	//Editar los registros
	public function editar ($idpersona, $tipo_persona, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email){
		$sql="UPDATE persona SET  tipo_persona='$tipo_persona', nombre='$nombre', tipo_documento='$tipo_documento', num_documento='$num_documento', direccion='$direccion', telefono='$telefono', email='$email' WHERE idpersona='$idpersona'";
		return ejecutarConsulta($sql);//Retorna 1 si funciono
	}

	//Eliminar registros
	public function eliminar($idpersona){
		$sql="DELETE FROM persona WHERE idpersona='$idpersona'";
		return ejecutarConsulta($sql);
	}

	//Mostrar datos de un registro
	public function mostrar($idpersona){
		$sql="SELECT * FROM persona WHERE idpersona='$idpersona'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Listar registros Proveedor
	public function listarp(){
		$sql="SELECT * FROM persona WHERE tipo_persona='Proveedor'";
		return ejecutarConsulta($sql);
	}
	//Listar registros Clientes
	public function listarc(){
		$sql="SELECT * FROM persona WHERE tipo_persona='Cliente'";
		return ejecutarConsulta($sql);
	}

	
}
?>