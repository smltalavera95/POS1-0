<?php 

//Incluir la conexion
require "../config/conexion.php";

Class Usuario {
	//Constructor
	public function _construct(){

	}

	//Insertar registro
	public function insertar($nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $cargo, $login, $clave, $imagen, $permisos){
		$sql= "INSERT INTO usuario(nombre,tipo_documento, num_documento, direccion, telefono, email, cargo, login, clave, imagen, condicion) VALUES ('$nombre', '$tipo_documento', '$num_documento', '$direccion', '$telefono', '$email', '$cargo', '$login', '$clave', '$imagen', '1')";//Los atributos van comillas simples
		//return ejecutarConsulta($sql);
		$idusuarionew=ejecutarConsulta_retornarID($sql);

		$num_elementos=0;
		$sw=true;

		while($num_elementos< count($permisos)){
			
			$sql_detalle= "INSERT INTO usuario_permiso(idusuario, idpermiso) VALUES ('$idusuarionew','$permisos[$num_elementos]')";
			ejecutarConsulta($sql_detalle) or $sw=false;
			$num_elementos=$num_elementos+1;

		}
		return $sw;
	}

	//Editar los registros
	public function editar ($idusuario, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $cargo, $login, $clave, $imagen, $permisos){
		$sql="UPDATE usuario SET nombre='$nombre', tipo_documento='$tipo_documento', num_documento='$num_documento', direccion='$direccion', telefono='$telefono', email='$email', cargo='$cargo', login='$login', clave='$clave', imagen='$imagen' WHERE idusuario='$idusuario'";
		 ejecutarConsulta($sql);
		 //Tiene o no permiso del usuario
		 //Eliminar primero todos los permisos asignados.
		 $sqldel="DELETE FROM usuario_permiso where idusuario='$idusuario'";
		 ejecutarConsulta($sqldel);

		 //Agregar los permisos asignados
		 $num_elementos=0;
		$sw=true;

		while($num_elementos< count($permisos)){
			
			$sql_detalle= "INSERT INTO usuario_permiso(idusuario, idpermiso) VALUES ('$idusuario','$permisos[$num_elementos]')";
			ejecutarConsulta($sql_detalle) or $sw=false;
			$num_elementos=$num_elementos+1;

		}
		return $sw;
	}

	//Desactivar registro
	public function desactivar($idusuario){
		$sql="UPDATE usuario SET condicion='0' WHERE idusuario='$idusuario'";
		return ejecutarConsulta($sql);
	}
	//Activar registro
	public function activar($idusuario){
		$sql="UPDATE usuario SET condicion='1' WHERE idusuario='$idusuario'";
		return ejecutarConsulta($sql);
	}

	//Mostrar datos de un registro
	public function mostrar($idusuario){
		$sql="SELECT * FROM usuario WHERE idusuario='$idusuario'";
		return ejecutarConsultaSimpleFila($sql);
	}

	//Listar registros
	public function listar(){
		$sql="SELECT * FROM usuario";
		return ejecutarConsulta($sql);
	}

	public function listarmarcados($idusuario){
		$sql= "SELECT * FROM usuario_permiso WHERE idusuario='$idusuario'";
		return ejecutarConsulta($sql);
	}

	//Verificar el acceso al sistema
	public function verificar ($login, $clave){
		$sql="SELECT idusuario, nombre, tipo_documento, num_documento, telefono, email, cargo, imagen, login from usuario where login='$login' AND clave='$clave' AND condicion='1'";
		return ejecutarConsulta($sql);
	}
}
?>