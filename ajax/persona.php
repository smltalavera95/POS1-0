<?php  

require_once "../modelos/Persona.php";//Require_once para incluir una sola vez la clase

$persona = new Persona();

$idpersona=isset($_POST ["idpersona"])?limpiarCadena($_POST ["idpersona"]):"";//Valida si existe la variable. Si no existe envia la cadena vacia.
$tipo_persona=isset($_POST["tipo_persona"])?limpiarCadena($_POST["tipo_persona"]):"";
$nombre=isset($_POST ["nombre"])?limpiarCadena($_POST ["nombre"]):"";
$tipo_documento=isset($_POST["tipo_documento"])?limpiarCadena($_POST["tipo_documento"]):"";
$num_documento=isset($_POST ["num_documento"])?limpiarCadena($_POST ["num_documento"]):"";
$direccion=isset($_POST ["direccion"])?limpiarCadena($_POST ["direccion"]):"";
$telefono=isset($_POST["telefono"])?limpiarCadena($_POST["telefono"]):"";
$email=isset($_POST ["email"])?limpiarCadena($_POST ["email"]):"";

//Cuando hagan un llamado a este archivo ajax, envien una operacion por una url, va saber que valor ejecutar
switch ($_GET["op"]){
	case 'guardaryeditar':
		//Validar si el idcategoria esta vacio, si esta vacio entonces entra a guardar.
		if (empty($idpersona)){
			$respuesta=$persona->insertar($tipo_persona, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email);
			echo $respuesta ? "Persona Registrada" :"Persona no se pudo registrar. Puede ser que la persona con ese nombre ya existe. Favor revisar";
		}else{
			$respuesta=$persona->editar($idpersona, $tipo_persona, $nombre, $tipo_documento, $num_documento, $direccion, $telefono, $email);
			echo $respuesta ? "Persona actualizada" :"Persona no se pudo actualizar";
		}
	break;

	case 'eliminar':
		$respuesta=$persona->eliminar($idpersona);
		echo $respuesta ? "Persona eliminada" :"Persona no se pudo eliminar"; 
	break;

	case 'mostrar':
		$respuesta=$persona->mostrar($idpersona);
		echo json_encode($respuesta);//Un json por la consulta por fila.
	break;

	case 'listarp':
		$respuesta=$persona->listarp();
		$data=Array();//Para almacenar todo lo que se va a listar
		while ($reg=$respuesta->fetch_object()){
			$data[]=array(
				"0"=>'<button class="btn btn-warning" onclick="mostrar('.$reg->idpersona.')"><i class="fa fa-pencil"></i></button>'.
				' <button class= "btn btn-danger" onclick="eliminar('.$reg->idpersona.')"><i class="fa fa-trash"></i></button>',

				"1"=>$reg->nombre,
				"2"=>$reg->tipo_documento,	
				"3"=>$reg->num_documento,
				"4"=>$reg->telefono,	
				"5"=>$reg->email
			);
		}
		$results = array(
			"sEcho"=>1,//Informacion para datatables
			"iTotalRecors"=> count ($data),//enviamos el total registros al datatables
			"iTotalDisplayRecords"=>count($data),//enviamos el total registros a visualizar
			"aaData"=>$data);
		echo json_encode($results);
	break;
	
	case 'listarc':
		$respuesta=$persona->listarc();
		$data=Array();//Para almacenar todo lo que se va a listar
		while ($reg=$respuesta->fetch_object()){
			$data[]=array(
				"0"=>'<button class="btn btn-warning" onclick="mostrar('.$reg->idpersona.')"><i class="fa fa-pencil"></i></button>'.
				' <button class= "btn btn-danger" onclick="eliminar('.$reg->idpersona.')"><i class="fa fa-trash"></i></button>',

				"1"=>$reg->nombre,
				"2"=>$reg->tipo_documento,	
				"3"=>$reg->num_documento,
				"4"=>$reg->telefono,	
				"5"=>$reg->email
			);
		}
		$results = array(
			"sEcho"=>1,//Informacion para datatables
			"iTotalRecors"=> count ($data),//enviamos el total registros al datatables
			"iTotalDisplayRecords"=>count($data),//enviamos el total registros a visualizar
			"aaData"=>$data);
		echo json_encode($results);
	break;
}
?>