<?php

namespace Geekbrains\Application\Domain\Controllers;

use Geekbrains\Application\Application\Application;
use Geekbrains\Application\Domain\Models\User;

class AbstractController {

    protected array $actionsPermissions = [];
    
    public function getUserRoles(): array {
        
        if (isset($_SESSION['id'])){
            $roles = User::getRoles($_SESSION['id']);
        }
       
        return $roles ?? [];
    }

    public function getActionsPermissions(string $methodName): array {
        return $this->actionsPermissions[$methodName] ?? [];
    }
}