
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">

<body style="background-color:  #d6e2e8; margin: 0px; padding: 30px; font-size: 18px;" >
    <div class="row text-center">
        <h3>Reporte de excepciones</h3>
        <table class="p-4" style="width: 100%;">
            <thead>
            </thead>
            <tbody>
                <tr>
                    <td width="70%">
                        <p><b>Tipo: </b>Tablas</p>
                        <p><b>Fecha: </b>{{$fecha}}</p>
                        <p><b>Usuario:</b> {{$user}}</p>
                        <p><b>Base de Datos: </b>{{$bd}}</p>
                    </td>
                    <td width="30%">
                        <img src="img/buscar.png" alt="">
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="row">
        <div class="px-4">
            <strong>ACCION:</strong>
            <ul>
                <li>Es crucial implementar restricciones de integridad referencial en la base de datos y verificar la integridad de los datos en la capa de la aplicación antes de realizar operaciones de inserción o actualización.</li>
            </ul>
        </div>
        <div class="p-4">
            <strong>RESULTADO:</strong>
            <ol>
                @foreach ($resultados as $r)
                    <li style="font-size: 10px">{{ $r->message }}</li>
                @endforeach
            </ol>
        </div>
    </div>
</body>
</html>

