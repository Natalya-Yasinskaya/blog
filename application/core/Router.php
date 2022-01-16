<?php 

namespace application\core;

use application\core\View;

class Router {

    protected $routes = [];
    protected $params = [];

    function __construct() {
        $routes = require 'application/config/routes.php';
        foreach ($routes as $route => $params) {
            $this->add($route, $params);
        }
    }

    public function add($route, $params) {
       $regex = '#'.$route.'#';
       $this->routes[$regex] = $params;
    }

    public function match($url) {               
        foreach ($this->routes as $route => $params) {
            if (preg_match($route, $url)) {     
                $this->params = $params;
                $this->regex = $route;
               return true;
            }
        }
        return false;
    }

    public function run(){
        $url = trim(parse_url($_SERVER['REQUEST_URI'])['path'], '/');
        if ($this->match($url)) {
            $path = 'application\controllers\\'.ucfirst($this->params['controller']).'Controller';
            if (class_exists($path)) {
                $action = $this->params['action'].'Action';
                if (method_exists($path, $action)) {
                    $controller = new $path($this->regex, $this->params);
                    $controller->$action();
                } else {
                    View::errorCode(404);
                }
            } else {
                View::errorCode(404);
            }
        } else {
            View::errorCode(404);
        }
    }
}
?> 