@extends('layout.layout')

@section('contenido')
    <h1 class="h3 mb-2 text-gray-800">Consultas mediante Scripts SQL:</h1>
    <p class="mb-4">
        Puedes ingresar un sentencia en lenguaje SQL para verificar informacion en la conexion actual establecida.
    </p>
    
    <div class="row contResult m-0 p-0">
        <div class="col-12 col-md-4">
            <div>
                <div id="sql-editor"></div>
            </div>
            <script>
                var editor = CodeMirror(document.getElementById("sql-editor"), {
                mode: "text/x-sql",
                theme: "monokai",
                lineNumbers: true
                });
            </script>
            <div class="mb-1">
                <p id="metrica"></p>
            </div>
            <div class="text-right">
                <button type="button" class="btn btn-primary" id="btnConsultar">Consultar</button>
            </div>
        </div>
        <div class="col-12 col-md-8">
            <p class="m-2" id="tableTittle"><b>Resultados de la busqueda:</b></p>
            <div id="resultExcepciones">
            </div>
            <div style="overflow-y: auto !important; max-height: 500px;">
                <div class="table-responsive align p-2">
                    <table class="table table-bordered text-center m-0 p-0" id="theadInfo" width="100px" cellspacing="0">
                        <thead id="theadDatos">
                        </thead>
                        <tbody id="tbodyDatos">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        $("#btnConsultar").click(function() {
            var sqlQuery = editor.getValue(); // Asegúrate de que 'editor' es tu instancia del editor de código
            $.ajax({
                type: "POST",
                url: "/consultar",
                data: {
                    query: sqlQuery,
                    _token: '{{csrf_token()}}' // Necesario para Laravel, para otros frameworks ajusta según sea necesario
                },
                success: function(response) {
                    if(response.datos.length > 0){
                        $('#btnTableModal').prop('disabled', false);

                        // Asumiendo que todos los objetos en 'datos' tienen la misma estructura,
                        // toma las claves del primer objeto para generar los encabezados de la tabla.
                        var encabezados = '';
                        $.each(Object.keys(response.datos[0]), function(i, key) {
                            encabezados += '<th>' + key + '</th>';
                        });
                        $('#theadDatos').html(encabezados);

                        // Generar las filas de la tabla.
                        var filas = '';
                        $.each(response.datos, function(i, fila) {
                            filas += '<tr>';
                            $.each(Object.keys(fila), function(j, key) {
                                filas += '<td>' + (fila[key] != null ? fila[key] : '') + '</td>';
                            });
                            filas += '</tr>';
                        });
                        $('#tbodyDatos').html(filas);
                    }else{
                        $('#metrica').text('');
                        var result = $('<div>').addClass('alert alert-info text-center').attr('role', 'alert').text("No hay resultados para la consulta ingresada.");
                        $('#resultExcepciones').empty();
                        $('#resultExcepciones').append(result);

                        setTimeout(function() {
                            $('#resultExcepciones').empty();
                        }, 4000);
                    }
                    // Aquí puedes manejar la respuesta del servidor, como mostrar un mensaje de éxito/error, etc.
                },
                error: function(xhr, status, error) {
                    var mensajeError = "Error desconocido";
                    if(xhr.responseJSON && xhr.responseJSON.error) {
                        mensajeError = xhr.responseJSON.error;
                    } else {
                        // Si no hay un mensaje de error JSON específico, utiliza el texto de estado del error.
                        mensajeError = error;
                    }
                    $('#metrica').text('');
                    var result = $('<div>').addClass('alert alert-info text-center').attr('role', 'alert').text('Error: ' + mensajeError);
                    $('#resultExcepciones').empty();
                    $('#resultExcepciones').append(result);

                    setTimeout(function() {
                        $('#resultExcepciones').empty();
                    }, 4000);

                }
            });
        });
    });
</script>
