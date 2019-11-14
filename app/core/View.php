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
            echo '<b>View not found!</b> '.$this->path;
        }
    }

    public function redirect($url) {
        header('location: '.$url);
        exit;
    }

    public static function errorCode($code) {
        http_response_code($code);
        $error_path = 'app/views/errors/'.$code.'.php';
        ob_start();
        if (file_exists($error_path)) {
            require $error_path;
        } else {
            echo '<b>Ошибка!</b>';
        }
        $error_content = ob_get_clean();
        require 'app/views/errors/ErrorLayout.php';
        exit;
    }

}
