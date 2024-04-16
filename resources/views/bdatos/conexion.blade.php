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
                <div class="row">
                    <div class="form-group col-6">
                        <label for="db_type">Gestor Base de Datos:</label>
                        <select name="db_type" id="db_type" class="form-control" required>
                            <option value="mysql" {{ request()->session()->get('conexion') && request()->session()->get('credencialesConsulta')['db_type'] == 'mysql' ? 'selected' : '' }}>MySQL</option>
                            <option value="sqlsrv" {{ request()->session()->get('conexion') && request()->session()->get('credencialesConsulta')['db_type'] == 'sqlsrv' ? 'selected' : '' }}>SQL Server</option>
                        </select>
                    </div>
                    <div class="form-group col-6">
                        <label for="host">Host:</label>
                        <input type="text" name="host" id="host" class="form-control" required value="{{ request()->session()->get('conexion') ? request()->session()->get('credencialesConsulta')['host'] : '' }}">                    
                    </div>
                    <div class="form-group col-6">
                        <label for="port">Puerto:</label>
                        <input type="text" name="port" id="port" class="form-control" required value="{{ request()->session()->get('conexion') ? request()->session()->get('credencialesConsulta')['port'] : '' }}"> 
                    </div>
                    <div class="form-group col-6">
                        <label for="database">Base de Datos:</label>
                        <input type="text" name="database" id="database" class="form-control" required value="{{ request()->session()->get('conexion') ? request()->session()->get('credencialesConsulta')['database'] : '' }}">                    
                    </div>
                    <div class="form-group col-6">
                        <label for="username">Usuario:</label>
                        <input type="text" name="username" id="username" class="form-control" required value="{{ request()->session()->get('conexion') ? request()->session()->get('credencialesConsulta')['username'] : '' }}">      </div>
                    <div class="form-group col-6">
                        <label for="password">Contraseña:</label>
                        <input type="password" name="password" id="password" class="form-control" value="{{ request()->session()->get('conexion') ? request()->session()->get('credencialesConsulta')['password'] : '' }}">                    
                    </div>
                    <div class="form-group col-12 text-center" id="btnsFormConexion">
                        <button id="btnConectar" type="button" class="btn btn-primary">Conectar</button>
                        <!-- Si $conex es true, el botón "Desconectar" estará oculto -->
                        <button id="btnDesconectar" type="button" class="btn btn-primary">Desconectar</button>

                        <a id="btnInformacion" href="{{route('infodb')}}" class="btn btn-info">Ver Información</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @if(request()->session()->get('conexion') === false)
        <h4 class="mb-2 text-gray-800">Ultimas conexiones:</h4>
        <div class="row p-2">
            @foreach ($conexiones as $c)
            <div class="card col mr-2" style="max-width: 200px; max-height: 150px">
                <div class="card-body d-flex flex-column justify-content-between">
                    <div>
                        <p class="card-text" style="font-size: 8px">
                            BD Tipo: {{ $c->db_type }} <br>
                            Host: {{ $c->host }} <br>
                            Puerto: {{ $c->port }} <br>
                            Base de Datos: {{ $c->database }} <br>
                            Usuario: {{ $c->username }} <br>
                            Último uso: {{ $c->last_use }} <br>
                        </p>
                    </div>
                    <div class="align-self-end">
                        <a href="#" class="btn btn-primary my-2 connectBtn" 
                        data-db_type="{{ $c->db_type }}" 
                        data-host="{{ $c->host }}" 
                        data-port="{{ $c->port }}" 
                        data-database="{{ $c->database }}" 
                        data-username="{{ $c->username }}" 
                        data-password="{{ $c->password }}" 
                        style="font-size: 8px; width: 80px; height: 26px">Conectar</a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    @endif
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

      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
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
                  $('.modal-footer').empty();
                  $('#conexionModal').modal('show');

                  $('#connectForm input, #connectForm select').prop('disabled', true);
                            
                  setTimeout(function() {
                        // Recargar la página
                        window.location.reload();
                    }, 500); // 3000 milisegundos = 3 segundos

                  $('#btnDesconectar').show();
                  $('#btnConectar').hide();
                  $('#btnInformacion').show();

              },
              error: function(xhr, status, error) {
                  //alert('no god');
                  // // Configurar y mostrar el modal de error
                  $('.modal-body').html('No se pudo establecer la conexión. Verifica las credenciales proporcionadas.').removeClass('alert-success').addClass('alert-danger');
                  $('.modal-footer').append('<button type="button" class="btn btn-warning" data-dismiss="modal">Intentar de Nuevo</button>');
                  $('#conexionModal').modal('show');
              }
          });
      });

  // Configurar evento para el botón "Desconectar"
      $('#btnDesconectar').click(function() {
          // Aquí puedes realizar las acciones necesarias para la 
          //alert('Desconectar');
          //$('#btnsFormConexion').html('<button id="btnConectar" type="button" class="btn btn-primary">Conectar</button>');
          $('select[name="db_type"]').val('');
            $('input[name="host"]').val('');
            $('input[name="port"]').val('');
            $('input[name="database"]').val('');
            $('input[name="username"]').val('');
            $('input[name="password"]').val('');

          $.ajax({
              type: 'GET',
              url: '{{ route('disconnect') }}',
              dataType: 'json',
              success: function(response) {
                  // // Lógica después de cambiar la conexión en la sesión
                   $('.modal-body').html(response.message).removeClass('alert-danger').addClass('alert-success');
                   $('#conexionModal').modal('show');

                   $('#connectForm input, #connectForm select').prop('disabled', false);
                   $('#btnDesconectar').hide();
                   $('#btnConectar').show();
                   $('#btnInformacion').hide();

                   setTimeout(function() {
                        // Recargar la página
                        window.location.reload();
                    }, 500); 
                

              },
              error: function(xhr, status, error) {
                  // Manejo de errores
                  alert('errro');
              }
          });
      });

      $('.connectBtn').click(function(e) {
        //e.preventDefault(); // Prevenir el comportamiento predeterminado del enlace
        
        // Obtener los datos de conexión de los atributos de datos del botón
        var db_type = $(this).data('db_type');
        var host = $(this).data('host');
        var port = $(this).data('port');
        var database = $(this).data('database');
        var username = $(this).data('username');
        var password = $(this).data('password');

        //alert(db_type + host + port + database + username + password);
        // Colocar los datos de conexión en los campos ocultos del formulario
        $('#db_type').val(db_type);
        $('#host').val(host);
        $('#port').val(port);
        $('#database').val(database);
        $('#username').val(username);
        $('#password').val(password);
        // Mostrar y enfocar el botón de envío del formulario
        $('#btnConectar').click();
    });


    });

</script>


