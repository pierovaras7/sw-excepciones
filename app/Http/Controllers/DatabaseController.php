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
            'port' => $credentials['port'], // Puedes ajustar el puerto si es necesario
            'database' => $credentials['database'],
            'username' => $credentials['username'],
            'password' => $credentials['password'] ?? '',
        ]);
    }

    public function connect(Request $request){

        $credentials = $request->only('host', 'database','port','username', 'password', 'db_type');

        // Instanciar conexiones de la base de datos
        $this->instanciarConexion($credentials);

        //dd($credentials);
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
           // dd($e);
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
        $dbType = session()->get('credencialesConsulta')['db_type'];
        if($dbType == 'mysql'){
            $tablas = DB::connection('consulta')->select('SHOW TABLES');
        }else if($dbType == 'sqlsrv'){
            $tablas = DB::connection('consulta')->select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE'");
        }
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
        $dbType = session()->get('credencialesConsulta')['db_type'];
        try {
            $this->reiniciaConexion($request);
            if($dbType == 'mysql'){
                $columns = DB::connection('consulta')->select('SHOW COLUMNS FROM ' . $tableName . '');
            }else if($dbType == 'sqlsrv'){
                
                $columns = DB::connection('consulta')->select("SELECT COLUMN_NAME AS 'Field', DATA_TYPE AS 'Type', IS_NULLABLE AS 'Null', COALESCE(COLUMN_DEFAULT, ' ') AS 'Default'
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_NAME = '$tableName'");
            }
            //dd($columns);
            return response()->json(['message' => 'Recibiendo informacion de la tabla...','columnas' => $columns]);
            //return response()->json(['message' => 'sss']);
        } catch (\Exception $e) {
            // Manejar cualquier excepción que ocurra durante la consulta
            return response()->json(['message' => 'Error al cargar la tabla.']);
        }
    }

    public function exRegistrosShow(Request $request){
        $this->reiniciaConexion($request);
        $dbType = session()->get('credencialesConsulta')['db_type'];
        if($dbType == 'mysql'){
            $tablas = DB::connection('consulta')->select('SHOW TABLES');
        }else if($dbType == 'sqlsrv'){
            $tablas = DB::connection('consulta')->select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE'");
        }
        // Formatear los resultados
        $listaTablas = [];
        foreach ($tablas as $tabla) {
            $nombreTabla = reset($tabla); // Obtener el nombre de la tabla del primer elemento del array
            $listaTablas[] = $nombreTabla;
        }

        //dd($listaTablas);
        //return view('layout.registros');
        return view('layout.registros', ['tablas' => $listaTablas]);
    }

    public function exRegistrosResult(Request $request){
        try {
            $this->reiniciaConexion($request);

            
            $excepcion = $request->input('excepcion', null);
            $tabla = $request->input('tabla', null);
            $columna = $request->input('columna', null);
            

                // Primero, validar si la columna es autoincrementable
                $dbType = session()->get('credencialesConsulta')['db_type'];
                if($dbType == 'mysql'){
                    $nombresColumnas = DB::connection('consulta')->select('SHOW COLUMNS FROM ' . $tabla);
                }else if($dbType == 'sqlsrv'){
                    $columns = DB::connection('consulta')->select("SELECT COLUMN_NAME AS 'Field', DATA_TYPE AS 'Type', IS_NULLABLE AS 'Null', COALESCE(COLUMN_DEFAULT, ' ') AS 'Default'
                    FROM INFORMATION_SCHEMA.COLUMNS
                    WHERE TABLE_NAME = '$tableName'");        
                }
                //$nombresColumnas = DB::connection('consulta')->select('SHOW COLUMNS FROM ' . $tabla);
                $registros = DB::connection('consulta')->table($tabla);

                $isAutoIncrement = false;

                foreach ($nombresColumnas as $col) {
                    if ($col->Field == $columna && strpos($col->Extra, 'auto_increment') !== false) {
                        $isAutoIncrement = true;
                        break;
                    }
                }

                if (!$isAutoIncrement) {
                    return response()->json(['campoInvalido' => 'La columna especificada no puede realizar un analisis de secuencialidad.']);
                }

                if($excepcion == 'secuencia'){
                    //dd('hola');
                    $resultados = DB::connection('consulta')->select("
                        WITH RECURSIVE num_series AS (
                        SELECT MIN($columna) AS start_id, MAX($columna) AS end_id FROM $tabla
                        UNION ALL
                        SELECT start_id + 1, end_id FROM num_series WHERE start_id < end_id
                        )
                        SELECT CONCAT('Falta el registro con ID ', ns.start_id) AS message
                        FROM num_series ns
                        LEFT JOIN $tabla tt ON ns.start_id = tt.$columna
                        WHERE tt.$columna IS NULL
                        ORDER BY ns.start_id;
                    ");
                    
                    
                    
                    return response()->json(['message' => 'Recibiendo resultados de la excepcion...','results' => $resultados,'columnas' => $nombresColumnas,
                    'datos' => $registros,'exceptions'=>'Secuencia']);
                }else {
                    // Obtener los nombres de las columnas de la tabla
                    $columns = DB::connection('consulta')->getSchemaBuilder()->getColumnListing($tabla);
                    
                    // Construir la parte de la consulta que especifica las columnas a agrupar
                    $groupedByColumns = implode(", ", $columns);
                    //dd($groupedByColumns);
                    // Construir y ejecutar la consulta
                    $query = "SELECT $groupedByColumns, COUNT(*) as repetitions
                              FROM $tabla
                              GROUP BY $groupedByColumns
                              HAVING COUNT(*) > 1";
                    $resultados = DB::connection('consulta')->select($query);
                    
                    // Devolver los resultados
                    if (count($resultados) > 0) {
                        return response()->json(['message' => 'Se encontraron registros duplicados.', 'results' => $resultados]);
                    } else {
                        return response()->json(['message' => 'Todos los registros son únicos.']);
                    }
                }
                
            //return response()->json(['message' => 'sss']);
        } catch (\Exception $e) {
            // Manejar cualquier excepción que ocurra durante la consulta
            return response()->json(['message' => 'Error al cargar la tabla.']);
        }
    }

    public function prueba(Request $request){
    
            $this->reiniciaConexion($request);

            $excepcion = $request->input('excepcion', null);
            $tabla = $request->input('tabla', null);
            $columna = $request->input('columna', null);
            

                // Primero, validar si la columna es autoincrementable
                $dbType = session()->get('credencialesConsulta')['db_type'];
                if($dbType == 'mysql'){
                    $nombresColumnas = DB::connection('consulta')->select('SHOW COLUMNS FROM ' . $tabla);
                }else if($dbType == 'sqlsrv'){
                    $nombresColumnas = DB::connection('consulta')->select("SELECT COLUMN_NAME AS 'Field', DATA_TYPE AS 'Type', IS_NULLABLE AS 'Null', COALESCE(COLUMN_DEFAULT, ' ') AS 'Default'
                    FROM INFORMATION_SCHEMA.COLUMNS
                    WHERE TABLE_NAME = '$tabla'");        
                }
                //$nombresColumnas = DB::connection('consulta')->select('SHOW COLUMNS FROM ' . $tabla);
                $registros = DB::connection('consulta')->select('SELECT * FROM ' . $tabla);
                $total = DB::connection('consulta')->table($tabla)->count();

                //dd($registros);
                $isAutoIncrement = false;

                foreach ($nombresColumnas as $col) {
                    if ($col->Field == $columna && strpos($col->Extra, 'auto_increment') !== false) {
                        $isAutoIncrement = true;
                        break;
                    }
                }

                if (!$isAutoIncrement) {
                    return response()->json(['campoInvalido' => 'La columna especificada no puede realizar un analisis de secuencialidad.','exception'=>'Secuencia',
                    'columnas' => $nombresColumnas,
                    'datos' => $registros]);
                }

                if($excepcion == 'secuencia'){
                    $resultados = DB::connection('consulta')->select("
                        WITH RECURSIVE num_series AS (
                        SELECT MIN($columna) AS start_id, MAX($columna) AS end_id FROM $tabla
                        UNION ALL
                        SELECT start_id + 1, end_id FROM num_series WHERE start_id < end_id
                        )
                        SELECT CONCAT('Falta el registro con ID ', ns.start_id) AS message
                        FROM num_series ns
                        LEFT JOIN $tabla tt ON ns.start_id = tt.$columna
                        WHERE tt.$columna IS NULL
                        ORDER BY ns.start_id;
                    ");
                    return response()->json(['message' => 'Recibiendo resultados de la excepcion...','results' => $resultados,'total' => $total,'columnas' => $nombresColumnas,
                    'datos' => $registros,'exception'=>'Secuencia']);
                }else {
                    // Obtener los nombres de las columnas de la tabla
                    
                    $primaryKeyColumns = DB::connection('consulta')->select("
                        SELECT COLUMN_NAME
                        FROM information_schema.KEY_COLUMN_USAGE
                        WHERE TABLE_NAME = ? AND CONSTRAINT_NAME = 'PRIMARY'
                    ", [$tabla]);

                    // Inicializar un arreglo para almacenar los nombres de las columnas de la clave primaria
                    $primaryKeyColumnNames = [];

                    foreach ($primaryKeyColumns as $column) {
                        $primaryKeyColumnNames[] = $column->COLUMN_NAME;
                    }

                    // Ahora $primaryKeyColumnNames contiene los nombres de todas las columnas de la clave primaria
                    // Puedes usar dd() para volcar y morir la variable para inspeccionarla
                    dd($primaryKeyColumnNames);
                    // $primaryKeyColumn = DB::connection('consulta')->select("
                    //     SELECT COLUMN_NAME
                    //     FROM information_schema.KEY_COLUMN_USAGE
                    //     WHERE TABLE_NAME = ? AND CONSTRAINT_NAME = 'PRIMARY'
                    // ", [ $tabla]);


                    // dd($primaryKeyColumn[0]->COLUMN_NAME);
                    // Construir la parte de la consulta que especifica las columnas a agrupar
                    $groupedByColumns = implode(", ", $columns);
                    //dd($groupedByColumns);
                    // Construir y ejecutar la consulta
                    $query = "SELECT $groupedByColumns, COUNT(*) as repetitions
                              FROM $tabla
                              GROUP BY $groupedByColumns
                              HAVING COUNT(*) > 1";
                    $resultados = DB::connection('consulta')->select($query);
                    
                    // Devolver los resultados
                    // if (count($resultados) > 0) {
                    //     return response()->json(['message' => 'Se encontraron registros duplicados.', 'results' => $resultados]);
                    // } else {
                    //     return response()->json(['message' => 'Todos los registros son únicos.']);
                    // }
                }
                
            //return response()->json(['message' => 'sss']);
   
    }

    public function exCamposShow(Request $request){
        $this->reiniciaConexion($request);
        $tablas = DB::connection('consulta')->select('SHOW TABLES');
        // Formatear los resultados
        $listaTablas = [];
        foreach ($tablas as $tabla) {
            $nombreTabla = reset($tabla); // Obtener el nombre de la tabla del primer elemento del array
            $listaTablas[] = $nombreTabla;
        }

        return view('layout.campos', ['tablas' => $listaTablas]);
    }

    public function exCamposResult(Request $request){
    
        $this->reiniciaConexion($request);
        $excepcion = $request->input('excepcion', null);
        $tabla = $request->input('tabla', null);
        $columna = $request->input('columna', null);
        

            // Primero, validar si la columna es autoincrementable
            $nombresColumnas = DB::connection('consulta')->select('SHOW COLUMNS FROM ' . $tabla);
            $registros = DB::connection('consulta')->select('SELECT * FROM ' . $tabla);
            $total = DB::connection('consulta')->table($tabla)->count();

            //dd($registros);
            $isAutoIncrement = false;

            foreach ($nombresColumnas as $col) {
                if ($col->Field == $columna && strpos($col->Extra, 'auto_increment') !== false) {
                    $isAutoIncrement = true;
                    break;
                }
            }

            if (!$isAutoIncrement) {
                return response()->json(['campoInvalido' => 'La columna especificada no puede realizar un analisis de secuencialidad.','exception'=>'Secuencia',
                'columnas' => $nombresColumnas,
                'datos' => $registros]);
            }

            if($excepcion == 'secuencia'){
                $resultados = DB::connection('consulta')->select("
                    WITH RECURSIVE num_series AS (
                    SELECT MIN($columna) AS start_id, MAX($columna) AS end_id FROM $tabla
                    UNION ALL
                    SELECT start_id + 1, end_id FROM num_series WHERE start_id < end_id
                    )
                    SELECT CONCAT('Falta el registro con ID ', ns.start_id) AS message
                    FROM num_series ns
                    LEFT JOIN $tabla tt ON ns.start_id = tt.$columna
                    WHERE tt.$columna IS NULL
                    ORDER BY ns.start_id;
                ");
                return response()->json(['message' => 'Recibiendo resultados de la excepcion...','results' => $resultados,'total' => $total,'columnas' => $nombresColumnas,
                'datos' => $registros,'exception'=>'Secuencia']);
            }else {
                // Obtener los nombres de las columnas de la tabla
                
                $primaryKeyColumns = DB::connection('consulta')->select("
                    SELECT COLUMN_NAME
                    FROM information_schema.KEY_COLUMN_USAGE
                    WHERE TABLE_NAME = ? AND CONSTRAINT_NAME = 'PRIMARY'
                ", [$tabla]);

                // Inicializar un arreglo para almacenar los nombres de las columnas de la clave primaria
                $primaryKeyColumnNames = [];

                foreach ($primaryKeyColumns as $column) {
                    $primaryKeyColumnNames[] = $column->COLUMN_NAME;
                }

                // Ahora $primaryKeyColumnNames contiene los nombres de todas las columnas de la clave primaria
                // Puedes usar dd() para volcar y morir la variable para inspeccionarla
                dd($primaryKeyColumnNames);
                // $primaryKeyColumn = DB::connection('consulta')->select("
                //     SELECT COLUMN_NAME
                //     FROM information_schema.KEY_COLUMN_USAGE
                //     WHERE TABLE_NAME = ? AND CONSTRAINT_NAME = 'PRIMARY'
                // ", [ $tabla]);


                // dd($primaryKeyColumn[0]->COLUMN_NAME);
                // Construir la parte de la consulta que especifica las columnas a agrupar
                $groupedByColumns = implode(", ", $columns);
                //dd($groupedByColumns);
                // Construir y ejecutar la consulta
                $query = "SELECT $groupedByColumns, COUNT(*) as repetitions
                          FROM $tabla
                          GROUP BY $groupedByColumns
                          HAVING COUNT(*) > 1";
                $resultados = DB::connection('consulta')->select($query);
                
                // Devolver los resultados
                // if (count($resultados) > 0) {
                //     return response()->json(['message' => 'Se encontraron registros duplicados.', 'results' => $resultados]);
                // } else {
                //     return response()->json(['message' => 'Todos los registros son únicos.']);
                // }
            }
            
        //return response()->json(['message' => 'sss']);

}
}
