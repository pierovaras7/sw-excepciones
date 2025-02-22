<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingresar al sistema</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">
</head>
<body>
        <div class="container">
        @if ($errors->any())
            <div>
                <ul>
                    @foreach ($errors->all() as $error)
                        <div id="resultMessage" class="alert alert-warning" role="alert">
                            {{ $error }}
                        </div>
                    @endforeach
                </ul>
            </div>
        @endif
            <div class="max-w-xs">
                <div>
                    <img src="https://www.shutterstock.com/image-photo/businessman-hands-working-laptop-top-600nw-2309662399.jpg" alt="">
                </div>
                
                <form class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4" method="POST" action="{{ route('logearse') }}">
                    @csrf
                    <div class="mb-4">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="username">
                        Email
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" name="email" type="text" placeholder="Email">
                    </div>
                    <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2" for="password">
                        Password
                    </label>
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 mb-3 leading-tight focus:outline-none focus:shadow-outline" name="password" type="password" placeholder="******************">
                    <p class="text-red-500 text-xs italic hidden">Please choose a password.</p>
                    </div>
                    <div class="flex items-center justify-center w-full">
                        <button class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline" type="submit">
                            Ingresar
                        </button>
                        <!-- <a class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800" href="#">
                            
                        </a> -->
                    </div>
                </form>
                <div class="flex flex-col items-center">
                    <p class="text-xs">Credentials</p>
                    <p class="text-xs">Email: test_user@gmail.com</p>
                    <p class="text-xs">Password: test_user</p>
                    <p class="text-xs">&copy;2024 - Piero Varas.</p>
                </p>
            </div>
        </div>
</body>
</html>
<script>
    setTimeout(function(){
        document.getElementById("resultMessage").style.display = "none";
    }, 2000); // 2000 milisegundos = 2 segundos
</script>