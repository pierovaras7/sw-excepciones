<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{
    //
    // Método para mostrar todos los usuarios
    public function index()
    {
       // Obtener todos los usuarios
            $usuarios = User::all();

            //$user->loginHistories()->latest('login_at')->first();
            
            // Array para almacenar los registros más recientes de LoginHistory para cada usuario
            // $latest = [];
            $i = 0;
            // // Iterar sobre cada usuario
            foreach ($usuarios as $user) {
                // Obtener el registro más reciente de LoginHistory para este usuario
                $l = $user->loginHistories()->latest('login_at')->first();

                // Agregar el registro más reciente al array
                $latest[$i] = $l;

                $i++;
            }
            //dd($latestLoginHistory);
        return view('usuarios.index', compact('usuarios','latest'));
    }

    // Método para mostrar el formulario de creación de usuario
    public function create()
    {
        return view('usuarios.create');
    }

    // Método para almacenar un nuevo usuario en la base de datos
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required:unique:users,email',
            'password' => 'required'
            // Agrega aquí otras reglas de validación según tus necesidades
        ]);

        User::create($request->all());
        //dd($request->all());
        return redirect()->route('usuarios.index')
                          ->with('success', 'Usuario creado correctamente.');
    }

    // Método para mostrar un usuario específico
    public function show($id)
    {
        $usuario = User::findOrFail($id);
        return view('usuarios.show', compact('usuario'));
    }

    // Método para mostrar el formulario de edición de usuario
    public function edit($id)
    {
        $usuario = User::findOrFail($id);
        return view('usuarios.edit', compact('usuario'));
    }

    // Método para actualizar un usuario en la base de datos
    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required',
            'email' => 'required|unique:users,email,'.$id,
            // Agrega aquí otras reglas de validación según tus necesidades
        ]);

        $usuario = User::findOrFail($id);
        $usuario->update($request->all());

        return redirect()->route('usuarios.index')
                         ->with('success', 'Usuario actualizado correctamente.');
    }

    // Método para eliminar un usuario
    public function destroy($id)
    {
        $usuario = User::findOrFail($id);
        $usuario->delete();

        return redirect()->route('usuarios.index')
                         ->with('success', 'Usuario eliminado correctamente.');
    }
}
