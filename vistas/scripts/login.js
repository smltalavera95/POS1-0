//Del formulario con nombre frmAcceso cuando se haga clic en submit, ejecuta la funcion
$("#frmAcceso").on('submit', function(e)
{
	e.preventDefault();//Cuando haga clic se trabaje esta funcion
	logina=$("#logina").val();
	clavea=$("#clavea").val();

	$.post("../ajax/usuario.php?op=verificar",
		{"logina":logina, "clavea":clavea},
		function(data)//Lo que devuelva lo almanacena en el objeto data
		{
			
			
			if(data!="null")//Si devuelve datos
			{
				$(location).attr("href", "escritorio.php");
			}
			else
			{
				bootbox.alert("Usuario y/o contraseña incorrectos")
			}

		});

})