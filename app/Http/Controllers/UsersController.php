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
        $usuarios = User::with('loginHistories')->paginate(10);
        return view('usuarios.index', compact('usuarios'));
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
        ], [
            '*' => 'Error al crear usuario.'
        ]);


        // Intenta crear el usuario
        try {
            User::create($request->all());
            return redirect()->route('usuarios.index')
                ->with('result', 'success')
                ->with('message', 'Usuario creado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('usuarios.index')
                ->with('result', 'danger')
                ->with('message', 'Error al registrar el usuario.');
        }
    }

    // Método para mostrar un usuario específico
    public function show($id)
    {
        

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
            'name' => 'required',
            'email' => 'required|unique:users,email,'.$id,
            'password'  => ''
            // Agrega aquí otras reglas de validación según tus necesidades
        ]);

        try {
            $usuario = User::findOrFail($id);
            $usuario->name = $request->name;
            $usuario->email = $request->email;
            if ($request->password != '' && $request->password != null){
                $usuario->password = $request->password;
            }
            $usuario->save();
            return redirect()->route('usuarios.index')
                ->with('result', 'success')
                ->with('message', 'Usuario actualizado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('usuarios.index')
                ->with('result', 'danger')
                ->with('message', 'Error al actualizar el usuario.');
        }
    }

    // Método para eliminar un usuario
    public function destroy($id)
    {
        // Intenta crear el usuario
        try {
            $usuario = User::findOrFail($id);
            $usuario->delete();
            return redirect()->route('usuarios.index')
                ->with('result', 'success')
                ->with('message', 'Usuario eliminado correctamente.');
        } catch (\Exception $e) {
            return redirect()->route('usuarios.index')
                ->with('result', 'danger')
                ->with('message', 'Error al eliminar el usuario.');
        }
    }
}
