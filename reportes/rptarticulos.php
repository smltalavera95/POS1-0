<?php

//Activar almacenamiento en buffer
ob_start();
if (strlen(session_id())<1)
	session_start();

//Validar el inicio de sesion del usuario
if(!isset ($_SESSION["nombre"])){
  echo 'Debe ingresar al sistema correctamente para visualizar el reporte';
} else{
if($_SESSION['almacen']==1){
//Incluir la clase pdfmc_table
	require('PDF_MC_Table.php');

	//Instanciar para generar documento PDF
	$pdf = new PDF_MC_Table();

	//Primera paagina al documento pdf
	$pdf->AddPage();

	//Margen superior
	$y_axis_initial=25;

	//Tipo de letra y titulo
	$pdf->SetFont('Arial', 'B', 12);
	$pdf->Cell(40,6,'',0,0,'C');
	$pdf->Cell(100,6, 'LISTA DE ARTICULOS', 1,0,'C');
	$pdf->Ln(10);//Rectangulo

	//Celdas para titulos de cada columna 
	$pdf->SetFillColor(232,232,232);//Rellenar fondo de color en gris
	$pdf->SetFont('Arial', 'B', 10);
	$pdf->Cell(58,6,'Nombre', 1,0, 'C', 1);
	$pdf->Cell(50,6, utf8_decode('Categoría'),1,0, 'C', 1);
	$pdf->Cell(30,6,utf8_decode('Código'), 1,0, 'C', 1);
	$pdf->Cell(12,6,'Stock', 1,0, 'C', 1);
	$pdf->Cell(35,6,utf8_decode('Descripción'), 1,0, 'C', 1);
	$pdf->Ln(10);


	//Crear filas de registros segun consulta MySQL
	require_once "../modelos/Articulo.php";
	$articulo = new Articulo();
	$rspta = $articulo->listar();

	//Implementar la tabla con los registros a mostrar
	$pdf->SetWidths(array(58,50,30,12,35));

	while($reg=$rspta->fetch_object()){
		$nombre= $reg->nombre;
		$categoria=$reg->categoria;
		$codigo=$reg->codigo;
		$stock=$reg->stock;
		$descripcion=$reg->descripcion;

		$pdf->SetFont('Arial','',10);
		$pdf->Row(array(utf8_decode($nombre), utf8_decode($categoria),$codigo,$stock, utf8_decode($descripcion)));

		
	}

	$pdf->Output();
}else{
	echo "
<html>
<head>
  <script type='text/javascript'>bootbox.alert('No tiene la autorizacion necesaria para visualizar el reporte')</script>;
  </head></html>
  ";
}


}
ob_end_flush();
?>