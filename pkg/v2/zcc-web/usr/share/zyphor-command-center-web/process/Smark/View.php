<?php

namespace App\Smark;

use eftec\bladeone\BladeOne;

class View
{
    public static function render($view, $data)
    {
        $views = './views';
        $cache = './cache';

        $layout = "<!DOCTYPE html>
<html lang=\"en\">
<head>
    <meta charset=\"UTF-8\">
    <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
    <title>App</title>
</head>
<body>
    @yield('content')
</body>
</html>
        ";

        $page = "@extends('layouts.app')
@section('content')
    <main>

        {{-- Silence is golden. --}}

    </main>
@endsection
        ";

        if (!is_dir($cache)) {
            mkdir($cache);
        }

        if (!is_dir($views)) {
            mkdir($views);
        }

        if (!is_dir($views."/layouts/")) {
            mkdir($views."/layouts/");
        }

        if (!file_exists($views."/".$view.".blade.php") ) {

            if (!file_exists($views.'/layouts/app.blade.php')) {
                file_put_contents($views."/layouts/app.blade.php", $layout);
            }

            file_put_contents($views."/".$view.".blade.php", $page);

            $blade = new BladeOne($views,$cache,BladeOne::MODE_DEBUG);
            echo $blade->run($view, $data);
        } else {
            $blade = new BladeOne($views,$cache,BladeOne::MODE_DEBUG);
            echo $blade->run($view, $data);
        }
    }
}
