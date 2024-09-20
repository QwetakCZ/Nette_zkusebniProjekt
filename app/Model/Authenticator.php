<?php

namespace App\Model;

use Exception;
use Nette\Database\ForeignKeyConstraintViolationException;
use Nette\Security\IIdentity;
use Nette\Security\Passwords;
use Nette\Security\Role;
use Nette\Security\SimpleIdentity;

class Authenticator implements \Nette\Security\Authenticator
{
    public function __construct(
        private UserManager $userManager,
        private Passwords $passwords,
        private RoleManager $roleManager,


    )
    {
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    function authenticate(string $user, string $password): IIdentity
    {
        $row = $this->userManager->getByEmail($user);

        if(!$row)
        {
            throw new Exception('Uzivatel nenalezen');
        }

        if(!$this->passwords->verify($password, $row->password))
        {
            throw new Exception('Spatne heslo');
        }

        $user = $row->toArray();
        unset($user['password']);


        $roles = $this->roleManager->findByUserIdToSelect($row->id);

        return new SimpleIdentity(
            $row->id,
            $roles,
            $user
        );


    }


}