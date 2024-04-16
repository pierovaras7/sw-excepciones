@extends('layout.layout')

@section('contenido')
        <!-- Page Heading -->
        <h1 class="h3 mb-2 text-gray-800">Historial</h1>
        <p class="mb-4">
            Mostrar las acciones y busquedas realizadas por el usuario
        </p>
        <div class="row">
            <div class="form-group col-6">
                <label for="filtrarPor">Filtrar por:</label>
                <select name="selectFiltrar" id="filtrarPor">
                    <option value="todos">Todos los registros</option>
                    <option value="unicidad">Unicidad</option>
                    <option value="secuencialidad">Secuencialidad</option>
                    <option value="campos">Integridad por campos</option>
                    <option value="tablas">Integridad por tablas</option>
                    <option value="scriptsql">Consulta SQL</option>
                </select>
            </div>
        </div>
        <div class="alert alert-warning" role="alert" id="prueba" hide>
        </div>
        <!-- DataTales Example -->
        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive align">
                    <table class="table table-bordered text-center" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <!-- <th>Conexion</th> -->
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Tablas implicadas</th>
                                <th>Resultado</th>
                            </tr>
                        </thead>
                        <tbody id="tablaBody">
                            @foreach($historiales as $h)
                            <tr>
                                <th>{{$h->fecha}}</th>
                                <th>{{$h->tipo}}</th>
                                <th>{{$h->tabla}}</th>
                                <th>
                                    <!-- Button trigger modal -->
                                    <button type="button" class="btn btn-primary" data-bs-id="{{$h->id}}" data-toggle="modal" data-target="#resultModal{{$h->id}}">
                                        Ver detalle
                                    </button>
                                    <!-- Modal -->
                                    <div class="modal fade" id="resultModal{{$h->id}}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">                                       
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="exampleModalLabel">Detalle</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    @foreach (explode("\n", $h->resultado) as $mensaje)
                                                        <div class="m-2">{{ $mensaje }}</div>
                                                    @endforeach
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">OK</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </th>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <nav aria-label="Page navigation example" class="mt-3">
            <ul class="pagination justify-content-end">
                <!-- Enlace "Anterior" -->
                <li class="page-item {{ $historiales->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $historiales->previousPageUrl() }}" tabindex="-1" aria-disabled="{{ $historiales->onFirstPage() ? 'true' : 'false' }}">Previous</a>
                </li>

                <!-- Enlaces de páginas -->
                @for ($i = 1; $i <= $historiales->lastPage(); $i++)
                    <li class="page-item {{ $i == $historiales->currentPage() ? 'active' : '' }}">
                        <a class="page-link" href="{{ $historiales->url($i) }}">{{ $i }}</a>
                    </li>
                @endfor

                <!-- Enlace "Siguiente" -->
                <li class="page-item {{ !$historiales->hasMorePages() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $historiales->nextPageUrl() }}">Next</a>
                </li>
            </ul>
        </nav>


@endsection
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // Ocultar el mensaje de éxito después de 3 segundos
    
    $(document).ready(function() {
        $('#prueba').hide();

        $(document).on('change', '#filtrarPor', function() {
            var tipo = $(this).val();  // Obtén el valor seleccionado en el select
            $.ajax({
                url: '/filtrarHistoriales/' + tipo, // Usa el valor seleccionado como parte de la URL
                type: 'GET',
                success: function(response) {
                    var tablaBody = $('#tablaBody');
                    tablaBody.empty(); 

                    if (response.historiales && response.historiales.data.length > 0) {
                        response.historiales.data.forEach(function(historial) {

                            
                            var fila = $('<tr>');
                            fila.append($('<td>').text(historial.fecha));
                            fila.append($('<td>').text(historial.tipo));
                            fila.append($('<td>').text(historial.tabla));
                    
                            // Agregar otras celdas de columna según sea necesario
                            var columnaBoton = $('<td>');
                            var botonDetalle = $('<button>').addClass('btn btn-primary').attr({
                                'type': 'button',
                                'data-bs-id': historial.id,
                                'data-toggle': 'modal',
                                'data-target': '#resultModal' + historial.id
                            }).text('Ver detalle');
                            columnaBoton.append(botonDetalle);
                            fila.append(columnaBoton);

                            // Agregar la fila a la tabla
                            tablaBody.append(fila);

                            // Agregar el modal correspondiente al botón Ver detalle
                            var modal = $('<div>').addClass('modal fade').attr({
                                'id': 'resultModal' + historial.id,
                                'tabindex': '-1',
                                'aria-labelledby': 'exampleModalLabel',
                                'aria-hidden': 'true'
                            });
                            var modalDialog = $('<div>').addClass('modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable');
                            var modalContent = $('<div>').addClass('modal-content');
                            var modalHeader = $('<div>').addClass('modal-header');
                            modalHeader.append($('<h5>').addClass('modal-title').attr('id', 'exampleModalLabel').text('Detalle'));
                            modalHeader.append($('<button>').addClass('close').attr({
                                'type': 'button',
                                'data-dismiss': 'modal',
                                'aria-label': 'Close'
                            }).append($('<span>').attr('aria-hidden', 'true').html('&times;')));
                            var modalBody = $('<div>').addClass('modal-body');
                            modalBody.append(
                                $.map(historial.resultado.split('\n'), function(mensaje) {
                                    return $('<div>').addClass('m-2').text(mensaje);
                                })
                            );
                            var modalFooter = $('<div>').addClass('modal-footer');
                            modalFooter.append($('<button>').addClass('btn btn-secondary').attr('type', 'button').attr('data-dismiss', 'modal').text('OK'));
                            modalContent.append(modalHeader, modalBody, modalFooter);
                            modalDialog.append(modalContent);
                            modal.append(modalDialog);
                            $('body').append(modal);
                            // Agregar la fila a la tabla
                            tablaBody.append(fila);
                        });

                    } 
                    if(response.noresult === true){
                        $('#prueba').html('No existe historial de ese tipo de acciones.').show();
                        // Ocultar el mensaje después de 3 segundos
                        setTimeout(function() {
                            $('#prueba').hide();
                        }, 5000);

                        $('#filtrarPor').val('todos');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                }
            });
        });

    });

</script>
