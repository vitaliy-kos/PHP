<?php
namespace Geekbrains\Application\Application;

use Exception;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\Environment;

class Render {

    private string $viewFolder = '/Domain/Views/';
    private FilesystemLoader $loader;
    private Environment $environment;

    public function __construct(){

        $this->loader = new FilesystemLoader(dirname(__DIR__) . $this->viewFolder);
        $this->environment = new Environment($this->loader, [
            'debug' => true,
           // 'cache' => $_SERVER['DOCUMENT_ROOT'].'/cache/',
        ]);
        $this->environment->addExtension(new DebugExtension());
    }

    public function renderPage(string $contentTemplateName = 'index.twig', array $templateVariables = []) {
        $template = $this->environment->load($contentTemplateName);

        $templateVariables['user_authorized'] = $_SESSION['id'] ?? false ? true : false;
        $templateVariables['user_firstname'] = $_SESSION['user_firstname'] ?? "";

        return $template->render($templateVariables);
    }

    public static function renderExceptionPage(Exception $exception): string {
        $contentTemplateName = "error.twig";
        $viewFolder = '/src/Domain/Views/';

        $loader = new FilesystemLoader($_SERVER['DOCUMENT_ROOT'] . $viewFolder);
        $environment = new Environment($loader, [
            // 'cache' => $_SERVER['DOCUMENT_ROOT'].'/cache/',
        ]);

        $template = $environment->load('error.twig');
        
        $templateVariables['content_template_name'] = $contentTemplateName;
        $templateVariables['error_message'] = $exception->getMessage();
        
        return $template->render($templateVariables);
    }

    public function renderPageWithForm(string $contentTemplateName = 'page-index.tpl', array $templateVariables = []) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        
        $templateVariables['csrf_token'] = $_SESSION['csrf_token'];
        
        return $this->renderPage($contentTemplateName, $templateVariables);
    }
}