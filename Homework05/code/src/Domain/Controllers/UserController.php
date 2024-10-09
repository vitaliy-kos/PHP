<?php

namespace Geekbrains\Application\Domain\Controllers;

use Exception;
use Geekbrains\Application\Application\Application;
use Geekbrains\Application\Application\Auth;
use Geekbrains\Application\Application\Render;
use Geekbrains\Application\Domain\Models\User;

class UserController extends AbstractController {
    public Render $render;
    protected array $actionsPermissions = [
        'actionIndex' => ['admin', 'user'],
        'actionCreate' => ['admin', 'user'],
        'actionEdit' => ['admin', 'user'],
        'actionSave' => ['admin'],
        'actionHash' => ['admin', 'user'],
    ];

    public function __construct() {
        $this->render = new Render;
    }

    public function actionIndex(): string {
        $users = User::all();

        if(!$users){
            return $this->render->renderPage(
                'user/empty.twig',
                [
                    'title' => 'Список пользователей в хранилище',
                    'message' => "Список пуст или не найден"
                ]
            );
        }
        else{
            return $this->render->renderPage(
                'user/index.twig',
                [
                    'title' => 'Список пользователей в хранилище',
                    'users' => $users
                ]
            );
        }
    }

    function actionCreate() : string {
        return $this->render->renderPageWithForm(
            'user/create.twig',
            [
                'title' => 'Создание пользователя'
            ]
        );
    }

    function actionEdit() {
        if (User::exists($_GET['id'])) {
            $user = User::all()[$_GET['id']];
            
            return $this->render->renderPageWithForm(
                'user/edit.twig',
                [
                    'title' => 'Редактирование пользователя',
                    'user' => $user
                ]
            );
        }
        else {
            throw new Exception("Пользователя с данным id не существует!");
        }
    }

    public function actionSave() {
        if (User::validateRequestData()) {
            $user = new User();
            $user->setParamsFromRequestData();
            $user->saveToStorage();

            header('Location: /user');
        }
        else {
            throw new Exception("Переданные данные некорректны");
        }
    }

    public function actionUpdate() {
        if (User::exists($_POST['id'])) {

            if (!User::validateRequestData()) throw new Exception("Ошибка валидации данных");

            $user = new User;
            $user->setId($_POST['id']);
            
            $arrayData = [];

            if (isset($_POST['firstname'])) {
                $arrayData['user_firstname'] = $_POST['firstname'];
            }

            if (isset($_POST['lastname'])) {
                $arrayData['user_lastname'] = $_POST['lastname'];
            }

            if (isset($_POST['birthday'])) {
                $arrayData['user_birthday_timestamp'] = strtotime($_POST['birthday']);
            }
            
            $user->updateUser($_POST['id'], $arrayData);
            return $this->actionIndex();
        }
        else {
            throw new Exception("Пользователь не существует");
        }
    }

    public function actionDelete(): void {
        if (User::exists($_POST['id'])) {
            User::deleteFromStorage($_POST['id']);
        }
    }

    public function actionAuth($error_message = false): string {
        return $this->render->renderPageWithForm(
            'auth.twig', 
            [
                'title' => 'Форма логина',
                'auth_success' => $error_message ? false : true,
                'auth_error' => $error_message
            ]
        );
    }

    public function actionHash(): string {
        return Auth::getPasswordHash($_GET['pass_string']);
    }

    public function actionLogout() {
        session_destroy();
        setcookie('auth', '', strtotime('+30 days'), '/');
        header('Location: /');
        exit;
    }

    public function actionLogin(): string {
        $result = false;

        if (isset($_POST['login']) && isset($_POST['password'])) {
            $result = Application::$auth->proceedAuth($_POST['login'], $_POST['password'], $_POST['remember']);
        }
        
        if (!$result){
            return $this->actionAuth("Неверный логин/пароль");
        }
        else {
            header('Location: /');
            exit;
        }
    }

    public function actionIndexRefresh(){
        $moreId = null;
        
        if(isset($_POST['maxId']) && ($_POST['maxId'] > 0)){
            $moreId = $_POST['maxId'];
        }

        $users = User::all($moreId);
        $usersData = [];

        if (count($users) > 0) {
            foreach($users as $user){
                $usersData[] = $user->getUserDataAsArray();
            }
        }

        return json_encode($usersData);
    }

}