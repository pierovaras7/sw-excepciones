@extends('layout.layout')

@section('contenido')
    <h1 class="h3 mb-2 text-gray-800">Secuencialidad de Registros:</h1>
    <p class="mb-4">
        Verificar columnas y confirmar su secuencialidad y coherencia.
    </p>
    <div class="row">
        <div class="m-1 col-12">
            <form action="{{ route('registrosResult') }}" method="post" id="verifyForm">
            @csrf
            <!-- Campos para las credenciales de conexión -->
                <div class="row">
                    <div class="form-group col">
                        <label for="excepcion">Tipo de Excepcion:</label>
                        <select id="excepcion" name="excepcion" class="form-control" required>
                            <!-- <option selected value="unicidad">Unicidad</option> -->
                            <option selected value="secuencia">Secuencia</option>
                            <option value="unicidad">Unicidad</option>
                        </select>
                    </div>
                    <div class="form-group col">
                        <label for="tabla">Tabla:</label>
                        <select id="tabla" name="tabla" class="form-control" required>
                            @foreach($tablas as $t)
                                <option value="{{$t}}">{{$t}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col" id="groupcolumna">
                        <label for="columna">Columna:</label>
                        <select id="columna" name="columna" class="form-control">
                        </select>
                    </div>
                    <!-- <div class="form-group col-6 col-md-3">
                        <label for="fecha">Fecha:</label>
                        <input type="date" id="fecha" name="fecha" class="form-control">
                        </input>
                    </div> -->
                    <div class="form-group col-12 text-center" id="btnsFormConexion">
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
            <div class="my-2 text-center row" id="btnsResult">
                <div class="col">
                    <button type="button" class="btn btn-primary" id="btnTableModal" disabled data-toggle="modal" data-target="#tableModal">
                        Ver informacion de registros
                    </button>
                </div>
                <form  method="POST" class="col" id="formResultados">
                    @csrf
                    <!-- Otros campos del formulario -->
                    
                </form>

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
    <div id="contReporte">
    </div>
@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    //alert($('#excepcion').val());
    var resultadosGlobales = null;
    // var routeURL = '/reporteRegistro'
    $(document).ready(function() {
        $('#formResultados').hide();
         // Escucha el evento change en el select de excepción
        if($('#excepcion').val() !== 'secuencia') {
            $('#groupcolumna').hide();
        }
        $('#excepcion').change(function(){
            if($('#excepcion').val() === 'secuencia') {
                $('#groupcolumna').show();
            } else {
                $('#groupcolumna').hide();
            }
        });


        //
        // Adjunta la función de clic al botón para generar el reporte




        $('#verifyForm').submit(function(event) {
            event.preventDefault(); // Previene el envío predeterminado
            // Realiza la petición AJAX
            $.ajax({
                url: $(this).attr('action'), // Obtiene la URL del atributo 'action' del formulario
                type: 'POST',
                data: $(this).serialize(), // Serializa los datos del formulario para el envío,
                success: function(response) {
                    // Mostrar registros de la tabla analizad
                    if (response.error) {
                        // Mostrar el mensaje de error en el div errorContainer
                        //alert(response.message);
                    } else {
                        if(response.results){
                            if(response.results.length > 0) {
                                //
                                resultadosGlobales = response.results;
                                //console.log(resultadosGlobales);
                                //
                                $('#metrica').addClass('text-center').text(response.results.length +' excepciones encontradas.');
                                var resultsDiv = $('#resultExcepciones');
                                // Limpia los resultados anteriores
                                resultsDiv.empty();
                                response.results.forEach(function(result) {

                                    // Crear el contenedor principal con las clases 'row' y 'alert alert-danger'
                                    var alertDiv = $('<div>').addClass('row alert alert-danger m-1 p-1').attr('role', 'alert');
                                    // Crear el div para la imagen
                                    var imgDiv = $('<div>').addClass('col-1').append($('<img>').attr('src', 'img/buscar.png').attr('alt', 'Icon Excepciones'));
                                    //var imgDiv =  $('<div>').addClass('text-center').html('<i class="fa fa-exclamation-circle" aria-hidden="true"></i>');
                                    // Crear el div para el mensaje, usando 'd-flex' y 'align-items-center' para el centrado vertical
                                    var messageDiv = $('<div>').addClass('col-11 d-flex flex-column align-items-center');
                                    if(response.exception == 'Secuencia'){
                                        var messagePart1 = $('<div>').html('<u>Excepción de secuencialidad encontrada</u>');
                                    }else{
                                        var messagePart1 = $('<div>').html('<u>Excepción de unicidad encontrada</u>');
                                    }
                                    var messagePart2 = $('<div>').text(result.message);
                                    
                                    messageDiv.append(messagePart1).append(messagePart2);
                                    alertDiv.append(imgDiv).append(messageDiv);

                                    resultsDiv.append(alertDiv);
                                    
                                    
                                })
                                    var resultadosJSON = JSON.stringify(resultadosGlobales);
                                // Construir el formulario dinámicamente
                                    
                                    // Agregar un campo oculto para el token CSRF
                                    $('#formResultados').show();
                                    console.log(response.exception);
                                    if(response.exception == 'Secuencia'){
                                        $('#formResultados').attr('action', '{{ route("reporteS") }}');
                                    }else{
                                        $('#formResultados').attr('action', '{{ route("reporteU") }}');
                                    }; 
                                    $('#formResultados').empty();
                                    $('#formResultados').attr('target','_blank');
                                    $('#formResultados').html('@csrf');
                                    $('#formResultados').append(
                                        $('<input>', {
                                            type: 'hidden',
                                            name: 'resultados',
                                            value: resultadosJSON
                                        }));
                                    $('#formResultados').append(
                                        $('<button>', {
                                            type: 'submit',
                                            class: 'btn btn-dark',
                                            text: 'Generar Reporte',
                                            id : 'btnReporte'
                                        }));
                                    // Agregar el formulario al elemento con id #btnsResult
                                    //$('#btnsResult').append(formulario);

                                    //$('#btnsResult').append(button);
                            }else{
                                $('#metrica').text('');
                                var result = $('<div>').addClass('alert alert-info text-center').attr('role', 'alert').text('No se encontraron resultados de excepciones.');
                                $('#resultExcepciones').empty();
                                $('#resultExcepciones').append(result);

                                setTimeout(function() {
                                    $('#resultExcepciones').empty();
                                }, 4000);
                            }
                        }else{
                            if(response.errortipo){
                                // No hay resultados y hay error
                                $('#metrica').text('');
                                var noResult = $('<div>').addClass('alert alert-warning text-center').attr('role', 'alert').text(response.errortipo);
                                $('#resultExcepciones').empty();
                                $('#resultExcepciones').append(noResult);

                                setTimeout(function() {
                                        $('#resultExcepciones').empty();
                                }, 4000);
                            }
                            if(response.noresult){
                                // No hay resultados y hay error
                                $('#metrica').text('');
                                var noResult = $('<div>').addClass('alert alert-warning text-center').attr('role', 'alert').text(response.noresult);
                                $('#resultExcepciones').empty();
                                $('#resultExcepciones').append(noResult);

                                setTimeout(function() {
                                        $('#resultExcepciones').empty();
                                }, 4000);
                            }
                            
                        }
                        if(response.datos){
                            if(response.datos.length > 0){
                                //alert('si jhay');
                                $('#btnTableModal').prop('disabled',false);
                                $('#tableTittle').html('<b>Registros de la tabla: ' +  $('#tabla').val())+ '</b>';
                                var encabezados = '';
                                $.each(response.columnas, function(i, columna) {
                                    encabezados += '<th>' + columna.Field + '</th>';
                                });
                                $('#theadDatos').html(encabezados); 
                                var filas = '';
                                $.each(response.results, function(i, fila) {
                                    filas += '<tr>';
                                    $.each(response.columnas, function(j, columna) {
                                        filas += '<td>' + (fila[columna.Field] != null ? fila[columna.Field] : '') + '</td>';
                                    });
                                    filas += '</tr>';
                                });
                                $('#tbodyDatos').html(filas); 
                            }
                            else{
                                $('#metrica').text('');
                                var result = $('<div>').addClass('alert alert-info text-center').attr('role', 'alert').text('La tabla analizada esta vacia.');
                                $('#resultExcepciones').empty();
                                $('#resultExcepciones').append(result);

                                setTimeout(function() {
                                    $('#resultExcepciones').empty();
                                }, 4000);
                            }
                        }
                    }
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

    $(document).on('change', '#excepcion', function() {
        //alert('ssssssssss');
        var resultsDiv = $('#resultExcepciones');
        resultsDiv.empty();
        $('#metrica').text('');
        var btnVer = $('#btnTableModal');
        btnVer.prop('disabled', true);
        $('#formResultados').hide();
    });

    $(document).on('change', '#columna', function() {
        //alert('ssssssssss');
        var resultsDiv = $('#resultExcepciones');
        resultsDiv.empty();
        $('#metrica').text('');
        var btnVer = $('#btnTableModal');
        btnVer.prop('disabled', true);
        $('#formResultados').hide();
    });

    $(document).on('change', '#tabla', function() {
        //alert('ssssssssss');
        var resultsDiv = $('#resultExcepciones');
        resultsDiv.empty();
        $('#metrica').text('');
        var btnVer = $('#btnTableModal');
        btnVer.prop('disabled', true);
        $('#btnReporte').remove();
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
                //alert('ssss')
                var $select = $('#columna'); // Selecciona el elemento <select> por su id
                $select.empty(); // Vacía el <select> para remover opciones previas

                // Itera sobre cada columna en la respuesta
                response.columnas.forEach(function(columna) {
                    // Crea un nuevo elemento <option> y lo añade al <select>
                    // Asume que columna tiene propiedades como 'Field' que quieres mostrar
                    // if(columna.Extra == 'auto_increment'){
                        var $option = $('<option>').val(columna.Field).text(columna.Field);
                        $select.append($option);
                    // }                    
                });
            },
            error: function(xhr, status, error) {
                // Manejo de errores
                alert('error'); // Esto te mostrará la estructura de la respuesta en la consola
            }
        });
    }
</script>


