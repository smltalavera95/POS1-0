//Variable para guardar los datatables
var tabla;

//Funcion que se ejecuta al inicio
function init(){
	mostrarform(false);
	listar();

}



//Funcion mostar formulario. Para tener el listado como el formulario en una pagina html
function mostrarform(flag){
	//limpiar();
	if (flag){
		$("#listadoregistros").hide();
		$("#formularioregistros").show();
		$("#btnGuardar").prop("disabled",false);
		$("#btnagregar").hide();
	}
	else{
		$("#listadoregistros").show();
		$("#formularioregistros").hide();
		$("#btnagregar").hide();
	}
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
				url:'../ajax/permiso.php?op=listar',//Obtenemos valores mediante la url
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

init();