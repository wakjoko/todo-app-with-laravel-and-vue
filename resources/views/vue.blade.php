<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-bs-theme="auto">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title></title>
        @vite(['resources/scss/app.scss'])
    </head>
    <body>
        <div id="app">
            <router-view />
        </div>

        <script type="text/javascript">      
            window.csrf_token = "{{ csrf_token() }}"
        </script>
        
        @vite(['resources/js/app.js'])
    </body>
</html>
