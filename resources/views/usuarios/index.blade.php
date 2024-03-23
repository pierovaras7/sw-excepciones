@extends('layout.layout')

@section('contenido')
                    <!-- Page Heading -->
                    <h1 class="h3 mb-2 text-gray-800">Usuarios</h1>
                    <p class="mb-4">DataTables is a third party plugin that is used to generate the demo table below.
                        For more information about DataTables, please visit the <a target="_blank"
                            href="https://datatables.net">official DataTables documentation</a>.</p>
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
                                                     name="name">
                                                    
                                                </div>
                                                <div class="form-group">
                                                    <label>Email</label>
                                                    <input type="email" class="form-control"
                                                    id="email" name="email">
                                                </div>
                                                <div class="form-group">
                                                    <label for="exampleInputPassword1">Password</label>
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
                    </div>
                    <!-- DataTales Example -->
                    <div class="card shadow mb-4">
                        <div class="card-body">
                            <div class="table-responsive text-center">
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Usuario</th>
                                            <th>Email</th>
                                            <th>Ultimo inicio de sesion</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        @foreach($usuarios as $u)
                                        <tr>
                                            <th>{{$u->id}}</th>
                                            <th>{{$u->name}}</th>
                                            <th>{{$u->email}}</th>
                                            
                                            <th>
                                                <a href="#" class="btn btn-warning btn-circle btn-sm" data-bs-id="{{$u->id}}" data-toggle="modal" data-target="#editModal{{$u->id}}">
                                                    <i class="fas fa-exclamation-triangle"></i>
                                                </a>
                                                <div class="modal fade" id="editModal{{$u->id}}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
                                                    aria-hiddzen="true">
                                                    <div class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <form action="{{ route('usuarios.update', $u->id) }}" method="POST">
                                                                <div class="modal-header">
                                                                    <h5 class="modal-title" id="exampleModalLabel">Editar usuario</h5>
                                                                    <button class="close" type="button" data-dismiss="modal" aria-label="Close">
                                                                        <span aria-hidden="true">×</span>
                                                                    </button>
                                                                </div>
                                                                <div class="modal-body">

                                                                        <input type="hidden" id="id" name="id">
                                                                        <div class="form-group">
                                                                            <label for="exampleInputEmail1">Nombre</label>
                                                                            <input type="text" class="form-control" id="exampleInputEmail1" 
                                                                            value="{{$u->name}}" name="name">
                                                                            
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label>Email</label>
                                                                            <input type="email" class="form-control"
                                                                            value="{{$u->id}}" id="email" name="email">
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label for="exampleInputPassword1">Password</label>
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
                                                <a href="#" class="btn btn-danger btn-circle btn-sm">
                                                    <i class="fas fa-trash"></i>
                                            </th>
                                        </tr>
                                        @endforeach
                                    </tfoot>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <!-- Logout Modal-->

                    
@endsection
<script>
    let editModal = document.getElementById('editModal');

    editModal.addEventListener('show.bs.modal', event => {
        let button = event.relatedTarget
        let id = button.getAttribute('data-bs-id'); 

        let inputId = editarModal.querySelector('.modal-body #id')




    });
</script>