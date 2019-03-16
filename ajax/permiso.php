<?php  

require_once "../modelos/Permiso.php";//Require_once para incluir una sola vez la clase

$permiso = new Permiso();


//Cuando hagan un llamado a este archivo ajax, envien una operacion por una url, va saber que valor ejecutar
switch ($_GET["op"]){
	

	case 'listar':
		$respuesta=$permiso->listar();
		$data=Array();//Para almacenar todo lo que se va a listar
		while ($reg=$respuesta->fetch_object()){
			$data[]=array(
				
				"0"=>$reg->nombre,
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