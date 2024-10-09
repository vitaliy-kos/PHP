<?php

namespace Geekbrains\Application\Application;

use Geekbrains\Application\Application\Render;
use Geekbrains\Application\Domain\Controllers\AbstractController;
use Geekbrains\Application\Infrastructure\Config;
use Geekbrains\Application\Infrastructure\Storage;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Level;
use Monolog\Logger;

class Application {
    private const APP_NAMESPACE = 'Geekbrains\Application\Domain\Controllers\\';
    private string $controllerName;
    private string $methodName;
    public static Config $config;
    public static Storage $storage;
    public static Auth $auth;
    public static Logger $logger;

    public function __construct(){
        Application::$config = new Config;
        Application::$storage = new Storage;
        Application::$auth = new Auth;
        
        Application::$logger = new Logger('application_logger');
        Application::$logger->pushHandler(new StreamHandler(
            $_SERVER['DOCUMENT_ROOT'] . "/log/" . Application::$config->get()['log']['LOGS_FILE'] . "-" . date('d-m-Y') . ".log", Level::Debug
        ));
        Application::$logger->pushHandler(new FirePHPHandler());
    }

    public function run() {
        session_start();

        if (isset($_COOKIE['auth'])) {
            Auth::authUserByCookies($_COOKIE['auth']);
        }
        
        $routeArray = explode('/', $_SERVER['REQUEST_URI']);
        
        $controllerName = isset($routeArray[1]) && $routeArray[1] != '' ? $routeArray[1] : "page";

        $this->controllerName = Application::APP_NAMESPACE . ucfirst($controllerName) . "Controller";

        if (class_exists($this->controllerName)){
            
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
                
                if ($controllerInstance instanceof AbstractController){
                    if ($this->checkAccessToMethod($controllerInstance, $this->methodName)) {
                        return call_user_func_array(
                            [$controllerInstance, $this->methodName],
                            []
                        );
                    }
                    else {
                        $render = new Render();
                        return $render->renderPage(
                            'error.twig',
                            [
                                'title' => 'Доступ запрещен',
                                'error_title' => 'Отказано в доступе',
                                'error_message' => 'Доступ к странице запрещен!'
                            ]
                        );
                    }
                }
                else{
                    return call_user_func_array(
                        [$controllerInstance, $this->methodName],
                        []
                    );
                }
                
            }
            else {
                $log_message = "Попытка вызова метода {$this->methodName} в контроллере {$this->controllerName}";
                Application::$logger->error($log_message);

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

    private function checkAccessToMethod(AbstractController $controllerInstance, string $methodName): bool {
        $userRoles = $controllerInstance->getUserRoles();
        
        $rules = $controllerInstance->getActionsPermissions($methodName);
        
        $isAllowed = false;

        if (!empty($rules)) {
            foreach ($rules as $rolePermission) {
                if (in_array($rolePermission, $userRoles)){
                    $isAllowed = true;
                    break;
                }
            }
        } else {
            $isAllowed = true;
        }
        
        return $isAllowed;
    }

}