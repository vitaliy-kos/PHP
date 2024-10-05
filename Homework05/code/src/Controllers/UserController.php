<?php

namespace Geekbrains\Application1\Controllers;

use Geekbrains\Application1\Render;
use Geekbrains\Application1\Models\User;

class UserController {
    public Render $render;

    public function __construct() {
        $this->render = new Render;
    }

    public function actionIndex(): string {
        $users = User::getAllUsersFromStorage();
        
        

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
                'title' => 'Список пользователей в хранилище',
                'message' => "Список пуст или не найден"
            ]
        );
    }

    function actionSave() : void {
        $name = $_GET['name'];
        $birthday = $_GET['birthday'];

        $user = new User();
        $user->setName($name);
        $user->setBirthdayFromString($birthday);
        $user->save();

        header('Location: /user');
    }
}