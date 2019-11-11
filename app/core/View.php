<?php

namespace App\Core;

class View
{
    public $path;
    public $route;
    public $layout = 'default';

    public function __construct($route) {
        $this->route = $route;
        $this->path = $route['controller'].'/'.$route['action'];
    }

    public function render($title, $vars = []) {
        extract($vars);
        ob_start();
        $path = 'app/views/'.$this->path.'.php';
        if (file_exists($path)) {
            require $path;
            $content = ob_get_clean();
            require 'app/views/layouts/'.$this->layout.'.php';
        } else {
            echo 'View not found! '.$this->path;
        }
    }

    public function redirect($url) {
        header('location: '.$url);
        exit;
    }

    public static function errorCode($code) {
        http_response_code($code);
        // require 'app/views/errors/ErrorContent.php';
        $errorPath = 'app/views/errors/'.$code.'.php';
        if (file_exists($errorPath)) {
            require $errorPath;
        } else {
            echo '<b>Ошибка!</b>';
        }
        exit;
    }

}
