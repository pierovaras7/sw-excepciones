@extends('layout.layout')

@section('contenido')
    <h1 class="h3 mb-2 text-gray-800">Integridad de tablas:</h1>
    <p class="mb-4">
        Analizar los valores de las columnas que hacen referencia a su existencia en otras tablas
    </p>
    <form action="{{ route('tablasResult') }}" method="post" id="verifyForm">
    @csrf
    <!-- Campos para las credenciales de conexión -->
        <div class="row colp justify-content-center pb-2">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="relacion" id="inlineRadio2" value="T" checked>
                <label class="form-check-label" for="inlineRadio2">Cualquier tabla</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="relacion" id="inlineRadio1" value="R">
                <label class="form-check-label" for="inlineRadio1">Tablas relacionadas</label>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-12 col-md-6 colp">
                <div>
                    <label for="tablao">Tabla Origen:</label>
                    <select id="tablao" name="tablao" class="form-control" required>
                        @foreach($tablas as $t)
                            <option value="{{$t}}">{{$t}}</option>
                        @endforeach
                    </select>
                </div>
                <!-- <hr> -->
                <div id="origen">
                    <hr>
                    <label for="columnao" id="titleColO">Columna:</label>
                    <select id="columnao" name="columnao" class="form-control" required>
                    </select>
                </div> 
            </div>
            <div id="destino" class="form-group col-12 col-md-6 colp">
                <div>
                    <label for="tablad">Tabla Destino:</label>
                    <select id="tablad" name="tablad" class="form-control" required>
                        @foreach($tablas as $t)
                            <option value="{{$t}}">{{$t}}</option>
                        @endforeach
                    </select>
                </div>
                <hr>
                <div>
                    <label for="columnad" id="titleColD">Columna:</label>
                    <select id="columnad" name="columnad" class="form-control" required>
                    </select>
                </div> 
            </div>
        </div>
        <div class="row">
            <div class="form-group col text-center" id="btnsFormConexion">
                <button id="btnVerificar" type="submit" class="btn btn-primary">Verificar</button>
            </div>
        </div>
    </form>
    <div class="row contResult">
        <div class="mb-1">
            <p id="metrica"></p>
        </div>
    </div>
    <div class="col-12">
        <div class="my-2 text-center row" id="btnsResult">
            <form  method="POST" class="col" id="formResultados">
                @csrf
                <!-- Otros campos del formulario -->
                
            </form>
        </div>
    </div>
    <div id="resultExcepciones" style="overflow-y: auto !important; max-height: 300px; padding-right: 100px; padding-left: 100px;">
    </div>
@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>

var resultadosGlobales = null;
    // var routeURL = '/reporteRegistro'
    $(document).ready(function() {
        $('#formResultados').hide();

        $('#verifyForm').submit(function(event) {
            // Previene el envío tradicional del formulario
            var to = $('#tablao').val();
            var td = $('#tablad').val();


            if(to!=td) {

                // Obtén los datos del formulario
                event.preventDefault();
                var formData = $(this).serialize();

                //alert(formData);
                // Envía los datos usando AJAX a tu controlador
                $.ajax({
                    type: 'POST',
                    url: $(this).attr('action'), // Cambia esto por la URL de tu controlador
                    data: formData,
                    success: function(response) {
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

                        if(response.results.length > 0) {
                            
                            $('#metrica').addClass('text-center').text(response.results.length +' excepciones encontradas.');
                            var resultsDiv = $('#resultExcepciones');
                            resultadosGlobales = response.results;
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
                    
                                var messagePart1 = $('<div>').html('<u>Excepción de tablas encontrada</u>');
                                var messagePart2 = $('<div>').text(result.message);
                                
                                messageDiv.append(messagePart1).append(messagePart2);
                                alertDiv.append(imgDiv).append(messageDiv);

                                resultsDiv.append(alertDiv);
                            })
                            var resultadosJSON = JSON.stringify(resultadosGlobales);
                            // Construir el formulario dinámicamente
                                
                                // Agregar un campo oculto para el token CSRF
                                $('#formResultados').show();
                                $('#formResultados').attr('action', '{{ route("reporteT") }}');
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
                                        text: 'Generar Reporte'
                                    }));
                        }
                        else{
                            $('#metrica').text('');
                            var result = $('<div>').addClass('alert alert-info text-center').attr('role', 'alert').text('No se encontraron resultados de excepciones para los parametros ingresados.');
                            $('#resultExcepciones').empty();
                            $('#resultExcepciones').append(result);

                            setTimeout(function() {
                                $('#resultExcepciones').empty();
                            }, 8000);
                        }
                        //alert(response.results);
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
            }
            else{
                event.preventDefault();
                alert('No se puede analizar la misma tabla.')
            }
        });


        var tipo = $('input[type="radio"][name="relacion"]:checked').val();
       
        if(tipo == 'T'){
            $('#origen').show();
            $('#destino').show();
            $('#btnVerificar').prop('disabled', false);
            $('#columnao').prop('readonly', false);
            $('#columnad').prop('readonly', false);
            var selectColumnas = $('#columnao');
            var selectedTableName = $('#tablao').val(); // Obtiene el valor de la opción seleccionada
                //alert(selectedTableName);
            loadColumns(selectedTableName,selectColumnas);
            
            var selectColumnas = $('#columnad');
            var selectedTableName = $('#tablad').val(); // Obtiene el valor de la opción seleccionada
                //alert(selectedTableName);
            loadColumns(selectedTableName,selectColumnas);
        }else{
            var selectTablasRel = $('#tablad');
            var selectedTableName = $('#tablao').val(); // Obtiene el valor de la opción seleccionada
            //alert(selectedTableName);
            loadTableRel(selectedTableName,selectTablasRel);

            // Código que quieres ejecutar después de la pausa de 2 segundos
            var selectRef = $('#columnao');
            var selectRefed = $('#columnad');
            var selectedTableName = $('#tablao').val();
            
            mostrarForaneas(selectedTableName,selectRef,selectRefed);
        }
        
        $(document).on('change', 'input[name="relacion"]', function() {
            tipo = $('input[type="radio"][name="relacion"]:checked').val();
            //alert(tipo);
            if(tipo=='R') {
                var selectTablasRel = $('#tablad');
                var selectedTableName = $('#tablao').val(); // Obtiene el valor de la opción seleccionada
                //alert(selectedTableName);
                loadTableRel(selectedTableName,selectTablasRel);

                // Código que quieres ejecutar después de la pausa de 2 segundos
                var selectRef = $('#columnao');
                var selectRefed = $('#columnad');
                var selectedTableName = $('#tablao').val();
                
                mostrarForaneas(selectedTableName,selectRef,selectRefed);


            }else{
                $('#origen').show();
                $('#destino').show();
                $('#columnao').prop('readonly', false);
                $('#columnad').prop('readonly', false);
                var selectColumnas = $('#columnao');
                var selectedTableName = $('#tablao').val(); // Obtiene el valor de la opción seleccionada
                    //alert(selectedTableName);
                loadColumns(selectedTableName,selectColumnas);

                var selectTablas = $('#tablad');
                mostrarTablas(selectTablas);
                
                var selectColumnas = $('#columnad');
                var selectedTableName = $('#tablad').val(); // Obtiene el valor de la opción seleccionada
                    //alert(selectedTableName);
                loadColumns(selectedTableName,selectColumnas);
            }
        });

        $(document).on('change', '#tablao', function() {
            $('#metrica').text('');
            var resultsDiv = $('#resultExcepciones');
            resultsDiv.empty();
            $('#formResultados').hide();
            tipo = $('input[type="radio"][name="relacion"]:checked').val();

            if(tipo=='R') {
                var selectTablasRel = $('#tablad');
                var selectedTableName = $('#tablao').val(); // Obtiene el valor de la opción seleccionada
                loadTableRel(selectedTableName,selectTablasRel);

                var selectRef = $('#columnao');
                var selectRefed = $('#columnad');
                var selectedTableName = $('#tablao').val();
                mostrarForaneas(selectedTableName,selectRef,selectRefed);

            }else{
                var selectColumnas = $('#columnao');
                var selectedTableName = $('#tablao').val(); // Obtiene el valor de la opción seleccionada
                    //alert(selectedTableName);
                loadColumns(selectedTableName,selectColumnas);
            }
        });

        $(document).on('change', '#tablad', function() {
            $('#metrica').text('');
            var resultsDiv = $('#resultExcepciones');
            resultsDiv.empty();
            $('#metrica').text('');
            $('#formResultados').hide();
            if(tipo=='T'){
                var selectColumnas = $('#columnad');
                var selectedTableName = $('#tablad').val(); // Obtiene el valor de la opción seleccionada
                loadColumns(selectedTableName,selectColumnas);
            }else{
                var selectRef = $('#columnao');
                var selectRefed = $('#columnad');
                var selectedTableName = $('#tablao').val();
                mostrarForaneas(selectedTableName,selectRef,selectRefed);
            }

        });

        $(document).on('change', '#columnao', function() {
            var resultsDiv = $('#resultExcepciones');
            resultsDiv.empty();
            $('#metrica').text('');
            $('#formResultados').hide();
        });

        $(document).on('change', '#columnad', function() {
            var resultsDiv = $('#resultExcepciones');
            resultsDiv.empty();
            $('#metrica').text('');
            $('#formResultados').hide();
        });

    });


    function loadColumns(tableName,selectColumnas) {
        $.ajax({
            url: '/cargar-info/'+tableName, // Ruta para cargar la tabla (ajusta según tu aplicación)
            method: 'GET',
            data: { table: tableName }, // Datos a enviar, por ejemplo el nombre de la tabla
            success: function(response) {
                selectColumnas.empty(); // Limpia las opciones anteriores

                // if(tipo == 'R'){
                //     var selectLlaves = $('#columnao');
                //     selectLlaves.empty(); //

                //     response.columnas.forEach(function(columna) {
                //         selectColumnas.append($('<option>').val(columna.TABLA).text(columna.TABLA));
                //         selectLlaves.append($('<option>').val(columna.COLUMNA).text(columna.COLUMNA));
                //     });

                    
                // }else{
                    response.columnas.forEach(function(columna) {
                        selectColumnas.append($('<option>').val(columna.Field).text(columna.Field)); 
                    });
                // }
                
                //mostrarTipoColumna();
            },
            error: function(xhr, status, error) {
                // Manejo de errores
                console.log('error'); // Esto te mostrará la estructura de la respuesta en la consola
            }
        });
    }

    function loadTableRel(tableName,selectColumnas){
        $.ajax({
            url: '/cargar-info-rel/'+tableName, // Ruta para cargar la tabla (ajusta según tu aplicación)
            method: 'GET',
            data: { table: tableName }, // Datos a enviar, por ejemplo el nombre de la tabla
            success: function(response) {
                var isFirstColumn = true; // Flag para identificar el primer elemento
                selectColumnas.empty(); // Limpia las opciones anteriores                
                if(response.columnas.length>0){
                    $('#origen').show();
                    $('#destino').show();
                    $('#btnVerificar').prop('disabled', false);
                    $('#columnao').prop('readonly', true);
                    $('#columnad').prop('readonly', true);
                    response.columnas.forEach(function(columna) {
                        //alert(columna.TABLA);
                        var nuevaOpcion = $('<option>').val(columna.TABLA).text(columna.TABLA);
                        
                        // Si es el primer elemento, establece selected=true
                        if (isFirstColumn) {
                            nuevaOpcion.attr('selected');
                            isFirstColumn = false; // Cambia el estado de la bandera después de seleccionar el primer elemento
                        }
                        
                        selectColumnas.append(nuevaOpcion);
                    });
                }
                else{
                    $('#origen').hide();
                    $('#destino').hide();
                    $('#btnVerificar').prop('disabled', true);
                }
                
            },
            error: function(xhr, status, error) {
                // Manejo de errores
                console.log('error'); // Esto te mostrará la estructura de la respuesta en la consola
            }
        });
    }

    function mostrarForaneas(tableName,selectRef,selectRefed){
        $.ajax({
            url: '/cargar-info-rel/'+tableName, // Ruta para cargar la tabla (ajusta según tu aplicación)
            method: 'GET',
            data: { table: tableName }, // Datos a enviar, por ejemplo el nombre de la tabla
            success: function(response) {
                selectRef.empty(); // Limpia las opciones anteriores
                selectRefed.empty(); //

                var tablaReferSelected = $('#tablad').val();
                //console.log('tabla elegida: ' + tablaReferSelected);
    
                response.columnas.forEach(function(columna) {
                    
                    if(columna.TABLA == tablaReferSelected){
                        selectRef.append($('<option>').val(columna.COLUMNA).text(columna.COLUMNA));
                        selectRefed.append($('<option>').val(columna.FORANEA).text(columna.FORANEA));
                    }

                });
            },
            error: function(xhr, status, error) {
                // Manejo de errores
                console.log('error'); // Esto te mostrará la estructura de la respuesta en la consola
            }
        });
    }

    function mostrarTablas(selectTablas){
        $.ajax({
            url: '/obtenerTablas', // Ruta para cargar la tabla (ajusta según tu aplicación)
            method: 'GET',
            // data: { table: tableName }, // Datos a enviar, por ejemplo el nombre de la tabla
            success: function(response) {
                selectTablas.empty(); // Limpia las opciones anteriores

                response.tablas.forEach(function(tabla) {
                    selectTablas.append($('<option>').val(tabla).text(tabla));
                    //console.log(tabla); 
                });

                //selectTablas.val(response.tablas[0]);
            },
            error: function(xhr, status, error) {
                // Manejo de errores
                alert('error'); // Esto te mostrará la estructura de la respuesta en la consola
            }
        });

        setTimeout(function() {
            var selectColumnas = $('#columnad');
            var selectedTableName = $('#tablad').val(); // Obtiene el valor de la opción seleccionada
            //alert(selectedTableName);
            loadColumns(selectedTableName,selectColumnas); // Llama a loadTable con ese valor
        }, 1000);
    }
</script>


