@extends('layout.layout')

@section('contenido')
    <h1 class="h3 mb-2 text-gray-800">Integridad de campos:</h1>
    <p class="mb-4">
        Analizar los valores de la base de datos para el campo elegido de cierta tabla.
    </p>
    <div class="row">
        <div class="m-1 col-12">
            <form action="{{ route('registrosResult') }}" method="post" id="verifyForm">
            @csrf
            <!-- Campos para las credenciales de conexión -->
                <div class="row">
                    <div class="form-group col-12">
                        <label for="tabla">Tabla:</label>
                        <select id="tabla" name="tabla" class="form-control" required>
                            @foreach($tablas as $t)
                                <option value="{{$t}}">{{$t}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col">
                        <label for="columna">Columna:</label>
                        <select id="columna" name="columna" class="form-control" required>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-2 d-flex align-items-center">
                        <p class="px-2">Parametros aceptados: </p>
                    </div>
                    <div class="form-group col-10">
                        <div class="row" id="inputsContainer">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col text-center" id="btnsFormConexion">
                        <button id="btnConectar" type="submit" class="btn btn-primary">Verificar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="row contResult">
        <div class="mb-1">
            <p id="metrica"></p>
        </div>
        <div class="col-12">
            <div class="my-2 text-center">
                <button type="button" class="btn btn-primary" id="btnTableModal" disabled data-toggle="modal" data-target="#tableModal">
                    Ver informacion de registros
                </button>
                <div class="modal fade" id="tableModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                    aria-hiddzen="true">
                    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
                        <div class="modal-content">
                            <p class="m-2" id="tableTittle"></p>
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
            </div>
            <b>Detalles:
            <div id="resultExcepciones" style="overflow-y: auto !important; max-height: 300px;">
            </div>
        </div>
    </div>
@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    //alert($('#excepcion').val());
    $(document).ready(function() {
        //  // Escucha el evento change en el select de excepción
        // if($('#excepcion').val() !== 'secuencia') {
        //     $('#columna').closest('.form-group').hide();
        // }
        // $('#excepcion').change(function(){
        //     if($('#excepcion').val() === 'secuencia') {
        //         $('#columna').closest('.form-group').show();
        //     } else {
        //         $('#columna').closest('.form-group').hide();
        //     }
        // });

        

        $('#verifyForm').submit(function(event) {
            event.preventDefault(); // Previene el envío predeterminado

            // Realiza la petición AJAX
            $.ajax({
                url: $(this).attr('action'), // Obtiene la URL del atributo 'action' del formulario
                type: 'POST',
                data: $(this).serialize(), // Serializa los datos del formulario para el envío,
                success: function(response) {
                    // Encuentra el div donde se insertarán los resultados
                    if(response.datos){
                            $('#btnTableModal').prop('disabled',false);
                            $('#tableTittle').html('<b>Registros de la tabla: ' +  $('#tabla').val())+ '</b>';
                            var encabezados = '';
                            $.each(response.columnas, function(i, columna) {
                                encabezados += '<th>' + columna.Field + '</th>';
                            });
                            $('#theadDatos').html(encabezados); 

                            var filas = '';
                            $.each(response.datos, function(i, fila) {
                                filas += '<tr>';
                                $.each(response.columnas, function(j, columna) {
                                    filas += '<td>' + (fila[columna.Field] != null ? fila[columna.Field] : '') + '</td>';
                                });
                                filas += '</tr>';
                            });
                            $('#tbodyDatos').html(filas); 
                    }
                    if(response.results){
                        if(response.total > 1){
                            if(response.results.length > 0) {
                                $('#metrica').addClass('text-center').text(response.results.length +' excepciones encontradas.');
                                var resultsDiv = $('#resultExcepciones');
                                // Limpia los resultados anteriores
                                resultsDiv.empty();
                                response.results.forEach(function(result) {

                                    // Crear el contenedor principal con las clases 'row' y 'alert alert-danger'
                                    var alertDiv = $('<div>').addClass('row alert alert-danger m-1 p-1').attr('role', 'alert');
                                    // Crear el div para la imagen
                                    var imgDiv = $('<div>').addClass('col-1').append($('<img>').attr('src', 'img/buscar.png').attr('alt', 'Icon Excepciones'));
                                    // Crear el div para el mensaje, usando 'd-flex' y 'align-items-center' para el centrado vertical
                                    var messageDiv = $('<div>').addClass('col-11 d-flex flex-column align-items-center');
                                    var messagePart1 = $('<div>').html('<u>Excepción de secuencialidad encontrada</u>');
                                    var messagePart2 = $('<div>').text(result.message);
                                    
                                    messageDiv.append(messagePart1).append(messagePart2);
                                    alertDiv.append(imgDiv).append(messageDiv);

                                    resultsDiv.append(alertDiv);
                                })
                            }else{
                                $('#metrica').text('');
                                var result = $('<div>').addClass('alert alert-info text-center').attr('role', 'alert').text('No se encontraron resultados de excepciones para la busqueda ingresada.');
                                $('#resultExcepciones').empty();
                                $('#resultExcepciones').append(result);
                            }
                        }
                        else{
                            $('#metrica').text('');
                            var result = $('<div>').addClass('alert alert-info text-center').attr('role', 'alert').text('No se encontraron resultados de excepciones para la busqueda ingresada. Solo hay 0 o 1 registro.');
                            $('#resultExcepciones').empty();
                            $('#resultExcepciones').append(result);
                        }
                    }else{
                        $('#metrica').text('');
                        if(response.exception == 'Secuencia'){
                            var noResult = $('<div>').addClass('alert alert-warning text-center').attr('role', 'alert').text('El campo elegido no cumple con las condiciones para realizar un analisis de secuencialidad.');
                            // $('#resultExcepciones').empty();
                            // $('#resultExcepciones').append(noResult);
                        }else{
                            var noResult = $('<div>').addClass('alert alert-warning text-center').attr('role', 'alert').text('No hay resultados?');
                        }
                        $('#resultExcepciones').empty();
                        $('#resultExcepciones').append(noResult);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error.message);
                }
            });
        });
    });



    $(document).on('change', '#tabla', function() {
        //alert('ssssssssss');
        var selectedTableName = $(this).val(); // Obtiene el valor de la opción seleccionada
        //alert(selectedTableName);
        loadTable(selectedTableName); // Llama a loadTable con ese valor
    });

    
    function loadTable(tableName) {
        $.ajax({
            url: '/cargar-info/'+tableName, // Ruta para cargar la tabla (ajusta según tu aplicación)
            method: 'GET',
            //data: { table: tableName }, // Datos a enviar, por ejemplo el nombre de la tabla
            success: function(response) {
                var $selectColumnas = $('#columna');
                $selectColumnas.empty(); // Limpia las opciones anteriores

                response.columnas.forEach(function(columna) {
                    $selectColumnas.append($('<option>').val(columna.Field).text(columna.Field).attr('data-type', columna.Type)); 
                });

                // Después de actualizar las opciones, dispara el evento 'change' manualmente
                mostrarTipoColumna();
                //$selectColumnas.trigger('change');
                //alert('xxxxxx');

            },
            error: function(xhr, status, error) {
                // Manejo de errores
                alert('error'); // Esto te mostrará la estructura de la respuesta en la consola
            }
        });
    }

    
    $(document).on('change', '#columna', function() {
        mostrarTipoColumna();
    });
    // // Define la función que muestra el tipo de columna
    
    function mostrarTipoColumna() {
    // Obtén el tipo de columna seleccionada del atributo de datos
        $('#inputsContainer').empty();
        var tipoColumna = $('#columna').find('option:selected').data('type');
        //alert('Tipo de Columna:' + tipoColumna);

        const tiposFecha = ['date', 'datetime', 'timestamp', 'time', 'year', 'datetime2', 'smalldatetime', 'datetimeoffset'];

        const tiposStrings = ['varchar', 'text', 'enum', 'set', 'nvarchar', 'nchar', 'ntext'];

        // Ejemplo de uso para verificar si un tipo de columna es una cadena en MySQL o SQL Server
        if(tipoColumna == 'char(1)'){
            $('<div class="form-group col mx-2 mb-3">')
                .append('<label for="valoresInput">Valores aceptados:</label>')
                .append('<input type="text" class="form-control" name="valoresInput" id="valoresInput" placeholder="Ingrese los valores">')
                .appendTo('#inputsContainer');
        }
        if (tiposStrings.some(tipo => tipoColumna.toLowerCase().includes(tipo))) {
            // Crea un solo input para manejar la cadena
            $('<div class="form-group col mx-2 mb-3">')
                .append('<label for="valorInput">Valor aceptado:</label>')
                .append('<input type="text" class="form-control" name="valorInput" id="valorInput" placeholder="Ingrese el valor">')
                .appendTo('#inputsContainer');
        }

        // Ejemplo de uso para verificar si un tipo de columna es una cadena en SQL Server
        if (tiposFecha.some(tipo => tipoColumna.toLowerCase().includes(tipo))) {
            // Crea un solo input para manejar la cadena
            $('<div class="form-group col mx-2 mb-3">')
                .append('<label for="fechaInicioInput">Fecha de inicio:</label>')
                .append('<input type="date" class="form-control" id="fechaInicioInput">')
                .appendTo('#inputsContainer');

            $('<div class="form-group col mx-2 mb-3">')
                .append('<label for="fechaFinInput">Fecha de fin:</label>')
                .append('<input type="date" class="form-control" id="fechaFinInput">')
                .appendTo('#inputsContainer');
        }

        $('<div class="form-check col-2 d-flex align-items-center">')
        .append('<input class="form-check-input" type="checkbox" value="" id="flexCheckDefault">')
        .append('<label class="form-check-label" for="flexCheckDefault">Es null</label>')
        .appendTo('#inputsContainer');
    }
</script>


