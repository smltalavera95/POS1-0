<?php  
if(strlen(session_id())<1)//Valida si hay una sesion abierta
	session_start();
require_once "../modelos/Ingreso.php";//Require_once para incluir una sola vez la clase

$ingreso = new Ingreso();

$idingreso=isset($_POST ["idingreso"])?limpiarCadena($_POST ["idingreso"]):"";//Valida si existe la variable. Si no existe envia la cadena vacia.
$idproveedor=isset($_POST ["idproveedor"])?limpiarCadena($_POST ["idproveedor"]):"";
$idusuario=$_SESSION["idusuario"];
$tipo_comprobante=isset($_POST ["tipo_comprobante"])?limpiarCadena($_POST ["tipo_comprobante"]):"";
$serie_comprobante=isset($_POST ["serie_comprobante"])?limpiarCadena($_POST ["serie_comprobante"]):"";

$num_comprobante=isset($_POST ["num_comprobante"])?limpiarCadena($_POST ["num_comprobante"]):"";
$fecha_hora=isset($_POST ["fecha_hora"])?limpiarCadena($_POST ["fecha_hora"]):"";
$impuesto=isset($_POST ["impuesto"])?limpiarCadena($_POST ["impuesto"]):"";
$total_compra=isset($_POST ["total_compra"])?limpiarCadena($_POST ["total_compra"]):"";

//Cuando hagan un llamado a este archivo ajax, envien una operacion por una url, va saber que valor ejecutar
switch ($_GET["op"]){
	case 'guardaryeditar':
		//Validar si el idcategoria esta vacio, si esta vacio entonces entra a guardar.
		if (empty($idingreso)){
			$respuesta=$ingreso->insertar($idproveedor, $idusuario, $tipo_comprobante, $serie_comprobante, $num_comprobante, $fecha_hora, $impuesto, $total_compra, $_POST["idarticulo"], $_POST["cantidad"], $_POST["precio_compra"], $_POST["precio_venta"]);
			echo $respuesta ? "Ingreso Registrado" :"No se pudieron registrar todos los datos del ingreso.";
		}else{
			
		}
	break;

	case 'anular':
		$respuesta=$ingreso->anular($idingreso);
		echo $respuesta ? "Ingreso Anulado" :"El ingreso no se pudo anular"; 
	break;

	case 'mostrar':
		$respuesta=$ingreso->mostrar($idingreso);
		echo json_encode($respuesta);//Un json por la consulta por fila.
	break;

	case 'listarDetalle':
		$id=$_GET['id'];

		$respuesta =$ingreso->listarDetalle($id);
		$total=0;
		echo '<thead style="background-color: #A9D0F5">
                                <th>Opciones</th>
                                <th>Artículos</th>
                                <th>Cantidad</th>
                                <th>Precio Compra</th>
                                <th>Precio Venta</th>
                                <th>Subtotal</th>
                              </thead>';

		while ($reg=$respuesta->fetch_object()){
			echo '<tr class="filas"><td></td><td>'.$reg->nombre.'</td><td>'.$reg->cantidad.'</td><td>'.$reg->precio_compra.'</td><td>'.$reg->precio_venta.'</td><td>'.$reg->precio_compra*$reg->cantidad.'</td></tr>';
			$total=$total+($reg->precio_compra*$reg->cantidad);
		}

		echo '<tfoot>
                                <th>Total</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th><h4 id="total">₡ '.$total.'</h4><input type="hidden" name="total_compra" id="total_compra"></th>
                              </tfoot>';
	break;

	case 'listar':
		$respuesta=$ingreso->listar();
		$data=Array();//Para almacenar todo lo que se va a listar
		while ($reg=$respuesta->fetch_object()){
			$data[]=array(
				"0"=>($reg->estado=='Aceptado')?'<button class="btn btn-warning" onclick="mostrar('.$reg->idingreso.')"><i class="fa fa-eye"></i></button>'.
				' <button class= "btn btn-danger" onclick="anular('.$reg->idingreso.')"><i class="fa fa-close"></i></button>':
				'<button class= "btn btn-warning" onclick="mostrar('.$reg->idingreso.')"><i class="fa fa-eye"></i></button>',

				"1"=>$reg->fecha,	
				"2"=>$reg->proveedor,
				"3"=>$reg->usuario,
				"4"=>$reg->tipo_comprobante,
				"5"=>$reg->serie_comprobante.'-'.$reg->num_comprobante,
				"6"=>$reg->total_compra,
				"7"=>($reg->estado=='Aceptado')?'<span class="label bg-green">Aceptado</span>':'<span class="label bg-red">Anulado</span>'//Para poner un label Aceptado o anulado
			);
		}
		$results = array(
			"sEcho"=>1,//Informacion para datatables
			"iTotalRecors"=> count ($data),//enviamos el total registros al datatables
			"iTotalDisplayRecords"=>count($data),//enviamos el total registros a visualizar
			"aaData"=>$data);
		echo json_encode($results);
	break;


	case "selectProveedor":	
		require_once "../modelos/Persona.php";
		$persona= new Persona();
		$rspta=$persona->listarP();


		while ($reg=$rspta->fetch_object()) {
			echo '<option value='. $reg->idpersona . '>' . $reg->nombre . '</option>';
		}
	break;


	case "listarArticulos":

		require_once "../modelos/Articulo.php";
		$articulo=new Articulo();
		$rspta=$articulo->listarActivos();


		$data=Array();//Para almacenar todo lo que se va a listar
		while ($reg=$rspta->fetch_object()){
			$data[]=array(
				"0"=>'<button class="btn btn-warning" onclick="agregarDetalle('.$reg->idarticulo.',\''.$reg->nombre.'\')"><span class="fa fa-plus"></span></button>',

				"1"=>$reg->nombre,	
				"2"=>$reg->categoria,//Se llama mediante el alias
				"3"=>$reg->codigo,
				"4"=>$reg->stock,
				"5"=>"<img src='../files/articulos/".$reg->imagen."' height='50px' width='50px'>"

				
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