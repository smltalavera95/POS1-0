<?php  

require_once "../modelos/Categoria.php";//Require_once para incluir una sola vez la clase

$categoria = new Categoria();

$idcategoria=isset($_POST ["idcategoria"])?limpiarCadena($_POST ["idcategoria"]):"";//Valida si existe la variable. Si no existe envia la cadena vacia.
$nombre=isset($_POST ["nombre"])?limpiarCadena($_POST ["nombre"]):"";
$descripcion=isset($_POST ["descripcion"])?limpiarCadena($_POST ["descripcion"]):"";

//Cuando hagan un llamado a este archivo ajax, envien una operacion por una url, va saber que valor ejecutar
switch ($_GET["op"]){
	case 'guardaryeditar':
		//Validar si el idcategoria esta vacio, si esta vacio entonces entra a guardar.
		if (empty($idcategoria)){
			$respuesta=$categoria->insertar($nombre, $descripcion);
			echo $respuesta ? "Categoria Registrada" :"Categoria no se pudo registrar. Puede ser que la categoria con ese nombre ya existe. Favor revisar";
		}else{
			$respuesta=$categoria->editar($idcategoria, $nombre, $descripcion);
			echo $respuesta ? "Categoria Modificada" :"Categoria no se pudo modificar";
		}
	break;

	case 'desactivar':
		$respuesta=$categoria->desactivar($idcategoria);
		echo $respuesta ? "Categoria desactivada" :"Categoria no se pudo desactivar"; 
	break;

	case 'activar':
		$respuesta=$categoria->activar($idcategoria);
		echo $respuesta ? "Categoria activada" :"Categoria no se pudo activar"; 
	break;

	case 'mostrar':
		$respuesta=$categoria->mostrar($idcategoria);
		echo json_encode($respuesta);//Un json por la consulta por fila.
	break;

	case 'listar':
		$respuesta=$categoria->listar();
		$data=Array();//Para almacenar todo lo que se va a listar
		while ($reg=$respuesta->fetch_object()){
			$data[]=array(
				"0"=>($reg->condicion)?'<button class="btn btn-warning" onclick="mostrar('.$reg->idcategoria.')"><i class="fa fa-pencil"></i></button>'.
				' <button class= "btn btn-danger" onclick="desactivar('.$reg->idcategoria.')"><i class="fa fa-close"></i></button>':
				'<button class= "btn btn-warning" onclick="mostrar('.$reg->idcategoria.')"><i class="fa fa-pencil"></i></button>'.
				' <button class= "btn btn-primary" onclick="activar('.$reg->idcategoria.')"><i class="fa fa-check"></i></button>',

				"1"=>$reg->nombre,	
				"2"=>$reg->descripcion,
				"3"=>($reg->condicion)?'<span class="label bg-green">Activado</span>':'<span class="label bg-red">Desactivado</span>'//Para poner un label activado o desactivado
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