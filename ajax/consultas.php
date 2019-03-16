<?php  

require_once "../modelos/Consultas.php";//Require_once para incluir una sola vez la clase

$consulta = new Consultas();



//Cuando hagan un llamado a este archivo ajax, envien una operacion por una url, va saber que valor ejecutar
switch ($_GET["op"]){
	
	case 'comprasFecha':
	//Obtener las dos variables
	$fecha_inicio=$_REQUEST["fecha_inicio"];
	$fecha_fin=$_REQUEST["fecha_fin"];
		$respuesta=$consulta->comprasFecha($fecha_inicio, $fecha_fin);
		$data=Array();//Para almacenar todo lo que se va a listar
		while ($reg=$respuesta->fetch_object()){
			$data[]=array(
				"0"=>$reg->fecha,

				"1"=>$reg->usuario,	
				"2"=>$reg->proveedor,
				"3"=>$reg->tipo_comprobante,	
				"4"=>$reg->serie_comprobante.' '.$reg->num_comprobante,
				"5"=>$reg->total_compra,	
				"6"=>$reg->impuesto,
				"7"=>($reg->estado=='Aceptado')?'<span class="label bg-green">Aceptado</span>':'<span class="label bg-red">Anulado</span>'//Para poner un label activado o desactivado
			);
		}
		$results = array(
			"sEcho"=>1,//Informacion para datatables
			"iTotalRecors"=> count ($data),//enviamos el total registros al datatables
			"iTotalDisplayRecords"=>count($data),//enviamos el total registros a visualizar
			"aaData"=>$data);
		echo json_encode($results);
	break;

	case 'ventasFechaCliente':
	//Obtener las dos variables
	$fecha_inicio=$_REQUEST["fecha_inicio"];
	$fecha_fin=$_REQUEST["fecha_fin"];
	$idcliente=$_REQUEST["idcliente"];
		$respuesta=$consulta->ventasFechaCliente($fecha_inicio,$fecha_fin,$idcliente);
		$data=Array();//Para almacenar todo lo que se va a listar

		while ($reg=$respuesta->fetch_object()){
			$data[]=array(
				"0"=>$reg->fecha,

				"1"=>$reg->usuario,	
				"2"=>$reg->cliente,
				"3"=>$reg->tipo_comprobante,	
				"4"=>$reg->serie_comprobante.' '.$reg->num_comprobante,
				"5"=>$reg->total_venta,	
				"6"=>$reg->impuesto,
				"7"=>($reg->estado=='Aceptado')?'<span class="label bg-green">Aceptado</span>':'<span class="label bg-red">Anulado</span>'//Para poner un label activado o desactivado
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