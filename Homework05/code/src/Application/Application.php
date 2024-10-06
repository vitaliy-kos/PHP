<?php

namespace Geekbrains\Application\Application;

use Geekbrains\Application\Application\Render;
use Geekbrains\Application\Infrastructure\Config;
use Geekbrains\Application\Infrastructure\Storage;

class Application {

    private const APP_NAMESPACE = 'Geekbrains\Application\Domain\Controllers\\';

    private string $controllerName;
    private string $methodName;
    public static Config $config;
    public static Storage $storage;

    public function __construct(){
        Application::$config = new Config;
        Application::$storage = new Storage;
    }

    public function run() : string {
        $routeArray = explode('/', $_SERVER['REQUEST_URI']);
        
        $controllerName = isset($routeArray[1]) && $routeArray[1] != '' ? $routeArray[1] : "page";

        $this->controllerName = Application::APP_NAMESPACE . ucfirst($controllerName) . "Controller";

        if(class_exists($this->controllerName)){
            
            if (isset($routeArray[2]) && $routeArray[2] != '') {
                $methodWithParamsArr = explode('?', $routeArray[2]);
                $methodName = $methodWithParamsArr[0];
            }
            else {
                $methodName = "index";
            }

            $this->methodName = "action" . ucfirst($methodName);
            
            if (method_exists($this->controllerName, $this->methodName)){
                
                $controllerInstance = new $this->controllerName();
                
                return call_user_func_array(
                    [$controllerInstance, $this->methodName],
                    []
                );
                
            }
            else {
                return $this->renderNotFoundPage();
            }
        }
        else {
            return $this->renderNotFoundPage();
        }
    }

    function renderNotFoundPage() : string {
        $render = new Render();

        http_response_code(404);

        return $render->renderPage(
            '404.twig',
            [
                'title' => 'Страница не найдена',
                'message' => 'Ошибка 404. Страница не существует!'
            ]
        );
    }

}