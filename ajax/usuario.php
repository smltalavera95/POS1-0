<?php  
session_start();
require_once "../modelos/Usuario.php";//Require_once para incluir una sola vez la clase

$usuario = new usuario();

$idusuario=isset($_POST ["idusuario"])?limpiarCadena($_POST ["idusuario"]):"";//Valida si existe la variable. Si no existe envia la cadena vacia.
$nombre=isset($_POST ["nombre"])?limpiarCadena($_POST ["nombre"]):"";
$tipo_documento=isset($_POST ["tipo_documento"])?limpiarCadena($_POST ["tipo_documento"]):"";
$num_documento=isset($_POST ["num_documento"])?limpiarCadena($_POST ["num_documento"]):"";
$direccion=isset($_POST ["direccion"])?limpiarCadena($_POST ["direccion"]):"";
$telefono=isset($_POST ["telefono"])?limpiarCadena($_POST ["telefono"]):"";
$email=isset($_POST ["email"])?limpiarCadena($_POST ["email"]):"";
$cargo=isset($_POST ["cargo"])?limpiarCadena($_POST ["cargo"]):"";
$login=isset($_POST ["login"])?limpiarCadena($_POST ["login"]):"";
$clave=isset($_POST ["clave"])?limpiarCadena($_POST ["clave"]):"";
$imagen=isset($_POST ["imagen"])?limpiarCadena($_POST ["imagen"]):"";

//Cuando hagan un llamado a este archivo ajax, envien una operacion por una url, va saber que valor ejecutar
switch ($_GET["op"]){
	case 'guardaryeditar':
		

	//Validar imagen
		if(!file_exists($_FILES ['imagen']['tmp_name']) || !is_uploaded_file($_FILES['imagen']['tmp_name']))
		{//Si el archivo existe. Si el usuario no ha seleccionado ningun archivo o si no ha sido cargado
			$imagen=$_POST["imagenactual"];//Variable imagen va estar vacida
		} else{
			$ext= explode(".", $_FILES ["imagen"]["name"]);//Obtiene extension
			if ($_FILES['imagen']['type']=="image/jpg" ||$_FILES['imagen']['type']=="image/jpeg"||$_FILES['imagen']['type']=="image/png" ){
				$imagen= round(microtime(true)).'.'.end($ext);//Se va renombrar la imagen mendiante el formato de tiempo
				move_uploaded_file($_FILES["imagen"]["tmp_name"], "../files/usuarios/".$imagen);//Subir el archivo que ha seleccionado el usuario
			}
		}

		//HASH SHA256
		$clavehash=hash("SHA256", $clave);

		//Validar si el idusuario esta vacio, si esta vacio entonces entra a guardar.
		if (empty($idusuario)){
			$respuesta=$usuario->insertar($nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $cargo, $login, $clavehash, $imagen, $_POST['permiso']);
			echo $respuesta ? "Usuario Registrado" :"Usuario no se pudo registrar todos los datos del usuario";
		}else{
			$respuesta=$usuario->editar($idusuario,$nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email, $cargo, $login, $clavehash, $imagen, $_POST['permiso']);//Se debe enviar idusuario para editar
			echo $respuesta ? "Usuario Modificado" :"Usuario no se pudo modificar";
		}
	break;

	case 'desactivar':
		$respuesta=$usuario->desactivar($idusuario);
		echo $respuesta ? "Usuario desactivado" :"Usuario no se pudo desactivar"; 
	break;

	case 'activar':
		$respuesta=$usuario->activar($idusuario);
		echo $respuesta ? "Usuario activado" :"Usuario no se pudo activar"; 
	break;

	case 'mostrar':
		$respuesta=$usuario->mostrar($idusuario);
		echo json_encode($respuesta);//Un json por la consulta por fila.
	break;

	case 'listar':
		$respuesta=$usuario->listar();
		$data=Array();//Para almacenar todo lo que se va a listar
		while ($reg=$respuesta->fetch_object()){
			$data[]=array(
				"0"=>($reg->condicion)?'<button class="btn btn-warning" onclick="mostrar('.$reg->idusuario.')"><i class="fa fa-pencil"></i></button>'.
				' <button class= "btn btn-danger" onclick="desactivar('.$reg->idusuario.')"><i class="fa fa-close"></i></button>':
				'<button class= "btn btn-warning" onclick="mostrar('.$reg->idusuario.')"><i class="fa fa-pencil"></i></button>'.
				' <button class= "btn btn-primary" onclick="activar('.$reg->idusuario.')"><i class="fa fa-check"></i></button>',

				"1"=>$reg->nombre,	
				"2"=>$reg->tipo_documento,//Se llama mediante el alias
				"3"=>$reg->num_documento,
				"4"=>$reg->telefono,
				"5"=>$reg->email,
				"6"=>$reg->login,
				"7"=>"<img src='../files/usuarios/".$reg->imagen."' height='50px' width='50px'>",
				"8"=>($reg->condicion)?'<span class="label bg-green">Activado</span>':'<span class="label bg-red">Desactivado</span>'//Para poner un label activado o desactivado
			);
		}
		$results = array(
			"sEcho"=>1,//Informacion para datatables
			"iTotalRecors"=> count ($data),//enviamos el total registros al datatables
			"iTotalDisplayRecords"=>count($data),//enviamos el total registros a visualizar
			"aaData"=>$data);
		echo json_encode($results);
	break;

	case 'permisos':
	//Obtener permisos de la tabla permisos
		require_once "../modelos/Permiso.php";
		$permiso= new Permiso();
		$rspta=$permiso->listar();

		//obtener la listar los permisos asignados al usuario
		$id=$_GET ['id'];
		$marcados= $usuario->listarmarcados($id);
		$valores=array();//almacena los permisos marcados

		//Almacenamiento de permisos
		while ($per =$marcados->fetch_object()) {
			array_push($valores, $per->idpermiso);
		}


		//Mostrar la lista de permisos en la vista y si estan marcados o no
		while ($reg =$rspta->fetch_object()) {
			$sw= in_array($reg->idpermiso, $valores)? 'checked':'';
			echo '<li> <input type="checkbox" '.$sw.' name="permiso[]" value="'.$reg->idpermiso.'">'.$reg->nombre.'</li>';
		}

	break;

	case 'verificar':
		$logina=$_POST['logina']; //POST para recibir lo que se esta escribiendo el usuario
		$clavea=$_POST['clavea'];

		//Encriptar la clave
		$clavehash=hash ("SHA256", $clavea);
		$rspta=$usuario-> verificar ($logina, $clavehash);
		$fetch = $rspta->fetch_object();

		if(isset ($fetch)){
			//Declarar las variables de sesion
			$_SESSION ['idusuario']=$fetch->idusuario;
			$_SESSION ['nombre']=$fetch->nombre;
			$_SESSION['imagen']=$fetch->imagen;
			$_SESSION['login']=$fetch->login;

			//Obtener los permisos del usuario
			$marcados = $usuario-> listarmarcados($fetch->idusuario);
			
			//Almacenar los permisos	
			$valores= array();
			while($per=$marcados->fetch_object()){
				array_push($valores, $per->idpermiso);
			}

			//Funcion condicional: Accesos del usuario
			in_array(1,$valores)?$_SESSION['escritorio']=1:$_SESSION['escritorio']=0;
	         in_array(2,$valores)?$_SESSION['almacen']=1:$_SESSION['almacen']=0;
	         in_array(3,$valores)?$_SESSION['compras']=1:$_SESSION['compras']=0;
	         in_array(4,$valores)?$_SESSION['ventas']=1:$_SESSION['ventas']=0;
	         in_array(5,$valores)?$_SESSION['acceso']=1:$_SESSION['acceso']=0;
	         in_array(6,$valores)?$_SESSION['consultac']=1:$_SESSION['consultac']=0;
	         in_array(7,$valores)?$_SESSION['consultav']=1:$_SESSION['consultav']=0;
	         

		} else{
			//session_unset();
			//session_destroy();
		}
		echo json_encode($fetch);

	break;

	case 'salir':
	//Limpiar variables de session
	session_unset();
	//Destruir la sesion
	session_destroy();	

	//Redireccionar
	header("Location: ../index.php");

	break;

}
?>