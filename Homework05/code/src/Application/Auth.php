<?php
namespace Geekbrains\Application\Application;

class Auth {
    public static function getPasswordHash(string $rawPassword): string {
        return password_hash($_GET['pass_string'], PASSWORD_BCRYPT);
    }

    public static function getToken(): string {
        return bin2hex(random_bytes(32));
    }

    public function proceedAuth(string $login, string $password, string $rememberMe): bool {
        $sql = "SELECT users.id, 
                       users.user_firstname, 
                       users.user_lastname, 
                       users.password_hash, 
                       user_roles.role
                FROM users 
                LEFT JOIN user_roles ON user_roles.id_user = users.id
                WHERE users.login = :login";

        $handler = Application::$storage->get()->prepare($sql);
        $handler->execute(['login' => $login]);
        $result = $handler->fetchAll();

        if(!empty($result) && password_verify($password, $result[0]['password_hash'])){
            static::setSession($result[0]['id'], $result[0]['user_firstname'], $result[0]['user_lastname'], $result[0]['role'] ?? '');
            
            if ((int) $rememberMe) {
                $auth_token = static::getToken();
                setcookie('auth', $auth_token, strtotime('+30 days'), '/');

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
            $sql = "SELECT users.id, users.user_firstname, users.user_lastname, user_roles.role
                    FROM users 
                    LEFT JOIN user_roles ON user_roles.id_user = users.id
                    WHERE users.hash = :hash";

            $handler = Application::$storage->get()->prepare($sql);
            $handler->execute(['hash' => $auth]);
            $result = $handler->fetchAll();

            if (!empty($result)) {
                static::setSession($result[0]['id'], $result[0]['user_firstname'], $result[0]['user_lastname'], $result[0]['role'] ?? '');
            }
           
        }
    }

    private static function setSession(int $id, string $user_firstname, string $user_lastname, string $role) : void {
        $_SESSION['id'] = $id;
        $_SESSION['user_firstname'] = $user_firstname;
        $_SESSION['user_lastname'] = $user_lastname;
        $_SESSION['role'] = $role;
    }

    
}