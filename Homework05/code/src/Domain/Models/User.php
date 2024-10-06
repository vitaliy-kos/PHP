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

    public static function validateRequestData(): bool{
        if (
            isset($_GET['firstname']) && !empty($_GET['firstname']) &&
            isset($_GET['lastname']) && !empty($_GET['lastname']) &&
            isset($_GET['birthday']) && !empty($_GET['birthday'])
        ){
            return true;
        }
        else{
            return false;
        }
    }

    public function setParamsFromRequestData(): void {
        $this->userFirstName = $_GET['firstname'];
        $this->userLastName = $_GET['lastname'];
        $this->setBirthdayFromString($_GET['birthday']); 
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

    public function updateUser(array $userDataArray): void{
        $sql = "UPDATE users SET ";

        $counter = 0;
        foreach($userDataArray as $key => $value) {
            $sql .= "$key = :$key";

            if($counter != count($userDataArray)-1) {
                $sql .= ",";
            }

            $counter++;
        }
        
        $handler = Application::$storage->get()->prepare($sql);
        $handler->execute($userDataArray);
    }

    public static function deleteFromStorage(int $user_id) : void {
        $sql = "DELETE FROM users WHERE id = :id";

        $handler = Application::$storage->get()->prepare($sql);
        $handler->execute(['id' => $user_id]);
    }
    
}