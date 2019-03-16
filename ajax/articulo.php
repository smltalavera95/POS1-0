<?php  

require_once "../modelos/Articulo.php";//Require_once para incluir una sola vez la clase

$articulo = new Articulo();

$idarticulo=isset($_POST ["idarticulo"])?limpiarCadena($_POST ["idarticulo"]):"";//Valida si existe la variable. Si no existe envia la cadena vacia.
$idcategoria=isset($_POST ["idcategoria"])?limpiarCadena($_POST ["idcategoria"]):"";
$codigo=isset($_POST ["codigo"])?limpiarCadena($_POST ["codigo"]):"";
$nombre=isset($_POST ["nombre"])?limpiarCadena($_POST ["nombre"]):"";
$stock=isset($_POST ["stock"])?limpiarCadena($_POST ["stock"]):"";
$descripcion=isset($_POST ["descripcion"])?limpiarCadena($_POST ["descripcion"]):"";
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
				move_uploaded_file($_FILES["imagen"]["tmp_name"], "../files/articulos/".$imagen);//Subir el archivo que ha seleccionado el usuario
			}
		}
		//Validar si el idarticulo esta vacio, si esta vacio entonces entra a guardar.
		if (empty($idarticulo)){
			$respuesta=$articulo->insertar($idcategoria, $codigo, $nombre, $stock, $descripcion, $imagen);
			echo $respuesta ? "Articulo Registrado" :"Articulo no se pudo registrar. Puede ser que la articulo con ese nombre ya existe. Favor revisar";
		}else{
			$respuesta=$articulo->editar($idarticulo,$idcategoria, $codigo, $nombre, $stock, $descripcion, $imagen);//Se debe enviar idarticulo para editar
			echo $respuesta ? "Articulo Modificado" :"Articulo no se pudo modificar";
		}
	break;

	case 'desactivar':
		$respuesta=$articulo->desactivar($idarticulo);
		echo $respuesta ? "Articulo desactivada" :"Articulo no se pudo desactivar"; 
	break;

	case 'activar':
		$respuesta=$articulo->activar($idarticulo);
		echo $respuesta ? "Articulo activada" :"Articulo no se pudo activar"; 
	break;

	case 'mostrar':
		$respuesta=$articulo->mostrar($idarticulo);
		echo json_encode($respuesta);//Un json por la consulta por fila.
	break;

	case 'listar':
		$respuesta=$articulo->listar();
		$data=Array();//Para almacenar todo lo que se va a listar
		while ($reg=$respuesta->fetch_object()){
			$data[]=array(
				"0"=>($reg->condicion)?'<button class="btn btn-warning" onclick="mostrar('.$reg->idarticulo.')"><i class="fa fa-pencil"></i></button>'.
				' <button class= "btn btn-danger" onclick="desactivar('.$reg->idarticulo.')"><i class="fa fa-close"></i></button>':
				'<button class= "btn btn-warning" onclick="mostrar('.$reg->idarticulo.')"><i class="fa fa-pencil"></i></button>'.
				' <button class= "btn btn-primary" onclick="activar('.$reg->idarticulo.')"><i class="fa fa-check"></i></button>',

				"1"=>$reg->nombre,	
				"2"=>$reg->categoria,//Se llama mediante el alias
				"3"=>$reg->codigo,
				"4"=>$reg->stock,
				"5"=>"<img src='../files/articulos/".$reg->imagen."' height='50px' width='50px'>",
				"6"=>($reg->condicion)?'<span class="label bg-green">Activado</span>':'<span class="label bg-red">Desactivado</span>'//Para poner un label activado o desactivado
			);
		}
		$results = array(
			"sEcho"=>1,//Informacion para datatables
			"iTotalRecors"=> count ($data),//enviamos el total registros al datatables
			"iTotalDisplayRecords"=>count($data),//enviamos el total registros a visualizar
			"aaData"=>$data);
		echo json_encode($results);
	break;

	case "selectCategoria":
		require_once "../modelos/Categoria.php";
		$categoria = new Categoria();
		$rspta = $categoria->select(); //Se almacena los registros devueltos por la funcion select de Categoria.php
		while ($reg = $rspta->fetch_object()) {//Para recorrer todos los registros que esta en la variable respuesta
			echo "<option value=" . $reg->idcategoria . ">" . $reg->nombre . "</option>";//Implementa las opciones que va tener la funcion select donde va tener todos los registros devueltos
		}
		break;
}
?>