<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; // Importar el facade DB
use Illuminate\Support\Facades\Config; // Importa la clase Config aquí
use Illuminate\Support\Facades\Auth;
use App\Models\Conexion;
use App\Models\User;
use App\Models\Historial;
use Barryvdh\DomPDF\Facade\Pdf;



class DatabaseController extends Controller
{

    //  Rutas GET:

    public function reporteSec(Request $request){
        $resultados = json_decode($request->input('resultados'));
        $fecha = now()->timezone('America/Lima')->toDateTimeString();
        $user = User::findOrFail(Auth::id())->name;
        $bd = session()->get('credencialesConsulta')['database'];
        $pdf = Pdf::loadView('pdfs.pdfSecuencialidad',compact('resultados','fecha','user','bd'));
        return $pdf->stream();
    }

    public function reporteUni(Request $request){
        $resultados = json_decode($request->input('resultados'));
        $fecha = now()->timezone('America/Lima')->toDateTimeString();
        $user = User::findOrFail(Auth::id())->name;
        $bd = session()->get('credencialesConsulta')['database'];
        $pdf = Pdf::loadView('pdfs.pdfUnicidad',compact('resultados','fecha','user','bd'));
        return $pdf->stream();
    }

    public function reporteCam(Request $request){
        $resultados = json_decode($request->input('resultados'));
        $fecha = now()->timezone('America/Lima')->toDateTimeString();
        $user = User::findOrFail(Auth::id())->name;
        $bd = session()->get('credencialesConsulta')['database'];
        $pdf = Pdf::loadView('pdfs.pdfCampos',compact('resultados','fecha','user','bd'));
        return $pdf->stream();
    }

    public function reporteTab(Request $request){
        $resultados = json_decode($request->input('resultados'));
        $fecha = now()->timezone('America/Lima')->toDateTimeString();
        $user = User::findOrFail(Auth::id())->name;
        $bd = session()->get('credencialesConsulta')['database'];
        $pdf = Pdf::loadView('pdfs.pdfTablas',compact('resultados','fecha','user','bd'));
        return $pdf->stream();
    }

    public function reporteSQL(Request $request){
        $resultados = json_decode($request->input('resultados'));
        $fecha = now()->timezone('America/Lima')->toDateTimeString();
        $user = User::findOrFail(Auth::id())->name;
        $bd = session()->get('credencialesConsulta')['database'];
        $pdf = Pdf::loadView('pdfs.pdfSQL',compact('resultados','fecha','user','bd'));
        return $pdf->stream();
    }
    // Vista del form Conexion:
    public function showConnectForm(Request $request)
    {
        if(!$request->session()->get('credencialesConsulta')){
            $request->session()->put('conexion', false);
        }
        $conexiones = Conexion::orderBy('last_use', 'desc')->take(5)->get();
        return view('bdatos.conexion', ['conex' => $request->session()->get('conexion'),'conexiones' => $conexiones]);
    }

    // Vista a la informacion de las tablas de la base de datos conectada.
    public function infodb(Request $request){
        $this->reiniciaConexion($request);
        $dbType = session()->get('credencialesConsulta')['db_type'];
        
        $tablas = $this->selectTablesByGestor($dbType);

        // Formatear los resultados
        $listaTablas = [];
        foreach ($tablas as $tabla) {
            $nombreTabla = reset($tabla); // Obtener el nombre de la tabla del primer elemento del array
            $listaTablas[] = $nombreTabla;
        }

        return view('bdatos.informacion', ['tablas' => $listaTablas]);

    }

    // Informacion de las tablas (columnas)
    public function loadInfo($tableName,Request $request)
    {
        try {
            $this->reiniciaConexion($request);
            $dbType = session()->get('credencialesConsulta')['db_type'];
            $columns = $this->selectColumnsByTable($dbType,$tableName);
            //dd($columns);
            return response()->json(['message' => 'Recibiendo informacion de la tabla...','columnas' => $columns]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al cargar la tabla.']);
        }
    }

    public function loadInfoRel($tableName,Request $request)
    {
        //return $tableName;
        try {
            $this->reiniciaConexion($request);
            $dbType = session()->get('credencialesConsulta')['db_type'];
            $columns = $this->selectColumnsRelByTable($dbType,$tableName);
            return response()->json(['message' => 'Recibiendo informacion de la tabla...','columnas' => $columns]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al cargar la tabla.']);
        }
    }

    public function obtenerTablas(Request $request)
    {
        try {
            $this->reiniciaConexion($request);
            $dbType = session()->get('credencialesConsulta')['db_type'];
            $tablas = $this->selectTablesByGestor($dbType);
            // Formatear los resultados
            $listaTablas = [];
            foreach ($tablas as $tabla) {
                $nombreTabla = reset($tabla); // Obtener el nombre de la tabla del primer elemento del array
                $listaTablas[] = $nombreTabla;
            }
            return response()->json(['message' => 'Recibiendo informacion de la base...','tablas' => $listaTablas]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error al cargar las tablas.']);
        }
    }

    // Vista de la excepcion Secuencialidad
    public function exRegistrosShow(Request $request){
        $this->reiniciaConexion($request);
        $dbType = session()->get('credencialesConsulta')['db_type'];
        $tablas = $this->selectTablesByGestor($dbType);
        // Formatear los resultados
        $listaTablas = [];
        foreach ($tablas as $tabla) {
            $nombreTabla = reset($tabla); // Obtener el nombre de la tabla del primer elemento del array
            $listaTablas[] = $nombreTabla;
        }

        return view('layout.registros', ['tablas' => $listaTablas]);
    }

    // Vista de la excepcion Campos
    public function exCamposShow(Request $request){
        $this->reiniciaConexion($request);
        $dbType = session()->get('credencialesConsulta')['db_type'];
        $tablas = $this->selectTablesByGestor($dbType);
        // Formatear los resultados
        $listaTablas = [];
        foreach ($tablas as $tabla) {
            $nombreTabla = reset($tabla); // Obtener el nombre de la tabla del primer elemento del array
            $listaTablas[] = $nombreTabla;
        }

        return view('layout.campos', ['tablas' => $listaTablas]);
    }

    // Vista de la excepcion Campos
    public function exTablasShow(Request $request){
        $this->reiniciaConexion($request);
        $dbType = session()->get('credencialesConsulta')['db_type'];
        $tablas = $this->selectTablesByGestor($dbType);
        // Formatear los resultados
        $listaTablas = [];
        foreach ($tablas as $tabla) {
            $nombreTabla = reset($tabla); // Obtener el nombre de la tabla del primer elemento del array
            $listaTablas[] = $nombreTabla;
        }

        return view('layout.tablas', ['tablas' => $listaTablas]);
    }
    
    
    public function viewHistorial(Request $request){
        $historiales = Historial::where('user', Auth::id())->where('conexion',session()->get('credencialesConsulta')['id'])->paginate(15);
        return view('layout.historial', ['historiales' => $historiales]);
    }

    public function filtrarHistorialPor($tipo){
        $historiales = Historial::where('user', Auth::id())->where('tipo', $tipo)->where('conexion',session()->get('credencialesConsulta')['id'])->paginate(15);
        if ($historiales->isEmpty()) {
            $historiales = Historial::where('user', Auth::id())->where('conexion',session()->get('credencialesConsulta')['id'])->paginate(15);
            if($tipo == 'todos'){
                return response()->json(['historiales' => $historiales]);
            }
            else{
                return response()->json(['historiales' => $historiales,'noresult' => true]);
            }
            
        }
        return response()->json(['historiales' => $historiales]);
    }

    public function vistaConsultaSQL(Request $request){
        return view('layout.consulta');
    }

    public function consultar(Request $request){
        $query =$request->input('query');
        $this->reiniciaConexion($request);
        try {
            $registros = DB::connection('consulta')->select($query);
            if(!$registros){
                $query = 'No hay excepciones';
            }
            Historial::create([
                'conexion' => session()->get('credencialesConsulta')['id'],
                'user' => Auth::id(),
                'fecha' => now()->timezone('America/Lima')->toDateTimeString(),
                'tipo' => 'scriptsql',
                'tabla' => 'test',
                'resultado' => $query
            ]);
            return response()->json(['message' => $query,'datos' => $registros]);
        } catch (\Exception $e) {
            // En caso de error, capturar la excepción y devolver un mensaje de error
            return response()->json(['message' => 'Error al ejecutar la consulta.', 'error' => $e->getMessage()], 500);
        }
    }



    // Rutas POST:

    // Obtener las credenciales desde form y conectar al gestor
    public function connect(Request $request){
        $credentials = $request->only('host', 'database','port','username', 'password', 'db_type');

        // Instanciar conexiones de la base de datos
        $this->instanciarConexion($credentials);

        // Verificar si la conexión 'consulta' está definida en la configuración
        try{
            // La conexión 'consulta' está definida y activa?
            $result = DB::connection('consulta')->select('select 1');

            $conexion = Conexion::firstOrCreate(
                [
                    'db_type' => $credentials['db_type'],
                    'host' => $credentials['host'],
                    'port' => $credentials['port'],
                    'database' => $credentials['database'],
                    'username' => $credentials['username'],
                    //'password' => $credentials['password'] ?? ''
                    // No incluir la contraseña como criterio de búsqueda
                ],
                $credentials // Todos los datos se usarán para crear el registro si no existe
            );

            $conexion->last_use= now()->timezone('America/Lima');
            $conexion->save();

            // Guardar valores para verificar la configuracion hecha en la sesion.
            $request->session()->put('credencialesConsulta', $conexion);
            $request->session()->put('conexion', true);
            
            return response()->json([
                'success' => true,
                'message' => 'La conexión se estableció correctamente.',
            ]);
        } catch(QueryException $e) {
            return response()->json([
                'error' => true,
                'message' => 'No se pudo establecer la conexión. Verifica las credenciales proporcionadas.',
            ]);
        }
    }   

    // Obtener los resultados de la exceccion Secuencialidad
    public function resultSecuencialidad(Request $request){
    
        $this->reiniciaConexion($request);

        $excepcion = $request->input('excepcion', null);
        $tabla = $request->input('tabla', null);
        $columna = $request->input('columna', null);

        // Primero, validar si la columna es autoincrementable
        $dbType = session()->get('credencialesConsulta')['db_type'];
        
        $nombresColumnas = $this->selectColumnsByTable($dbType,$tabla); //   

        $registros = DB::connection('consulta')->select('SELECT * FROM ' . $tabla);
        $total = DB::connection('consulta')->table($tabla)->count();

        if($excepcion == 'secuencia'){
            $isAutoIncrement = false;
            foreach ($nombresColumnas as $col) {
                if ($col->Field == $columna && strpos($col->Extra, 'auto_increment') !== false) {
                    $isAutoIncrement = true;
                    break;
                }
            }

            if (!$isAutoIncrement) {
                return response()->json(['errortipo' => 'La columna especificada no puede realizar un analisis de secuencialidad.','exception'=>'Secuencia',
                'columnas' => $nombresColumnas,
                'datos' => $registros]);
            }

            try {
                $query = $this->querySecuencialidad($dbType,$tabla,$columna);
                $resultados = DB::connection('consulta')->select($query);
                
                if(!$resultados){
                    $query = 'No hay excepciones';
                }


                Historial::create([
                    'conexion' => session()->get('credencialesConsulta')['id'],
                    'user' => Auth::id(),
                    'fecha' => now()->timezone('America/Lima')->toDateTimeString(),
                    'tipo' => 'secuencialidad',
                    'tabla' => $tabla,
                    'resultado' => $query
                ]);

                if(!empty($resultados)){
                    return response()->json(['message' => 'Recibiendo resultados de la excepcion...','results' => $resultados,'total' => $total,'columnas' => $nombresColumnas,
                    'datos' => $registros,'exception'=>'Secuencia']);

                }else{
                    return response()->json(['total' => $total,'columnas' => $nombresColumnas,
                    'datos' => $registros,'exception'=>'Secuencia','noresult' => 'No se encontraron excepciones de secuencialidad.']);
                }
            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()]);
            }
        }else{
            try {
                $columnas = array_column($nombresColumnas, 'Field');
                // Preparar las columnas para la selección y el agrupamiento
                if($dbType == 'mysql'){
                    $selectGroupColumns = "`" . implode("`, `", $columnas) . "`";

                    $columnasParaMensaje = implode(", ', ', ", array_map(function($col) {
                        return "CAST(`$col` AS CHAR)";
                    }, $columnas));

                    // Construir la consulta completa con el mensaje
                    $query = "SELECT CONCAT('Se encontraron registros duplicados para este registro. Informacion de registro: ', $columnasParaMensaje, '. Numero de existencias: ', COUNT(*)) AS 'message' FROM `$tabla` GROUP BY $selectGroupColumns HAVING COUNT(*) > 1";
                }else{
                    $columnas = array_column($nombresColumnas, 'Field');
                    // Preparar las columnas para la selección y el agrupamiento
                    $selectGroupColumns = "[" . implode("], [", $columnas) . "]";

                    $columnasParaMensaje = implode(" + ', ' + ", array_map(function($col) {
                        return "CAST([$col] AS NVARCHAR(MAX))";
                    }, $columnas));

                    // Construir la consulta completa con el mensaje
                    $query = "SELECT CONCAT('Se encontraron registros duplicados para este registro. Informacion de registro: ', $columnasParaMensaje, '. Numero de existencias: ', COUNT(*)) AS [message] FROM [$tabla] GROUP BY $selectGroupColumns HAVING COUNT(*) > 1";

                }


                //$query = "SELECT `" . implode("`, `", $columnas) . "`, COUNT(*) AS Existencias FROM `$tabla` GROUP BY `" . implode("`, `", $columnas) . "`";
                $resultados = DB::connection('consulta')->select($query);
                //dd($resultados.length);

                // if(!$resultados){
                //     $query = 'No hay excepciones';
                // }
                
                $hist = Historial::create([
                    'conexion' => session()->get('credencialesConsulta')['id'],
                    'user' => Auth::id(),
                    'fecha' => now()->timezone('America/Lima')->toDateTimeString(),
                    'tipo' => 'unicidad',
                    'tabla' => $tabla,
                    'resultado' => 'Se encontraron ' . count($resultados) . ' excepciones.'
                ]);

                // Generar PDF
                $pdf = PDF::loadView('reporte'.$hist->id, $data);
                return $pdf->download('reporte_excepciones.pdf');

            
                if(!empty($resultados)){
                    return response()->json(['message' => 'Recibiendo resultados de la excepcion...','results' => $resultados,'total' => $total,'columnas' => $nombresColumnas,
                    'datos' => $registros,'exception'=>'Duplicidad']);

                }else{
                    return response()->json(['total' => $total,'columnas' => $nombresColumnas,
                    'datos' => $registros,'exception'=>'Duplicidad','noresult' => 'No se encontraron excepciones de unicidad.']);
                }

            } catch (\Exception $e) {
                return response()->json(['error' => $e->getMessage()]);
            }
        }
    }
    public function resultCampos(Request $request){

        $tiposFecha = ['date', 'datetime', 'timestamp', 'time', 'year', 'datetime2', 'smalldatetime', 'datetimeoffset'];

        $tiposStrings = ['char','tinytext','mediumtext','longtext','text','tinytext','binary','varchar', 'text', 'enum', 'set', 'nvarchar', 'nchar', 'ntext'];

        $tiposNumericos = [ 'int', 'integer', 'smallint', 'mediumint', 'bigint', 'decimal','smallmoney','money' ,'numeric', 'float', 'double', 'real' ];

        $tiposBooleano = [ 'bit','boolean','bool','tinyint'];


        $type = $request->typecolumna;
        $tabla = $request->tabla;
        $column = $request->columna;

        // Función para verificar la coincidencia
        $typeMatches = function($type, $typeArray) {
            foreach ($typeArray as $typeValue) {
                if (strpos($type, $typeValue) === 0) {
                    return true; // Retorna verdadero si encuentra una coincidencia al inicio
                }
            }
            return false; // Retorna falso si no encuentra coincidencias al inicio
        };

        $param = '';
        
        // Verificar si $type está en alguno de los arrays de tipos
        if ($typeMatches($type, $tiposFecha)) {
            //dd('Es fecha');
            $first = $request->input('fechaInicioValue',null);
            $last = $request->input('fechaFinValue',null);
            $conditionAdded = false;
            if ($first !== null) {
                $param .= "WHERE " . $column . " < '" . $first . "'";
                $conditionAdded = true;
            }
            if ($last !== null) {
                $param .= ($conditionAdded ? ' or ' : 'WHERE ') . $column . " < '" . $last . "'";
            }
           // $param .= ' AND ' . $column . ' IS NOT NULL';
        } else if ($typeMatches($type, $tiposStrings)) {
            $value = $request->input('stringValue',null);
            if ($value != null) {
                $param = 'WHERE ' . $column . " != '" . $value . "'";
            }
            //$param .= ' AND ' . $column . ' IS NOT NULL';
        }else if ($typeMatches($type, $tiposNumericos)) {
            // dd('Es numero');
            $min = $request->input('minValue',null);
            $max = $request->input('maxValue',null);
            $conditionAdded = false;
            if ($min !== null) {
                $param .= 'WHERE ' . $column . ' < ' . $min;
                $conditionAdded = true;
            }
            if ($max !== null) {
                $param .= ($conditionAdded ? ' or ' : 'WHERE ') . $column . ' > ' . $max;
            }
           // $param .= ' AND ' . $column . ' IS NOT NULL';
        }else if ($typeMatches($type, $tiposBooleano)) {
            //dd('Es bool');
            $bool = $request->input('boolValue','false');
            $param = "WHERE " . $column . " != '" . $bool . "'" ;
            //$param .= ' AND ' . $column . ' IS NOT NULL';
        };
        //dd($param);
        $isNull = $request->input('isNull','NOT NULL');
        if($param == ''){
            $param .= ' WHERE ' . $column . ' IS ' . $isNull;
        }else{
            $param .= ' and (' . $column . ' IS ' . $isNull . ')';
        };
        
        $this->reiniciaConexion($request);

        // Primero, validar si la columna es autoincrementable
        $dbType = session()->get('credencialesConsulta')['db_type'];
        
        $nombresColumnas = $this->selectColumnsByTable($dbType,$tabla);     
        $registros = DB::connection('consulta')->select('SELECT * FROM ' . $tabla);

        if (!$param) {
            return response()->json(['errortipo' => 'No se ingresaron parametros para la consulta.']);        
        }

        if (!$registros) {
            return response()->json(['errortipo' => 'La tabla analizada esta vacia.']);        
        }

        try{
            if($dbType=='mysql'){
                $pk = DB::connection('consulta')->select("SELECT column_name FROM information_schema.key_column_usage WHERE table_name = '" . $tabla . "' AND constraint_name = 'PRIMARY'");
            }else{
                $pk = DB::connection('consulta')->select("
                    SELECT COLUMN_NAME 
                    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
                    WHERE TABLE_NAME = '$tabla' 
                    AND CONSTRAINT_NAME IN (
                        SELECT CONSTRAINT_NAME
                        FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
                        WHERE TABLE_NAME = '$tabla'
                        AND CONSTRAINT_TYPE = 'PRIMARY KEY'
                    )
                ");
            }
            //dd($pk);


            //dd($pk);
            $query= $this->queryCampos($dbType, $tabla, $pk, $column, $param);
            //dd($query);
            $resultados = DB::connection('consulta')->select($query);
                
            
            Historial::create([
                'conexion' => session()->get('credencialesConsulta')['id'],
                'user' => Auth::id(),
                'fecha' => now()->timezone('America/Lima')->toDateTimeString(),
                'tipo' => 'campos',
                'tabla' => $tabla,
                'resultado' => $query
                // 'resultado' => $messagesConcatenados
            ]);
            return response()->json(['message' => 'Recibiendo resultados de la excepcion...','results' => $resultados,'columna' => $column, 'columnas' => $nombresColumnas , 'datos' => $registros]);
        } catch(QueryException $e) {
            return response()->json([
                'error' => true,
                'message' => 'No se pudo realizar la consulta.',
            ]);
        }
    }

    public function resultTablas(Request $request){
    
        $this->reiniciaConexion($request);

        $tablao = $request->input('tablao', null);
        $tablad = $request->input('tablad', null);
        $columnao = $request->input('columnao', null);
        $columnad = $request->input('columnad', null);

        //dd($tablao,$tablad,$columnao,$columnad);
        $dbType = session()->get('credencialesConsulta')['db_type'];

        try{
            $query= $this->queryTablas($dbType,$tablao,$tablad,$columnao,$columnad);
            //dd($query);
            $resultados = DB::connection('consulta')->select($query);
            
            
            //dd($query);
            Historial::create([
                'conexion' => session()->get('credencialesConsulta')['id'],
                'user' => Auth::id(),
                'fecha' => now()->timezone('America/Lima')->toDateTimeString(),
                'tipo' => 'tablas',
                'tabla' => $tablao . ',' . $tablad,
                'resultado' => $query
            ]);
            return response()->json(['message' => 'Recibiendo resultados de la excepcion...','results' => $resultados]);
        } catch (\Exception $e) {
            // En caso de error, capturar la excepción y devolver un mensaje de error
            return response()->json(['message' => 'Error al ejecutar la consulta.', 'error' => $e->getMessage()], 500);
        }
        
    }



    // Metodos:

    // Crear la configuracion dinamicamente
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

    // Cada que se hace una consulta HTTP el controlador se instancia nuevamente.
    // Por ello se usa session para guardar algunos valores
    // Aqui los recolectamos y conectamos
    public function reiniciaConexion(Request $request){
        $credentials = $request->session()->get('credencialesConsulta');
        $this->instanciarConexion($credentials);
    }

    // Lo que hacemos aqui es borrar las credenciales de una bd
    // conectada para poder conectarnos nuevamente a otra.
    public function disconnect(Request $request){
            $request->session()->put('conexion', false);
            $request->session()->forget('credencialesConsulta');

            // Devolver una respuesta de éxito
            return response()->json([
                'success' => true,
                'message' => 'Se desconectó correctamente.',
            ]);
            
    }

    // Para mostrar las tablas segun el gestor elegido
    public function selectTablesByGestor($dbType){
        if($dbType == 'mysql'){
            $tablas = DB::connection('consulta')->select('SHOW TABLES');
        }else if($dbType == 'sqlsrv'){
            $tablas = DB::connection('consulta')->select("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_TYPE = 'BASE TABLE'");
        }
        return $tablas;
    }

    // Para mostrar las columnas de la tabla elegida
    public function selectColumnsByTable($dbType,$tableName){
        if($dbType == 'mysql'){
            $columns = DB::connection('consulta')->select("SELECT DISTINCT COLUMN_NAME as 'Field',COLUMN_TYPE as 'Type',Extra,IS_NULLABLE as 'Null', COLUMN_DEFAULT as 'Default' FROM information_schema.columns WHERE TABLE_NAME = '" . $tableName . "'");
        }else if($dbType == 'sqlsrv'){
            $columns = DB::connection('consulta')->select("SELECT 
                ic.COLUMN_NAME AS Field,
                CONCAT(ic.DATA_TYPE, 
                    CASE 
                        WHEN ic.DATA_TYPE IN ('nvarchar', 'varchar', 'char', 'nchar', 'binary', 'varbinary') THEN '(' + CAST(ic.CHARACTER_MAXIMUM_LENGTH AS VARCHAR(10)) + ')'
                        ELSE ''
                    END
                ) AS Type ,
                CASE 
                    WHEN sc.is_identity = 1 THEN 'auto_increment'
                    ELSE 'NO'  
                END AS 'Extra',
                CASE 
                    WHEN ic.IS_NULLABLE = 'YES' THEN 'YES'
                    ELSE 'NO'  
                END AS 'Null',
                ic.COLUMN_DEFAULT AS 'Default'
                FROM INFORMATION_SCHEMA.COLUMNS ic
                LEFT JOIN sys.columns sc ON ic.COLUMN_NAME = sc.name AND OBJECT_NAME(sc.object_id) = ic.TABLE_NAME
                WHERE ic.TABLE_NAME = '$tableName';" 
            );      
        }
        return $columns;
    }

    public function selectColumnsRelByTable($dbType,$tableName){
        if ($dbType == 'mysql') {
            $columns = DB::connection('consulta')->select("
                SELECT
                    COLUMN_NAME as COLUMNA,
                    REFERENCED_TABLE_NAME as TABLA,
                    REFERENCED_COLUMN_NAME as FORANEA
                FROM
                    INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE
                    TABLE_NAME = '" . $tableName . "'
                    AND REFERENCED_TABLE_NAME IS NOT NULL");
        }else{
            $columns = DB::connection('consulta')->select("
            SELECT
                cu.COLUMN_NAME as COLUMNA,
                ccu.TABLE_NAME as TABLA,
                ccu.COLUMN_NAME as FORANEA
            FROM
                INFORMATION_SCHEMA.TABLE_CONSTRAINTS tc
            JOIN
                INFORMATION_SCHEMA.CONSTRAINT_COLUMN_USAGE cu
            ON
                tc.CONSTRAINT_NAME = cu.CONSTRAINT_NAME
            JOIN
                INFORMATION_SCHEMA.REFERENTIAL_CONSTRAINTS rc
            ON
                tc.CONSTRAINT_NAME = rc.CONSTRAINT_NAME
            JOIN
                INFORMATION_SCHEMA.CONSTRAINT_COLUMN_USAGE ccu
            ON
                rc.UNIQUE_CONSTRAINT_NAME = ccu.CONSTRAINT_NAME
            WHERE
                tc.TABLE_NAME = '" . $tableName . "'
                AND cu.TABLE_NAME = '" . $tableName . "'");
        }
        
        return $columns;
    }

    // Query secuencialidad segun el tipo de gestor
    public function querySecuencialidad($dbType, $tabla, $columna){
        $query = "";
    
        if($dbType == 'mysql'){
            $query = "
                WITH RECURSIVE num_series AS (
                SELECT 1 AS start_id, MAX(`{$columna}`) AS end_id FROM `{$tabla}`
                UNION ALL
                SELECT start_id + 1, end_id FROM num_series WHERE start_id < end_id
                )
                SELECT CONCAT('En la tabla ', '{$tabla}', ' , falta el registro con ID ', ns.start_id) AS message
                FROM num_series ns
                LEFT JOIN `{$tabla}` tt ON ns.start_id = tt.`{$columna}`
                WHERE tt.`{$columna}` IS NULL
                ORDER BY ns.start_id;
            ";
        }else if($dbType == 'sqlsrv'){
            $query = "
                DECLARE @max_number INT;
                SELECT @max_number = MAX({$columna}) FROM {$tabla};
                WITH NumbersCTE AS (
                    SELECT ROW_NUMBER() OVER (ORDER BY (SELECT NULL)) AS n
                    FROM sys.columns AS c1
                    CROSS JOIN sys.columns AS c2
                )
                SELECT CONCAT('En la tabla ', '{$tabla}', ' , falta el registro con ID ', n) AS message
                FROM NumbersCTE
                WHERE n <= @max_number
                AND n NOT IN (SELECT {$columna} FROM {$tabla})
                ORDER BY n;
            ";
        }
        return $query;
    }
    
    
    public function queryTablas($dbType, $tablao, $tablad, $columnao, $columnad){
        $query = "";
        if($dbType == 'mysql'){
            // Para MySQL
            $query = "SELECT 
                    CASE 
                        WHEN t2.$columnad IS NULL THEN 
                            CONCAT('Excepción de integridad. El dato ', t1.$columnao, ' en la columna $columnao no son encontrados en la columna $columnad de la tabla $tablad.')
                    END AS 'message'
                    FROM 
                        $tablao AS t1
                    LEFT JOIN 
                        $tablad AS t2 ON t1.$columnao = t2.$columnad
                    WHERE t2.$columnad IS NULL;";
        }else if($dbType == 'sqlsrv'){
            // Para SQLServer
            $query = "SELECT 
                    CASE 
                        WHEN t2.$columnad IS NULL THEN 
                            'Excepción de integridad. El dato ' + CONVERT(VARCHAR, t1.$columnao) + ' en la columna $columnao no son encontrados en la columna $columnad de la tabla $tablad.'
                    END AS 'message'
                    FROM 
                        $tablao AS t1
                    LEFT JOIN 
                        $tablad AS t2 ON t1.$columnao = t2.$columnad
                    WHERE t2.$columnad IS NULL;";
        }
        return $query;
    }
    
    public function queryCampos($dbType, $tabla, $pk, $column, $param){
        $sqlQuery = "";
        
        if($dbType == 'mysql'){
            $pkConcatenated = implode(", ', ', ", array_map(function($col) {
                return "CAST(`{$col->column_name}` AS CHAR)";
            }, $pk));
    
            $sqlQuery = "SELECT *, CONCAT('En el registro con identificador(es) ', $pkConcatenated, ' presenta una excepción de campo \"" . $column . "\" para los parametros ingresados.') AS message FROM $tabla $param";
        }else if($dbType == 'sqlsrv'){
            // Para SQLServer
            $pkConcatenated = implode(" + ', ' + ", array_map(function($col) {
                return "CAST({$col->COLUMN_NAME} AS VARCHAR)";
            }, $pk));
    
            $sqlQuery = "SELECT *, ('En el registro con identificador(es) ' + $pkConcatenated + ' presenta una excepción de campo \"" . $column . "\" para los parametros ingresados.') AS message FROM $tabla $param";
        }
        return $sqlQuery;
    }
    

    // public function queryTablas($dbType, $tablao, $tablad, $columnao, $columnad){
    //     if($dbType == 'mysql'){
    //         // Para MySQL
    //         $resultados = DB::connection('consulta')->select(
    //             "SELECT 
    //             CASE 
    //                 WHEN t2.$columnad IS NULL THEN 
    //                     CONCAT('Excepción de integridad. El dato ', t1.$columnao, ' en la columna $columnao no son encontrados en la columna $columnad de la tabla $tablad.')
    //             END AS 'message'
    //             FROM 
    //                 $tablao AS t1
    //             LEFT JOIN 
    //                 $tablad AS t2 ON t1.$columnao = t2.$columnad
    //             WHERE t2.$columnad IS NULL;"
    //         );
    //     }else if($dbType == 'sqlsrv'){
    //         // Para SQLServer
    //         $resultados =  DB::connection('consulta')->select(
    //             "SELECT 
    //             CASE 
    //                 WHEN t2.$columnad IS NULL THEN 
    //                     'Excepción de integridad. El dato ' + CONVERT(VARCHAR, t1.$columnao) + ' en la columna $columnao no son encontrados en la columna $columnad de la tabla $tablad.'
    //             END AS 'message'
    //             FROM 
    //                 $tablao AS t1
    //             LEFT JOIN 
    //                 $tablad AS t2 ON t1.$columnao = t2.$columnad
    //             WHERE t2.$columnad IS NULL;"
    //         );
    //     }
    //     return $resultados;
    // }
    
    
}
