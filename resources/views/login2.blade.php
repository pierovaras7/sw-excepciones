<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ingresar al sistema</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
        <div class="container">
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
                <p class="text-center text-gray-500 text-xs">
                    &copy;2020 Grupo 03.
                </p>
            </div>
        </div>
</body>
</html>