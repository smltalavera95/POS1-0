<?php 

//Incluir la conexion
require "../config/conexion.php";

Class Categoria {
	//Constructor
	public function _construct(){

	}

	//Insertar registro
	public function insertar($nombre, $descripcion){
		$sql= "INSERT INTO categoria(nombre,descripcion,condicion) VALUES ('$nombre', '$descripcion', '1')";//Los atributos van comillas simples
		return ejecutarConsulta($sql);

	}

	//Editar los registros
	public function editar ($idcategoria, $nombre, $descripcion){
		$sql="UPDATE categoria SET nombre='$nombre', descripcion='$descripcion' WHERE idcategoria='$idcategoria'";
		return ejecutarConsulta($sql);//Retorna 1 si funciono
	}

	//Desactivar categoria
	public function desactivar($idcategoria){
		$sql="UPDATE categoria SET condicion='0' WHERE idcategoria='$idcategoria'";
		return ejecutarConsulta($sql);
	}
	//Activar categoria
	public function activar($idcategoria){
		$sql="UPDATE categoria SET condicion='1' WHERE idcategoria='$idcategoria'";
		return ejecutarConsulta($sql);
	}

	//Mostrar datos de un registro
	public function mostrar($idcategoria){
		$sql="SELECT * FROM categoria WHERE idcategoria='$idcategoria'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Listar registros
	public function listar(){
		$sql="SELECT * FROM categoria";
		return ejecutarConsulta($sql);
	}
	//Funcion para listar registro y llenar el select de articulo.js
	public function select(){
		$sql="SELECT * FROM categoria where condicion=1";
		return ejecutarConsulta($sql);
	}
}
?>