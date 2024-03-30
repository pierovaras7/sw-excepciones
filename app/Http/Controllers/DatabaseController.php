<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Importar el facade DB
use Illuminate\Support\Facades\Config; // Importa la clase Config aquí


class DatabaseController extends Controller
{

    public function showConnectForm(Request $request)
    {
        return view('bdatos.conexion', ['conex' => $request->session()->get('conexion')]);
    }

    protected function instanciarConexion($credentials)
    {
        // Configurar dinámicamente la conexión
        Config::set("database.connections.consulta", [
            'driver' => $credentials['db_type'],
            'host' => $credentials['host'],
            'port' => '3306', // Puedes ajustar el puerto si es necesario
            'database' => $credentials['database'],
            'username' => $credentials['username'],
            'password' => $credentials['password'] ?? '',
        ]);
    }

    public function connect(Request $request){

        $credentials = $request->only('host', 'database', 'username', 'password', 'db_type');

        // Instanciar conexiones de la base de datos
        $this->instanciarConexion($credentials);

        //$configuraciones = Config::get('database.connections');

        // Verificar si la conexión 'consulta' está definida en la configuración
        try{
            // La conexión 'consulta' está definida
            $result = DB::connection('consulta')->select('select 1');

            $request->session()->put('credencialesConsulta', $credentials);
            $request->session()->put('conexion', true);
            
            return response()->json([
                'success' => true,
                'message' => 'La conexión se estableció correctamente.',
            ]);
        } catch(QueryException $e) {
            // La conexión 'consulta' no está definida
            return response()->json([
                'error' => true,
                'message' => 'No se pudo establecer la conexión. Verifica las credenciales proporcionadas.',
            ]);
        }
    }   

    public function reiniciaConexion(Request $request){
        $credentials = $request->session()->get('credencialesConsulta');
        $this->instanciarConexion($credentials);
        //dd(Config::get('database.connections'));
    }

    public function disconnect(Request $request){
        
            // Modificar el valor de la sesión
            $request->session()->put('conexion', false);
            $request->session()->forget('credencialesConsulta');

            // Devolver una respuesta de éxito
            return response()->json([
                'success' => true,
                'message' => 'Se desconectó correctamente.',
            ]);
            
    }

    public function infodb(Request $request){
        $this->reiniciaConexion($request);
        $tablas = DB::connection('consulta')->select('SHOW TABLES');
        // Formatear los resultados
        $listaTablas = [];
        foreach ($tablas as $tabla) {
            $nombreTabla = reset($tabla); // Obtener el nombre de la tabla del primer elemento del array
            $listaTablas[] = $nombreTabla;
        }

        return view('bdatos.informacion', ['tablas' => $listaTablas]);

    }

    public function loadInfo($tableName,Request $request)
    {
        try {
            $this->reiniciaConexion($request);
            $columns = DB::connection('consulta')->select('SHOW COLUMNS FROM ' . $tableName . '');
            return response()->json(['message' => 'Recibiendo informacion de la tabla...','columnas' => $columns]);
            //return response()->json(['message' => 'sss']);
        } catch (\Exception $e) {
            // Manejar cualquier excepción que ocurra durante la consulta
            return response()->json(['message' => 'Error al cargar la tabla.']);
        }
    }

    public function exRegistrosShow(Request $request){
        $this->reiniciaConexion($request);
        $tablas = DB::connection('consulta')->select('SHOW TABLES');
        // Formatear los resultados
        $listaTablas = [];
        foreach ($tablas as $tabla) {
            $nombreTabla = reset($tabla); // Obtener el nombre de la tabla del primer elemento del array
            $listaTablas[] = $nombreTabla;
        }
        return view('excepciones.registros', ['tablas' => $listaTablas]);

        // $columns = DB::connection('consulta')->select('SHOW COLUMNS FROM ' . $tableName . '');

        // try {
        //     $this->reiniciaConexion($request);
        //     $columns = DB::connection('consulta')->select('SHOW COLUMNS FROM ' . $tableName . '');
        //     return response()->json(['message' => 'Recibiendo informacion de la tabla...','columnas' => $columns]);
        //     //return response()->json(['message' => 'sss']);
        // } catch (\Exception $e) {
        //     // Manejar cualquier excepción que ocurra durante la consulta
        //     return response()->json(['message' => 'Error al cargar la tabla.']);
        // }
    }
    
}
