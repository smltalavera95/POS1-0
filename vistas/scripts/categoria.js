//Variable para guardar los datatables
var tabla;

//Funcion que se ejecuta al inicio
function init(){
	mostrarform(false);
	listar();
	$("#formulario").on("submit", function (e){
		guardaryeditar(e);
	});

}

//Funcion limpiar
function limpiar(){
	$("#idcategoria").val("");
	$("#nombre").val("");
	$("#descripcion").val("");
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
				url:'../ajax/categoria.php?op=listar',//Obtenemos valores mediante la url
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
		url: "../ajax/categoria.php?op=guardaryeditar",
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

//Funcion mostrar datos necesarios para editar categoria
function mostrar(idcategoria){
	$.post("../ajax/categoria.php?op=mostrar",{idcategoria : idcategoria}, function (data, status)
	{
		data= JSON.parse(data);//Esta convertiendo los datos de la URL a JS
		mostrarform(true);
		$("#nombre").val(data.nombre);
		$("#descripcion").val(data.descripcion);
		$("#idcategoria").val(data.idcategoria);
	})
}

//Funcion para desactivar categoria
function desactivar(idcategoria){
	bootbox.confirm("¿Está seguro de desactivar la categoría?", function (result){
		if (result){
			$.post("../ajax/categoria.php?op=desactivar", {idcategoria : idcategoria}, function(e){
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})//Para confirmacion de desactivacion
}

//Funcion para activar categoria
function activar(idcategoria){
	bootbox.confirm("¿Está seguro de activar la categoría?", function (result){
		if (result){
			$.post("../ajax/categoria.php?op=activar", {idcategoria : idcategoria}, function(e){
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})//Para confirmacion de activacion
}
init();