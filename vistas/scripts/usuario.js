//Variable para guardar los datatables
var tabla;

//Funcion que se ejecuta al inicio
function init(){
	mostrarform(false);
	listar();
	$("#formulario").on("submit", function (e){
		guardaryeditar(e);
	});

	$("#imagenmuestra").hide();//oculta la etiqueta img, ya que cuando se va registrar un nuevo articulo no es necesario
	//Muestra los permisos
	$.post("../ajax/usuario.php?op=permisos&id=", function (r){
		$("#permisos").html(r);
	});
}

//Funcion limpiar
function limpiar(){
	$("#nombre").val("");//codigo de barra en modo texto
	$("#num_documento").val("");
	$("#direccion").val("");
	$("#telefono").val("");
	$("#email").val("");
	$("#cargo").val("");	
	$("#login").val("");
	$("#clave").val("");
	$("#imagenmuestra").attr("src","");
	$("#imagenactual").val("");
	$("#idusuario").val("");
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
				url:'../ajax/usuario.php?op=listar',//Obtenemos valores mediante la url
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
		url: "../ajax/usuario.php?op=guardaryeditar",
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
function mostrar(idusuario){
	$.post("../ajax/usuario.php?op=mostrar",{idusuario : idusuario}, function (data, status)
	{
		data= JSON.parse(data);//Esta convertiendo los datos de la URL a JS
		mostrarform(true);
		$("#nombre").val(data.nombre);
		$("tipo_documento").val(data.tipo_documento)
		$("#tipo_documento").selectpicker('refresh');//Para este caso es valido utlizar comilla simple o comillas para referise a un objeto de formulario
		$("#num_documento").val(data.num_documento);
		$("#direccion").val(data.direccion);
		$("#telefono").val(data.telefono);
		$("#email").val(data.email);
		$("#cargo").val(data.cargo);
		$("#login").val(data.login);
		$("#clave").val(data.clave);
		$("#imagenmuestra").show();//Para cuando edite se muestre la imagen muestra
		$("#imagenmuestra").attr("src", "../files/usuarios/"+data.imagen);//en el atributo src para mostrar la imagen
		$("#imagenactual").val(data.imagen);//envia la direccion donde esta la imagen actual
		$("#idusuario").val(data.idusuario);
	
	});
	$.post("../ajax/usuario.php?op=permisos&id="+idusuario, function (r){
		$("#permisos").html(r);
	})
}

//Funcion para desactivar usuario
function desactivar(idusuario){
	bootbox.confirm("¿Está seguro de desactivar el usuario?", function (result){
		if (result){
			$.post("../ajax/usuario.php?op=desactivar", {idusuario : idusuario}, function(e){
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})//Para confirmacion de desactivacion
}

//Funcion para activar usuario
function activar(idusuario){
	bootbox.confirm("¿Está seguro de activar el usuario?", function (result){
		if (result){
			$.post("../ajax/usuario.php?op=activar", {idusuario : idusuario}, function(e){
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})//Para confirmacion de activacion
}


init();