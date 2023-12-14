<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    @vite(['resources/js/app.js', 'resources/css/app.css'])
</head>
<body class="min-h-screen bg-slate-50 dark:bg-sky-900 text-sky-900 dark:text-sky-50 p-4 grid">
{{ $slot }}
</body>
</html>
