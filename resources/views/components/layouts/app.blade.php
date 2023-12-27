<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
    @filamentStyles
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-slate-50 dark:bg-sky-900 text-sky-900 dark:text-sky-50 p-4 grid">
{{ $slot }}

@livewire('notifications')
@filamentScripts
@vite('resources/js/app.js')
</body>
</html>
