<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Inkluvia adalah platform pendidikan inklusif berbasis web untuk tunanetra">
    <title>@yield('title', 'Inkluvia - Platform Edukasi Inklusif')</title>
    <link rel="icon" href="{{ asset('assets/icon.png') }}">
    @vite(['public/css/app.css', 'public/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">
    @yield('content')
</body>
</html>