//Variable para guardar los datatables
var tabla;

//Funcion que se ejecuta al inicio
function init(){
	mostrarform(false);
	listar();
	$("#formulario").on("submit", function (e){
		guardaryeditar(e);
	});

	//Cargar items al select categoria. Esta va hasta la url que hace referencia
	//Y carga la funcion que se esta llamando
	$.post("../ajax/articulo.php?op=selectCategoria", function (r){
		$("#idcategoria").html(r);//R, opciones que devuelve el archivo articulo.php
		$('#idcategoria').selectpicker('refresh');
	});
	$("#imagenmuestra").hide();//oculta la etiqueta img, ya que cuando se va registrar un nuevo articulo no es necesario

}

//Funcion limpiar
function limpiar(){
	$("#codigo").val("");//codigo de barra en modo texto
	$("#nombre").val("");
	$("#descripcion").val("");
	$("#stock").val("");
	$("#imagenmuestra").attr("src","");
	$("#imagenactual").val("");
	$("#print").hide();
	$("#idarticulo").val("");
}

//Funcion mostar formulario. Para tener el listado como el formulario en una pagina html
function mostrarform(flag){
	limpiar();
	if (flag){
		$("#listadoregistros").hide();
		$("#formularioregistros").show();
		$("#btnGuardar").prop("disabled",false);
		$("#btnagregar").hide();
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
				url:'../ajax/articulo.php?op=listar',//Obtenemos valores mediante la url
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
	$("#btnGuardar").prop("disabled", true);
	var formData = new FormData($("#formulario")[0]);
	$.ajax({
		url: "../ajax/articulo.php?op=guardaryeditar",
		type: "POST",
		data: formData,
		contentType: false,
		processData: false,

		success: function(datos){
			bootbox.alert(datos);//Enviar alerta
			mostrarform(false);//Formulario lo ocultara
			tabla.ajax.reload();//el Datatable lo va recargar
		}
	});
	limpiar();//Limpia los objetos del formulario
}

//Funcion mostrar datos necesarios para editar idcategoriaiculo
function mostrar(idarticulo){
	$.post("../ajax/articulo.php?op=mostrar",{idarticulo : idarticulo}, function (data, status)
	{
		data= JSON.parse(data);//Esta convertiendo los datos de la URL a JS
		mostrarform(true);
		$("#idcategoria").val(data.idcategoria);
		$('#idcategoria').selectpicker('refresh');//Para este caso es valido utlizar comilla simple o comillas para referise a un objeto de formulario
		$("#codigo").val(data.codigo);
		$("#nombre").val(data.nombre);
		$("#stock").val(data.stock);
		$("#descripcion").val(data.descripcion);
		$("#imagenmuestra").show();//Para cuando edite se muestre la imagen muestra
		$("#imagenmuestra").attr("src", "../files/articulos/"+data.imagen);//en el atributo src para mostrar la imagen
		$("#imagenactual").val(data.imagen);//envia la direccion donde esta la imagen actual
		$("#idarticulo").val(data.idarticulo);
		generarbarcode();
	})
}

//Funcion para desactivar articulo
function desactivar(idarticulo){
	bootbox.confirm("¿Está seguro de desactivar el articulo?", function (result){
		if (result){
			$.post("../ajax/articulo.php?op=desactivar", {idarticulo : idarticulo}, function(e){
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})//Para confirmacion de desactivacion
}

//Funcion para activar articulo
function activar(idarticulo){
	bootbox.confirm("¿Está seguro de activar el articulo?", function (result){
		if (result){
			$.post("../ajax/articulo.php?op=activar", {idarticulo : idarticulo}, function(e){
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})//Para confirmacion de activacion
}
function generarbarcode(){
	codigo=$("#codigo").val();
	JsBarcode("#barcode", codigo);
	$("#print").show();	
}

function imprimir(){
	$("#print").printArea();
}

init();