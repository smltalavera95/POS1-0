<?php

//Activar almacenamiento en buffer
ob_start();
if (strlen(session_id())<1)
	session_start();

//Validar el inicio de sesion del usuario
if(!isset ($_SESSION["nombre"])){
  echo 'Debe ingresar al sistema correctamente para visualizar el reporte';
} else{
if($_SESSION['ventas']==1){
//Incluir la clase pdfmc_table
	require('Factura.php');

	//Datos de empresa
	$logo= "logo.jpg";
	$ext_logo="jpg";
	$empresa= "Celfix Liberia";
	$documento= "00000";
	$direccion="75 metros sur de TOYS Liberia";
	$telefono="+506 2665 2820";
	$email= "celfixliberia@gmail.com";

	//Datos de cabecera de la venta
	require_once "../modelos/Venta.php";
	$venta= new Venta();
	$rsptav= $venta->ventacabecera($_GET["id"]);

	//Valores obtenidos
	$regv = $rsptav->fetch_object();



	//Instanciar para generar documento PDF
	$pdf = new PDF_Invoice('P','mm', 'A4');

	//Primera paagina al documento pdf
	$pdf->AddPage();

	//Enviar datos de la empresa al metodo addSociete de la clase factura
	$pdf->addSociete(utf8_decode($empresa),
						$documento."\n".
						utf8_decode("Direccion: ").utf8_decode($direccion)."\n".
						utf8_decode("Teléfono: ").$telefono."\n" .
						"Email: ".$email,$logo,$ext_logo);
	$pdf->fact_dev( "$regv->tipo_comprobante ", "$regv->serie_comprobante-$regv->num_comprobante" );
	$pdf->temporaire("");
	$pdf->addDate($regv->fecha);

	//Enviar metodos de cliente
	$pdf->addClientAdresse(utf8_decode($regv->cliente), "Domicilio: ".utf8_decode($reg->direccion), $regv->tipo_documento.": ".$regv->num_documento, "Email: ".$regv->email, utf8_decode("Teléfono: ").$regv->telefono);

	//Establecer columnas que va tener la seccion donde se muestra los detalles de la venta

	$cols=array("CODIGO"=>23,
				"DESCRIPCION"=>78,
				"CANTIDAD"=>22,
				"P.U."=>25,
				"DSCTO"=>20,
				"SUBTOTAL"=>22);
	$pdf->addCols ($cols);
	$cols=array("CODIGO"=>"L",
				"DESCRIPCION"=>"L",
				"CANTIDAD"=>"C",
				"P.U."=>"R",
				"DSCTO"=>"R",
				"SUBTOTAL"=>"C");
	$pdf->addLineFormat($cols);
	$pdf->addLineFormat($cols);

	//Actualizar el valor de la coordenada "y", ubicacion donde se empiezan a mostrar los datos
	$y=89;

	//Obtener respueta de todos los datos de la ventana
	$rsptad= $venta->ventadetalle($_GET["id"]);

	while ($regd=$rsptad->fetch_object()){
		$line=array ("CODIGO"=> "$regd->codigo",
					"DESCRIPCION"=> utf8_decode("$regd->articulo"),
					"CANTIDAD"=>"$regd->cantidad",
					"P.U."=>"$regd->precio_venta",
					"DSCTO"=> "$regd->descuento",
					"SUBTOTAL"=>"$regd->subtotal");
		$size=$pdf->addLine($y, $line);
		$y += $size+2;
	}


	//Total en Letras
	require_once "Letras.php";
	$V= new EnLetras();
	$con_letra=strtoupper($V->ValorEnLetras ($regv->total_venta, "Colones"));
	$pdf->addCadreTVAs("---".$con_letra);


	//Mostrar impuesto
	$pdf->addTVAs($regv->impuesto, $regv->total_venta, "C/");
	$pdf->addCadreEurosFrancs("IGV"."$regv->impuesto %");
	$pdf->Output('Reporte de Venta', 'I');

}else{
	echo "No tiene permiso para visualizar el reporte";
}


}
ob_end_flush();
?>