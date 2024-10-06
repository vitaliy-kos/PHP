<?php

namespace Geekbrains\Application\Domain\Controllers;

use Exception;
use Geekbrains\Application\Application\Render;
use Geekbrains\Application\Domain\Models\User;

use function PHPSTORM_META\map;

class UserController {
    public Render $render;

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
        return $this->render->renderPage(
            'user/create.twig',
            [
                'title' => 'Создание пользователя'
            ]
        );
    }

    function actionEdit() {
        if (User::exists($_GET['id'])) {
            $user = User::all()[$_GET['id']];
            
            return $this->render->renderPage(
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
        if (User::exists($_GET['id'])) {
            $user = new User;
            $user->setId($_GET['id']);
            
            $arrayData = [];

            if (isset($_GET['firstname'])) {
                $arrayData['user_firstname'] = $_GET['firstname'];
            }

            if (isset($_GET['lastname'])) {
                $arrayData['user_lastname'] = $_GET['lastname'];
            }

            if (isset($_GET['birthday'])) {
                $arrayData['user_birthday_timestamp'] = strtotime($_GET['birthday']);
            }
            
            $user->updateUser($arrayData);
            return $this->actionIndex();
        }
        else {
            throw new Exception("Пользователь не существует");
        }
    }

    public function actionDelete() {
        if(User::exists($_GET['id'])) {
            User::deleteFromStorage($_GET['id']);

            header('Location: /user');
        }
        else {
            throw new Exception("Пользователь не существует");
        }
    }

}