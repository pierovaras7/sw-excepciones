<link rel="stylesheet" href="style.css">
@extends('layout.layout')

@section('contenido')
    
        <div class="row">
            <!-- Div de la izquierda, col-5 y scrollable -->
            <div class="col-xs-12 col-md-3">
                <h5 class="px-2 text-gray-800">Tablas disponibles:</h5>
                <div style="overflow-y: auto; max-height: calc(100vh - 250px);">
                    @foreach($tablas as $t)
                        <div class="card-tables" onclick="loadTable('{{ $t }}')" data-table-name="{{ $t }}">
                            {{$t}}
                        </div>
                    @endforeach
                </div>
            </div>
            
        <!-- Div de la derecha, ocupará el espacio restante -->
            <div class="col-xs-12 col-md-9" style="max-height: calc(100vh - 250px);">
                <h5 class="px-2 text-gray-800">Informacion de la tabla <p id="tableName" style="display: inline; font-weight: bold;"></p></h5>
                <div class="table-responsive align m-0 p-0">
                    <table class="table table-bordered text-center m-0 p-0" id="theadInfo" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th style="width: 40%;">Columna</th>
                                <th style="width: 20%;">Tipo</th>
                                <th style="width: 20%;">¿Es nulo?</th>
                                <th style="width: 20%;">Valor por defecto</th>
                            </tr>
                        </thead>
                    </table>
                </div>
                <div class="table-responsive align m-0 p-0" id="tbodyInfo"  style="overflow-y: auto !important; max-height: calc(100vh - 300px);">
                    <table class="table table-bordered text-center" id="dataTable" width="100%" cellspacing="0">
                        <tbody id="bodyTableInfo">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 text-center py-2 my-2">
                <button type="button" class="btn btn-info">Analizar excepciones</button>
            </div>
        </div>
@endsection
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        // Encuentra el primer div.card-tables y obtiene su atributo data-table-name
        var firstTableName = $('.card-tables:first').data('table-name');
        //alert(firstTableName);
        if(firstTableName) {
            loadTable(firstTableName); // Llama a tu función loadTable con el primer nombre de tabla
        }
    });


    function loadTable(tableName) {
        $.ajax({
            url: '/cargar-info/'+tableName, // Ruta para cargar la tabla (ajusta según tu aplicación)
            method: 'GET',
            //data: { table: tableName }, // Datos a enviar, por ejemplo el nombre de la tabla
            success: function(response) {
                // Aquí puedes manejar la respuesta y actualizar el contenido de la página con la tabla
                $('#tableName').text('"'+tableName+'"');
                displayColumns(response.columnas); // Función para procesar y mostrar la información
                //$('#infoTable').html(response.msj);
                //alert(response.columnas);
            },
            error: function(xhr, status, error) {
                // Manejo de errores
                //alert(error.message); // Esto te mostrará la estructura de la respuesta en la consola
            }
        });
    }

    function displayColumns(columns) {
        let html = '';
        columns.forEach(function(column) {
            html += `<tr>
                        <td style="width: 40%;">${column.Field}</td>
                        <td style="width: 20%;">${column.Type}</td>
                        <td style="width: 20%;">${column.Null}</td>
                        <td style="width: 20%;">${column.Default}</td>
                    </tr>`;
        });
        html += '</tbody></table>';

        $('#bodyTableInfo').html(html); // Asumiendo que tienes un div con id="infoTable" para mostrar la tabla
    }
</script>