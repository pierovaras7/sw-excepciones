@extends('layout.layout')

@section('contenido')
        <!-- Page Heading -->
        @if(session('result') && session('message'))
            <div id="resultMessage" class="alert alert-{{session('result')}}" role="alert">
            {{ session('message') }}
            </div>
        @endif
        <h1 class="h3 mb-2 text-gray-800">Usuarios</h1>
        <p class="mb-4">
        Aquí puedes agregar, editar y eliminar usuarios fácilmente, gestionando sus roles y permisos para una administración fluida y segura del sistema.
        </p>
        <div class="my-2">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#editModal">
                Registrar nuevo
            </button>
            <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                aria-hiddzen="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <form action="{{ route('usuarios.store') }}" method="POST">
                            @csrf
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Registrar usuario</h5>
                                <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                    <input type="hidden" id="id" name="id">
                                    <div class="form-group">
                                        <label for="exampleInputEmail1">Nombre</label>
                                        <input type="text" class="form-control" id="name" 
                                            name="name" required>
                                    </div>
                                    <div class="form-group">
                                        <label>Email</label>
                                        <input type="email" class="form-control"
                                        id="email" name="email" required>
                                    </div>
                                    <div class="form-group">
                                        <label for="exampleInputPassword1">Password</label>
                                        <input type="password" class="form-control" id="password" name="password" required>
                                    </div>
                                
                            </div>
                            <div class="modal-footer"> 
                                <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                                <button class="btn btn-primary" type="submit">Registrar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>  
        </div>
        <!-- DataTales Example -->
        <div class="card shadow">
            <div class="card-body">
                <div class="table-responsive align">
                    <table class="table table-bordered text-center" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Usuario</th>
                                <th>Email</th>
                                <th>Ultimo inicio de sesion</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($usuarios as $u)
                            <tr>
                                <th>{{$u->id}}</th>
                                <th>{{$u->name}}</th>
                                <th>{{$u->email}}</th>
                                <th>{{$u->lastlogin ?? 'No ha iniciado sesion aun.'}}</th>
                                <th>
                                    <a href="#" class="btn btn-warning btn-circle btn-sm" title="Editar usuario" data-bs-id="{{$u->id}}" data-toggle="modal" data-target="#editModal{{$u->id}}">
                                        <i class="fa fa-retweet"></i>
                                    </a>
                                    <div class="modal fade" id="editModal{{$u->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                                        aria-hiddzen="true">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <form action="{{ route('usuarios.update', $u->id) }}" method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title" id="exampleModalLabel">Editar usuario</h5>
                                                        <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">×</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body text-left">
                                                            <input type="hidden" id="id" name="id">
                                                            <div class="form-group">
                                                                <label for="exampleInputEmail1">Nombre</label>
                                                                <input type="text" class="form-control" id="exampleInputEmail1" 
                                                                value="{{$u->name}}" name="name">
                                                                
                                                            </div>
                                                            <div class="form-group">
                                                                <label>Email</label>
                                                                <input type="email" class="form-control"
                                                                value="{{$u->email}}" id="email" name="email">
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="exampleInputPassword1">Nueva Password (opcional)</label>
                                                                <input type="password" class="form-control" id="password" name="password">
                                                            </div>
                                                        
                                                    </div>
                                                    <div class="modal-footer"> 
                                                        <button class="btn btn-secondary" type="button" data-dismiss="modal">Cancelar</button>
                                                        <button class="btn btn-primary" type="submit">Editar</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    @if($u->id != 1)
                                        <a href="{{route('usuarios.eliminar',$u->id)}}" title="Eliminar usuario" class="btn btn-danger btn-circle btn-sm">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    @endif
                                    <a href="#" class="btn btn-info  btn-circle btn-sm" title="Ver historial de inicios de sesion" data-bs-id="{{$u->id}}" data-toggle="modal" data-target="#historialModal{{$u->id}}">
                                        <i class="fa fa-history"></i>
                                    </a>
                                    <div class="modal fade" id="historialModal{{$u->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                                        aria-hiddzen="true">
                                        <div class="modal-dialog modal-dialog-scrollable">
                                            <div class="modal-content">
                                                <div class="modal-body">
                                                    <div class="card shadow">
                                                        <div class="card-body">
                                                            <h4 class="mb-2 text-gray-800"><b>Historial del usuario: {{$u->name}}</h4>
                                                            @if ($u->loginHistories->isNotEmpty())
                                                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Hora de Inicio:</th>
                                                                            <th>Hora de Salida:</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                            @foreach ($u->loginHistories->sortByDesc('login_at') as $loginHistory)
                                                                            <tr>
                                                                                <th>{{ $loginHistory->login_at }}</th>
                                                                                <th>{{ $loginHistory->logout_at }}</th>
                                                                            </tr>
                                                                            @endforeach
                                                                    </tbody>
                                                                </table>
                                                            @else
                                                                No hay registros de inicio de sesión
                                                            @endif
                                                        </div>
                                                    </div>
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
                <li class="page-item {{ $usuarios->onFirstPage() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $usuarios->previousPageUrl() }}" tabindex="-1" aria-disabled="{{ $usuarios->onFirstPage() ? 'true' : 'false' }}">Previous</a>
                </li>

                <!-- Enlaces de páginas -->
                @for ($i = 1; $i <= $usuarios->lastPage(); $i++)
                    <li class="page-item {{ $i == $usuarios->currentPage() ? 'active' : '' }}">
                        <a class="page-link" href="{{ $usuarios->url($i) }}">{{ $i }}</a>
                    </li>
                @endfor

                <!-- Enlace "Siguiente" -->
                <li class="page-item {{ !$usuarios->hasMorePages() ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $usuarios->nextPageUrl() }}">Next</a>
                </li>
            </ul>
        </nav>


@endsection
<script>
    // Ocultar el mensaje de éxito después de 3 segundos
    setTimeout(function() {
        document.getElementById('resultMessage').style.display = 'none';
    }, 3000); // 3000 milisegundos = 3 segundos



</script>
