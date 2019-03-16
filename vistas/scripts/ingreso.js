//Variable para guardar los datatables
var tabla;

//Funcion que se ejecuta al inicio
function init(){
	mostrarform(false);
	listar();
	$("#formulario").on("submit", function (e){
		guardaryeditar(e);
	});

	//Mostrar las opciones de los proveedores
	$.post("../ajax/ingreso.php?op=selectProveedor",function(r){
		$("#idproveedor").html(r);
		$('#idproveedor').selectpicker('refresh');
	});

}

//Funcion limpiar
function limpiar(){
	$("#idproveedor").val("");//codigo de barra en modo texto
	$("#proveedor").val("");
	$("#tipo_comprobante").val("Boleta");
	$("#tipo_comprobante").selectpicker('refresh');
	$("#serie_comprobante").val("");
	$("#num_comprobante").val("");
	$("#fecha_hora").val("");
	$("#impuesto").val("0");

	//Fecha Actual
	var now= new Date();
	var day=  ("0"+now.getDate()).slice(-2);
	var month= ("0"+(now.getMonth()+1)).slice(-2);
	var today= now.getFullYear()+"-"+(month)+"-"+(day);
	$('#fecha_hora').val(today);

	$("#total_compra").val("");
	$(".filas").remove();
	$("#total").html("0");
	
}

//Funcion mostar formulario. Para tener el listado como el formulario en una pagina html
function mostrarform(flag){
	limpiar();
	if (flag){
		$("#listadoregistros").hide();
		$("#formularioregistros").show();
		//$("#btnGuardar").prop("disabled",false);
		$("#btnagregar").hide();
		listarArticulos();

		$("#btnGuardar").hide();
		$("#btnCancelar").show();
		detalles=0;
		$("#btnAgregarArt").show();
	}
	else{
		$("#listadoregistros").show();
		$("#formularioregistros").hide();
		$("#btnagregar").show();
	}
}
//Funcion para cancelar form
function cancelarform(){
	limpiar();
	mostrarform(false);
}

//Funcion listar
function listar(){
	tabla=$('#tbllistado').dataTable(
		{
			"aProcessing": true,//Activamos el procesamiento de datatables
			"aServerSide": true,//Paginacion y filtrado realizados por el servidor
			dom: 'Bfrtip',//Definimos los elmentos del control de tabla
			buttons:[
				'copyHtml5',
				'excelHtml5',
				'csvHtml5',
				'pdf'
			],//Botones para exportar

			"ajax":
			{
				url:'../ajax/ingreso.php?op=listar',//Obtenemos valores mediante la url
				type: "get",//Obtener
				dataType: "json",//Codificar en Json
				error: function (e){
					console.log(e.responseText);
				}
			},

			"bDestroy": true,
			"iDisplayLength":5,//Paginacion 
			"order":[[0, "desc"]]//Ordenar los datos
		}).DataTable();
}


function listarArticulos(){
	tabla=$('#tblarticulos').dataTable(
		{
			"aProcessing": true,//Activamos el procesamiento de datatables
			"aServerSide": true,//Paginacion y filtrado realizados por el servidor
			dom: 'Bfrtip',//Definimos los elmentos del control de tabla
			buttons:[
				
			],//Botones para exportar

			"ajax":
			{
				url:'../ajax/ingreso.php?op=listarArticulos',//Obtenemos valores mediante la url
				type: "get",//Obtener
				dataType: "json",//Codificar en Json
				error: function (e){
					console.log(e.responseText);
				}
			},

			"bDestroy": true,
			"iDisplayLength":5,//Paginacion 
			"order":[[0, "desc"]]//Ordenar los datos
		}).DataTable();
}
//Funcion para guardar o editar
function guardaryeditar(e){
	e.preventDefault(); //No se activar la accion predeterminada del evento
	//$("#btnGuardar").prop("disabled", true);
	var formData = new FormData($("#formulario")[0]);
	$.ajax({
		url: "../ajax/ingreso.php?op=guardaryeditar",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,

		success: function(datos){
			bootbox.alert(datos);//Enviar alerta
			mostrarform(false);//Formulario lo ocultara
			listar();//el Datatable lo va recargar
		}
	});
	limpiar();//Limpia los objetos del formulario
}

//Funcion mostrar datos necesarios para editar 
function mostrar(idingreso){
	$.post("../ajax/ingreso.php?op=mostrar",{idingreso : idingreso}, function (data, status)
	{
		data= JSON.parse(data);//Esta convertiendo los datos de la URL a JS
		mostrarform(true);
		$("#idproveedor").val(data.idproveedor);
		$("#idproveedor").selectpicker('refresh');
		$("#tipo_comprobante").val(data.tipo_comprobante);
		$("#tipo_comprobante").selectpicker('refresh');
		$("#serie_comprobante").val(data.serie_comprobante);
		$("#num_comprobante").val(data.num_comprobante);
		$("#fecha_hora").val(data.fecha);
		$("#impuesto").val(data.impuesto);
		$("#idingreso").val(data.idingreso);
		//Ocultar botones y mostrar
		$("#btnGuardar").hide();
		$("#btnCancelar").show();
		$("#btnAgregarArt").hide();
	
	});
	$.post("../ajax/ingreso.php?op=listarDetalle&id="+idingreso, function (r)
	{
		$("#detalles").html(r);
	});

}


//Funcion para anular
function anular(idingreso){
	bootbox.confirm("¿Está seguro de desactivar el ingreso?", function (result){
		if (result){
			$.post("../ajax/ingreso.php?op=anular", {idingreso : idingreso}, function(e){
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})//Para confirmacion de desactivacion
}



//Declaracion de variables necesarias para trabajar con las compras y detalles
 var impuesto=13;
 var cont=0;
 var detalles=0;

//$("#guardar").hide();
$("#btnGuardar").hide();
$("#tipo_comprobante").change(marcarImpuesto);

function marcarImpuesto(){
	var tipo_comprobante=$("#tipo_comprobante option:selected").text();
	if (tipo_comprobante=='Factura' || tipo_comprobante=='Ticket') {
		$("#impuesto").val(impuesto);
	}else{
		$("#impuesto").val("0");
	}
}

function agregarDetalle(idarticulo, articulo){
	var cantidad=1;
	var precio_compra=1;
	var precio_venta=1;

	if (idarticulo!=""){
		//agregar detalles
		var subtotal=cantidad*precio_compra;
		var fila='<tr class="filas" id="fila'+cont+'">'+
		'<td><button type="button" class="btn btn-danger" onclick="eliminarDetalle('+cont+')">X</button></td>'+
		'<td><input type="hidden" name="idarticulo[]" value="'+idarticulo+'">'+articulo+'</input></td>'+
		'<td><input type="number" name="cantidad[]" id="cantidad[]"value="'+cantidad+'"></input></td>'+
		'<td><input type="number" name="precio_compra[]" id="precio_compra[]" value="'+precio_compra+'"></input></td>'+
		'<td><input type="number" name="precio_venta[]" value="'+precio_venta+'"></input></td>'+
		'<td><span name="subtotal" id="subtotal'+cont+'">'+subtotal+'</span></td>'+
		'<td><button type="button" class="btn btn-info" onclick="modificarSubtotales()"><i class="fa fa-refresh"></i></button></td>'+
		'</tr>';
		cont++;
		detalles=detalles+1;
		$('#detalles').append(fila);
		modificarSubtotales();
	}else{
		alert("¡Error al ingresar los detalles del articulo!")
	}
}

function modificarSubtotales(){
	var cant= document.getElementsByName("cantidad[]");
	var prec= document.getElementsByName("precio_compra[]");
	var sub= document.getElementsByName("subtotal");

	for (var i = 0; i <cant.length; i++) {
		var inpC=cant[i];
		var inpP=prec[i];
		var inpS=sub[i];

		inpS.value= inpC.value*inpP.value;

		document.getElementsByName("subtotal")[i].innerHTML=inpS.value;
	}

	calcularTotales();
}

function calcularTotales(){
	var sub= document.getElementsByName("subtotal");
	var total=0.0;

	for (var i = 0; i < sub.length; i++) {
		total +=document.getElementsByName("subtotal")[i].value;
	}
	$("#total").html("₡ "+total);
	$("#total_compra").val(total);
	evaluar();
}

function evaluar(){
	if (detalles>0){
		$("#btnGuardar").show();
	}else{
		$("#btnGuardar").hide();
		cont=0;
	}
}

function eliminarDetalle(indice){
	$("#fila"+indice).remove();
	calcularTotales();
	detalles=detalles-1;
	evaluar();
}



init();