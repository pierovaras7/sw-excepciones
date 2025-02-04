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
            //dd(Auth::id());
            $user = User::findOrFail(Auth::id());
            $user->lastlogin = now()->timezone('America/Lima');
            $user->save();

            LoginHistory::create([
                'user_id' => Auth::id(), // $event->userId contiene el ID del usuario que inició sesión
                'login_at' => now()->timezone('America/Lima')
            ]);

            $request->session()->forget('conexion');
            $request->session()->forget('credencialesConsulta');
            $request->session()->put('userOnLine', $user->name);

            if( Auth::id() != 1){
                return redirect()->intended('connect')->withSuccess('Logeado Correctamente');
            }else{
	            return redirect()->intended('usuarios')->withSuccess('Logeado Correctamente');
            }
	    }
        return back()->withErrors(['message' => 'Las credenciales proporcionadas no son válidas.']);
    }

    public function logout()
    {
        $latestLogin = LoginHistory::where('user_id', Auth::id())
            ->latest('login_at')
            ->first();
        $latestLogin->logout_at = now()->timezone('America/Lima');
        $latestLogin->updateTimestamps(); // Evita que Laravel actualice las marcas de tiempo
        $latestLogin->save();

        Auth::logout(); // Cerrar sesión del usuario
        
        return redirect('/'); // Redirigir a la página de inicio de sesión
    }
}
