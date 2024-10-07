<?php
namespace Geekbrains\Application\Domain\Models;
use Geekbrains\Application\Application\Application;

class User {
    private ?int $idUser;
    private ?string $userFirstName;
    private ?string $userLastName;
    private ?int $userBirthday;

    public function __construct(string $firstName = null, string $lastName = null, int $birthday = null, int $id_user = null){
        $this->userFirstName = $firstName;
        $this->userLastName = $lastName;
        $this->userBirthday = $birthday;
        $this->idUser = $id_user;
    }

    public function setId(int $id_user): void {
        $this->idUser = $id_user;
    }

    public function getId(): ?int {
        return $this->idUser;
    }

    public function setFirstName(string $userName) : void {
        $this->userFirstName = $userName;
    }

    public function setLastName(string $userLastName) : void {
        $this->userLastName = $userLastName;
    }

    public function getFirstName(): string {
        return $this->userFirstName;
    }

    public function getLastName(): string {
        return $this->userLastName;
    }

    public function getBirthday(): int {
        return $this->userBirthday;
    }

    public function setBirthdayFromString(string $birthdayString) : void {
        $this->userBirthday = strtotime($birthdayString);
    }

    public static function validateRequestData(): bool {
        $result = true;

        if (!(isset($_POST['firstname']) && !empty($_POST['firstname']) &&
              isset($_POST['lastname']) && !empty($_POST['lastname']) &&
              isset($_POST['birthday']) && !empty($_POST['birthday']))
        ){
            return false;
        }

        if(!preg_match('/^(\d{2}-\d{2}-\d{4})$/', $_POST['birthday'])){
            $result =  false;
        }
        
        if(!isset($_SESSION['csrf_token']) || $_SESSION['csrf_token'] != $_POST['csrf_token']){
            $result = false;
        }

        return $result;
    }

    public function setParamsFromRequestData(): void {
        $this->userFirstName = htmlspecialchars($_POST['firstname']);
        $this->userLastName = htmlspecialchars($_POST['lastname']);
        $this->setBirthdayFromString($_POST['birthday']); 
    }

    public function saveToStorage(){
        $sql = "INSERT INTO users (user_firstname, user_lastname, user_birthday_timestamp) VALUES (:user_firstname, :user_lastname, :user_birthday)";

        $handler = Application::$storage->get()->prepare($sql);
        $handler->execute([
            'user_firstname' => $this->userFirstName,
            'user_lastname' => $this->userLastName,
            'user_birthday' => $this->userBirthday
        ]);
    }

    public static function all() : array {
        $sql = "SELECT * FROM users";

        $handler = Application::$storage->get()->prepare($sql);
        $handler->execute([]);

        $data = $handler->fetchAll();

        $result = [];

        foreach ($data as $dbString) {
            $result[$dbString['id']] = new User($dbString['user_firstname'], $dbString['user_lastname'], $dbString['user_birthday_timestamp'], $dbString['id']);
        }

        return $result;
    }

    public static function exists(int $id): bool{
        $sql = "SELECT count(id) as users_count FROM users WHERE id = :id";

        $handler = Application::$storage->get()->prepare($sql);
        $handler->execute([
            'id' => $id
        ]);

        $result = $handler->fetchAll();

        if (count($result) > 0 && $result[0]['users_count'] > 0) {
            return true;
        }
        else{
            return false;
        }
    }

    public function updateUser(int $id, array $userDataArray): void {
        $sql = "UPDATE users SET ";

        $counter = 0;
        foreach($userDataArray as $key => $value) {
            $sql .= "$key = :$key";

            if ($counter != count($userDataArray)-1) {
                $sql .= ",";
            }

            $counter++;
        }

        $sql .= " WHERE id = $id";
        
        $handler = Application::$storage->get()->prepare($sql);
        $handler->execute($userDataArray);
    }

    public static function deleteFromStorage(int $user_id) : void {
        $sql = "DELETE FROM users WHERE id = :id";

        $handler = Application::$storage->get()->prepare($sql);
        $handler->execute(['id' => $user_id]);
    }

    public static function getRoles($id) : array {
        $roles = [];

        $rolesSql = "SELECT * FROM user_roles WHERE id_user = :id";

        $handler = Application::$storage->get()->prepare($rolesSql);
        $handler->execute(['id' => $id]);
        $result = $handler->fetchAll();

        if(!empty($result)){
            foreach($result as $role){
                $roles[] = $role['role'];
            }
        }

        return $roles;
    }
    
}