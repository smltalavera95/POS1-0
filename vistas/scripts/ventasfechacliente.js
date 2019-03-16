//Variable para guardar los datatables
var tabla;

//Funcion que se ejecuta al inicio
function init(){
	listar();
	$.post("../ajax/venta.php?op=selectCliente", function(r){
                $("#idcliente").html(r);
                $('#idcliente').selectpicker('refresh');
    }); 
}


//Funcion listar
function listar(){

	var fecha_inicio=$("#fecha_inicio").val();
	var fecha_fin=$("#fecha_fin").val();
	var idcliente=$("#idcliente").val()
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
				url:'../ajax/consultas.php?op=ventasFechaCliente',//Obtenemos valores mediante la url
				data:{fecha_inicio: fecha_inicio,fecha_fin: fecha_fin,idcliente: idcliente},
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