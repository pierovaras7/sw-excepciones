@extends('layout.layout')

@section('contenido')
    <h1 class="h3 mb-2 text-gray-800">Conexion:</h1>
    <p class="mb-4">
        Ingresar credenciales para conectarse a la base de datos
    </p>
    <div class="row">
        <div class="m-1 col-12">
            <form action="{{ route('connect') }}" method="post" id="connectForm">
            @csrf
            <!-- Campos para las credenciales de conexión -->
                <div class="form-group">
                    <label for="excepcion">Tipo de Excepcion:</label>
                    <select name="excepcion" class="form-control" required>
                        <option value="unicidad">Unicidad</option>
                        <option value="secuencia">Secuencia</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="tabla">Tabla:</label>
                    <select id="tabla" name="tabla" class="form-control" required>
                        @foreach($tablas as $t)
                            <option value="{{$t}}">{{$t}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="columna">Columna:</label>
                    <select id="columna" name="columna" class="form-control" required>
                    </select>
                </div>
                <div class="form-group text-center" id="btnsFormConexion">
                    <button id="btnConectar" type="submit" class="btn btn-primary">Verificar</button>
                </div>
            </form>
        </div>
    </div>
@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    //alert('ssssssssss');
    $(document).on('change', '#tabla', function() {
        //alert('ssssssssss');
        var selectedTableName = $(this).val(); // Obtiene el valor de la opción seleccionada
        alert(selectedTableName);
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
                    var $option = $('<option>').val(columna.Field).text(columna.Field);
                    $select.append($option);
                });
            },
            error: function(xhr, status, error) {
                // Manejo de errores
                alert(error.message); // Esto te mostrará la estructura de la respuesta en la consola
            }
        });
    }
</script>


