<?php
namespace Geekbrains\Application\Application;

class Auth {
    public static function getPasswordHash(string $rawPassword): string {
        return password_hash($_GET['pass_string'], PASSWORD_BCRYPT);
    }

    public function proceedAuth(string $login, string $password, string $rememberMe): bool {
        $sql = "SELECT id, user_firstname, user_lastname, password_hash FROM users WHERE login = :login";

        $handler = Application::$storage->get()->prepare($sql);
        $handler->execute(['login' => $login]);
        $result = $handler->fetchAll();

        if(!empty($result) && password_verify($password, $result[0]['password_hash'])){
            $_SESSION['user_firstname'] = $result[0]['user_firstname'];
            $_SESSION['user_lastname'] = $result[0]['user_lastname'];
            $_SESSION['id'] = $result[0]['id'];
            
            if ((int) $rememberMe) {
                $auth_token = md5(random_bytes(32));
                setcookie('auth', $auth_token, strtotime('+365 days'), '/');

                $sql = "UPDATE users SET hash = :hash WHERE id = :id";
                $handler = Application::$storage->get()->prepare($sql);
                $handler->execute(['hash' => $auth_token, 'id' => $result[0]['id'] ]);
            }
            
            return true;
        }
        else {
            return false;
        }
    }

    public static function authUserByCookies($auth = false): void {

        if ($auth) {
            $sql = "SELECT id, user_firstname, user_lastname FROM users WHERE hash = :hash";

            $handler = Application::$storage->get()->prepare($sql);
            $handler->execute(['hash' => $auth]);
            $result = $handler->fetchAll();

            if (!empty($result)) {
                $_SESSION['user_firstname'] = $result[0]['user_firstname'];
                $_SESSION['user_lastname'] = $result[0]['user_lastname'];
                $_SESSION['id'] = $result[0]['id'];
            }
           

        }
    }

    
}