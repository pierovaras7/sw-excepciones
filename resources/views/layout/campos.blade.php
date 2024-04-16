@extends('layout.layout')

@section('contenido')
    <h1 class="h3 mb-2 text-gray-800">Integridad de campos:</h1>
    <p class="mb-4">
        Analizar los valores de la base de datos para el campo elegido de cierta tabla.
    </p>
    <div class="row">
        <div class="m-1 col-12">
            <form action="{{ route('camposResult') }}" method="post" id="verifyForm">
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
                <input type="text" id="typecolumna" name="typecolumna" hidden>
                <div class="row">
                    <div class="form-group col-2 d-flex align-items-center">
                        <p class="px-2">Parametros: </p>
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
@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    var resultadosGlobales = null;
    // var routeURL = '/reporteRegistro'
    $(document).ready(function() {
        $('#formResultados').hide();
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
            // Previene el envío tradicional del formulario
            event.preventDefault();

            // Obtén los datos del formulario
            var formData = $(this).serialize();

            //alert(formData);
            // Envía los datos usando AJAX a tu controlador
            $.ajax({
                type: 'POST',
                url: $(this).attr('action'), // Cambia esto por la URL de tu controlador
                data: formData,
                success: function(response) {
                    if (response.errortipo) {
                        var resultsDiv = $('#resultExcepciones');
                        resultsDiv.empty();
                        $('#formResultados').hide();
                        $('#metrica').text('');
                        var btnVer = $('#btnTableModal');
                        btnVer.prop('disabled', true);
                        var result = $('<div>').addClass('alert alert-info text-center').attr('role', 'alert').text(response.errortipo);
                        $('#resultExcepciones').empty();
                        $('#resultExcepciones').append(result);

                        setTimeout(function() {
                            $('#resultExcepciones').empty();
                        }, 4000);
                    }else{
                        if(response.datos){
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

                        if(response.results.length > 0) {
                            $('#metrica').addClass('text-center').text(response.results.length +' excepciones encontradas.');
                            var resultsDiv = $('#resultExcepciones');
                            // Limpia los resultados anteriores
                            resultsDiv.empty();
                            resultadosGlobales = response.results;
                            response.results.forEach(function(result) {
                                // Crear el contenedor principal con las clases 'row' y 'alert alert-danger'
                                var alertDiv = $('<div>').addClass('row alert alert-danger m-1 p-1').attr('role', 'alert');
                                // Crear el div para la imagen
                                var imgDiv = $('<div>').addClass('col-1').append($('<img>').attr('src', 'img/buscar.png').attr('alt', 'Icon Excepciones'));
                                //var imgDiv =  $('<div>').addClass('text-center').html('<i class="fa fa-exclamation-circle" aria-hidden="true"></i>');
                                // Crear el div para el mensaje, usando 'd-flex' y 'align-items-center' para el centrado vertical
                                var messageDiv = $('<div>').addClass('col-11 d-flex flex-column align-items-center');
                    
                                var messagePart1 = $('<div>').html('<u>Excepción de campos encontrada</u>');
                                var messagePart2 = $('<div>').text(result.message);
                                
                                messageDiv.append(messagePart1).append(messagePart2);
                                alertDiv.append(imgDiv).append(messageDiv);

                                resultsDiv.append(alertDiv);
                            })
                            var resultadosJSON = JSON.stringify(resultadosGlobales);
                                // Construir el formulario dinámicamente
                                    
                                    // Agregar un campo oculto para el token CSRF
                                    $('#formResultados').show();
                                    $('#formResultados').attr('action', '{{ route("reporteC") }}');
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
                            var resultsDiv = $('#resultExcepciones');
                            resultsDiv.empty();
                            $('#formResultados').hide();
                            $('#metrica').text('');
                            var btnVer = $('#btnTableModal');
                            btnVer.prop('disabled', true);
                            var result = $('<div>').addClass('alert alert-info text-center').attr('role', 'alert').text('No se encontraron resultados de excepciones para los parametros ingresados.');
                            $('#resultExcepciones').empty();
                            $('#resultExcepciones').append(result);

                            setTimeout(function() {
                                $('#resultExcepciones').empty();
                            }, 4000);
                        }
                    }
                    //alert(response.results);
                },
                error: function() {
                    var resultsDiv = $('#resultExcepciones');
                    resultsDiv.empty();
                    $('#formResultados').hide();
                    $('#metrica').text('');
                    var btnVer = $('#btnTableModal');
                    btnVer.prop('disabled', true);
                    console.log('Hubo un error al enviar los datos');
                }
            });
        });
    });



    $(document).on('change', '#tabla', function() {
        //alert('ssssssssss');
        var resultsDiv = $('#resultExcepciones');
        resultsDiv.empty();
        $('#formResultados').hide();
        $('#metrica').text('');
        var btnVer = $('#btnTableModal');
        btnVer.prop('disabled', true);
        
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
                mostrarTipoColumna();
            },
            error: function(xhr, status, error) {
                // Manejo de errores
                alert('error'); // Esto te mostrará la estructura de la respuesta en la consola
            }
        });
    }

    
    $(document).on('change', '#columna', function() {
        var resultsDiv = $('#resultExcepciones');
        resultsDiv.empty();
        $('#formResultados').hide();
        $('#metrica').text('');
        var btnVer = $('#btnTableModal');
        btnVer.prop('disabled', true);
        mostrarTipoColumna();
    });
    // // Define la función que muestra el tipo de columna
    
    function mostrarTipoColumna() {
    // Obtén el tipo de columna seleccionada del atributo de datos
        var tipoColumna = $('#columna').find('option:selected').data('type');
        $('#typecolumna').val(tipoColumna);
        //alert('Valor del input oculto:' + $('#typecolumna').val())
        var nameColumna = $('#columna').find('option:selected').val();

        var tabla = $('#tabla').val();

        //alert(tabla);
        var query = '';


        //alert('Columna:' + nameColumna);

        const tiposFecha = ['date', 'datetime', 'timestamp', 'time', 'year', 'datetime2', 'smalldatetime', 'datetimeoffset'];

        const tiposStrings = ['char','tinytext','mediumtext','longtext','text','tinytext','binary','varchar', 'text', 'enum', 'set', 'nvarchar', 'nchar', 'ntext'];

        const tiposNumericos = [ 'int', 'integer', 'smallint', 'mediumint', 'bigint', 'decimal','smallmoney','money' ,'numeric', 'float', 'double', 'real' ];

        const tiposBooleano = [ 'bit','boolean','bool','tinyint'];

        // Ejemplo de uso para verificar si un tipo de columna es una cadena en MySQL o SQL Server
        // if(tipoColumna == 'char(1)'){
        //     $('#inputsContainer').empty();
        //     $('<div class="form-group col mx-2 mb-3">')
        //         .append('<label for="valoresInput">Valores aceptados:</label>')
        //         .append('<input type="text" class="form-control" name="valoresInput" id="valoresInput" placeholder="Ingrese los valores">')
        //         .appendTo('#inputsContainer');
            
        // }

        
        if (tiposStrings.some(tipo => tipoColumna.toLowerCase().includes(tipo))) {
            // Crea un solo input para manejar la cadena
            $('#inputsContainer').empty();
            $('<div class="form-group col mx-2 mb-3">')
                .append('<label for="valorInput">Valor aceptado:</label>')
                .append('<input type="text" class="form-control" name="stringValue" id="valorInput" placeholder="Ingrese el valor">')
                .appendTo('#inputsContainer');
        }

        // Ejemplo de uso para verificar si un tipo de columna es numérica en SQL Server
        if (tiposNumericos.some(tipo => tipoColumna.toLowerCase().includes(tipo))) {
            // Crea un input para el valor mínimo del rango numérico
            $('#inputsContainer').empty();
            $('<div class="form-group col mx-2 mb-3">')
                .append('<label for="minValorInput">Valor mínimo:</label>')
                .append('<input type="number" class="form-control" name="minValue" placeholder="Introduce el valor mínimo">')
                .appendTo('#inputsContainer');

            // Crea un input para el valor máximo del rango numérico
            $('<div class="form-group col mx-2 mb-3">')
                .append('<label for="maxValorInput">Valor máximo:</label>')
                .append('<input type="number" class="form-control" name="maxValue" placeholder="Introduce el valor máximo">')
                .appendTo('#inputsContainer');
        }

        // Ejemplo de uso para verificar si un tipo de columna es una cadena en SQL Server
        if (tiposFecha.some(tipo => tipoColumna.toLowerCase().includes(tipo))) {
            // Crea un solo input para manejar la cadena
            $('#inputsContainer').empty();
            $('<div class="form-group col mx-2 mb-3">')
                .append('<label for="fechaInicioInput">Fecha de inicio:</label>')
                .append('<input type="date" class="form-control" name="fechaInicioValue">')
                .appendTo('#inputsContainer');

            $('<div class="form-group col mx-2 mb-3">')
                .append('<label for="fechaFinInput">Fecha de fin:</label>')
                .append('<input type="date" class="form-control" name="fechaFinValue">')
                .appendTo('#inputsContainer');
        }

        if (tiposBooleano.some(tipo => tipoColumna.toLowerCase().startsWith(tipo))) {
            $('#inputsContainer').empty();
            $('<div class="form-check col-2 d-flex align-items-center">')
            .append('<input class="form-check-input" type="checkbox" value="true" name="boolValue" id="flexCheckDefault">')
            .append('<label class="form-check-label" for="flexCheckDefault">True</label>')
            .appendTo('#inputsContainer');
        }

        $('<div class="form-check col-2 d-flex align-items-center">')
        .append('<input class="form-check-input" type="checkbox" value="NULL" name="isNull" id="flexCheckDefault">')
        .append('<label class="form-check-label" for="flexCheckDefault">Mostrar valores nulos</label>')
        .appendTo('#inputsContainer');
    }
</script>


