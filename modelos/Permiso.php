<?php 

//Incluir la conexion
require "../config/conexion.php";

Class Permiso {
	//Constructor
	public function _construct(){

	}

	
	//El usuario solo podra listar los permisos ya que ne la base de datos es donde se crearan los permisos
	public function listar(){
		$sql="SELECT * FROM permiso";
		return ejecutarConsulta($sql);
	}

}
?>