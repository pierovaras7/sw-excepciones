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
                    <label for="db_type">Gestor Base de Datos:</label>
                    <select name="db_type" class="form-control" required>
                        <option value="mysql">MySQL</option>
                        <option value="sqlsrv">SQL Server</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="host">Host:</label>
                    <input type="text" name="host" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="database">Base de Datos:</label>
                    <input type="text" name="database" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="username">Usuario:</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="password">Contraseña:</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <div class="form-group text-center" id="btnsFormConexion">
                    <button id="btnConectar" type="button" class="btn btn-primary">Conectar</button>
                    <!-- Si $conex es true, el botón "Desconectar" estará oculto -->
                    <button id="btnDesconectar" type="button" class="btn btn-primary">Desconectar</button>

                    <a id="btnInformacion" href="{{route('infodb')}}" class="btn btn-info">Ver Información</a>
                </div>
            </form>
        </div>
    </div>
@endsection
<!-- Modal para mensajes de conexión -->
<div class="modal fade" id="conexionModal" tabindex="-1" role="dialog" aria-labelledby="conexionModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <pack class="modal-title" id="conexionModalLabel">Estado de la Conexión</pack>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body alert alert-success m-2">
        <!-- Aquí va el mensaje de éxito o error -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    // $(document).ready(function() {
    //     $('#connectForm').submit(function(event) {
    //         event.preventDefault(); // Prevenir el envío del formulario normal

    //         // Obtener los datos del formulario
    //         var formData = $(this).serialize();

    //         // Enviar el formulario a través de AJAX
    //         $.ajax({
    //             type: 'POST',
    //             url: $(this).attr('action'),
    //             data: formData,
    //             success: function(response) {
    //             // Configurar y mostrar el modal de éxito
    //                 $('.modal-body').html(response.message).removeClass('alert-danger').addClass('alert-success');
    //                 $('.modal-footer').html('<a href="{{route('infodb')}}" class="btn btn-info">Ver Información</a>');
    //                 $('#conexionModal').modal('show');

    //                 if(response.conexion == true){
    //                   $('#estado').text('Desconectar');
    //                 }
    //             },
    //             error: function(xhr, status, error) {
    //                 // Configurar y mostrar el modal de error
    //                 $('.modal-body').html(response.erro).removeClass('alert-success').addClass('alert-danger');
    //                 $('.modal-footer').html('<button type="button" class="btn btn-warning" data-dismiss="modal">Intentar de Nuevo</button>');
    //                 $('#conexionModal').modal('show');

    //             }

    //         });
    //     });
    // });

    $(document).ready(function() {

      var conex = {{ json_encode($conex) }};
      //alert(conex);

      if(conex==true){
        $('#btnConectar').hide();
        $('#connectForm input, #connectForm select').prop('disabled', true);
      }else{
        $('#btnDesconectar').hide();
        $('#btnInformacion').hide();
      }

      $('#btnConectar').click(function() {
          // Obtener los datos del formulario
          var formData = $('#connectForm').serialize();

          // Enviar el formulario a través de AJAX
          $.ajax({
              type: 'POST',
              url: $('#connectForm').attr('action'),
              data: formData,
              dataType: 'json',
              success: function(response) {
                  // Configurar y mostrar el modal de éxito
                  // alert('god');
                  $('.modal-body').html(response.message).removeClass('alert-danger').addClass('alert-success');
                  $('.modal-footer').html('<a href="{{route('infodb')}}" class="btn btn-info">Ver Información</a>');
                  $('#conexionModal').modal('show');

                  $('#connectForm input, #connectForm select').prop('disabled', true);

                  $('#btnDesconectar').show();
                  $('#btnConectar').hide();
                  $('#btnInformacion').show();
                  //$('#btnsFormConexion').html('<button id="btnDesconectar" type="button" class="btn btn-primary">Desconectar</button>');

              },
              error: function(xhr, status, error) {
                  //alert('no god');
                  // // Configurar y mostrar el modal de error
                  $('.modal-body').html('No se pudo establecer la conexión. Verifica las credenciales proporcionadas.').removeClass('alert-success').addClass('alert-danger');
                  $('.modal-footer').html('<button type="button" class="btn btn-warning" data-dismiss="modal">Intentar de Nuevo</button>');
                  $('#conexionModal').modal('show');
              }
          });
      });

  // Configurar evento para el botón "Desconectar"
      $('#btnDesconectar').click(function() {
          // Aquí puedes realizar las acciones necesarias para la 
          //alert('Desconectar');
          //$('#btnsFormConexion').html('<button id="btnConectar" type="button" class="btn btn-primary">Conectar</button>');

          $.ajax({
              type: 'GET',
              url: '{{ route('disconnect') }}',
              dataType: 'json',
              success: function(response) {
                  // // Lógica después de cambiar la conexión en la sesión
                   $('.modal-body').html(response.message).removeClass('alert-danger').addClass('alert-success');
                   $('.modal-footer').html('<a class="btn btn-info" data-dismiss="modal">OK</a>');
                   $('#conexionModal').modal('show');

                   $('#connectForm input, #connectForm select').prop('disabled', false);
                   $('#btnDesconectar').hide();
                   $('#btnConectar').show();
                   $('#btnInformacion').hide();

              },
              error: function(xhr, status, error) {
                  // Manejo de errores
                  alert('errro');
              }
          });
      });
    });

</script>


