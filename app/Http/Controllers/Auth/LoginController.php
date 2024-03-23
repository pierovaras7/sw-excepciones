<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Events\UserLoggedIn;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LoginHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Importa la clase Hash


class LoginController extends Controller
{
    //
    public function showLoginForm()
    {
        return view('login2'); // Vista de formulario de inicio de sesión
    }

    public function login(Request $request)
    {

        $request->validate([
	        'email' => 'required',
	        'password' => 'required',
	    ]);
	
	    // Almacenamos las credenciales de email y contraseña
	    $credentials = $request->only('email', 'password');
	
	    // Si el usuario existe lo logamos y lo llevamos a la vista de "logados" con un mensaje
	    if (Auth::attempt($credentials)) {
            // dd(Auth::id());
            LoginHistory::create([
                'user_id' => Auth::id(), // $event->userId contiene el ID del usuario que inició sesión
                'login_at' => now()->timezone('America/Lima')
            ]);
	        return redirect()->intended('usuarios')
	            ->withSuccess('Logado Correctamente');
	    }

        //ndd('xxxx');
        // Autenticación fallida
        return back()->withErrors(['email' => 'Las credenciales proporcionadas no son válidas.']);
    }

    public function logout()
    {
        //dd('sssssss');
        Auth::logout(); // Cerrar sesión del usuario
        //dd('sssssss');
        return redirect('/login'); // Redirigir a la página de inicio de sesión
    }
}
