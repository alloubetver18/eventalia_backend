<?php

namespace App\Classes;

use Symfony\Component\Security\Core\User\UserInterface;

class CustomCommonUser implements UserInterface
{
    private $roles;
    private $userIdentifier;

    public function __construct(string $userIdentifier, array $roles)
    {
        $this->userIdentifier = $userIdentifier;
        $this->roles = $roles;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function eraseCredentials(): void
    {
        // Aquí puedes implementar la lógica para borrar las credenciales sensibles
    }

    public function getUserIdentifier(): string
    {
        return $this->userIdentifier;
    }
}
