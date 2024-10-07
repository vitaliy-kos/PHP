<?php
namespace Geekbrains\Application\Domain\Controllers;

use Geekbrains\Application\Application\Render;

class PageController {

    public function actionIndex() {
        $render = new Render();
        
        return $render->renderPage(
            'index.twig', 
            [
                'title' => 'Главная страница'
            ]
        );
    }
}