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
	$("#nombre").val("");
	$("#num_documento").val("");
	$("#direccion").val("");
	$("#telefono").val("");
	$("#email").val("");
	$("#idpersona").val("");
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
				url:'../ajax/persona.php?op=listarc',//Obtenemos valores mediante la url
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
		url: "../ajax/persona.php?op=guardaryeditar",
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

//Funcion mostrar datos necesarios para editar registro
function mostrar(idpersona){
	$.post("../ajax/persona.php?op=mostrar",{idpersona : idpersona}, function (data, status)
	{
		data= JSON.parse(data);//Esta convertiendo los datos de la URL a JS
		mostrarform(true);
		$("#nombre").val(data.nombre);
		$("#tipo_documento").val(data.tipo_documento);
		$("#tipo_documento").selectpicker('refresh');//Como va ser select, se refresca el select, cuando se elige.
		$("#num_documento").val(data.num_documento);
		$("#direccion").val(data.direccion);
		$("#telefono").val(data.telefono);
		$("#email").val(data.email);
		$("#idpersona").val(data.idpersona);
		})
}

//Funcion para eliminar registro
function eliminar(idpersona){
	bootbox.confirm("¿Está seguro de eliminar el cliente?", function (result){
		if (result){
			$.post("../ajax/persona.php?op=eliminar", {idpersona : idpersona}, function(e){
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})//Para confirmacion de desactivacion
}


init();